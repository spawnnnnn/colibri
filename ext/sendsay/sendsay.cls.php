<?php

    class SendSayFormField extends Object {
        
        public function __construct($id, $params) {
            parent::__construct();
            $this->id = $id;
            
            if($params['type'] == '1m') {
                // удаляем order и answers и делаем нормальный список значений
                $order = $params['order'];
                $values = $params['answers'];
                $v = array();
                foreach($order as $o) {
                    $v[$o] = $values[$o];
                }     
                $params['values'] = $v;
                unset($params['order']);
                unset($params['answers']);
            }
            
            foreach($params as $param => $value) {
                $this->$param = $value;
            }
        }
        
    }

    class SendSayForm extends Object {
        
        public function __construct($id, $params, $fields) {
            parent::__construct();
            $this->id = $id;
            foreach($params as $param => $value) {
                $this->$param = $value;
            }
            
            $this->fields = $fields;
            
        }
        
    }

    class SendSayMember extends Object {
        
        private $_forms;
        
        public function __construct($data, $forms) {
            parent::__construct();
            $this->_forms = $forms;
            
            // Получаем список полей, и обрабатываем данные
            foreach($this->_forms as $form) {
                
                if(!isset($data[$form->id])) {
                    $data[$form->id] = array();
                }
                
                $dta = $data[$form->id];
                foreach($form->fields as $field) {
                    if(!isset($dta[$field->id])) {
                        // тут нужно выбрать значение по умолчанию
                        $dta[$field->id] = '';
                    }
                    
                    $value = $dta[$field->id];
                    switch($field->type) {
                        case 'free':
                            
                            break;
                        case 'dt':
                            $value = Variable::IsEmpty($value) ? strftime('%Y-%m-%d', time()) : $value;
                            break;
                        case '1m':
                            if(Variable::IsEmpty($value) && $field->values) {
                                // $r = array_keys($field->values);
                                $value = null; //reset($r);
                            }
                            else {
                                if(is_array($value)) $value = reset($value);
                            }
                            break;
                    }
                    
                    $this->{$field->id} = $value;
                    
                }
                
            }
            
            
            
        } 
        
        public function getData() {
            $result = array();
            foreach($this->_forms as $form) {
                $result[$form->id] = array();
                foreach($form->fields as $field) {
                    
                    $value = $this->_data[$field->id];
                    switch($field->type) {
                        case '1m':
                            $value = array($value);
                            break;
                    }
                    
                    $result[$form->id][$field->id] = $value;
                }
            }
            return $result;
        }
        
    }

    class SendSayException extends BaseException {
        
        private $_errors;
        
        public function __construct($errorsArray) {
            parent::__construct('Ошибка в обработке запроса', 404);
            $this->_errors = $errorsArray;
        }
        
    }
    
    class SendSayMessage extends Object {
        
        public function __construct($data) {
            parent::__construct($data);
        }
        
        public static function CreateHTMLMessage($subject, $fromName, $fromEmail, $replyName, $replyEmail, $toName, $html) {
            return new SendSayMessage(array(
                'format' => 'html',
                'subject' => $subject,
                'from.name' => $fromName,
                'from.email' => $fromEmail,
                'reply.name' => $replyName,
                'reply.email' => $replyEmail,
                'to.name' => $toName,
                'message' => array('html' => $html),
            ));
        }
        
        public static function CreateTEXTMessage($subject, $fromName, $fromEmail, $replyName, $replyEmail, $toName, $html) {
            return new SendSayMessage(array(
                'format' => 'text',
                'subject' => $subject,
                'from.name' => $fromName,
                'from.email' => $fromEmail,
                'reply.name' => $replyName,
                'reply.email' => $replyEmail,
                'to.name' => $toName,
                'message' => array('text' => $html),
            ));
        }

        public static function CreateFromDraft($id) {
            return new SendSayMessage(array(
                'format' => 'draft',
                'draft.id' => $id
            ));
        }
        
    }
    
    class SendSayIssue extends Object {
        
        public function __construct($data) {
            parent::__construct($data);
            $this->letter = new SendSayMessage($this->letter);
        }
        
        public function __set($property, $value) {
            switch(strtolower($property)) {
                default: parent::__set($property, $value); break;
                case 'letter': {
                    $this->_data['letter'] = $value;
                    $this->sender = $this->letter->{'from.name'};
                    $this->from = $this->letter->{'from.email'};
                    $this->format = $this->letter->format;
                    $this->subject = $this->letter->subject;
                    $this->name = $this->letter->subject.' / '.Date::ToDBString(time());
                    if($this->format == 'html')
                        $this->text = $this->letter->message['html'];
                    else if($this->format == 'text')
                        $this->text = $this->letter->message['text'];
                    break;
                }
            }
        }
        
        public function Copy() {
            $data = $this->_data;
            unset($data['id']);
            return new SendSayIssue($data);
        }
        
    }
    
    class SendSayApi {
    
        const GroupMassSending = 'masssending';
        const GroupPersonal = 'personal';
        
        const SendNow = 'now';
        const SendSave = 'save';
        
        const ResultSave = 'save';
        const ResultResponse = 'response';
        
        const StatsResultFormatCSV = 'csv';
        const StatsResultFormatXLSX = 'xlsx';
        
        const StatsResultSortDirectionAsc = 0;
        const StatsResultSortDirectionDesc = 1;
        
        const StatsTotalResultNone = 'none';
        const StatsTotalResultYes = 'yes';
        const StatsTotalResultOnly = 'only';
        
        private $_credencials;
        private $_sessionId;
        
        public function __construct($login, $sublogin, $password) {
            $this->_credencials = array(
                'login'    => $login,
                'sublogin' => $sublogin,
                'passwd'   => $password
            );
        }
        
        public static function Create($login, $sublogin, $password) {
            return new SendSayApi($login, $sublogin, $password);
        }
        
        private function _request($redirect = '', $params = array(), $auth = true) {
            
            // добавляем доступы
            $data = $params;
            if($auth) {
                if($this->_sessionId)
                    $data['session'] = $this->_sessionId;
                else
                    $data['one_time_auth'] = $this->_credencials;
            }
                
            $curl = curl_init('https://pro.subscribe.ru/api'.$redirect.'?apiversion=100&json=1');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, 'request='.urlencode(json_encode($data)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($curl);
            curl_close($curl);

            $json = json_decode($result, true);
            if(!$json) {
                return (object)array('error' => 'error/bad_json', 'explain' => $result);
            }
            
            $json = (object)$json;

            if(isset($json->REDIRECT))
                return $this->_request($json->REDIRECT, $params);
            
            return $json;
        }
        
        public function Auth() {
            
            $result = $this->_request('', array_merge(array('action' => 'login'), $this->_credencials), false);
            if(isset($result->session)) {
                $this->_sessionId = $result->session;
                return true;
            }
            
            $this->_sessionId = false;
            return false;
        
        }
        
        /**
         * Проверяет доступность сервера Sendsay.
         * 
         * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D0%B8%D0%BD%D0%B3-%D0%B1%D0%B5%D0%B7-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8][Документация]
         * 
         * @return bool
         */
        public function Ping() {
            $result = $this->_request('', array('action' => 'ping'), false);
            return isset($result->pong);
        }
        
        /**
         * Пинг с авторизацией.
         * 
         * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D0%B8%D0%BD%D0%B3-%D1%81-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B5%D0%B9][Документация]
         * 
         * @return bool
         */
        public function Pong() {
            $result = $this->_request('', array('action' => 'pong'), true);// с авторизацией
            return isset($result->ping);
        }
        
        public function Forms() { // anketa.list
            $list = array();
            $data = $this->_request('', array('action' => 'anketa.list'), true);
            foreach($data->list as $form) {
                
                $form = (object)$form;
                $formData = (object)$this->_request('', array('action' => 'anketa.get', 'id' => $form->id), true);
                $fieldsOrder = $formData->obj['order'];
                $fields = $formData->obj['quests'];
                
                $flds = array();
                foreach($fieldsOrder as $fieldId) {
                    $flds[] = new SendSayFormField($fieldId, $fields[$fieldId]);
                }
                
                $formId = $formData->obj['id'];
                $formParams = $formData->obj['param'];
                
                $list[] = new SendSayForm($formId, $formParams, $flds);

            }
            return $list;
        }
        
        /**
        * получает или создает новый email
        * 
        * 
        * @param mixed $email - электронный адрес 
        * @param mixed $forms - список акнет (весь список) полученный через функцию Forms
        * @return SendSayMember
        */
        public function Member($email, $forms) { // список анкет, чтобы спарсить
            $data = (object)$this->_request('', array('action' => 'member.get', 'email' => $email), true);
            if(!isset($data->obj)) {
                $data->obj = array('member' => array('email' => $email));
            }
            return new SendSayMember($data->obj, $forms);
        } 
        
        public function SaveMember(SendSayMember $member, $requestConfirmation = false, $notifyTemplate = false) {
            
            $result = (object)$this->_request('', array(
                'action'         => 'member.set',
                'addr_type'      => 'email',
                'email'          => $member->email,
                'source'         => $_SERVER['REMOTE_ADDR'],
                'if_exists'      => 'overwrite',
                'newbie.confirm' => $requestConfirmation,
                'obj' => $member->getData(),
                'newbie.letter.no-confirm' => !$requestConfirmation ? null : $notifyTemplate
            ), true);   
            
            if(isset($result->errors)) {
                throw new SendSayException($result->errors);
            }
            
            return (object)$result;
        }
        
        public function DeleteMembers($members, $sync = false) {
            $params = array(
                'action'         => 'member.delete',
                'sync'           => $sync,
            );
            if($members instanceOf SendSayMember) {
                $params['list'] = array($members->email);
            }
            else if(is_array($members)) {
                $list = array();
                foreach($members as $member) {
                    if($member instanceOf SendSayMember) { $list[] = $member->email; } else { $list[] = $member; }
                }
                $params['list'] = $list;
            }
            else {
                $params['group'] = $members;
            }
            

            $result = (object)$this->_request('', $params, true);   
            if(isset($result->errors)) {
                throw new SendSayException($result->errors);
            }
                                 
            return $result;
            
        }
        
        /**
        * получение списка выпусков из арxива
        * 
        * @param mixed $dts - дата с
        * @param mixed $dte - дата по
        * @param mixed $groups - список групп
        * @param mixed $format - формат выпуска email / sms
        */
        public function ArchivedIssues($dts = '1900-01-01', $dte = null, $groups = array(), $format = 'email') {
            $params = array(
                'action'         => 'issue.list',
                'from'           => $dts,
                'group'          => $groups,
                'format'         => $format,
            );
            
            if(!is_null($dte)) {
                $params['upto'] = $dte;
            }
            
            $result = (object)$this->_request('', $params, true);   
            if(isset($result->errors)) {
                throw new SendSayException($result->errors);
            }
                                 
            return $result;
        }
        
        /**
        * получение арxивного выпуска по ID
        * 
        * @param mixed $id
        */
        public function ArchivedIssue($id) {
            $params = array(
                'action'         => 'issue.get',
                'id'             => $id,
            );
            
            $result = (object)$this->_request('', $params, true);   
            if(isset($result->errors)) {
                throw new SendSayException($result->errors);
            }
                                 
            return $result;
        }
        
        /**
        * получение черновика по ID
        * 
        * @param mixed $id
        */
        public function Issue($id = 99999999) {
            $params = array(
                'action'         => 'issue.draft.get',
                'id'             => $id,
            );
            
            $result = (object)$this->_request('', $params, true);   
            if(isset($result->errors)) {
                // не существует, просто создаем новый обьект
                $params = array();
                if($id != 99999999) {
                    $params['id'] = $id;
                }
                return new SendSayIssue($params);
            }
                                 
            return new SendSayIssue($result->obj);
        }
        
        /**
        * получение черновика по ID
        * 
        * @param mixed $id
        */
        public function SaveIssue(SendSayIssue $issue) {
            
            $objectParams = array();
            $objectParams['name'] = $issue->name;
            $objectParams['format'] = $issue->format;
            $objectParams['from'] = $issue->from;
            $objectParams['sender'] = $issue->sender;
            $objectParams['reply.email'] = $issue->letter->{'reply.email'};
            $objectParams['reply.name'] = $issue->letter->{'reply.name'};
            $objectParams['to.name'] = $issue->letter->{'to.email'};
            $objectParams['subject'] = $issue->subject;
            $objectParams['text'] = $issue->text;
            
            $params = array(
                'action'            => 'issue.draft.set',
                'obj'               => $objectParams,
                'return_fresh_obj'  => true
            );
            
            if(!is_null($issue->id)) {
                $params['id'] = $issue->id;
            }
            
            $result = (object)$this->_request('', $params, true);   
            if(isset($result->errors)) {
                throw new SendSayException($result->errors);
            }
                                 
            return new SendSayIssue($result->obj);
        }
        
        /**
        * удаление черновиков
        * 
        * 
        * @param mixed $ids
        */
        public function DeleteIssue($ids) {
            $params = array(
                'action'           => 'issue.draft.delete',
                'id'               => $ids
            );
            
            $result = (object)$this->_request('', $params, true);   
            if(isset($result->errors)) {
                throw new SendSayException($result->errors);
            }
            
            return true;
        }
        
        
        /**
        * Отправка сообщения
        * 
        * @param SendSayIssue $issue
        * @param mixed $recipients - массив из SendSayMember
        * @param mixed $relink - массив преобразований
        * @param mixed $when - когда
        */
        public function SendIssue(SendSayIssue $issue, array $recipients, array $relink = null, $whom = SendSayApi::GroupPersonal, $when = SendSayApi::SendNow) {
            
            $letter = $issue->letter;
            
            $params = array(
                'action'            => 'issue.send',
                'group'             => $whom,
                'letter'            => $issue->letter->ToArray(),
                'sendwhen'          => $when,
                'relink'            => is_null($relink) ? 0 : 1,
                'relink.param'      => is_null($relink) ? array() : array_merge(array('link' => 1, 'image' => 0, 'test' => 1), $relink)
            );

            /*if (is_array($sender)) {
                $params['extra'] = $issue->from;
            }*/

            $recps = array();
            foreach($recipients as $r) {
                $recps[] = $r->email;
            }
            
            $params['users.list'] = $recps;
            
            $result = (object)$this->_request('', $params, true);   
            if(isset($result->errors)) {
                out($result);
                throw new SendSayException($result->errors);
            }
                                 
            return $result;
            
        }
        
        /**
        * Извлекает статистику активности подписчиков.
        * 
        * @param mixed $page
        * @param mixed $pagesize
        * @param mixed $filter
        * @param mixed $sort
        * @param mixed $sortDirection
        * @param mixed $result
        * @param mixed $format
        */
        public function GetActivityStats($page = 1, $pagesize = 20, $filter = array(), $sort = 'date', $sortDirection = SendSayApi::StatsResultSortDirectionDesc, $result = SendSayApi::ResultResponse, $format = SendSayApi::StatsResultFormatCSV) {
            
            $params = array(
                'action'   => 'stat.activity',
                'sort'     => $sort,
                'desc'     => $sortDirection,
                'result'   => is_array($result) ? 'email' : $result,
                'page'     => $page,
                'pagesize' => $pagesize
            );
            
            if($params['result'] == 'email') {
                $params['email'] = $result;
            }
            else if($params['result'] == 'save') {
                $this->params['result.format'] = $format;
            }
            
            $result = (object)$this->_request('', $params, true);   
            if(isset($result->errors)) {
                throw new SendSayException($result->errors);
            }
            
            return $result->list;
            
        }
                                 
        public function GetIssueStats($dts = null, $dte = null, $groups = array(), $groupby = 'YM', $total = SendSayApi::StatsTotalResultNone, $withEmpty = false, $result = SendSayApi::ResultResponse, $format = SendSayApi::StatsResultFormatCSV) {
            
            $params = array(
                'action'     => 'stat.issue',
                'group'      => $groups,
                'groupby'    => $groupby,
                'total'      => $total,
                'withempty'  => $withEmpty,
                'result'     => is_array($result) ? 'email' : $result
            );

            if(!is_null($dts))
                $params['issue.from'] = $dts;
            if(!is_null($dte))
                $params['issue.upto'] = $dte;

            
            if($params['result'] == 'email') {
                $params['email'] = $result;
            }
            else if($params['result'] == 'save') {
                $this->params['result.format'] = $format;
            }
            
            $result = (object)$this->_request('', $params, true);   
            if(isset($result->errors)) {
                throw new SendSayException($result->errors);
            }
            
            return $result->list;
            
        }
        
        public function GetUniversalStats($select, $filter = array(), $order = array(), $result = SendSayApi::ResultResponse, $format = SendSayApi::StatsResultFormatCSV, $skip = 0, $count = null) {
            
            $params = array(
                'action' => 'stat.uni',
                'skip'   => $skip,
                'select' => $select,
                'order'  => $order,
                'filter' => $filter,
                'result' => is_array($result) ? 'email' : $result
            );

            if(!is_null($count))
                $params['first'] = $count;

            if($params['result'] == 'email') {
                $params['email'] = $result;
            }
            else if($params['result'] == 'save') {
                $this->params['result.format'] = $format;
            }
            
            $result = (object)$this->_request('', $params, true);   
            if(isset($result->errors)) {
                throw new SendSayException($result->errors);
            }
            
            return $result->list;
            
        }
        
    }
?>
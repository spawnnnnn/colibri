<?php
    
    Core::Using('Lib::Services', _PROJECT);
    
    class AuthApi extends ServiceApi {

        private $_sessionID = false;
        private $_apitoken = false;
        
        private static $_checkedInstance = null;
        
        public static function GetCheckedInstance() {
            if (self::$_checkedInstance === null) {
                self::$_checkedInstance = new self();
                self::$_checkedInstance->Check();
            }
            return self::$_checkedInstance;
        }
        
        public $current = false;
        
        public function __construct($domain = false) {
            parent::__construct($this->_serviceUrl('auth', false, $domain), null, false);
            if(isset($_COOKIE['MNSESSID']))
                $this->_sessionID = $_COOKIE['MNSESSID'];
            else {
                $_COOKIE['MNSESSID'] = session_id();
                setcookie('MNSESSID', session_id(), time() + 30 * 86400, '/');
                $this->_sessionID = session_id();
            }
            $this->Token();
        }
        
        public function ServiceIds($rev = null) {
            if(is_null($rev))
                return (object)array(
                    'test' => 0,
                    'casco' => 1,
                    'mobile' => 2,
                    'consumer' => 3,
                    'deposits' => 4,
                    'autocredits' => 5,
                    'hypothec' => 6,
                    'greencard' => 7,
                    'osago' => 8,
                    'cards' => 9,
                    'microcredits' => 10,
                );
            else 
                return array(
                    'test',
                    'casco',
                    'mobile',
                    'consumer',
                    'deposits',
                    'autocredits',
                    'hypothec',
                    'greencard',
                    'osago',
                    'cards',
                    'microcredits',
                );
        }
        
        public function Token() {
            $options = (object)array(
                'session' => $this->_sessionID
            );
            
            if(isset($_COOKIE['TKN'.$this->_sessionID])) {
                $this->_apitoken = json_decode(base64_decode($_COOKIE['TKN'.$this->_sessionID]));
            }
            else {
                $res = parent::_request('AuthAjaxHandler.Token', $options);
                if(!$res)
                    return false;
                    
                $this->_apitoken = $res->token;
                setcookie('TKN'.$this->_sessionID, base64_encode(json_encode($this->_apitoken)), strtotime($this->_apitoken->created) + $this->_apitoken->lifetime, '/');
                $_COOKIE['TKN'.$this->_sessionID] = base64_encode(json_encode($this->_apitoken));
                
            }
            
            return $this;
        }
        
        public function Check() {
            $options = (object)array( 'hash' => $this->_apitoken->token, 'session' => $this->_sessionID);
            
            $res = parent::_request('AuthAjaxHandler.Check', $options);
            
            if(!$res || ($res->error && $res->message == 'session is not valid')) {
                $this->_apitoken = false;
                setcookie('TKN'.$this->_sessionID, null, -1, '/');
                unset($_COOKIE['TKN'.$this->_sessionID]);
                $this->Token();
            }
            else {
                $this->current = $res->member;
            }
            
            
            return $this;
        }

        public function Login($login, $password) {
            $options = (object)array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
                'login' => $login,
                'password' => $password,
            );
            
            $geo = $this->_getGeoInfo();
            if (isset($geo->id))
                $options->city = $geo->id;
            if (isset($geo->timeoffset))
                $options->timeoffset = $geo->timeoffset;
            
            $res = parent::_request('AuthAjaxHandler.Login', $options); 
            if(!$res)
                return false;    

            $this->current = isset($res->member) ? $res->member : false;
                    
            return $res;
        }
        
        public function ManagerLogin($id) {
            $options = (object)array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
                'id' => $id,
            );
            
            $res = parent::_request('AuthAjaxHandler.ManagerLogin', $options); 
            if(!$res)
                return false;    

            $this->current = isset($res->member) ? $res->member : false;
                    
            return $res;
        }
        
        public function Logout() {
            $options = (object)array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
            );
            
            $res = parent::_request('AuthAjaxHandler.Logout', $options); 
            if(!$res)
                    return false;    

            return $res;
        }
        
        public function Reset($email) {
            $options = (object)array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
                'email' => $email
            );
            
            $res = parent::_request('AuthAjaxHandler.Reset', $options); 
            if(!$res)
                    return false;    

            return $res;
        }
        
        public function SaveProfile($data) {
            $options = (object)array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
                'profile' => $data
            );
            
            $res = parent::_request('AuthAjaxHandler.SaveProfile', $options); 
            if(!$res)
                    return false;    

            return $res;
        }
        
        public function Register($data) {

            $geo = $this->_getGeoInfo();
            if (isset($geo->id))
                $data->city = $geo->id;
            if (isset($geo->timeoffset))
                $data->timeoffset = $geo->timeoffset;
            
            $opts = array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
                'profile' => $data
            );
            

            $res = parent::_request('AuthAjaxHandler.Register', $opts);
            if(!$res)
                return false;
                
            return $res;
        }
        
        public function MemberExists($data) {
            $res = parent::_request('AuthAjaxHandler.MemberExists', $data);
            if(!$res)
                return false;
                
            return $res;
        }
        
        public function AddCalculation($cid, $service, $request, $cleanData) {
            
            $services = $this->ServiceIds();

            $desc = ''; 
            if($services[service] == 6)
                $desc = Numeric::ToMoney($request->price) . '&nbsp;<span class="icon-rur"></span><br />на ' . $request->years . ' лет';
            else if($services[service] == 5)
                $desc = Numeric::ToMoney($request->price) . '&nbsp;<span class="icon-rur"></span><br />на ' . $request->months . ' мес';
            else if($services[service] == 4)
                $desc = Numeric::ToMoney($request->price) . '&nbsp;<span class="icon-rur"></span><br />на ' . $request->months . ' мес';
            else if($services[service] == 3)
                $desc = Numeric::ToMoney($request->price) . '&nbsp;<span class="icon-rur"></span><br />на ' . $request->months . ' мес';
            else if($services[service] == 9)
                $desc = $cleanData->targets[$request->target].name;            
            else if($services[service] == 10)
                $desc = request.price.toMoney() . '&nbsp;<span class="icon-rur"></span><br />на ' . $request->weeks . ' недель';
            
            $options = (object)array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
                'cid' => $cid,
                'desc' => $desc,
                'service' => $services[service],
                'bestrate' => 0
            );
            
            $res = parent::_request('AuthAjaxHandler.AddCalculation', $options);
            if(!$res) 
                return false;
            
            return $res;
        }
        
        public function GetCalculations($page, $pagesize) {
            $options = (object)array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
                'page' => $page,
                'pagesize' => $pagesize,
            );
            
            $res = parent::_request('AuthAjaxHandler.GetCalculations', $options);
            if(!$res)
                return false;
            
            return $res;
        } 
        

        public function AddApplication($cid, $landing, $form, $processor, $data, $additional, $requestid = '', $serviceid = 0, $status = '') {
            /*
                $cid
                $landing -id 
                $form - id
                $processor - string
                $data - object
                $aditional - object
            */
            
            $services = $this->ServiceIds();
            $options = (object)array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
                'cid' => $cid,
                'landing' => $landing,
                'form' => $form,
                'processor' => $processor,
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'additional' => json_encode($additional, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'requestid' => $requestid,
                'serviceid' => $serviceid,
                'status' => $status,
            );
            
            $geo = $this->_getGeoInfo();
            if (isset($geo->timeoffset))
                $options->timeoffset = $geo->timeoffset;
            
            if($this->current) {
                $options->member = $this->current->id;
            }
            
            $res = parent::_request('AuthAjaxHandler.AddApplication', $options);
            
            if(!$res) 
                return false;
            
            return $res;
        }

        public function UpdateApplication($update, $id) {
            
            $options = (object)array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
                'id' => $id,
            );
            foreach ($update as $k => $v) {
                $options->$k = $v;
            }
            
            if($this->current) {
                $options->member = $this->current->id;
            }
            
            $res = parent::_request('AuthAjaxHandler.UpdateApplication', $options);
            
            if(!$res) 
                return false;
            
            return $res;
        }
        
        public function UpdateApplicationStatus($id, $status) {
            
            $options = (object)array(
                'id' => $id,
                'status' => $status,
            );

            $res = parent::_request('AuthAjaxHandler.UpdateApplicationStatus', $options);
            if(!$res) 
                return false;
            
            return $res;
        }
        
        public function GetApplications($page, $pagesize) {
            $options = (object)array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
                'page' => $page,
                'pagesize' => $pagesize,
            );
            
            $res = parent::_request('AuthAjaxHandler.GetApplications', $options);
            if(!$res)
                return false;
            
            return $res;
        }
        
        public function GetApplication($id) {
            $options = (object)array(
                'hash' => $this->_apitoken->token,
                'session' => $this->_sessionID,
                'id' => $id,
            );
            
            $res = parent::_request('AuthAjaxHandler.GetApplication', $options);
            if(!$res)
                return false;
            
            return $res;
        }
        
        public function ManagerGetApplication($id) {
            $options = (object)array(
                'id' => $id,
            );
            
            $res = parent::_request('AuthAjaxHandler.ManagerGetApplication', $options);
            if(!$res)
                return false;
            
            return $res;
        } 
        
        public function AddPostback($id, $name, $data) {
            $services = $this->ServiceIds();
            $options = (object) array(
                'id' => $id,
                'name' => $name,
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            );
            
            $res = parent::_request('AuthAjaxHandler.AddPostback', $options);
            if(!$res) 
                return false;
            
            return $res;
        }
        
        protected function _getGeoInfo() {
            $geo = json_decode(Request::$i->cookie->city);
            return $geo ? $geo : (object) array();
        }
    }
    
    class ScheduleApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('auth'), null, false);
        }
        
        public function AddTask($api, $command, $args, $raising_dts, $raising_dte = null) {
            $options = (object)array(
                'api' => $api,
                'command' => $command,
                'args' => $args,
                'raising_dts' => $raising_dts,
                'raising_dte' => $raising_dte,
            );
            
            $res = parent::_request('ScheduleAjaxHandler.AddTask', $options);
            if(!$res)
                return false;
            
            return $res;
        }
    }
    
    class SendBoxApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('auth'), null, false);
        }
        
        public function FindMails($applicationId = null, $memberId = null, $trackId = null) {
            return parent::_request('SendBoxAjaxHandler.FindMails', array(
                'applicationId' => $applicationId,
                'memberId' => $memberId,
                'trackId' => $trackId,
            ));
        }
    }
    
?>
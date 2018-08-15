<?php
    
    class TransportSource {
        
        private $_sources;
        
        public function __construct() {
            
            $this->_sources = array();
            
            $dtp = new DataPoint('postgres');
            $reader = $dtp->Query('select transport_sources.email as email, transport_sources.password as password, transport_sources_log.date as date, coalesce(transport_sources_log.count, 0) as cnt from transport_sources left join transport_sources_log on transport_sources.email=transport_sources_log.email and transport_sources_log.date=\''.strftime('%Y-%m-%d', time()).'\' order by coalesce(transport_sources_log.count, 0)');
            while($r = $reader->Read()) {
                if(!$r->date) $r->date = strftime('%Y-%m-%d', time());
                if(!$r->cnt) $r->cnt = 0;
                $this->_sources[] = $r;
            }
        }
        
        public function GetSource() {
            $source = reset($this->_sources);
            
            $dtp = new DataPoint('postgres');
            $reader = $dtp->Query('select * from transport_sources_log where date=\''.strftime('%Y-%m-%d', time()).'\' and email=\''.$source->email.'\'');
            if($reader->count > 0)
                $dtp->Update('transport_sources_log', array( 'count' => $source->cnt + 1), 'date=\''.$source->date.'\' and email=\''.$source->email.'\'');    
            else
                $dtp->Insert('transport_sources_log', array( 'email' => $source->email, 'date' => $source->date, 'count' => $source->cnt + 1), 'email');
            
            return $source;
        }
        
        
    }
    
    class TransportMail extends Object {
        
        public function __construct() {
            $args = func_get_args();
            
            if (func_num_args() == 1) {
                parent::__construct($args[0]);
            } elseif (func_num_args() == 2) {
                parent::__construct();
                $this->html = $args[1];
                $this->text = '';
                $this->subject = $args[0];
            }
        }
        
        public function AddFrom($name, $email) {
            $this->from = array(
                'name' => $name,
                'email' => $email,
            );
        }
        
        public function AddTo($name, $email) {
            $this->to = array(
                'name' => $name,
                'email' => $email,
            );
        }
        
        public function AddBcc($name, $email) {
            if ( ! $this->bcc) {
                $this->bcc = array();
            }
            $this->_data['bcc'][] = array(
                'name' => $name,
                'email' => $email,
            );
        }
        
        public static function Create($subject, $message) {
            return new TransportMail($subject, $message);
        }
        
        public static function Restore($data) {
            return new TransportMail($data);
        }
    }
    
    class TransportSms extends Object {
        
        public function __construct() {
            $args = func_get_args();
            
            if (func_num_args() == 1) {
                parent::__construct($args[0]);
            } elseif (func_num_args() == 2) {
                parent::__construct();
                $this->to = str_replace('(', '', str_replace(')', '', str_replace('-', '', str_replace(' ', '', $args[0]))));
                $this->message = $args[1];
            }
        }
        
        public function AddTo($phone) {
            $this->to = $phone;
        }        
        
        public static function Create($phone, $message) {
            return new TransportSms($phone, $message);
        }
        
        public static function Restore($data) {
            return new TransportSms($data);
        }
    }
    
    class Transport { 
        
        public $error;
        
        /**
        * Заявка принята
        */
        const EMAIL_1 = 2372346;
        
        /**
        * Вам звонили?
        */
        const EMAIL_2 = 2372432;
        
        /**
        * До сих пор не звонили?
        */
        const EMAIL_3 = 2372440;
        
        /**
        * Одобрение Заявки
        */
        const EMAIL_4 = 2372445;
        
        /**
        * Иди в банк
        */
        const EMAIL_5 = 2372465;
        
        /**
        * Вас ждут в банке
        */
        const EMAIL_6 = 2372474;
        
        /**
        * Расскажите про свой визит
        */
        const EMAIL_7 = 2372485;
        
        /**
        * Расскажите про свой визит
        */
        const EMAIL_7b = 2372501;
        
        /**
        * Письмо №8
        */
        const EMAIL_8 = 2372505;
        
        /**
        * Письмо №9
        */
        const EMAIL_9 = 2372511;
        
        /**
        * Письмо №10
        */
        const EMAIL_10 = 2372517;
        
        /**
        * Письмо №11
        */
        const EMAIL_11 = 2372532;
        
        /**
        * Письмо №12
        */
        const EMAIL_12 = 2372522;

        
        const LOGIN_USERID = '7996807fb4b4cef847bc7262eb0ffd11';
        const LOGIN_SECRET = '43b0f01cc25250beadfa67161df97c00';
        
        public static $i = false;
        
        private $_api;
        
        private $_protos;
        
        public function __construct($userId = null, $secret = null, $protos = false) {
            if ( ! $userId || ! $secret) {
                $userId = self::LOGIN_USERID;
                $secret = self::LOGIN_SECRET;
            }
            $this->_protos = $protos;
            $this->_api = new SendpulseApi($userId, $secret, 'file');
        }
        
        public static function Create($userId = null, $secret = null, $protos = false) {
            if (self::$i === false) {
                self::$i = new Transport($userId, $secret, $protos);
            }
        }
        
        public function GetTemplate($id) {
            $info = $this->_api->getCampaignInfo($id);
            
            return (object) array(
                'sender_name' => $info->message->sender_name,
                'sender_email' => $info->message->sender_email,
                'subject' => $info->message->subject,
                'body' => $info->message->body,
            );
        }
        
        public function SendMail(TransportMail $mail, $return_ids = false) {
            $data = $mail->ToArray();
            
            $trackId = md5(microtime(true));
            
            $mailer = MailSender::Create(MailerTypes::Smtp, $this->_protos);
            // $mailer->SMTPDebug = true;

            $body = $data['html'];
            $body = str_replace('</body>', '<img width="1" height="1" src="'.ServiceApi::_serviceUrl('auth').'/.sendbox_stats/?trackid='.$trackId.'&action=open" /></body>', $body);
            
            $xml = XMLNode::LoadHTML('<'.'?xml version="1.0" encoding="utf-8" ?'.'>'.$body, false, 'utf-8');
            foreach($xml->Query('//a') as $a) {
                if(strstr($a->attributes->href->value, 'mailto:') === false)
                    $a->attributes->href->value = ServiceApi::_serviceUrl('auth').'/.sendbox_stats/?trackid='.$trackId.'&amp;action=click&amp;redirect='.urlencode($a->attributes->href->value);
            }
            
            $body = $xml->html;
            
            $mail = new MailMessage(new MailAddress($data['from']['email'], $data['from']['name']), new MailAddress($data['to']['email'], $data['to']['name']), $data['subject']);
            $mail->id = $trackId;
            $mail->charset = 'utf-8';
            $mail->contenttype = 'text/html';
            $mail->body = $body;
            $mail->ConfirmReadingTo = 'noreply@moneymatika.ru'; // получаем уведомление о прочтении
            $mail->ReturnPath = 'noreply@moneymatika.ru'; // получаем уведомление о прочтении
            $mail->replyto->Add(new MailAddress('julia.orlova@moneymatika.ru', 'Юлия Орлова'));
            
            
            // ob_start();
            $mailer->Send($mail);
            /*$debugContent = ob_get_contents();
            ob_end_clean();*/
            
            
            // out($debugContent);
            
            return $trackId;
            
            /*$sendMail = $this->_api->smtpSendMail($data);
            
            if ($return_ids) {
                
                sleep(3);
                
                $emails = array();
                foreach ($mail->to as $item) {
                    $emails[] = $item['email'];
                }
                
                $return = array();
                foreach ($emails as $email) {
                    $mailInfo = $this->_api->smtpListEmails(1, 0, '', '', '', $email);
                    $mailInfo = current($mailInfo);
                    $return[$email] = $mailInfo->id;
                }
                
                return $return;
            } else {
                return $sendMail->result;
            }*/
        }
        
        public function SendSms(TransportSms $sms) {
            $data = $sms->ToArray();
            $trackId = md5(microtime(true));
            
            $message = $data['message'];
            $res = preg_match_all('/(https?\:\/\/[^\s]+)/', $message, $matches);
            if($res > 0) {
                foreach($matches[0] as $url) {
                    $googl = new Googl('AIzaSyBvJSJssV8Nics9PUP1-GQfVYu-BwJZrLM');
                    $url2 = ServiceApi::_serviceUrl('auth').'/.sendbox_stats/?trackid='.$trackId.'&action=click&redirect='.urlencode($url);
                    $url2 = $googl->shorten($url2);
                    $message = str_replace($url, $url2, $message);
                }
            }
            
            $sms = SmsService::Create();
            $sms->Send($data['to'], $message);
            
            return $trackId;
            
            /*$sendMail = $this->_api->smtpSendMail($data);
            
            if ($return_ids) {
                
                sleep(3);
                
                $emails = array();
                foreach ($mail->to as $item) {
                    $emails[] = $item['email'];
                }
                
                $return = array();
                foreach ($emails as $email) {
                    $mailInfo = $this->_api->smtpListEmails(1, 0, '', '', '', $email);
                    $mailInfo = current($mailInfo);
                    $return[$email] = $mailInfo->id;
                }
                
                return $return;
            } else {
                return $sendMail->result;
            }*/
        }
        
        public function MailInfo($id) {
            $mailInfo = $this->_api->smtpGetEmailInfoById($id);
            
            return $mailInfo;
        }
    }

?>
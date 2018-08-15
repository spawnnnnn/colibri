<?
    class SmsService {

        private $client, $sessid;
        private $con = 'http://ws.devinosms.com/SmsService.asmx?wsdl';
        private $login = 'moneymatika';
        private $password = 'Moneycall13';
        
        private $debug = true;
        
        private $logdevice = null;
        
        public static function Create($server_url = null, $login = null, $password = null) {
            return new SmsService($server_url, $login, $password);
        }
        
        public function __construct($server_url = null, $login = null, $password = null) {

            $this->debug = SENDSMS ? false : true;

            $this->logdevice = new LogDevice('sms');
            
            $this->_connect($server_url, $login, $password);
        }
        
        private function _connect($server_url = null, $login = null, $password = null) {
            if ($server_url)
                $this->con = $server_url;
                
            if ($login)
                $this->login = $login;
                
            if ($password)
                $this->password = $password;
                
            try{
                $this->client = new SoapClient($this->con, array("trace" => 1,"exceptions" => 1));
            } catch (SoapFault $exception){
                $this->log('ERROR: new SoapClient '.$exception->getCode().' '.$exception->getMessage()); 
                return false;
            }
            
            try{
                $data = new stdClass();
                $data->login = $this->login;
                $data->password = $this->password;
                
                $this->sessid = $this->client->GetSessionID($data);
            } catch (SoapFault $exception){
                $this->log('ERROR: GetSessionID '.$exception->getCode().' '.$exception->getMessage()); 
                return false;
            }
        }

        public function Send($phone, $body){
            if (!$this->client || !$this->sessid)
                return false;
            
            $body = trim(strip_tags($body));
            
            $phone = explode(';', $phone);
            foreach($phone as $k => $v) {
                $phone[$k] = str_replace(array('+7', '(', ')', ' ', '-'), '', '8'.$v);
                if ( !$phone[$k] ) {
                    unset($phone[$k]);
                }
            }
            if ( empty($phone) ) {
                $this->log('ERROR: PhoneDetecting'); 
            }
            
            $message = new stdClass();
            $message->Data = $body;
            $message->DestinationAddresses = $phone;
            $message->SourceAddress = 'moneymatika';
            $message->ReceiptRequested = false;
            
            $data = new stdClass();
            $data->message = $message;
            $data->sessionID = $this->sessid->GetSessionIDResult;
            
            if ($this->debug == true) {
                $this->log('SUCCESS: message="'.$message->Data.'" sended to "'.implode(', ', $phone).'"'); 
            } else {
                try {
                    
                    $this->client->SendMessage($data);
                    $this->log('SUCCESS: message="'.$message->Data.'" sended to "'.implode(', ', $phone).'"'); 
                    
                } catch (SoapFault $exception){
                    
                    $this->log('ERROR: SendMessage '.$exception->getCode().' '.$exception->getMessage()); 
                    return false;
                }
            }
            
            return true;
            
        }
        
        private function log($message){
            $this->logdevice->WriteLine(date('d.m.Y h:i:s',time())." - ".$message. " \n\n");
        }
 
    }
    
    /*$serv = new smsService('sms_remind_about_future_calc');
    $serv->add_var(':cid:', 12567);
    $serv->add_var(':type:', 'КАСКО+ОСАГО');
    if ($serv->send('( 926) 130 -30-   77', 'moneymatika'))
        echo 'sended';
    */
    
?>
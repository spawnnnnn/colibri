<?php

    class ServiceApi {
        
        protected $_domain = null;
        protected $_serviceName = null;
        protected $_token = null;
        protected $_log = null;
        protected $_keepSession = false;
        protected $_allowedCache = array(
            'MobileAjaxHandler.Info',
            'InfoBanksAjaxHandler.PartnersRawInfo',
            'AdvAjaxHandler.Process',
        );
        
        const TYPE_CASCO = 1;
        const TYPE_GREENCARD = 7;
        const TYPE_OSAGO = 8;
        
        const TYPE_TARIFFS = 2;
        const TYPE_NUMBERS = 2;
        
        const TYPE_AUTOCREDITS = 5;
        const TYPE_HYPOTHECCREDITS = 6;
        const TYPE_CONSUMERCREDITS = 3;
        const TYPE_CARDS = 9;
        const TYPE_DEPOSITS = 4;
        const TYPE_MICROCREDITS = 10;

        protected static $enabledInternalCache = false;
        protected static $internalCache = false;
        
        public $cookies = [];
        
        public function __construct($domain, $token = null, $keepSession = false) {
            $this->_domain = $domain;
            $this->_token = $token ? $token : 'token_'.base64_encode(time());
            $this->_keepSession = $keepSession;
            
            $domain = explode('.', str_replace(array('http://', 'https://'), '', $domain));
            $this->_serviceName = reset($domain);
            $this->_serviceName = $this->_serviceName == 'www' ? 'app' : $this->_serviceName;
            
            if(defined('SERVICE_API_LOG') && SERVICE_API_LOG)
                $this->_log = new LogDevice('service-api-requests');
        }
        
        public function __destruct() {
            if($this->_keepSession) {
                try { FileInfo::Delete(Request::$i->server->DOCUMENT_ROOT.'/_cache/'.md5($this->_token).'.cookie'); } catch(Exception $e) { out($e); }
            }
        }
        
        private static function _parseServices() {
            $services = array();
            $dservices = explode(',', API_SERVICES);
            foreach($dservices as $service) {
                $s = explode('=', $service);
                $services[$s[0]] = $s[1];
            }
            return $services;
        }
                
        public static function _serviceUrl($serviceID, $domain = false, $parseFrom = false) {
            $host = !$parseFrom ? $_SERVER['HTTP_HOST'] : $parseFrom;
            $host = str_replace(array('http://', 'https://'), '', $host);
            $host = str_replace('www.', '', $host);
            $services = ServiceApi::_parseServices();
            
            $host = explode('.', $host);
            if(in_array($host[0], $services)) {
                $host[0] = $services[$serviceID];
            }
            else {
                array_splice($host, 0, 0, $services[$serviceID]);
            }
            return ($domain ? '' : $_SERVER['HTTPS'] ? 'https://' : 'http://').implode('.', $host);
        }
        
        public static function _servicePath($serviceID) {
            $droot = $_SERVER['DOCUMENT_ROOT'];
            if(!$droot) $droot = $_SERVER['PWD'];
            $d = explode('/', $droot);
            $d[count($d) - 1] = $serviceID;
            return implode('/', $d);
        }
        
        protected function _request($cmd, $data = array()) {

            if (self::$enabledInternalCache) {
                $cache_key = md5($cmd.serialize($data));
                
                $return = isset(self::$internalCache[$cache_key]) ? self::$internalCache[$cache_key] : null;
                
                if ( ! $return) {
                    $return = $this->__request($cmd, $data);
                    if ($return) {
                        self::$internalCache[$cache_key] = $return;
                    }
                }
            }
            elseif (in_array($cmd, $this->_allowedCache)) {
                $cache_key = md5(serialize($data));
                
                $return = Mem::$i->get('serviceapi'.$cache_key);
                if ( ! $return) {
                    $return = $this->__request($cmd, $data);
                    if ($return) {
                        Mem::Write('serviceapi'.$cache_key , $return, 10*Date::MINUTE);
                    }
                }
            }
            else {
                $return = $this->__request($cmd, $data);
            }
            
            return $return;
        }
        
        /**
        * Отправляет запрос в сервис
        * 
        * @param string $cmd Class.Method
        * @param array $data
        */
        private function __request($cmd, $data = array()) {

            $data = (array) $data;
            
            $data['requestTime'] = time();
            $data['token'] = $this->_token;
            $data['remote_addr'] = Request::$i->server->REMOTE_ADDR;
            
            $requestData = array(
                '__p' => base64_encode(serialize($data))
            );
            
            if($this->_log) $this->_log->WriteLine('Request', Request::$i->remoteip, Request::$i->requesteduri, $data['token'], $data['remote_addr'], $cmd, json_encode($data), json_encode($_SERVER));

            $request = new WebRequest($this->_domain . '/.service/?cmd=' . $cmd, RequestType::Post);
            $request->cookies = $this->cookies ? $this->cookies : $_COOKIE;
            if(isset($data['async'])) {
                $request->async = true;
            }
            else {
                $request->timeout = 60;
            }
            if($this->_keepSession) {
                $request->cookieFile = _CACHE.md5($this->_token).'.cookie';
            }
            $result = $request->Request(RequestData::FromArray($requestData));
            //if($cmd == 'HypothecBanksAjaxHandler.Process') 
            //    out($result);
            
            if ($cmd == 'AuthAjaxHandler.AddApplication') {
                $this->_log = new LogDevice('landing-api-requests');
            }
            
            if($this->_log) $this->_log->WriteLine('Result', Request::$i->remoteip, Request::$i->requesteduri, $result->status, json_encode($result->headers), strlen($result->data), substr($result->data, 0, 10000));

            if ($result->status == 200)
                return json_decode($result->data);

            return false;
        }
        
        private function __requestShell($cmd, $data = array()) {

            $data['requestTime'] = time();
            $data['token'] = $this->_token;
            $data['remote_addr'] = Request::$i->server->REMOTE_ADDR;
            
            $requestData = array(
                't' => base64_encode(json_encode($data))
            );
            
            if($this->_log) $this->_log->WriteLine('Request', Request::$i->remoteip, Request::$i->requesteduri, $data['token'], $data['remote_addr'], $cmd, json_encode($data), json_encode($_SERVER));

            $path = $this->_servicePath($this->_serviceName);
            $return = shell_exec('cd '.$path.' && /usr/bin/php index.php '.$this->_domain.' /.service cmd='.$cmd.' t='.base64_encode(json_encode($data)).(isset($data['async']) ? ' > /dev/null' : ''));
            // out(substr($return, 1, strlen($return) - 2));
            if(substr($return, 0, 1) == '(')
                $return = json_decode(substr($return, 1, strlen($return) - 2));
            else
                $return = json_decode($return);
            if(!$return)
                return false;

            return $return;
            
        }
        
        public function __get($property) {
            if ($property == 'domain') {
                return $this->_domain;
            }
            return null;
        }
        
        public static function NewCID() {
            $webrequest = new WebRequest(ServiceApi::_serviceUrl('app').'/.cid/?token='.sha1(date('H') . '::' . 'cid'));
            $res = $webrequest->Request();
            $cid = intval($res->data);
            return $cid;
        }
        
        public static function EnableInternalCache() {
            self::$enabledInternalCache = true;
        }
        public static function DisableInternalCache() {
            self::$enabledInternalCache = false;
        }
        
        public function Request($cmd, $data = array()) {
            $res = $this->_request($cmd, $data);
            if(!$res)
                return false;
            
            return $res;
        }
        
    }
?>
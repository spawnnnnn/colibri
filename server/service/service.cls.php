<?php
    
    /**
    * Service class
    */
    class Service {
        
        private $_domain;
        private $_service;
        private $_template;
        
        public static function GetList($domain) {
            $list = array();
            $xml = Config::Load(_SERVICES);
            
            if ( ! ($domain = Config::FindDomain($xml, $domain)) )
                return array();
            
            $services = $domain->children;
            foreach($services as $service) {
                $list[] = (object)array(
                    'name' => $service->attributes->name->value,
                    'template' => _SERVICE.$service->attributes->template->value,
                );
            }   
            return $list;
        }
        
        public function __construct($domain, $service) {
            
            $this->_domain = $domain;
            $this->_service = $service;
            
            $this->_template = _SERVICE.$this->_service->attributes->template->value;

            if(_DEBUG) {
                if(!FileInfo::Exists($this->_template))
                    throw new BaseException('Service not found', 500);
            }
            else {
                if(!function_exists('svc_'.md5($this->_template)))
                    require_once(_CACHE.'code/.svc.release');
                if(!function_exists('svc_'.md5($this->_template))) {
                    throw new BaseException('Service not found: '.$this->_template.': '.'svc_'.md5($this->_template), 500);
                }
                
            }
                
            
        }
        
        public function Render($args = array()) {

            $service = $this;
            
            // trying to clean the ob
            try { ob_end_clean(); } catch(Exception $e) {  }
            
            ob_start();
            
            if(!_DEBUG) {
                if(!function_exists('svc_'.md5($this->_template)))
                    require_once(_CACHE.'code/.svc.release');
                if(!function_exists('svc_'.md5($this->_template))) {
                    out($this->_template, 'svc_'.md5($this->_template));
                }
                call_user_func('svc_'.md5($this->_template), $args, $service);
            }
            else
                require($this->_template);
            
            $ret = ob_get_contents();
            ob_end_clean();
            
            return $ret;

        }
        
        public static function Create($domain, $page) {
            return new Service($domain, $page);
        }
        
        public function __get($prop) {
            switch(strtolower($prop)) {

                case 'domain':
                    return $this->_domain;
                
                case 'process':
                    return $this->_page;
                    
                case 'template':
                    return $this->_template;

                case 'service':
                    return $this;

            }
        }
        
        public static function checkServiceIsRunningOnShell($shellCommand) {
            $output = '';
            $code = 0;
            exec("/bin/ps -auxww | /bin/grep ".$shellCommand." | /bin/grep -v grep", $output, $code); 
            if($code!=0 && $code!=1) { 
                throw new BaseException("Unable to '/bin/ps -auxww | /bin/grep $filename | /bin/grep -v grep'. Error code: $code");
                return;
            } 
            if(count($output)>1) { 
                return true;
            }  
            
            return false;
        }
        
    }
    
?>

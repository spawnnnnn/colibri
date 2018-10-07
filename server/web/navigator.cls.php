<?php
    
    /**
    * Navigator class
    */
    class Navigator {
        
        static $i;
        
        private $_pages;
        private $_current;
        private $_service;
        private $_template;
        private $_domain;
        
        public $_initException;
        
        public function __construct() {
            $this->_parse();
        }
        
        public static function Create() {
            if(!Navigator::$i) {
                Navigator::$i = new Navigator();
            }
            
            if(Navigator::$i->_initException) {
                throw new BaseException(Navigator::$i->_initException->getMessage(), Navigator::$i->_initException->getCode());
            }
            
            return Navigator::$i;
        }
        
        private function _parse() {
            
            try {
                
                $domain = Request::$i->server->HTTP_HOST;
                $uri = Request::$i->uri;
                
                $isService = substr($uri, 0, 2) == '/.';
                
                $this->_pages = Config::Load($isService ? _SERVICES : _PAGES);
                if ( ! ($this->_domain = Config::FindDomain($this->_pages, $domain)) )
                    throw new BaseException('There is no website in configuration');

                // if the url starts with . - this is a service, else a layout page
                if($isService) {
                    
                    // replace dot witj nothing to make path usable
                    $uri = str_replace('/.', '/', $uri);
                    $this->_service = $this->_findByPath($uri);
                    if(!$this->_service)
                        throw new BaseException('There is no service in configuration', 404);
                    
                    $this->_template = $this->_service->attributes->template->value;                
                }
                else {
                    $template = '';
                    if($uri == '/') {
                        // it is root
                        $this->_current = $this->_domain;
                        $this->_template = $this->_current->attributes->template->value;
                    }
                    else {
                    
                        $this->_current = $this->_findByPath($uri);
                        if(!$this->_current)
                            throw new BaseException('There is no page in configuration', 404);
                            
                        $this->_template = $this->_current->attributes->template->value;
                        
                    }
                }
            
            }
            catch(Exception $e) {
                $this->_initException = $e;
            }
            
            
        }
        
        private function _findByPath($uri) {
            $uriPattern = '';
            $uriParts = explode('/', trim($uri, '/'));
            foreach($uriParts as $p) {
                $uriPattern .= '/*[@name="'.$p.'"]';
            }
            $uriPattern = trim($uriPattern, '/');

            $pages = $this->_domain->Query('./'.$uriPattern);
            if($pages->count == 0)
                return false;
                
            return $pages->first;
        }
        
        public function __get($prop) {
            switch(strtolower($prop)) {
                
                case 'isservice':
                
                    return !$this->_current && $this->_service;
                
                case 'islayout':
                    
                    return $this->_current && !$this->_service;
                    
                case 'current':
                    
                    return $this->_current;
                
                case 'service':
                    
                    return $this->_service;
                
                case 'template':
                    
                    return $this->_template;
                
                case 'domain':
                    
                    return $this->_domain;
                    
                case 'pages':
                    return $this->_pages;
                                        
            }
        }
        
        public function __set($prop, $value) {
            switch(strtolower($prop)) {
                case 'current':
                    $this->_current = $value;
                    break;
                case 'domain':
                    $this->_domain = $value;
                    break;
            }
        }
        
        private function _path($t) {
            $path = '';
            $p = $t;
            while($p->name != 'domain') {
                $path = $p->attributes->name->value.'/'.$path;
                $p = $p->parent;
            }
            return '/'.$path;
        }
        
        public function Url($t, $p = array(), $anchor = null) {
            if(Variable::IsString($t)) { 
                // if string
                $t = $this->_findByPath($t);
            }
            
            if(!$t)
                return false;
                
            $path = $this->_path($t);
            
            $params = '';
            if(count($p) > 0) {
                $params = '';
                foreach($p as $k=>$v) {
                    $params .= '&'.$k.'='.$v;
                }
                $params = '?'.substr($params, 1);
            }

            if($anchor) {
                $params .= '#'.$anchor;
            }
            
            return $path.$params;
            
        }
        
        public function Redirect($url, $args = array(), $anchor = null) {
            
            $pref = '?';
            if(strstr($url, '?') !== false) {
                $pref = '&';
            }
            
            $argsS = '';
            if(count($args) > 0) {
                foreach($args as $k => $v) {
                    $argsS .= '&'.$k.'='.urlencode($v);
                }
                $argsS = $pref.substr($argsS, 1);
            }
            
            if($anchor) {
                $argsS .= '#'.$anchor;
            }
                
            header('Location: '.$url.$argsS);
        }
            
        public function RedirectPath() {
            $path = '';
            $p = $this->_current;
            while($p->name != 'pages') {
                $path = $p->attributes->name->value.'/'.$path;
                $p = $p->parent;
            }
            return trim($path, '/');
        }
        
    }
    
?>

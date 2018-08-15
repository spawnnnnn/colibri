<?php
    
    /**
    * Layout class
    */
    class Layout {
        
        private $_domain;
        private $_page;
        private $_template;
        private $_location; 
        
        public function __construct($domain, $page) {

            $this->_domain = $domain;
            $this->_page = $page;
            
            if(!$this->_domain->attributes->location)
                $this->_location = _PROJECT;
            else
                $this->_location = $this->_domain->attributes->location->value;
            
            if($this->_page && $this->_page->attributes->template)
                $this->_template = _PATH.$this->_location.$this->_page->attributes->template->value;
            else {
                throw new BaseException('Layout not found', 500);
            }
            
            if(_DEBUG) {
                if(!FileInfo::Exists($this->_template))
                    throw new BaseException('Layout not found', 500);
            }
            else {
                
                if(!function_exists('tpl_'.md5($this->_template)))
                    require_once(_CACHE.'code/.tpl.release');
                if(!function_exists('tpl_'.md5($this->_template))) {
                    throw new BaseException('Layout not found: '.$this->_template.': '.'tpl_'.md5($this->_template), 500);
                }

            }
            
            
        }
        
        public function Render($args = array()) {
            
            $layout = $this;
            $template = null;

            ob_start();
            
            if(!_DEBUG) {
                if(!function_exists('tpl_'.md5($this->_template)))
                    require_once(_CACHE.'code/.tpl.release');
                if(!function_exists('tpl_'.md5($this->_template))) {
                    out($this->_template, 'tpl_'.md5($this->_template));
                }
                call_user_func('tpl_'.md5($this->_template), $args, $layout, $template);
            }
            else
                require($this->_template);
            
            $ret = ob_get_contents();
            ob_end_clean();
            
            return trim($ret);
        }
        
        public static function Create($domain, $page) {
            return new Layout($domain, $page);
        }
        
        public function __get($prop) {
            switch(strtolower($prop)) {

                case 'domain':
                    return $this->_domain;
                
                case 'location':
                    return $this->_location;
                
                case 'page':
                    return $this->_page;
                    
                case 'template':
                    return $this->_template;

                case 'layout':
                    return $this;

            }
        }
        
        
    }
    
?>
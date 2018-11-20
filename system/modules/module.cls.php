<?php
    
    Core::Using('System::Ajax');

    class ModuleConfig {
        
        private $_x;
        
        public function __construct($x) {
            $this->_x = $x;
        }
        
        public function __get($prop) {
            $prop = strtolower(Strings::FromCamelCaseAttr($prop));
            switch($prop) {
                default: {
                    if($this->_x->attributes->$prop) {
                        return $this->_x->attributes->$prop->value;
                    }
                    return false;
                }
            }
        }
        
        public function __set($prop, $value) {
            $prop = strtolower(Strings::FromCamelCaseAttr($prop));       
            switch($prop) {
                default: {
                    if($this->_x->attributes->$prop) {
                        $this->_x->attributes->$prop->value = $value;
                    }
                    break;
                }
            }
        }
        
        public function ToArray() {
            $ret = array();
            foreach($this->_x->attributes as $attr) {
                $ret[Strings::ToCamelCaseAttr($attr->name)] = $attr->value;
            }
            return $ret;
        }
        
    }
    
    class ModuleAjaxHandler extends AjaxHandler {
        
        public function GetConfig($data) {
            
            $moduleAjaxHandlerName = str_replace('AjaxHandler', '', ClassKit::GetName($this));
            
            $configArray = $moduleAjaxHandlerName::$i->Config()->ToArray();
            
            $res = new ObjectEx();
            $res->error = false;
            $res->message = 'ok';
            $res->config = $configArray;
            return $res->data;
        }
        
        public function SaveConfig($data) {
            
            $moduleAjaxHandlerName = str_replace('AjaxHandler', '', ClassKit::GetName($this));
            
            $oldConfig = clone $moduleAjaxHandlerName::$i->Config();
            
            $config = $data->config;
            foreach($config as $key => $value) {
                $moduleAjaxHandlerName::$i->Config()->$key = $value;
            }
            
            ModuleManager::$i->Save();
            
            $configArray = $moduleAjaxHandlerName::$i->Config()->ToArray();
            
            $res = new ObjectEx();
            $res->error = false;
            $res->message = 'ok';
            $res->config = $configArray;
            return $res->data;
            
        }
        
        
        
    }
    
    class Module extends IEventDispatcher {
        
        protected $_config;
        protected $_modulePath;
        

        public function __construct($node) {
            $this->_config = new ModuleConfig($node);
            
            $this->_modulePath = dirname(_MODULES.$this->_config->template).'/';
            
        }
         
        public function RegisterEventHandlers() {
                            
        }

        public function UnregisterEventHandlers() {
            
        }

        public function InitializeModule() {
            
        }
        
        public function Install() {
            
        }
        
        public function Uninstall() {
            
        }

        public function Dispose() {
            
        }
        
        public function __get($prop) {
            if(strtolower($prop) == 'modulepath')
                return $this->_modulePath;
            return false;
        }
        
        public function CompilerFiles() {
            $ret = array();
            return $ret;                                                            
        }
        
        public function Config() {
            return $this->_config;
        }

    }
    
?>

<?php
    
    Core::Using("System::IO");
    
    class ModuleManager {
        
        static $i;
        
        private $_settings;
        private $_domain;
        private $_list;
                      
        public function __construct() {
            $this->_list = new Hashtable();
        }
            
        public static function Create() {
            if (!self::$i){
                $c = __CLASS__;
                self::$i = new $c();
                self::$i->Initialize(Config::Load(_MODS));
            }
            return self::$i;
        }
        
        public function Initialize($template) {
            $this->_settings = $template;
            
            $domain = Request::$i->server->HTTP_HOST;
            $nodes = $this->_settings->Query('./module');
            foreach($nodes as $node) {
                
                $domains = $node->attributes->domains ? explode(',', $node->attributes->domains->value) : array();
                if(!$this->_checkDomain($domain, $domains)) {
                    continue;
                }
                
                $this->InitModule($node);
                
            }

        }

        private function _checkDomain($domain, $domains) {
            foreach($domains as $d) {
                $d = preg_quote($d);
                $d = str_replace('\*', '.*', $d);
                if(preg_match('/^'.$d.'$/i', $domain, $matches) > 0) {
                    return true;
                }
            }
            return false;
        }
        
        public function InitModule($node) {
            $moduleName = $node->attributes->name->value;
            $moduleEntry = $node->attributes->entry->value;
            $moduleTemplate = $node->attributes->template ? $node->attributes->template->value : null;
            $moduleEnable = $node->attributes->enable ? $node->attributes->enable->value : false;
            
            if(!$moduleEnable) {
                return false;
            }

            if(_DEBUG && !is_null($moduleTemplate) && FileInfo::Exists(_MODULES.$moduleTemplate)) {
                require_once(_MODULES.$moduleTemplate);
            }
            
            if(!ClassKit::Exists($moduleEntry))
                return false;
            
            $module = CodeModel::CreateSingletonObject($moduleEntry, $node); 
            $this->_list->Add($moduleEntry, $module);
            $module->InitializeModule();
            
            return $module;
        }
        
        public function __get($property) {
            $property = strtolower($property);
            switch($property) {
                case 'settings': 
                    return $this->_settings;
                case 'list':
                    return $this->_list;
                default: 
                    return $this->_list->$property;
            }
        }
        
        public function Config($name) {
            return $this->_settings->Query('./module[@name="'.$name.'"]')->first;
        }
        
        public function Save() {
           $this->_settings->Save(_MODS);
        }
            
    }
    
?>

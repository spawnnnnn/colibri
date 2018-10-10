<?php

    Core::Using("System");

    class DataRow extends ObjectEx {
        
        protected $_properties;
        
        public function __construct($properties, $data = null, $tablePrefix = '') {
            parent::__construct($data, $tablePrefix);
            $this->_properties = $properties;
            $this->_properties = Variable::ChangeArrayValueCase($this->_properties, CASE_LOWER);
        }
        
        public function GetName($index) {
            return $this->_properties[$index];
        }
        
        public function __get($property) {                        
            $property = strtolower($property);
            switch($property) {
                case 'properties':
                    return $this->_properties;
                case 'original':
                    return parent::__get($property);
                case 'prefix':
                    return parent::__get($property);
                default:
                    // if(in_array($this->_prefix.$property, $this->_properties) || in_array($property, $this->_properties))
                        return parent::__get($property);
                    /*else
                        return false;*/
            }
        }
        
        public function __set($property, $value) {
            $property = strtolower($property);
            switch($property) {
                case 'properties':
                    break;
                default:
                    // if(in_array($this->_prefix.$property, $this->_properties) || in_array($property, $this->_properties))
                    parent::__set($property, $value);
                    break;
            }
        }
            
        public function CopyToObject() {
            return new ObjectEx($this->_data, $this->_prefix);
        }            
    }

?>
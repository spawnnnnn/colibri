<?php
    
    class Delegate {
        
        protected $_object;
        protected $_method;
        
        public function __construct($object, $method) {
            $this->_object = $object;
            $this->_method = $method;
        }
        
        public static function CreateByDelegateInfo($info) {
            return ContentProvider::Parse($info);
        }
        
        public function Invoke() {
            $handler = $this->_method;
            $args = func_get_args();
            
            // get the needle object
            if(!is_object($this->_object)) eval('$this->_object='.$this->_object.';');

            if(is_null($this->_object)) {
                if(CodeKit::Exists($handler))
                    return CodeKit::Invoke($handler, $args);
            }
            else { 
                if(ClassKit::HasMethod($this->_object, $handler)) {
                    return ClassKit::InvokeMethod($this->_object, $handler, $args);
                }
                else {
                    return null;
                }
            }            
        }
        
    }
    
?>
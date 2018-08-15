<?php

    class SharedMemory {
        
        private $_id;
        private $_vars;
        
        public function __construct($memoryIdentifier = false) {
            
            $this->_vars = array(0);
            
            if(!$memoryIdentifier) {
                $tmp = tempnam('/tmp', 'PHP');
                $token = ftok($tmp, 'a');
                $this->_id = shm_attach($token);
                if(!$this->_id) {
                    throw new Exception('can not create shared memory fragment');
                }
                
                shm_put_var($this->_id, 0, array(0));
            }
            else {
                $this->_id = $memoryIdentifier;
                
                // берем список ключей в области памяти
                $this->_vars = shm_get_var($this->_id, 0);
            }

        }
        
        public function __destruct() {
            shm_detach($this->_id);
        }
        
        public static function Create() {
            return new SharedMemory();
        }   

        public function __set($property, $value) {
            $varIndex = array_search($property, $this->_vars, true);
            if($varIndex === false) {
                $this->_vars[] = $property;
                $varIndex = array_search($property, $this->_vars, true);
            }
            
            shm_put_var($this->_id, 0, $this->_vars);
            shm_put_var($this->_id, $varIndex, $value);
        }
        
        public function __get($property) {
            
            $this->_vars = shm_get_var($this->_id, 0);
            
            $varIndex = array_search($property, $this->_vars, true);
            if($varIndex === false)
                return false;
                
            return shm_get_var($this->_id, $varIndex);
        }
        
    }

?>

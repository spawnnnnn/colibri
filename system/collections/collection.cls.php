<?php
    
    /**
    * All Collection classes myst implement this interface
    */
    interface ICollection {
        
        public function Exists($key);
        public function Key($index);
        public function Item($key);
        public function ItemAt($index);
        
        public function Add($key, $value);
        public function Delete($key);
        
        public function ToString($splitters = null);
        public function ToArray();
        
    }
    
    /**
    * Default itterator for collection classes
    */
    class CollectionIterator implements Iterator {
        
        private $_class;
        private $_current = 0;
        
        public function __construct($class = null) {
            $this->_class = $class;
        }
        
        public function rewind() {
            $this->_current = 0;
            return $this->_class->Key($this->_current);
        }
        
        public function current() {
            if($this->valid())
                return $this->_class->ItemAt($this->_current);
            else    
                return false;
        }
        
        public function key() {
            return $this->_class->Key($this->_current);
        }
        
        public function next() {
            $this->_current++;
            if($this->valid())
                return $this->_class->ItemAt($this->_current);
            else    
                return false;
        }
        
        public function valid() {
            return $this->_current >= 0 && $this->_current < $this->_class->count;
        }

    }
    
    /**
    * Standart collection class
    */
    class BaseCollection implements ICollection, IteratorAggregate {
        
        protected $data = null;
        
        protected function __construct($data = array()) {
            if(is_array($data))
                $this->data = $data;
            else if(is_object($data)) {
                if($data instanceof ICollection)
                    $this->data = $data->ToArray();
            }
            if(is_null($this->data))
                $this->data = array();
                
            $this->data = array_change_key_case($this->data, CASE_LOWER);
            
        }
        
        public function Exists($key) {
            return array_key_exists($key, $this->data);
        }

        public function Contains($item){
            return in_array($item, $this->data, true);
        }
        
        public function IndexOf($item){
            return array_search($item, $this->data, true);
        }
        
        public function Key($index) {
            if($index >= $this->count)
                return false;
                
            $keys = array_keys($this->data);
            if(count($keys) > 0)
                return $keys[$index];
            
            return null;
        }
        
        public function Item($key) {
            if($this->Exists($key))
                return $this->data[$key];
            return null;
        }
        
        public function ItemAt($index) {
            $key = $this->Key($index);
            if(!$key)
                return false;
            return $this->data[$key];
        }
        
        public function getIterator() {
            return new CollectionIterator($this);
        }                                       
        
        public function Add($key, $value) {
            $this->data[strtolower($key)] = $value;
            return $value;
        }
        
        public function Insert($index, $key, $value) {
            $this->data = array_merge(
                            array_splice($this->data, 0, $index), 
                            array(strtolower($key) => $value), 
                            array_splice($this->data, $index)
            );
            return $value;
        }
        
        public function Delete($key) { 
            $key = strtolower($key);
            if(array_key_exists($key, $this->data)) {
                $ret = $this->data[$key];
                unset($this->data[$key]);
                return $ret;
            }
            return false;
        }
        
        public function Clear() {
            $this->data = array();
        }
        
        public function ToString($splitters = null) {
            $ret = '';
            foreach($this->data as $k => $v) {
                $ret .= $splitters[1].$k.$splitters[0].$v;
            }
            return substr($ret, 1);
        }
        
        public function ToArray() {
            return $this->data;
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case 'first':
                    return $this->ItemAt(0);
                case 'last':
                    return $this->ItemAt($this->count-1);
                case 'count':
                    return count($this->data);
                default:
                    return $this->Item(strtolower($property));
            }
        }   
        
        public function __set($key, $value) {
            $this->Add($key, $value);
        }
        
    }
    
?>
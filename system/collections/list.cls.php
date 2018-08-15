<?php

    /**
    * All list classes must implements this interface
    */
    interface IList {
        
        public function Item($index);
        
        public function Add($value);
        public function AddRange($values);
        
        public function Delete($value);
        public function DeleteAt($index);
        
        public function ToString($splitter = ',');
        public function ToArray();
        
    }
    
    /**
    * Standart list itterator based on IList interface
    */
    class ListIterator implements Iterator {
        
        private $_class;
        private $_current = 0;
        
        public function __construct($class = null) {
            $this->_class = $class;
        }
        
        public function rewind() {
            $this->_current = 0;
            return $this->_current;
        }
        
        public function current() {
            if($this->valid())
                return $this->_class->Item($this->_current);
            else    
                return false;
        }
        
        public function key() {
            return $this->_current;
        }
        
        public function next() {
            $this->_current++;
            if($this->valid())
                return $this->_class->Item($this->_current);
            else    
                return false;
        }
        
        public function valid() {
            return $this->_current >= 0 && $this->_current < $this->_class->count;
        }

    }    
    
    /**
    * Base class for standart list class
    */
    class BaseList implements IList, IteratorAggregate {
        
        protected $data = null;
        
        protected function __construct($data = array()) {
            
            if(is_array($data))
                $this->data = $data;
            else if(is_object($data)) {
                if($data instanceof IList)
                    $this->data = $data->ToArray();
            }

            if(is_null($this->data))
                $this->data = array();
        }
        
        public function getIterator() {
            return new ListIterator($this);
        }
        
        public function Contains($item){
            return in_array($item, $this->data, true);
        }
        
        public function IndexOf($item){
            return array_search($item, $this->data, true);
        }
        
        public function Item($index) {
            return $this->data[$index];
        }
        
        public function Add($value) {
            $this->data[] = $value;
            return $value;
        }
        
        public function Set($index, $value) {
            $this->data[$index] = $value;
            return $value;
        }
        
        public function AddRange($values) {
            $this->data = array_merge($this->data, $values);
        }

        public function InsertAt($value, $toIndex){
            $piece = array_splice($this->data, $toIndex);
            $this->data[] = $value;
            $this->data = array_merge($this->data, $piece);
        }
        
        public function Delete($value) {
            $indices = array_search($value, $this->data, true);
            if($indices && count($indices) > 0)
                return array_splice($this->data, $indices[0], 1);
            return false;
        }

        public function DeleteAt($index) {
            return array_splice($this->data, $index, 1);
        }
        
        public function Clear() {
            $this->data = array();
        }
                
        public function ToString($splitter = ',') {
            return implode($splitter, $this->data);
        }
        
        public function ToArray($splitter = ',') {
            return $this->data;
        }
        
        function __get($property){
            switch ($property){
                case 'first' :
                    return reset($this->data);
                case 'last' :
                    return end($this->data);
                case 'count' :
                    return count($this->data);
                default :
                    /*$index = substr($property, 1);
                    return $this->item($index);*/
            }
        }
        
    }

    

?>
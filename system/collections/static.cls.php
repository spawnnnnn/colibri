<?php

    class ReadonlyCollection extends BaseCollection {
        
        public function __construct($data = array()) {
            parent::__construct($data);
        }
        
        // removes all empty items
        public function Clean() {
            while(($index = $this->IndexOf('')) > -1) {
                array_splice($this->data, $index, 1);
            }
        }
        
        public function Add($key, $value) { throw new BaseException('This is a readonly collection'); }
        public function Delete($key) { throw new BaseException('This is a readonly collection'); }
        
         
    }
    
    class ReadonlyList extends BaseList {
        
        public function __construct($data = array()) {
            parent::__construct($data);
        }

        public function Clean() {
            while(($index = $this->IndexOf('')) > -1) {
                array_splice($this->data, $index, 1);
            }
        }
        
        public function Add($value) { throw new BaseException('This is a readonly list'); }
        public function AddRange($values) { throw new BaseException('This is a readonly list'); }
        public function InsertAt($value, $toIndex){ throw new BaseException('This is a readonly list'); }
        public function Delete($value) { throw new BaseException('This is a readonly list'); }
        public function DeleteAt($index) { throw new BaseException('This is a readonly list'); }
        public function Clear() { throw new BaseException('This is a readonly list'); }
        
    }
    
    
    class ObjectCollection extends ReadonlyCollection {
        
        private $itemClass = '';
        
        public function __construct($data = array(), $itemClass = '') {
            parent::__construct($data);
            $this->itemClass = $itemClass;
        }
        
        public function Item($key) {
            if(empty($this->itemClass))
                return $this->data[$key];
            else
                return new $this->itemClass($this->data[$key]);
        }
        
        public function ItemAt($index) {
            $key = $this->Key($index);
            if(!$key)
                return false;
                
            if(empty($this->itemClass))
                return $this->data[$key];
            else
                return new $this->itemClass($this->data[$key]);
        }
        
    }

?>

<?php

    class ObjectList extends BaseList {
        
        public function __construct($data = array()) {
            parent::__construct($data);
        }
        
        public function Find($property, $value) {
            $r = new ObjectList();
            foreach($this as $item) {
                if($item->$property == $value)
                    $r->Add($item);
            }
            return $r;
        }
        
    }

?>

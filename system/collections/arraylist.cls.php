<?php
    
    /**
    * ArrayList class
    */
    class ArrayList extends BaseList {
        
        public function __construct($data = array()) {
            parent::__construct($data);
        }
        
        public function Join($delimiter = ',') {
            $ret = '';
            foreach($this as $item) {
                $it = $item;
                if(is_object($it) && method_exists($it, "Join"))
                    $it = $it->Join($delimiter);
                $ret .= $delimiter.$it;
            }
            $ret = substr($ret, strlen($delimiter));
            return $ret;
        }
        
        public function Sort($k, $sorttype = SORT_ASC) {
            $keys = array();
            $rows = array();
            $i = 0;
            foreach ($this->data as $row) {
                if(is_object($row))
                    $key = $row->$k;
                else
                    $key = $row[$k];
                
                if(isset($rows[$key]))
                    $key = $key.($i++);    
                $rows[$key] = $row;
            }
           
            if($sorttype == SORT_ASC)
                ksort($rows);
            else
                krsort($rows);
            $this->data = array_values($rows);
        }  
        
        public function Splice($start, $count) {
            $part = array_splice($this->data, $start, $count);
            return new ArrayList($part);
        }      
    }
    
?>
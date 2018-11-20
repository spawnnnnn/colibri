<?php

    class QueryDef extends ObjectEx {
            
        public function __construct($select, $filter, $order, $join = false, $groupBy = false) {
            parent::__construct();
            $this->select = $select;
            $this->filter = $filter;
            $this->order = $order;
            $this->join = $join;
            $this->groupBy = $groupBy;
        }
        
        public static function Create($select, $filter, $order, $join = false, $groupBy = false) {
            return new QueryDef($select, $filter, $order, $join, $groupBy);
        }
        
        public function ToString() {
            // ! TODO: доделать Join и groupby
            if(!$this->table)
                return '';
            return 'select '.(!$this->select ? '*' : $this->select).' from '.$this->table.($this->filter ? ' where '.$this->filter : '').($this->groupBy ? ' group by '.$this->groupBy : '').($this->order ? ' order by '.$this->order : '');
        }
        
    }


?>
<?php
    
    Core::Using('System::Data');
    Core::Using('System::Data::SqlClient');
    
    final class PgSqlDataReader implements IDataReader {
        
        private $_results;
        private $_count = null;
        private $_affected = null;
        
        public function __construct($results, $affected = null) {
            $this->_results = $results;
            $this->_affected = $affected;
        }
        
        public function __destruct() {
            $this->Close();
        }
        
        public function Close() {
            if($this->_results)
                @pg_free_result($this->_results);
        }
        
        public function Read() {
            $result = pg_fetch_object($this->_results);
            if(!$result)
                return false;
            
            return $result;
        }
        
        public function Fields() {
            $fields = array();
            $num = pg_num_fields($this->_results);
            for($i=0; $i<$num; $i++) {
                $fields[] = pg_field_name($this->_results, $i);
            }
            return $fields;
        }
        
        public function __get($property) {
            switch($property) {
                case 'hasrows':
                    return $this->_results && pg_num_rows($this->_results) > 0;
                case 'affected':
                    return $this->_affected;
                case 'count':
                    if(is_null($this->_count))
                        $this->_count = pg_num_rows($this->_results);
                    return $this->_count;
            }
        }
        
    }
    
   
    
    
    
    
?>

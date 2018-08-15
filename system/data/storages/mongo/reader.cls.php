<?php
    
    final class MongoDataReader implements IStorageDataReader {
        
        private $_results;
        private $_count = null;
        private $_affected = null;
        
        public function __construct($results, $affected = null) {
            $this->_results = $results;
            $this->_affected = $affected ? $affected : $this->_results->count();
            $this->_count = $this->_results->count(true);
        }
        
        public function __destruct() {
            $this->Close();
        }
        
        public function Close() {
            unset($this->_results);
        }
        
        public function Read() {
            if(!$this->_results->hasNext())
                return false;
            
            return (object)$this->_results->getNext();
        }
        
        public function __get($property) {
            switch($property) {
                case 'hasrows':
                    return $this->_results->count() > 0;
                case 'affected':
                    if(is_null($this->_affected))
                        $this->_affected = $this->_results->count();
                    return $this->_affected;
                case 'count':
                    if(is_null($this->_count))
                        $this->_count = $this->_results->count(true);
                    return $this->_count;
            }
        }
        
    }
    

?>

<?php

    Core::Using("System");
    Core::Using("System::Data::SqlClient");
    
    class DataTable implements IteratorAggregate {
        
        protected $_reader;
        protected $_cache;
        protected $_returnAs;
        
        public function __construct(IDataReader $reader, $returnAs = 'DataRow') {
            $this->_reader = $reader;
            $this->_cache = new ObjectList();
            $this->_returnAs = $returnAs;
        }
        
        public function getIterator() {
            return new ListIterator($this);
        }
        
        public function __get($property) {
            switch($property) {
                case 'hasrows':
                    return $this->_reader->hasrows;
                case 'count':
                    return $this->_reader->count;
                case 'affected':
                    return $this->_reader->affected;
                case 'loaded':
                    return $this->_cache->count;
            }
        }
        
        protected function _createDataRowObject($result) {
            return CodeModel::CreateObject($this->_returnAs, 
                        $this->_reader->Fields(), 
                        $result);
        }
        
        protected function _read() {
            return $this->_createDataRowObject(
                        $this->_reader->Read()
                   );
        }
        
        protected function _readTo($index) {
            while($this->_cache->count < $index) {
                $this->_cache->Add($this->_read());
            }
            return $this->_cache->Add($this->_read());
        }
        
        public function Item($index) {
            if($index >= $this->_cache->count)
                return $this->_readTo($index);
            else {
                return $this->_cache->Item($index);
            }
        }
        
        public function First() {
            return $this->Item(0);
        }
        
        public function CacheAll($closeReader = true) {
            $this->_readTo($this->count-1);
            if($closeReader) 
                $this->_reader->Close();
            return $this->_cache;
        }
        
        public function createEmpty() {
            return $this->_createDataRowObject(new stdClass());
        }
        
        public function Set($index, $data) {
            $this->_cache->Set($index, $data);
        }
        
    }
    

?>
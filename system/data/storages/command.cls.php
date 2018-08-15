<?php
    
    abstract class StorageCommand {
        
        const Select = 0;
        const Insert = 1;
        const InsertBatch = 2;
        const Update = 3;
        const Delete = 4;
        const ListCollections = 5;
        const MapReduce = 6;
        
        protected $_connection = null;
        
        protected $_commandType = StorageCommand::Select;
        protected $_commandData = array();
        
        protected $_pagesize =  10;
        protected $_page =  -1;
        
        public function __construct($command = StorageCommand::Select, $commandData = array(), IStorageConnection $connection = null) {
            
            $this->_commandType = $command;
            $this->_commandData = $commandData;
            
            $this->_connection = $connection;
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case 'query':
                case 'commandtext':
                case 'text':
                case 'type':
                    return $this->_commandType;
                case 'data':
                    return $this->_commandData;
                case 'connection':
                    return $this->_connection;
                case 'page':
                    return $this->_page;
                case 'pagesize':
                    return $this->_pagesize;
            }
        }
        
        public function __set($property, $value) {
            switch(strtolower($property)) {
                case 'query':
                case 'commandtext':
                case 'text':
                case 'type':
                    $this->_commandType = $value;
                    break;
                case 'connection':
                    $this->_connection = $value;
                    break;
                case "page":
                    $this->_page = $value;
                    break;
                case "pagesize":
                    $this->_pagesize = $value;
                    break;
            }
        }

        abstract public function ExecuteReader();
        abstract public function ExecuteNonQuery();
        
        abstract public function Prepare();
        
    }
    

?>

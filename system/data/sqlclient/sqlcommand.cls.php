<?php

    abstract class SqlCommand {
        
        protected $_connection = null;
        protected $_commandtext = '';
        protected $_pagesize =  10;
        protected $_page =  -1;
        
        public function __construct($commandtext = '', IConnection $connection = null) {
            $this->_commandtext = $commandtext;
            $this->_connection = $connection;
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case 'query':
                case 'commandtext':
                case 'text':
                    return $this->_commandtext;
                case 'connection':
                    return $this->_connection;
                case 'type':
                    $parts = explode(' ', $this->query);
                    return strtolower($parts[0]);
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
                    $this->_commandtext = $value;
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
        
        abstract public function PrepareQueryString();
        
    }

?>

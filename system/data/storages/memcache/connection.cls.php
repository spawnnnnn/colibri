<?php
    
    final class MemcacheStorageConnection implements IStorageConnection {
        
        private $_connectioninfo = null;
        private $_resource = null;
        private $_database = null;
        
        public function __construct($connectioninfo = null) {
            $this->_connectioninfo = $connectioninfo;
        }
        
        public function Open($connectioninfo = null) {
            if(!is_null($connectioninfo))
                $this->_connectioninfo = $connectioninfo;
            
            if(is_null($this->_connectioninfo)) {
                throw new BaseException('You must provide a connection info object while creating a connection.');
                return false;
            }
            
            try {
                $this->_resource = new MemcacheClient($this->_connectioninfo->server);
            }
            catch(MemcacheConnectionException $e) {
                throw new BaseException('Error connecting to MongoDB server');
            }

            $this->_database = $this->_resource->selectDB($this->_connectioninfo->database);
            
            return true;
        }
        
        public function Reopen() {
            if(is_null($this->_connectioninfo)) {
                throw new BaseException('You must provide a connection info object while creating a connection.');
                return false;
            }
            
            try {
                $this->_resource = new MongoClient($this->_connectioninfo->server);
            }
            catch(MongoConnectionException $e) {
                throw new BaseException('Error connecting to MongoDB server');
            }
            
            return true;
        }
        
        public function Close() {
            MongoClient::close($this->_resource);
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case "resource":
                case "raw":
                case "connection":
                    return $this->_resource;
                case "database":
                    return $this->_database;
                case "isAlive":
                    return $this->_resource->connected;
                
                
                
            }
        }
        
    }
    

?>

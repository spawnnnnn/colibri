<?php

    Core::Using('System::Data::SqlClient');
     
    final class PgSqlConnection implements IConnection {
        
        private $_connectioninfo = null;
        private $_resource = null;
        
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
            
            $this->_resource = pg_connect($this->_connectioninfo->ToString(' '));
            if(!$this->_resource) {
                throw new BaseException(pg_last_error());
                return false;
            }
            
            return true;
        }
        
        public function Reopen() {
            if(is_null($this->_connectioninfo)) {
                throw new BaseException('You must provide a connection info object while creating a connection.');
                return false;
            }
            
            $this->_resource = pg_connect($this->_connectioninfo->ToString(' '), PGSQL_CONNECT_FORCE_NEW);
            if(!$this->_resource) {
                throw new BaseException(pg_last_error());
                return false;
            }
            
            return true;
        }
        
        public function Close() {
            if(is_resource($this->_resource))
                pg_close($this->_resource);
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case "resource":
                case "raw":
                case "connection":
                    return $this->_resource;
                case "isAlive":
                    return pg_connection_status($this->_resource) == PGSQL_CONNECTION_OK;
                
                
                
            }
        }
        
    }
    

?>

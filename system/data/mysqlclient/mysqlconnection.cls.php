<?php

    Core::Using('System::Data::SqlClient');
     
    final class MySqlConnection implements IConnection {
        
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
            
            $this->_resource = mysqli_connect($this->_connectioninfo->server, $this->_connectioninfo->user, $this->_connectioninfo->password);
            if(!$this->_resource) {
                throw new BaseException(mysqli_error($this->_resource));
                return false;
            }
            
            if(!empty($this->_connectioninfo->database)) {
                if(!mysqli_select_db($this->_resource, $this->_connectioninfo->database)) {
                    throw new BaseException(mysqli_error($this->_resource));
                    return false;
                }
            }
            
            mysqli_query($this->_resource, 'set names utf8');

            return true;
        }
        
        public function Reopen() {
            if(is_null($this->_connectioninfo)) {
                throw new BaseException('You must provide a connection info object while creating a connection.');
                return false;
            }
            
            $this->_resource = mysqli_connect($this->_connectioninfo->server, $this->_connectioninfo->user, $this->_connectioninfo->password);
            if(!$this->_resource) {
                throw new BaseException(mysqli_error($this->_resource));
                return false;
            }
            
            if(!empty($this->_connectioninfo->database)) {
                if(!mysqli_select_db($this->_resource, $this->_connectioninfo->database)) {
                    throw new BaseException(mysqli_error($this->_resource));
                    return false;
                }
            }
            
            mysqli_query($this->_resource, 'set names utf8');

            return true;
        }
        
        public function Close() {
            if(is_resource($this->_resource))
                mysqli_close($this->_resource);
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case "resource":
                case "raw":
                case "connection":
                    return $this->_resource;
                case "isAlive":
                    return mysqli_ping($this->_resource);
                
                
                
            }
        }
        
    }
    

?>

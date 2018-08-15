<?php
    
    class StorageConnectionInfo {
        
        public $server;
        public $database;
        public $user;
        public $password;
        
        public function __construct($server, $user, $password, $database) {
            $this->server = $server;
            $this->database = $database;
            $this->user = $user;
            $this->password = $password;
        }
        
        public function ToString($splitter = ';') {
            return 'host='.$this->server.$splitter.'user='.$this->user.$splitter.'password='.$this->password.$splitter.'dbname='.$this->database;
        }
        
    }
    

?>

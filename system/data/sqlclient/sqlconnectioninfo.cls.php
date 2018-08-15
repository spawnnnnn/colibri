<?php

    class ConnectionInfo {
        
        public $server;
        public $port;
        public $user;
        public $password;
        public $database;
        
        public function __construct($server, $user, $password, $database) {
            $this->server = $server;
            $this->port = 5432;
            $this->user = $user;
            $this->password = $password;
            $this->database = $database;
            if(strstr($this->server, ':') !== false) {
                $s = explode(':', $this->server);
                $this->server = $s[0];
                $this->port = $s[1];
            }
        }
        
        public function ToString($splitter = ';') {
            return 'host='.$this->server.$splitter.'port='.$this->port.$splitter.'user='.$this->user.$splitter.'password='.$this->password.$splitter.'dbname='.$this->database;
        }
        
    }

?>

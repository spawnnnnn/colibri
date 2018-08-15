<?php

    interface IConnection {
        
        public function Open($connectioninfo = null);
        public function Reopen();
        public function Close();
        
    }

?>

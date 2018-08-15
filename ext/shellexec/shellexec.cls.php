<?php

    class ShellExec {

        protected $pid = 0;
        
        public function __construct($command = null) {
            if ($command) {
                $this->Run($command);
            }
        }

        public function Run($command, $priority = 0){
            if ($priority) {
                //$this->pid = shell_exec("nohup nice -n $priority $command > /dev/null & echo $!");
                $this->pid = shell_exec("$command > /dev/null & echo $!");
            }
            else {
                //$this->pid = shell_exec("nohup $command > /dev/null & echo $!");
                $this->pid = shell_exec("$command > /dev/null & echo $!");
            }
            
            return $this;
        }
        
        public function IsRunning() {
            if ($this->pid) {
                exec('ps ' . $this->pid , $state);
                return (count($state) >= 2);
            }
            return false;
        }

        function Kill(){
            if ( $this->IsRunning()) {
                exec('kill -KILL ' . $this->pid);
                return true;
            }
            else {
                return false;
            }
        }
    }

?>

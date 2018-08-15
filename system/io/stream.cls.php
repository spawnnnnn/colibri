<?php

    abstract class Stream { 
        
        protected $length = 0;
        protected $stream;
        
        function __construct(){  }
        
        function __destruct(){
            if($this->stream)
                $this->close();
            unset($this->stream);
        }
        
        function __get(/*string*/ $property){
            switch ($property){
                case 'length' :
                    return $this->length;
            }
        }
        
        abstract public function seek($offset = 0);
        abstract public function read($offset = null, $count = null);
        abstract public function write($content, $offset = null); //$count = 0
        abstract public function flush();
        abstract public function close(); //?
    }
    
    // переделать без reader-а и writer-а
    class FileStream extends Stream {
        
        private $virtual;
        
        public function __construct($source, $virtual = false){ 
            $this->virtual = $virtual;
            $this->stream = fopen($source, "rw+");
            if(!$this->virtual)
                $this->length = filesize($source);
            else
                $this->length = -1;
            
        }
        
        public function seek($offset = 0){
            if($offset == 0) return;
            
            fseek($this->stream, $offset);
            
        }
        
        public function read($offset = 0, $count = 0){
            $this->seek($offset);
            return fread($this->stream, $count);
        }
        
        public function write($buffer, $offset = 0){
            $this->seek($offset);
            return fwrite($this->stream, $buffer);
        }
        
        public function flush(){
            fflush($this->stream);
        }
        
        public function close(){
            $this->flush();
            fclose($this->stream);
            $this->stream = false;
        }
    }
    
?>
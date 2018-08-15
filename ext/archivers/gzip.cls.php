<?php

    class GZFile {
        
        private $pointer;
        
        public function __construct($filename, $mode = MODE_READ) {
            $this->pointer = gzopen($filename, $mode);
        }
        
        public function read($length = 255) {
            return gzgets($this->pointer, $length);
        }
        
        public function readall() {
            $sret = "";
            while($s = $this->read()) {
                $sret .= $s;
            }
            return $sret;
        }

        public function write($string, $length = null) {
            if($length)
                return gzputs($this->pointer, $string, $length);
            else
                return gzputs($this->pointer, $string);
        }
        
        public function close() {
            gzclose($this->pointer);
        }

    }

?>
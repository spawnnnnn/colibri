<?php

    class ResponseTypes { 
        const TextHtml = 'text/html';
    }
    
    
    class Response {
        
        static $i;
        
        public function __construct() {
            
        }
        
        public static function Create() {
            if(!Response::$i) {
                Response::$i = new Response();
            }
            return Response::$i;
        }
        
        private function _addHeader($name, $value) {
            header($name.': '.$value);
        }
        
        public function NoCache() {
            $this->_addHeader('Pragma', 'no-cache');
            $this->_addHeader('X-Accel-Expires', '0');
        }
        
        public function ContentType($type, $encoding = false) {
            $this->_addHeader('Content-type', $type.($encoding ? "; charset=".$encoding : ""));
        }
        
        public function ExpiresAfter($seconds) {
            $this->_addHeader('Expires', gmstrftime("%a, %d %b %Y %H:%M:%S GMT", time() - $seconds));
        }
        
        public function ExpiresAt($date) {
            $this->_addHeader('Expires', gmstrftime("%a, %d %b %Y %H:%M:%S GMT", $date));
        }
        
        public function Cache($seconds) {
            $this->_addHeader('Pragma', 'no-cache');
            $this->_addHeader('X-Accel-Expires', $seconds);
        }
        
        public function P3P() {
            $this->_addHeader('P3P', 'CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
        }
        
        public function Redirect($url) {
            header('Location: '.$url);
        }
        
    }
  

?>
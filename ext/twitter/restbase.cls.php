<?php

    if (!defined ('GET')) { define ('GET', 0); }
    if (!defined ('POST')) { define ('POST', 1); }
    if (!defined ('JSON')) { define ('JSON', 0); }
    if (!defined ('XML')) { define ('XML', 1); }
    if (!defined ('ATOM')) { define ('ATOM', 2); }
    if (!defined ('RSS')) { define ('RSS', 3); }

    class RestBase {
        
        public static $errors = array();
        public static $config = array();
        
        protected $_ErrNo;
        protected $_ErrMessage; 
        
        public function __construct() {
        }
        
        protected function _Error($code, $message = '') {
            $this->_ErrNo = $code;
            $this->_ErrMessage = $message;
        }

        protected function _FlushError() {
            $this->_Error (0, '');
        }
        
        public function GetLastError() {    
            return array('ErrNo' => $this->_ErrNo, 'Message' => $this->_ErrMessage);
        }
        
    }       
    
?>
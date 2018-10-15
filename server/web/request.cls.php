<?php
    
    class RequestHandler {
        
        public static function Handle($uri) {
            return $uri;
        }
        
    }
        
    class RequestedFile {

        public $name;
        public $ext;
        public $mimetype;
        public $error;
        public $size;
        public $temporary;


        function __construct($arrFILE) {

            if(!$arrFILE) {
                return;
            }
            
            $this->name = $arrFILE["name"];       
            $ret = preg_split("/\./i", $this->name);
            if(count($ret) > 1 )
                $this->ext = $ret[count($ret) - 1];

            $this->mimetype = $arrFILE["type"];
            $this->temporary = $arrFILE["tmp_name"];
            $this->error = $arrFILE["error"];
            $this->size = $arrFILE["size"];

        }
        
        public function __get($prop) {
            switch($prop) {
                case 'isValid':
                    return !Variable::IsEmpty($this->name);
                case 'binary': 
                    return FileINfo::ReadAll($this->temporary);
            }
        }
        
        function __destruct(){
            if(FileInfo::Exists($this->temporary))
                @unlink($this->temporary);
        }
        
        function MoveTo($path) {
            return  FileInfo::Move($this->temporary, $path);
        }
        
    }
            
    class RequestCollection extends ReadonlyCollection {
        
        private $_magicquotes;
        
        public function __construct($data = array(), $mq = null) {
            parent::__construct($data);
            // save the current value of magic_quotes_gpc setting
            $this->_magicquotes = is_null($mq) ? (get_magic_quotes_runtime() == '1') : $mq;
        }

        private function _stripSlashes($obj){
            if (is_array($obj)){
                foreach($obj as $k => $v) {
                    $obj[$k] = $this->_stripSlashes($v);
                }
                return $obj;
            } else {
                return stripslashes($obj);
            }
        }        
        
        public function __get($property) {
            // todo: надо понять правильно ли сделано - иначе будут глюки
            $val = parent::__get($property);
            if($val && !$this->_magicquotes)
                $val = $this->_stripSlashes($val);
            return $val;
        }
        
    }
    
    class FileCollection extends RequestCollection {
        
        public function __get($property) {
            return new RequestedFile(parent::__get($property));
        }
        
    }

    /**
    * Request class
    */
    class Request {
        
        static $i;
        
        private $_requestedUri;
        private $_handledUri;
        private $_uri;
        private $_get;
        
        private $_handlers;
        
        public function __construct() {
            
            $this->_requestedUri = $_SERVER['REQUEST_URI'];
            $this->_handledUri = RequestHandler::Handle($_SERVER['REQUEST_URI']);
            $this->_getHandlers();
            
            $this->_handleRequest();
            
            $this->_parse();
        }
        
        private function _getHandlers() {
            $this->_handlers = new Hashtable();
            $xml = Config::Load(_REQUEST_HANDLERS);;
            $handlers = $xml->Query('//handler');
            foreach($handlers as $handler) {
                $this->_handlers->Add($handler->attributes->name->value, $handler->attributes->entry->value);
            }
        }
        
        private function _handleRequest() {
            foreach($this->_handlers as $handler) {
                eval('$this->_handledUri = '.$handler.'::Handle($this->_handledUri);');
            }
        }
        
        public static function Create() {
            if(!Request::$i) {
                Request::$i = new Request();
            }
            return Request::$i;
        }
        
        private function _parse() {
            
            $uri = $this->_handledUri;
            
            $parts = explode('?', $uri);
            $this->_uri = $parts[0];
            
            if(count($parts) > 1) {
                // parse get request
                $gets = explode('&', $parts[1]);
                foreach($gets as $part) {
                    $p = explode('=', $part);
                    $this->_get[$p[0]] = isset($p[1]) ? $p[1] : 0;
                }
            } else if(count($_GET) > 0) {
                foreach($_GET as $key => $part) {
                    $this->_get[$key] = $part;
                }
            }
                
        }
        
        public function Uri($add = array(), $remove = array()) {
            $get = $this->_get;
            foreach($remove as $v)
                unset($get[$v]);
                
            foreach($add as $k => $v)
                $get[$k] = $v;
            
            $url = '';
            foreach($get as $k => $v)
                $url .= '&'.$k.'='.$v;     
                
            return '?'.substr($url, 1);
        }
        
        public function __get($prop) {
            switch(strtolower($prop)) {
                case 'get':
                    return new RequestCollection($this->_get);
                case 'post':
                    return new RequestCollection($_POST);
                case 'files':
                    return new FileCollection($_FILES);
                case 'session':
                    return new RequestCollection($_SESSION);
                case 'server':
                    return new RequestCollection($_SERVER);
                case 'cookie':
                    return new RequestCollection($_COOKIE);
                case 'remoteip':
                    if($this->server->REMOTE_ADDR == $this->server->SERVER_ADDR || $this->server->HTTP_X_FORWARDED_FOR)
                        return $this->server->HTTP_X_FORWARDED_FOR;
                    return ($this->server->HTTP_X_FORWARDED_FOR ? $this->server->HTTP_X_FORWARDED_FOR : ($this->server->REMOTE_ADDR ? $this->server->REMOTE_ADDR : ($this->server->X_REAL_IP ? $this->server->X_REAL_IP : ($this->server->HTTP_FORWARDED ? $this->server->HTTP_FORWARDED : ''))));
                case 'uri':
                    return $this->_uri;
                case 'requesteduri':
                    return $this->_requestedUri;
            }
            
        }
        
        
        
    }
    
?>
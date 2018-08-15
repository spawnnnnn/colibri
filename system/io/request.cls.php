<?php
    
    /**
    * Checks if the curl module loaded
    * 
    */
    function __checkWebRequest() {
        return CodeKit::Exists('curl_init');
    }
    
    class RequestStream {
        
        
    }
    
    class RequestCredentials {
        
        // login & password
        public $login = '';
        public $password = '';
        
        // use ssl
        public $ssl = false;
        
        public function __construct($login = '', $password = '', $ssl = false) {
            $this->login = $login;
            $this->password = $password;
            $this->ssl = $ssl;
        }
        
        
    }
    
    class RequestType {
        
        const Post = 'post';
        const Get = 'get';
        const Head = 'head';
        
    }
    
    class RequestEncryption {
        
        const Multipart = 'multipart/form-data';
        const UrlEncoded = 'application/x-www-form-urlencoded';
        const XmlEncoded = 'application/x-www-form-xmlencoded';
        const JsonEncoded = 'application/json';
        
    }
    
    class RequestDataItem extends Object {
        
        public function __construct($name, $data) {
            parent::__construct();
            $this->name = $name;
            $this->value = $data;
        }                         
        
    }
    
    class RequestDataFile extends RequestDataItem {
        
        public function __cosntruct($name, $data) {
            
            // $data is file path
            if(!FileInfo::Exists($data)) {
                throw new BaseException('File does not exists', 500);
            }
            
            $fi = new FileInfo($data);
            $type = $fi->extension;
            
            parent::__construct($name, $fi->content);
            
            $this->mime = MimeType::FromExt($type);
            $this->file = $data;
            
        }
        
    }   
    
    class RequestData extends ArrayList { 
        
        static function FromArray($array) {
            $d = new RequestData();
            foreach($array as $k => $v) {
                $d->Add(new RequestDataItem($k, $v));
            }
            return $d;
        }
        
    }
    
    class RequestResult {
        public $status;
        public $data;
        public $headers;
    }
    
    class WebRequest {
        
        const Boundary = '---------------------------';
        const BoundaryEnd = '--';
        
        public $credentials;
        
        public $target;
        public $method = RequestType::Get;
        public $postData = null;
        public $encryption = RequestEncryption::UrlEncoded;
        public $boundary = null;
        public $timeout = 60;
        public $async = false;
        public $cookies = array();
        public $cookieFile = '';
        public $referer = '';
        public $headers = false;
        public $useragent = null;
        public $sslVerify = true;
        
        public function __construct($target, 
                                    $method = RequestType::Get, 
                                    $encryption = RequestEncryption::UrlEncoded, 
                                    $postData = null,
                                    $boundary = '') {
            
            if(!__checkWebRequest())
                throw new BaseException('Can not load module curl.', 500);
               
            // create boundary
            $this->boundary = !Variable::IsEmpty($boundary) ? $boundary : Strings::Randomize(8);
                
            $this->target = $target;
            $this->method = $method;
            $this->postData = $postData;
                
            $this->credentials = null;
                
            $this->encryption = $encryption;
        }
        
        private function _joinPostData() {
            
            $data = array();
            
            if($this->encryption == RequestEncryption::Multipart) {
            
                $data[] = WebRequest::Boundary.$this->boundary;
                foreach($this->postData as $value) {
                    
                    $data[] = 'Content-Disposition: form-data; name="'.$value->name.'"';
                    if($value instanceOf RequestDataFile) {
                        $data[] = 'filename="'.$value->file.'"';
                        $data[] = 'Content-Type: '.$value->mime;
                        $data[] = $value->value;
                    }
                    else {
                        $data[] = '';
                        $data[] = $value->value;
                    }
                        
                    $data[] = WebRequest::Boundary.$this->boundary;
                }
                $data[count($data)-1] .= WebRequest::BoundaryEnd;
                
                return join("\r\n", $data);
            }
            else if($this->encryption == RequestEncryption::XmlEncoded) {
                
                $data = '<request>';
                
                foreach($this->postData as $value) {
                    $data .= '<'.$value->name.'>'.Strings::PrepareAttribute($value->value).'</'.$value->name.'>';
                }
                
                $data .= '</request>';
                return $data;
                
            }
            else if($this->encryption == RequestEncryption::JsonEncoded) {
                return Variable::IsString($this->postData) ? $this->postData : json_encode($this->postData);
            }
            else {
                
                foreach($this->postData as $value) {
                    $data[] = $value->name.'='.rawurlencode($value->value);
                }
                return implode("&", $data);
                
            }                            
            
            
        }
                 
        
        public function Request($postData = null) {
            
            if(!Variable::IsNull($postData)) {
                $this->postData = $postData;
            }
            
            $handle = curl_init();

            curl_setopt($handle, CURLOPT_URL, $this->target); 
            if(!$this->async)
                curl_setopt($handle, CURLOPT_TIMEOUT, $this->timeout);
            else {
                curl_setopt($handle, CURLOPT_TIMEOUT_MS, 100);
                curl_setopt($handle, CURLOPT_NOSIGNAL, 1);
            }
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
            
            if(!empty($this->referer))
                curl_setopt($handle, CURLOPT_REFERER, $this->referer);
            else
                curl_setopt($handle, CURLOPT_REFERER, $_SERVER['SERVER_NAME']);
            if(!empty($this->cookieFile)) {
                if ( ! FileInfo::Exists($this->cookieFile)) {
                    FileInfo::Create($this->cookieFile);
                }
                curl_setopt($handle, CURLOPT_COOKIEJAR, $this->cookieFile);
                curl_setopt($handle, CURLOPT_COOKIEFILE, $this->cookieFile);
            }

            if(!Variable::IsNull($this->credentials)){
                curl_setopt($handle, CURLOPT_USERPWD, $this->credentials->login.':'.$this->credentials->password);
                if($this->credentials->ssl)
                    curl_setopt($handle, CURLOPT_FTP_SSL, true);
            }

            $headers = array(
                "Connection: Keep-Alive",
                'HTTP_X_FORWARDED_FOR: '.Request::$i->remoteip
            );
            
            if ($this->cookies) {
                $cookies = is_array($this->cookies) ? http_build_query($this->cookies, '', '; ') : $this->cookies;
                $headers[] = "Cookie: ".$cookies;
            }
            
            if($this->encryption == RequestEncryption::Multipart) {
                //"Accept-Language: en-US;en;q=0.5",
                // "Accept: image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, application/x-shockwave-flash, application/x-icq, */*",
                // "UA-CPU: x86",
                $headers[] = "Content-Type: multipart/form-data; boundary=".WebRequest::Boundary.$this->boundary;
                curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
            }
            else {
                curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
            }


            if($this->method == RequestType::Post) {
                curl_setopt($handle, CURLOPT_POST, true);
                if(!Variable::IsNull($this->postData)) {
                    curl_setopt($handle, CURLOPT_POSTFIELDS, $this->_joinPostData($this->postData));
                }
            }
            else
                curl_setopt($handle, CURLOPT_HTTPGET, true);
            
            // curl_setopt($handle, CURLOPT_HEADER, true);
            if(is_array($this->headers))
                curl_setopt($handle, CURLOPT_HTTPHEADER, $this->headers);

            /*
            if($this->_noBody)
             curl_setopt($handle, CURLOPT_NOBODY, true);

            if($this->_binary)
             curl_setopt($handle,CURLOPT_BINARYTRANSFER,true);


            curl_setopt($handle, CURLOPT_REFERER, $this->_referer);
            */
            
            if ($this->useragent) {
                curl_setopt($handle, CURLOPT_USERAGENT, $this->useragent);
            }
            
            if ( ! $this->sslVerify) {
                curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
            }
            
            curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.122 Safari/534.30");
            
            $result = new RequestResult();

            $result->data = curl_exec($handle);
            $result->status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            $result->headers = curl_getinfo($handle);
            
            curl_close($handle); 
            
            return $result;
        }
        
        
        
        
        
    }
?>

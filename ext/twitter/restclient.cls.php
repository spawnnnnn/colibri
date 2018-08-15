<?php

    class RestClient extends RestBase {
        
        private $_Format = JSON;
        
        public function __construct($Format = JSON) {
            
            parent::__construct();
            
            RestBase::$errors['E_REST_OK'] = 'It\'s OK!';
            RestBase::$errors['E_REST_CONNECTION'] = 'Couldn\'t open connection.';
            RestBase::$errors['E_REST_PARAMS'] = 'Parameters, passed for request are invalid.';
            RestBase::$errors['E_REST_NOT_SUPPORTED'] = 'This feature is not supported by library.';    
            
            RestBase::$config['CURL_TIMEOUT'] = 30;
                                 
            $this->_Format = $Format;
            $this->_FlushError();
        }
        
        
        /*
        * Generates the string after http://www.site.com/... for GET request
        * 
        * @param $pairs -- array of key-value pairs, that are parameters for request
        */    
        private function PreparePairsForHTTP (&$pairs) {
            if ($pairs===0) { return ""; }
            
            $uri = "?";
            
            $first = true;
            foreach ($pairs as $key=>$value) {
                if (!$first) { 
                    $uri .= '&'; 
                    $first = false;
                }
                $uri.=urlencode($key).'='.urlencode($value);  
//                var_dump($uri);
            }
            
            return $uri;
        }    

        /*
        * Request data from REST-service. cURL used to retrieve data, no parsing will be applied.
        * 
        * @param $url -- URL to request for
        * @param $data -- array of key-value pairs, that are parameters for request
        * @param $userpwdpair -- array ('user'=>$username, 'pass'=>$password) pair
        */    
        private function RESTRequest ($url, $type, $data = 0, $userpwdpair = 0) {
            
            $ch = curl_init();
            if (!$url) {
                return 0;
            }
            
            $a = array (CURLOPT_URL => $url,
            CURLOPT_TIMEOUT => RestBase::$config['CURL_TIMEOUT'],
            CURLOPT_RETURNTRANSFER => TRUE);
            $a[CURLOPT_HTTPHEADER] = array('Expect:');
            
            if ($type == GET) {
                $a[CURLOPT_URL] .= $this->PreparePairsForHTTP ($data);
                $a[CURLOPT_HTTPGET] = TRUE;
            } elseif ($type == POST) {
                $a[CURLOPT_POST] = TRUE;
                $a[CURLOPT_POSTFIELDS] = $data;
            } else {
                $this->_Error ('E_REST_NOT_SUPPORTED',RestBase::$errors['E_REST_NOT_SUPPORTED']);
                return 0;
            }
                                       
            if ($userpwdpair) {
                $a[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
                $a[CURLOPT_USERPWD] = $userpwdpair['Username'].':'.$userpwdpair['Password'];
            }
            curl_setopt_array($ch, $a);
            $content = curl_exec($ch);

            if (!$content) {                              
                $this->_Error (curl_errno($ch),'cURL: '.curl_error($ch));
            }                  
            

            curl_close($ch);

            return $content;
        }

        /*
        * Requests data from REST-service and decodes it (JSON only).
        * 
        * @param $type -- type of request: 'get' or 'rest'
        * @param $url -- URL to request for
        * @param $data -- array of key-value pairs, that are parameters for request        * 
        * @param $userpwdpair -- array ('user'=>$username, 'pass'=>$password) pair
        */    
        public function RequestAndDecode ($url, $type, $data = 0,  $userpwdpair = 0) {

            $content = $this->Request( $url, $type, $data,  $userpwdpair);
            
            if (!$content) {
                return 0;
            }
                        
            if ($this->_Format == JSON) {
                return json_decode($content);
            }
            
            $this->_Error ('E_REST_NOT_SUPPORTED', RestBase::$errors['E_REST_NOT_SUPPORTED']);
                        
            return 0;
        }

        /*
        * Requests data from REST-service without decoding.
        * 
        * @param $type -- type of request: 'get' or 'rest'
        * @param $url -- URL to request for
        * @param $data -- array of key-value pairs, that are parameters for request        * 
        * @param $userpwdpair -- array ('user'=>$username, 'pass'=>$password) pair
        */               
        public function Request ($url, $type = GET ,$data = 0,  $userpwdpair = 0) {
            return $this->RESTRequest($url, $type, $data, $userpwdpair);
        }
                        
    }
    
    
?>
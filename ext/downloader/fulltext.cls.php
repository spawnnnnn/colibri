<?php

    class FullTextDownloader {
        
        private $_rules;
        
        public function __construct($rules) {
            $this->_rules = $rules;
        }
        
        public function UrlInfo($url) {
            
            try {
            
                $info = array();
                $info['href'] = $url;
                $urls = explode('/', trim($url, '/'));
                $info['abb'] = end($urls);
                //out('downloading', $url);

                $r = new WebRequest($url, RequestType::Get);
                $data = $r->Request();  
                if($data->status != 200)
                    return array();
                
                /*if(strstr($data->headers['url'], '404.shtml') !== false)
                    continue;*/
                
                $content = $data->data;   
                // file_put_contents(_CACHE.'data.txt', $content);
                if(!$content)
                    return array();
                
                // $content = trim(preg_replace('/<\!\-\-(.*?)\-\->/mi', '', str_replace("\n", '', $content)));
                
                libxml_use_internal_errors(true);
                $xml = XMLNode::LoadHTML('<'.'?xml version="1.0" charset="utf-8"?'.'>'.$content, false);
                foreach($this->_rules as $key => $value) {
                                         
                    if(!is_array($value[0])) {

                        if(!isset($info[$key]))
                            $info[$key] = array();

                        $result = $xml->Query($value[0]);
                        if($result->count > 0) {
                            foreach($result as $d) {
                                eval('$ret = '.$value[1].';');
                                $info[$key][] = $ret;
                            }
                        }
                        
                    }
                    else {
                        
                        foreach($value as $v) {
                            if(!isset($info[$key]))
                                $info[$key] = array();
                                                          
                            $result = $xml->Query($v[0]);
                            if($result->count > 0) {
                                foreach($result as $d) {
                                    eval('$ret = '.$v[1].';');
                                    $info[$key][] = $ret;
                                }
                            }                        
                        }
                    }
                    
                }
                
                /*if(count($info) == 3) {
                    out('error: can not download info', $info);
                }*/
            
            }
            catch(Exception $e) {
                out($e->getMessage());
                return array();
            }
            
            return $info;
            
        }

    }


?>
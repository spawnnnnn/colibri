<?
    
    class Feed extends XMLNode {
        
        public static function Load($url, $timeout = 10) {
            $r = new WebRequest($url, RequestType::Get);
            $r->timeout = $timeout;
            $result = $r->Request();
            if($result->status == 200 && (
                        strstr($result->headers['content_type'], 'xml') != false || 
                        strstr($result->headers['content_type'], 'html') != false
                    )
                ) {
              
                $data = $result->data;
                $data = preg_replace('/xmlns=\"(.*)\"/', '', $data);
                
                $dom = new DOMDocument();
                $dom->loadXML($data, LIBXML_COMPACT | LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
                return new Feed($dom->documentElement, $dom);
            }
            else {
                return false;
            }
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case 'version':
                    return $this->attributes->version->value;
                case 'channel':
                    $xmlnode = XMLNode::LoadNode('<channel></channel>', $this->encoding);
                    $xmlnode->Append($this->Query('//title')->first);
                    $xmlnode->Append($this->Query('//link')->first);
                    $xmlnode->Append($this->Query('//description')->first);
                    $xmlnode->Append($this->Query('//language')->first);
                    $xmlnode->Append($this->Query('//generator')->first);
                    $xmlnode->Append($this->Query('//pubDate')->first);
                    $xmlnode->Append($this->Query('//lastBuildDate')->first);
                    $xmlnode->Append($this->Query('//image')->first);
                    return $xmlnode;
                case 'items':   
                    return $this->Query('//item');
                default:
                    return parent::__get($property);
            }
        }
        
        
        
    }
    
?>
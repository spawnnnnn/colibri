<?
    
    class Xml {
        
        public static function Serialize($object, $tagName = null, $cdata = true) {
                       
            $ret = '';
            if($tagName)
                $ret .= '<'.$tagName.'>';
            
            $obj = $object;
            if(!Variable::IsArray($obj))
                $obj = ClassKit::GetProperties($obj);
            
            foreach($obj as $k => $v) {
                
                if(Variable::IsNumeric($k)) 
                    $ret .= '<object index="'.$k.'">';
                else
                    $ret .= '<'.$k.'>';
                
                if(Variable::IsObject($v) || Variable::IsArray($v))
                    $ret .= Xml::Serialize($v);
                else if(Variable::IsBool($v))
                    $ret .= ($v ? 'true' : 'false');
                else if(Variable::IsNull($v) || Variable::IsEmpty($v))
                    $ret .= '';
                else {
                    // sometimes bool values is 't' or 'f'
                    if($v == 't' || $v == 'f')
                        $ret .= ($v == 't' ? 'true' : 'false');
                    else
                        $ret .= $cdata ? '<![CDATA['.$v.']]>' : $v;
                }
                
                if(Variable::IsNumeric($k)) 
                    $ret .= '</object>';
                else
                    $ret .= '</'.$k.'>';
                
            }
                        
            if($tagName)
                $ret .= '</'.$tagName.'>';
            
            return $ret;
        }
        
        public static function Unserialize($string) {
            
        }                  
            
    }
    
    class XmlTemplate extends XMLNode {
        
        public static function Create($xmlTemplateString) {
            $dom = new DOMDocument();
            $dom->loadHTML('<'.'?xml version="1.0" encoding="utf-8"?'.'>'.$xmlTemplateString);
            return new XmlTemplate($dom->documentElement, $dom);
            
        }      
        
        private function _cleanDataKeys($data) {
            $d = array();
            foreach($data as $key => $value) {
                $key = trim($key, ':');
                $d[$key] = html_entity_decode($value);
            }
            return $d;
        }
        
        public function Render($data) {
            
            try {
                
                $data = $this->_cleanDataKeys($data);
                
                $updatables = $this->Query('//*[@data-updatable]');
                foreach($updatables as $updatable) {

                    if(isset($data['cid'])) {
                        $updatable->attributes->Append('data-cid', $data['cid']);
                    }
                        
                    $commands = $updatable->Query('./descendant-or-self::*[@data-update-cmd]');
                    foreach($commands as $cmd) {
                        
                        $cmdName = 'data-update-cmd';
                        $cmdData = $cmd->attributes->$cmdName->value;
                        $cmdData = json_decode(str_replace('\'', '"', $cmdData));
                        
                        if(!$cmdData)
                            continue;
                        
                        foreach($cmdData as $c) {
                            $field = $c->field;
                            $removeCommand = isset($c->not_exists) ? $c->not_exists : '';
                            $defCommand = isset($c->def) ? $c->def : false;
                            if(!isset($data[$field]) && !$defCommand) {
                                if($removeCommand == 'remove')
                                    $cmd->Remove();
                                continue;              
                            }
                            
                            if(!isset($data[$field]))
                                $data[$field] = $defCommand;

                                
                            $attr = $c->attr;
                            if($attr == 'html') {
                                $cmd->value = '';
                                if(!Variable::IsEmpty($data[$field])) {
                                    $cmd->Append(XMLNode::LoadHTML('<'.'?xml encoding="utf-8"?'.'>'.$data[$field], false, 'utf-8'));
                                }
                            }
                            else if($attr == 'text') {
                                $cmd->value = '';
                                if(!Variable::IsEmpty($data[$field]))
                                    $cmd->value = $data[$field];
                            }
                            else if($attr == 'class') {
                                if(!$cmd->attributes->class)
                                    $cmd->attributes->Append('class', $data[$field]);
                                else
                                    $cmd->attributes->class->value .= ' '.$data[$field];
                            }
                            else if($attr == 'background') {
                                if(!$cmd->attributes->style)
                                    $cmd->attributes->Append('style', 'background-image: url('.Strings::PrepareAttribute($data[$field]).');');
                                else
                                    $cmd->attributes->style->value = 'background-image: url('.Strings::PrepareAttribute($data[$field]).')';
                            }
                            else if($attr == 'bgcolor') {
                                if(!$cmd->attributes->style)
                                    $cmd->attributes->Append('style', 'background-color: '.Strings::PrepareAttribute($data[$field]).';');
                                else
                                    $cmd->attributes->style->value = 'background-color: '.Strings::PrepareAttribute($data[$field]);
                            }
                            else {
                                if(!$cmd->attributes->$attr)
                                    $cmd->attributes->Append($attr, Strings::PrepareAttribute($data[$field]));
                                else
                                    $cmd->attributes->$attr->value = Strings::PrepareAttribute($data[$field]);
                            }
                            
                        }
                        
                    }
                    
                }
            
            }
            catch(Exception $e) {
                out($e->getMessage(), $e->getLine(), $e->getFile());
            }
            
            return $this->html;
                
        }
        
        
        
    }
    
?>
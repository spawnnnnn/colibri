<?php

    Core::Using("System::Utilities");
    Core::Using("System::Events");  
                                        
    class ObjectEx extends IEventDispatcher {
        
        protected $_original;
        protected $_data;
        protected $_prefix = "";
        
        public function __construct($data = null, $prefix = "") {
            if(is_null($data))
                $this->_data = array();
            else {
                if($data instanceof ObjectEx) {
                    $this->_data = $data->ToArray();
                    $this->_prefix = $data->prefix;
                }
                else if(is_array($data)) { 
                    $this->_data = $data;
                }
                else {   
                    $this->_data = ClassKit::GetProperties($data);
                }
            }

            if(!empty($prefix) && substr($prefix, strlen($prefix)-1, 1) != "_") 
                $prefix = $prefix."_";

            $this->_prefix = $prefix;
            
            $this->_data = Variable::ChangeArrayKeyCase($this->_data, CASE_LOWER);
            
            $this->_original = $this->_data;
            
        }
        public function __destruct() {
            unset($this->_data);
        }
        
        public function Clear() {
            $this->_data = array();
        }
        
        public function ToArray($noPrefix = false) {
            if(!$noPrefix)
                return $this->_data;
            else {
                $data = array();
                foreach($this->_data as $key => $value) {
                    $data[substr($key, strlen($this->_prefix))] = $value;
                }
                return $data;
            }
        }
        
        public function ToJSON() {
            return json_encode($this->ToArray());
        }
        
        public function __get($property) {
            switch($property) {
                case 'prefix':
                    return $this->_prefix;
                case 'data':
                    return $this->_data;
                case 'original':
                    return (object)$this->_original;
                default:
                    if(!empty($this->_prefix) && strpos($property, $this->_prefix) === false)
                        $property = $this->_prefix.$property;
                    return isset($this->_data[$property]) ? $this->_data[$property] : null;
            }
        }
        public function __set($property, $value) {
            if(!empty($this->_prefix) && strpos($property, $this->_prefix) === false)
                $property = $this->_prefix.$property;
            @$this->_data[$property] = $value;
        }
        
        public function AddProperty($property, $value) {
            $property = $this->_prefix.$property;
            $this->_data[$property] = $value;
        }
        public function GetProperty($property) {
            $property = $this->_prefix.$property;
            return isset($this->_data[$property]) ? $this->_data[$property] : null;
        }
        public function DeleteProperty($property) {
            $property = $this->_prefix.$property;
            unset($this->_data[$property]);
        }
        public function ToString() {
            $ret = '<data>';
            foreach($this->_data as $k => $value) {
                
                $v = $value;
                if(is_bool($v)) {
                    $v = $v ? 'true' : 'false';
                }
                else if(is_array($v)) {
                    $vvv = '';
                    foreach($v as $k => $vv) {
                        if(is_string($vv))
                            $vvv .= ','.$vv;
                        else 
                            $vvv .= ','.serialize($vv);
                        
                    }
                    $v = substr($vvv, 1);
                }
                else {
                    if(is_object($v)) {
                        if($v instanceOf ObjectEx)
                            $v = $v->ToString();
                        else if($v instanceOf stdClass){
                            $vv = new ObjectEx($v);
                            $v = $vv->ToString();
                        }
                        else 
                            $v = serialize($v);
                    }
                }
                
                $ret .= '<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
            }
            $ret .= '</data>';
            return $ret;
        }      
        
        public function ExportTable() {
            $ret = '<table width="100%" style="margin-bottom: 20px; border-collapse: collapse; border: 1px solid #c0c0c0;">';
            foreach($this->_data as $k => $value) {
                
                $v = $value;
                if(is_bool($v)) {
                    $v = $v ? 'Да' : 'Нет';
                }
                else if(is_array($v)) {
                    $vvv = '';
                    foreach($v as $k => $vv) {
                        if(is_string($vv))
                            $vvv .= ','.$vv;
                        else 
                            $vvv .= ','.serialize($vv);
                        
                    }
                    $v = substr($vvv, 1);
                }
                else {
                    if(is_object($v)) {
                        if($v instanceOf ObjectEx)
                            $v = $v->ExportTable();
                        else if($v instanceOf stdClass){
                            $vv = new ObjectEx($v);
                            $v = $vv->ExportTable();
                        }
                        else 
                            $v = serialize($v);
                    }
                }
                
                $ret .= '<tr><td style="width: 30%; font-weight: bold;">'.$k.'</td><td style="padding-left: 20px;">'.$v.'</td></tr>';
            }
            $ret .= '</table>';
            return $ret;
        }
        
        private static function _isFilePath($value) {
            if(strstr($value, '/') !== false && strstr($value, '.') !== false)
                return true;
            return false;
        }
        
        public static function ToPlaneObject($object, $prefix, $url = '') {
            $return = array();
            foreach($object as $key => $value) {
                if(is_object($value) || is_array($value)) {
                    $return = array_merge($return, ObjectEx::_createTransformArray($value, is_numeric($key) ? $prefix.'['.$key.']' : $prefix.'.'.$key, $url));
                }
                else {
                    if(is_numeric($key)) {
                        $return[$prefix.'['.$key.']'] = (ObjectEx::_isFilePath($value) ? $url : '').$value;
                        if(ObjectEx::_isFilePath($value)) {
                            $return[$prefix.'['.$key.'].aslink'] = '<a target="_blank" href="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'">(content)</a>';
                            $return[$prefix.'['.$key.'].asimage'] = '<img src="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'" />';
                            $return[$prefix.'['.$key.'].asimagelink'] = '<a target="_blank" href="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'"><img class="snapshot" src="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'" /></a>';
                            $return[$prefix.'['.$key.'].aslink.inp'] = '<p><a target="_blank" href="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'">(content)</a></p>';
                            $return[$prefix.'['.$key.'].asimage.inp'] = '<p><img src="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'" /></p>';
                            $return[$prefix.'['.$key.'].asimagelink.inp'] = '<p><a target="_blank" href="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'"><img class="snapshot" src="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'" /></a></p>';
                        }
                    }
                    else {
                        $return[$prefix.'.'.$key] = (ObjectEx::_isFilePath($value) ? $url : '').$value;
                        if(ObjectEx::_isFilePath($value)) {
                            $return[$prefix.'.'.$key.'.aslink'] = '<a target="_blank" href="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'">(content)</a>';
                            $return[$prefix.'.'.$key.'.asimage'] = '<img src="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'" />';
                            $return[$prefix.'.'.$key.'.asimagelink'] = '<a target="_blank" href="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'"><img class="snapshot" src="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'" /></a>';
                            $return[$prefix.'.'.$key.'.aslink.inp'] = '<p><a target="_blank" href="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'">(content)</a></p>';
                            $return[$prefix.'.'.$key.'.asimage.inp'] = '<p><img src="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'" /></p>';
                            $return[$prefix.'.'.$key.'.asimagelink.inp'] = '<p><a target="_blank" href="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'"><img class="snapshot" src="'.(ObjectEx::_isFilePath($value) ? $url : '').$value.'" /></a></p>';
                        }
                    }
                }
            }
            return $return;
        }        
        
            
    }
    

?>
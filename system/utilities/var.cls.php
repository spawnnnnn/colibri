<?php

    class Variable {
        
        static function IsEmpty($var) {
            if(is_object($var))
                return is_null($var);
            return ($var === null || $var === "");
        }
        
        static function IsNull($var) {
            return is_null($var);
        }
        
        static function IsObject($var) {
            return is_object($var);
        }
        
        static function IsArray($var) {
            return is_array($var);
        }
        
        static function IsBool($var) {
            return is_bool($var);
        }
        
        static function IsString($var) {
            return is_string($var);
        }
        
        static function IsNumeric($var) {
            return is_numeric($var);
        }    
        
        static function IsDate($var) {
            if(!$value)
                return false;
            if(is_null($value))
                return false;
            if(is_string($value))
                return strtotime($value) !== false;
            else    
                return true;
        }
        
        function IsTime($value) {
            if(preg_match('/\d{2}:\d{2}/', $value, $matches) > 0)
                return true;
            return false;
        }    

        static function ChangeArrayValueCase($array, $case = CASE_LOWER) {
            for($i=0; $i<count($array); $i++)
                $array[$i] = $case == CASE_LOWER ? Strings::ToLower($array[$i]) : Strings::ToUpper($array[$i]);
            return $array;
        }

        static function ChangeArrayKeyCase($array, $case = CASE_LOWER) {
            return array_change_key_case($array, $case);
        }
        
        static function ObjectToArray($object) {
            if (Variable::IsObject($object)) {
                $object = get_object_vars($object);
                
                foreach ($object as $k => $v) {
                    $array[$k] = self::ObjectToArray($v);
                }
            }

            return $object;
        }
        
        static function ArrayToObject($array) {
            if (Variable::IsObject($array)) {
                $array = get_object_vars($array);
            }
            if (is_array($array)) {
                foreach ($array as $k=>$v) {
                    $array[$k] = self::ArrayToObject($v);
                }
                $array = (object) $array;
            }
            return $array;
            
            /*if( ! is_array($array)) {
                return $array;
            }

            $object = new stdClass();
            if (is_array($array) && count($array) > 0) {
                foreach ($array as $name=>$value) {
                    $name = ''.strtolower(trim($name));
                    if (!empty($name)) {
                        $object->$name = self::ArrayToObject($value);
                    }
                }
                return $object; 
            }
            else {
                return false;
            }*/
        }

        static function Bin2Hex($data) {
            return bin2hex($data);
        }
        
        static function Hex2Bin($data) {
           $len = strlen($data);
           return pack("H" . $len, $data);
        }
        
        static function isSerialized($v) {
            $vv = @unserialize($v);
            if(is_array($vv) || is_object($vv))
                return true;
            return false;
        }
        
        static function Serialize($obj) {
            return '0x'.Variable::Bin2Hex(serialize($obj));
        }

        static function Unserialize($string) {
            if(substr($string, 0, 2) == '0x')
                $string = Variable::Hex2Bin(substr($string, 2));
            return @unserialize($string);
        }
        
        static function CreateHash($array) {
            
            $a = Variable::FillArray($array);
            $c = count($a);
            
            $rret = '';
            foreach($a as $b) {
                $rret = $rret == '' ? md5($b) : $rret & md5($b);
            }
            
            return md5($rret);
        }
        
        static function FillArray($items, $perms = array()) {
            static $retperms = array();
            if (!empty($items)) {
                for ($i = count($items) - 1; $i >= 0; --$i) {
                    $newitems = $items;
                    $newperms = $perms;
                    list($foo) = array_splice($newitems, $i, 1);
                    array_unshift($newperms, $foo);
                    Variable::FillArray($newitems, $newperms);
                }
            }
            else {
                $retperms[] = $perms;
            }
            
            $a = array();
            foreach($retperms as $b) {
                $a[] = join('', $b);
            }
            
            return $a;
            
        }

        public static function Extend($o1, $o2) {
            
            $o1 = (array)$o1;
            $o2 = (array)$o2;
            
            foreach($o1 as $k => $v) {
                if(isset($o2[$k])) {
                    $o1[$k] = $o2[$k];
                }
            }
            
            foreach($o2 as $k => $v) {
                if(!isset($o1[$k])) {
                    $o1[$k] = $v;
                }
            }
            
            return $o1;
            
        }
        
        public static function Coalesce($d, $def) {
            if(is_null($d))
                return $def;
            return $d;
        }
        
        public static function ToString($object, $spl1 = ' ', $spl2 = '=', $quote = true, $keyPrefix = '') {
            $ret = array();
            $object = (array)$object;
            foreach($object as $k => $v) {
                $ret[] = $keyPrefix.$k.$spl2.($quote ? '"' : '').Strings::PrepareAttribute($v).($quote ? '"' : '');
            }
            return implode($spl1, $ret);
        }
        
    }
    
    if(!function_exists('hex2bin')) {
	    function hex2bin($hex) {
	        return pack("H*" , $hex);
	    }
    }

?>

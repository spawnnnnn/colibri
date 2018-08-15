<?php
    
    class Json {
        
        public static function Encode($arr) {
            //convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
            if(!is_array($arr)) $arr = array();
            array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
            return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
        }
        
        public static function EncodeUTF8($data) {
            return preg_replace_callback('/\\\u([01-9a-fA-F]{4})/', function ($matches) {
                return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
            }, json_encode($data));
        }
        
        public static function Decode($s) {
            return json_decode(str_replace(array("\r", "\n", "\t"), array("", "", ""), $s));
        }
        
        public static function Serialize($data) {
            return Json::Encode($data);
        }

        public static function Unserialize($data) {
            return Json::Decode($data);
        }
        
        
    }
    
    
?>
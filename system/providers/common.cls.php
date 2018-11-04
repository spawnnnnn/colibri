<?php
    
    interface IContentProvider {
        public static function GetData($dataId);
    }

    /**
    * @desc Parses the strings like: provider(source) and prepares a specific provider for the get data operation
    */
    class ContentProvider {
        
        public static function Parse($string) {
            if(is_object($string)) return $string;
            if(preg_match('/([^\(]+)\((.+)\)$/', $string, $matches)) {
                $providerName = $matches[1];
                $providerData = $matches[2];
                if(ClassKit::Exists($providerName.'Provider')) {
                    eval('$r = '.$providerName.'Provider::GetData($providerData);');
                    return $r;
                }
            }
            return $string;
        }
        
    }
    
?>

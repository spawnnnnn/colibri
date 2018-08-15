<?php
    
    Core::Using('System::Collections');
    Core::Using('System::Reflection');
    
    class HashtableProvider implements IContentProvider {
        public static function GetData($dataId) {
            // male:string(male);female:string(female);
            $h = new Hashtable();
            $ar = split(";", $dataId);
            foreach($ar as $a) {
                if(trim($a) != "") {
                    $b = split(":", $a);
                    $id = $b[0];
                    $value = ContentProvider::Parse($b[1]);
                    $h->Add($id, $value);
                }
            }
            return $h;
        }
    }
    
    class ArrayProvider implements IContentProvider {
        public static function GetData($dataId) {
            // 1;2;3;4;5;6;male;female
            $h = new Hashtable();
            $ar = split(";", $dataId);
            foreach($ar as $a) {
                if(trim($a) != "")
                    $h->Add(ContentProvider::Parse($a));
            }
            return $h;
        }
    }
    
    class DelegateProvider implements IContentProvider {
        
        public static function GetData($dataId) {
            // delegate(ajax, OnApplicationLoaded)
            preg_match_all('/\s?([^\,]+)\s?\,\s?([^\)]+)/i', $dataId, $matches);
            if(count($matches) > 0) {
                $object = strtolower($matches[1][0]);
                $handler = $matches[2][0];
                
                if($object == 'null' || $object == 'nothing') 
                    return new Delegate(null, $handler);
                else {
                    
                    // this is a delegato to module method
                    if(ClassKit::Exists($object)) {
                        return new Delegate($object::$i, $handler);
                    }
                    
                    // this is a delegate to a global object                    
                    return new Delegate($GLOBALS[$object], $handler);
                }
                
            }
            return null;
        }
    }    
    
    
    
?>
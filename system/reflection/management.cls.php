<?php
    
    class ClassKit {
        
        static function Exists($class) {
            return class_exists($class);
        }
        
        static function HasMethod($class, $method) {
            return method_exists($class, $method);
        }
        
        static function HasProperty($class, $method) {
            return property_exists($class, $method);
        }

        static function GetProperties($object) {
            return get_object_vars($object);
        }    
        
        static function GetName($object) {
            return get_class($object);
        }        
        
        static function InvokeMethod($object, $method, $args) {
            return call_user_func_array(array($object, $method), $args);
        }
        
    }
    
    class CodeKit {

        static function Exists($func) {
            return function_exists($func);
        }
        
        static function Invoke($func, $args) {
            return call_user_func_array($func, $args);
        }
        
    }
    
    class CodeModel {
    
        public static function CreateObject() {
            
            $args = func_get_args();
            
            $className = $args[0];
            
            $eval = '$o = new '.$className.'(';
            for($i=1; $i<count($args); $i++) {
                $eval .= ($i != 1 ? ',' : '').'$args['.$i.']';
            }        
            $eval .= ');';
            
            eval($eval);
            return $o;
        }
        
        public static function CreateSingletonObject() {
            $args = func_get_args();
            
            $className = $args[0];
            
            $eval = 'if(!'.$className.'::$i) { '.$className.'::$i = new '.$className.'(';
            for($i=1; $i<count($args); $i++) {
                $eval .= ($i != 1 ? ',' : '').'$args['.$i.']';
            }        
            $eval .= '); } ';
            eval($eval);
            
            $eval = '$o = '.$className.'::$i;';
            eval($eval);
            
            return $o;
        }
        
        public static function CallStaticMethod($className, $methodName) {
            
            $eval = '$result = '.$className.'::'.$methodName.'(';
            for($i=2; $i<count($args); $i++) {
                $eval .= ($i != 1 ? ',' : '').'$args['.$i.']';
            }        
            $eval .= ');';

            eval($eval);
            return $result;
        }
        
        function CreateComponentList($code, $returnToVarName = "\$ret", $contextFieldName = "\$context") {
            // create the component list from a template html text    
            $retNameStart = (is_null($returnToVarName) ? $contextFieldName."->Out(" : $returnToVarName." .= ");
            $retNameEnd = (is_null($returnToVarName) ? ");" : ";");
                                                               
            $code = str_replace("&lt;?", "<"."?", $code);
            $code = str_replace("<"."?php", "<"."?", $code); 
            $code = str_replace("?&gt;", "?".">", $code);
            
            $retcode = "";
            $blocks = array();
            $lastpos = 0;
            $i = 1;
            $splitter = "<?";
            while(($ipos = strpos($code, $splitter)) !== false) {
                if($splitter == "?>")
                    $blocks[] = "<?".substr($code, 0, $ipos)."?>";
                else
                    $blocks[] = substr($code, 0, $ipos);
                $code = substr($code, $ipos + 2);
                $splitter =    ($splitter == "<?" ? "?>" : "<?");
            }
            $blocks[] = substr($code, $ipos);

            $blocks1 = array();
            foreach($blocks as $block) {
                if($block == "")
                    continue;
                    
                if(substr(trim($block), 0, 3) == "<?=") {
                    $cc = trim(substr(trim($block), 3, strlen(trim($block)) - 5));
                    if(substr($cc, strlen($cc)-1, 1) == ";")
                        $cc = substr($cc, 0, strlen($cc)-1);
                        
                    $blocks1[] = $retNameStart.$cc.$retNameEnd;
                }
                else if(substr(trim($block), 0, 2) == "<?")
                    $blocks1[] = substr(trim($block), 2, strlen(trim($block)) - 4);
                else {
                    $block = str_replace("\\","\\\\", $block);
                    $block = str_replace("\"","\\\"", $block);
                    $block = str_replace("\$","\\\$", $block);
                    $blocks1[] = $retNameStart."\"".$block."\"".$retNameEnd;
                }
            }

            $retcode = "";
            foreach($blocks1 as $block) {
                if($block != "")
                    $retcode .= $block;
            }

            return $retcode;
        }
        
        
    }
    

    
?>

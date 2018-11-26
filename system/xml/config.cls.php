<?php

    class Config extends XMLNode {
        
        public static function Exists($source) {
            return FileInfo::Exists($source);
        }
        
        public static function Create($source, $xmlRoot = '<root></root>') {
            FileInfo::Create($source, 0777);
            FileInfo::WriteAll($source, $xmlRoot);
        }
        
        public static function Load(string $source, bool $isFile = true) : XMLNode {
            if(FileInfo::Exists($source)) {
                $content = FileInfo::ReadAll($source);
                try {
                    return XMLNode::LoadNode($content);
                }
                catch(Exception $e) {
                    return XMLNode::Load($content, false);
                }
            }
            return XMLNode::LoadNode($source);
        }

        public static function FindDomain($xml, $domain, $default = false) {
            $domains = $xml->Query('//domain[@name="'.$domain.'"]');
            if ($domains->count == 0) {
                $domains = $xml->Query('//domain');
                foreach ($domains as $item) {
                    if (Strings::Substring($item->attributes->name->value, 0, 1) == '/' && Strings::Substring($item->attributes->name->value, -1) == '/') {
                        if (preg_match($item->attributes->name->value, $domain)) {
                            return $item;
                        }
                    }
                }
                    
                return !$default ? false : $xml->Query('//domain[@default="true"]')->first;
            }

            return $domains->first;
        }
        
    }

?>
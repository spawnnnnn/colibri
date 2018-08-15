<?php

    define("MODE_READ", "rb9");
    define("MODE_WRITE", "wb9");
    define("MODE_APPEND", "ab9");
    define("MODE_CREATEWRITE", "wb9");
    
    class FileInfo {
        
        private $attributes,
                $info,
                $_size = 0,
                $access;
        
        function __construct($path) {
            $this->info = pathinfo($path);
            if ($this->info['basename'] == '')
                throw new BaseException('path argument is not a file path');
            
            if ($this->info['dirname'] == '.')
                $this->info['dirname'] = '';
            
        }
        
        function __get($property){
            switch (strtolower($property)){
                case 'attributes' :
                    return $this->getAttributesObject();
                case 'filename' :
                    return $this->info['filename'];
                case 'name' :
                    return $this->info['basename'];
                case 'extension' :
                    if (array_key_exists('extension', $this->info))
                        return $this->info['extension'];
                    return '';
                case 'directory' :
                    if ($this->info['dirname'] == '')
                        return;
                    
                    if (!($this->info['dirname'] instanceof DirectoryInfo))
                        $this->info['dirname'] = new DirectoryInfo($this->info['dirname'] . '/#');
                    
                    return $this->info['dirname'];
                case 'dotfile':
                    return substr($this->name, 0, 1) == '.';
                case 'path' :
                    $dirname = $this->info['dirname'] instanceof DirectoryInfo ? $this->info['dirname']->path : $this->info['dirname'];
                    return $dirname . ($dirname ? '/' : '') . $this->info['basename'];
                case 'size' :
                    if($this->_size == 0) {
                        $this->_size = filesize($this->path);
                    }
                    return $this->_size;
                case 'exists' :
                    return FileInfo::exists($this->path);
                case 'access' :
                    return $this->getSecurityObject();
                case 'content' :
                    if (FileInfo::exists($this->path))
                        return file_get_contents($this->path);
                default: {
                    if(strstr(strtolower($property), 'attr_') !== false) {
                        $p = str_replace('attr_', '', strtolower($property));
                        return $this->getAttributesObject()->$p;
                    }
                    break;
                }
                
                
            }
        }
        
        function __set($property, $value){
            switch (strtolower($property)){
                case 'created' :
                    $this->getAttributesObject()->created = $value;
                    break;
                case 'modified' :
                    $this->getAttributesObject()->modified = $value;
                    break;
                case 'readonly' :
                    $this->getAttributesObject()->readonly = $value;
                    break;
                case 'hidden' :
                    $this->getAttributesObject()->hidden = $value;
                    break;
            }
        }
        
        protected function getAttributesObject(){
            if ($this->attributes === null)
                $this->attributes = new FileAttributes($this);
            
            return $this->attributes;
        }
        
        protected function getSecurityObject(){
            if ($this->access === null)
                $this->access = new FileSystemSecurity($this);
            
            return $this->access;
        }
        
        public function CopyTo($path){
            FileInfo::copy($this->path, $path);
        }
        
        public function MoveTo($path){
            FileInfo::move($this->path, $path);
        }
        
        public function ToString(){
            return $this->name;
        }
        
        public static function ReadAll($path) {
            if (FileInfo::Exists($path))
                return file_get_contents($path);
            return false;
        }
        
        public static function WriteAll($path, $content, $recursive = false, $mode = 0777) {
            
            if(!FileInfo::Exists($path)) {
                FileInfo::Create($path, $recursive, $mode);
            }
            
            /*$stream = false;       
            if(!FileInfo::Exists($path))
                $stream = FileInfo::Create($path, $recursive, $mode);
            else
                $stream = FileInfo::Open($path);
            
            if(!$stream) {
                return false;
            }
            else {
                $stream->Write($content);
                $stream->Close();
                return true;
            }*/
            
            file_put_contents($path, $content);
            return true;
            
        }
        
        public static function Open($path){ //ireader
            if (FileInfo::Exists($path))
                return new FileStream($path);
            return false;
        }
        
        public static function Exists($path){
            return file_exists($path);
        }
        
        public static function IsEmpty($path){
            try { //use exception | file_exists ?
                $info = stat($path);
                return $info['size'] == 0;
            } catch (BaseException $e){
                return true;
            }
        }
        
        public static function Create($path, $recursive = true, $mode = 0777){
            if(!DirectoryInfo::Exists($path) && $recursive)
                DirectoryInfo::Create($path, $recursive, $mode);
            
            if(!FileInfo::Exists($path))
                touch($path);
                
            return new FileStream($path);
        }
        
        public static function Delete($path){
            if (!FileInfo::exists($path))
                throw new BaseException('file not exists');
            
            return unlink($path);
        }
        
        public static function Copy($from, $to){
            if (!FileInfo::exists($from))
                throw new BaseException('file not exists');
            
            //stream_copy_to_stream(); file_get_contents() + file_put_contents();
            
            copy($from, $to);
        }
        
        public static function Move($from, $to){
            if (!FileInfo::exists($from))
                throw new BaseException('source file not exists');
            
            rename($from, $to);
        }
        
        public static function IsDirectory($path) {
            return is_dir($path);
        }

        public function ToArray() {
            return array(
                'name' => $this->name,
                'filename' => $this->filename,
                'ext' => $this->extension,
                'path' => $this->path,
                'size' => $this->size,
                
                'created' => $this->attr_created,
                'modified' => $this->attr_modified,
                'lastaccess' => $this->attr_lastaccess,
            );
        }
        
        
    }
    
    class DirectoryInfo {
        
        private $attributes,
                $path,
                $parent,
                $access,
                $pathArray;
                
        function __construct($path){
            $this->path = dirname($path[strlen($path) - 1] == '/' ? $path . '#' : $path);
        }
        
        function __get($property){
            switch (strtolower($property)){
                case 'current' :
                    return;
                case 'attributes' :
                    return $this->getAttributesObject();
                case 'name' :
                    if(!$this->pathArray)
                        $this->pathArray = explode('/', $this->path);
                    return $this->pathArray[count($this->pathArray) - 1];
                case 'path' :
                    return $this->path.'/';
                case 'dotfile':
                    return substr($this->name, 0, 1) == '.';
                case 'size' :
                    return;
                case 'parent' :
                    if ($this->parent == null)
                        $this->parent = new DirectoryInfo('');
                    return $this->parent;
                case 'access' :
                    return $this->getSecurityObject();
            }
        }
        
        function __set($property, $value){
            switch ($property){
                case 'created' :
                    $this->getAttributesObject()->created = $value;
                    break;
                case 'modified' :
                    $this->getAttributesObject()->modified = $value;
                    break;
                case 'readonly' :
                    $this->getAttributesObject()->readonly = $value;
                    break;
                case 'hidden' :
                    $this->getAttributesObject()->hidden = $value;
                    break;
            }
        }
        
        protected function getAttributesObject(){
            if ($this->attributes === null)
                $this->attributes = new FileAttributes($this);
            return $this->attributes;
        }
        
        protected function getSecurityObject(){
            if ($this->access === null)
                $this->access = new FileSystemSecurity($this);
            return $this->attributes;
        }
        
        public function CopyTo($path){
            DirectoryInfo::copy($this->path, $path);
        }
        
        public function MoveTo($path){
            DirectoryInfo::move($this->path, $path);
        }
        
        public function ToString(){
            return $this->path;
        }
        
        static function IsDir($path) {
            try {           
                return substr($path, strlen($path) - 1, 1) == '/';
                // return !pathinfo($path, PATHINFO_EXTENSION);     
            }
            catch(Exception $e) {
                return false;
            }
        }
        
        static function Exists($path){            
            return FileInfo::Exists(dirname($path[strlen($path) - 1] == '/' ? $path . '#' : $path));
        }
        
        static function Create($path, $recursive = true, $mode = 0777) {
            if(!DirectoryInfo::Exists($path)) {
                $path2 = dirname($path[strlen($path) - 1] == '/' ? $path . '#' : $path);
                mkdir($path2, $mode, $recursive);
                chmod($path2, $mode);
            }
                
            return new DirectoryInfo($path);
        }
        
        static function Delete($path){
            
            if (!DirectoryInfo::exists($path))
                throw new BaseException('directory not exists');
            
            if (is_dir($path)) { 
                $objects = scandir($path); 
                foreach ($objects as $object) { 
                    if ($object != '.' && $object != '..') { 
                        if (is_dir($path."/".$object))
                            DirectoryInfo::Delete($path.'/'.$object);
                        else
                            unlink($path.'/'.$object); 
                    } 
                }
                rmdir($path); 
            } 

        }
        
        static function Copy($from, $to){
            if (!DirectoryInfo::exists($from))
                throw new BaseException('source directory not exists');
            if (DirectoryInfo::exists($to))
                throw new BaseException('target directory exists');
                
            $dir = opendir($from); 
            DirectoryInfo::Create($to, true, 0766); 
            while(false !== ( $file = readdir($dir)) ) { 
                if (( $file != '.' ) && ( $file != '..' )) { 
                    if ( is_dir($from . '/' . $file) ) { 
                        DirectoryInfo::Copy($from . '/' . $file . '/', $to . '/' . $file . '/'); 
                    } 
                    else { 
                        FileInfo::Copy($from . '/' . $file, $to . '/' . $file); 
                    } 
                } 
            }
            closedir($dir); 
                  
        }
        
        static function Move($from, $to){
            if (!DirectoryInfo::exists($from))
                throw new BaseException('source directory not exists');
            if (DirectoryInfo::exists($to))
                throw new BaseException('target directory exists');
            
            rename($from, $to);
        }                    
        
        public function ToArray() {
            return array(
                'name' => $this->name,
                'path' => $this->path.'/',
                'created' => $this->getAttributesObject()->created,
                'modified' => $this->getAttributesObject()->modified,
                'lastaccess' => $this->getAttributesObject()->lastaccess,
                /* get directory security */
            );
        }
        
    }
    
    class FileAttributes {
        
        protected $source;
        protected $attributes = array();
        
        function __construct(/*FileInfo, DirectoryInfo*/ $source){
            $this->source = $source;
        }
        
        function __get(/*string*/ $property){
            switch ($property){
                case 'created' :
                    if (!array_key_exists('created', $this->attributes))
                        $this->attributes['created'] = filectime($this->source->path);
                    
                    return $this->attributes['created'];
                case 'modified' :
                    if (!array_key_exists('created', $this->attributes))
                        $this->attributes['created'] = filemtime($this->source->path);
                    
                    return $this->attributes['created'];
                case 'lastaccess' :
                    if (!array_key_exists('created', $this->attributes))
                        $this->attributes['created'] = fileatime($this->source->path);
                    
                    return $this->attributes['created'];
                default :
                    if (array_key_exists($property, $this->attributes))
                        return $this->attributes->$property;
            }
        }
        
        function __set($property, $value){
            switch ($property){
                default :
                    if (array_key_exists($property, $this->attributes))
                        $this->update($property, $value);
            }
        }
        
        private function update($property, $value){ //apply all values to real file
            //update every time on set new value -> С‚.Рє. 
            //    1) РѕРїРµСЂР°С†РёРё РїСЂРё РѕР±РЅРѕРІР»РµРЅРёРё СЂР°Р·РЅС‹С… Р°С‚С‚СЂРёР±СѓС‚РѕРІ РјРѕРіСѓС‚ СЂР°Р·Р»РёС‡Р°С‚СЊСЃСЏ
            //    2) Р±РѕР»РµРµ РІРµСЂРѕСЏС‚РЅРѕ РёР·РјРµРЅРµРЅРёРµ РјР°Р»РѕРіРѕ РєРѕР»-РІР° Р°С‚С‚СЂРёР±СѓС‚РѕРІ Р·Р° СЂР°Р·, 
            //    С‡С‚РѕР±С‹ РЅРµ РїСЂРёС…РѕРґРёР»РѕСЃСЊ РѕР±РЅРѕРІР»СЏС‚СЊ РІРµСЃСЊ СѓР¶Рµ Р·Р°РіСЂСѓР¶РµРЅРЅС‹Р№ СЃРїРёСЃРѕРє РєР°Р¶РґС‹Р№ СЂР°Р·
        }
    }
    
    class FileSystemSecurity {
        
        protected $source;
        protected $flags; 
        
        function __construct(/*FileInfo, DirectoryInfo*/ $source, $flags = null){
            $this->source = $source;
            if ($flags === null)
                return;
                
            if ($flags instanceof ICollection)
                $this->flags = $flags->rawArray;
            else if (is_array($flags))
                $this->flags = $flags;
            else
                throw new BaseException('illegal arguments: ' . __CLASS__);
        }
        
        function __get($property){
            switch ($property){
                case 'denied' :
                    return;
                case 'grant' :
                    return;
                case 'read' :
                    return;
                case 'write' :
                    return;
                case 'delete' :
                    return;
                case 'execute' :
                    return;
                case 'owner' :
                    return;
            }
        }
        
        function __set($property, $value){
            switch ($property){
                case 'denied' :
                    break;
                case 'grant' :
                    break;
                case 'read' :
                    break;
                case 'write' :
                    break;
                case 'delete' :
                    break;
                case 'execute' :
                    break;
                case 'owner' :
                    break;
            }
        }
        
        private function update() {
            
        }
        
        public function set() {
            
        }
    }
    
    class DirectoryFinder {
        
        public function __construct() { }
        
        public function Files($path, $match = '', $sortField = false, $sortType = false) {
            if(!DirectoryInfo::Exists($path))
                return new ArrayList();
            
            $ret = new ArrayList();

            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    
                    if ($file != "." && $file != ".." && filetype($path . '/' . $file) != "dir") {
                        
                        if(!Variable::IsEmpty($match) && preg_match($match, $file) == 0)
                            continue;
                        
                        $ret->Add(new FileInfo($path . '/' . $file));
                        
                    }
                }
                closedir($handle);
            }    
            
            if($sortField)
                $ret->Sort($sortField, $sortType);
            return $ret;
        }
        
        public function Directories($path, $sortField = false, $sortType = false) {
            if(!DirectoryInfo::Exists($path))
                return new ArrayList();
            
            $ret = new ArrayList();
            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != ".." && @filetype($path . '/' . $file) == "dir")
                        $ret->Add(new DirectoryInfo($path . $file . '/'));
                }
                closedir($handle);
            }    
            if($sortField)
                $ret->sort($sortField, $sortType);
            return $ret;
        }
        
        public function Children($path) {
            if(!DirectoryInfo::Exists($path))
                return new ArrayList();
            
            $ret = new ArrayList();

            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        if(@filetype($path . '/' . $file) == "dir")
                            $ret->Add(new DirectoryInfo($path . '/' . $file));
                        else                                    
                            $ret->Add(new FileInfo($path . '/' . $file));
                    }
                }
                closedir($handle);
            }    
            // $ret->sort($sortField, $sortType);
            return $ret;
        }

    }
    
?>
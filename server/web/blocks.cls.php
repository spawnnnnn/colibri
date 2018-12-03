<?php

    class StyleCache {
        
        public static function Create($styles = array(), $include_release = true, $filename = null) {
            
            $name = '';
            $content = '';
            
            foreach($styles as $style) {
                if(!FileInfo::Exists(_PATH.$style))                
                    throw new Exception('file not exists '._PATH.$style);
                $f = new FileInfo(_PATH.$style);
                $moddate = $f->attributes->modified;
                $name .= md5($style.'-'.$moddate); 
            }
            
            if ($include_release) {
                $f = new FileInfo(_CACHE.'code/.css.release');
                $moddate = $f->attributes->modified;
                $name .= md5('.css.release-'.$moddate);
            }

            $name = $filename ? $filename : md5($name).'.cache.css';
            $link = trim(_CACHE.'code/'.$name, '.');
            $path = _CACHE.'code/'.$name;

            if (_DEBUG && FileInfo::Exists($path))
                FileInfo::Delete($path);
            
            if (!FileInfo::Exists($path)) {

                foreach($styles as $style) {
                    $content .= '/* style sheet file begin '.$style.'*/'."\n\n";
                    $c = FileInfo::ReadAll(_PATH.$style);
                    //if(_DEBUG) $c = str_replace('{', '{ file-name: '.$style.';', $c);
                    $content .= $c."\n\n";
                    $content .= '/* style sheet file end '.$style.'*/'."\n\n";
                }
                
                if ($include_release) {
                    $cp = _CACHE.'code/.css.release';
                    $content .= '/* style file begin .css.release*/'."\n\n";
                    $c = FileInfo::ReadAll($cp);
                    //if(_DEBUG) $c = str_replace('{', '{ file-name: .css.release;', $c);
                    $content .= $c."\n\n";
                    $content .= '/* style file end .css.release*/'."\n\n";
                }
            
                /*if(!_DEBUG) {
                    $minifier = new CssMinifier($content);
                    $content = $minifier->getMinified();
                }*/
                
                if(FileInfo::Exists($path))
                    FileInfo::Delete($path);
                FileInfo::WriteAll($path, $content, true, 0x777);
            }
            $f = new FileInfo($path);
            $moddate = $f->attributes->modified;
            
            return '<link rel="stylesheet" href="'.$link.'?ver='.$moddate.'" type="text/css" />';
        }
        
    }
    
    class JSCache {
        
        public static function Create($scripts = array(), $include_release = true, $filename = null, $defer = false) {
            
            $name = '';
            $content = '';
            
            foreach($scripts as $script) {
                if(!FileInfo::Exists(_PATH.$script))                
                    throw new Exception('file not exists '._PATH.$script);
                $f = new FileInfo(_PATH.$script);
                $moddate = $f->attributes->modified;
                $name .= md5($script.'-'.$moddate); 
            }

            if ($include_release) {
                $f = new FileInfo(_CACHE.'code/.js.release');
                $moddate = $f->attributes->modified;
                $name .= md5('.js.release');
            }

            $name = $filename ? $filename : md5($name).'.cache.js';
            $link = trim(_CACHE.'code/'.$name, '.');
            $path = _CACHE.'code/'.$name;
            
            if (_DEBUG && FileInfo::Exists($path))
                FileInfo::Delete($path);
            
            if (!FileInfo::Exists($path)) {

                foreach($scripts as $script) {
                    $content .= '/* script file begin '.$script.'*/'."\n\n";
                    //$content .= Javascript::Pack(FileInfo::ReadAll(_PATH.$script))."\n\n";
                    $content .= FileInfo::ReadAll(_PATH.$script)."\n\n";
                    $content .= '/* script file end '.$script.'*/'."\n\n";
                }

                /*foreach(Core::$nsScriptFiles as $path => $scripts) {
                    foreach($scripts as $script) {
                        $script = $path.'/'.$script;
                        
                        if(!FileInfo::Exists(_PATH.$script))                
                            throw new Exception('file not exists '._PATH.$script);

                        $name .= md5($script); 
                        
                        $content .= '/ * script file begin '.$script.'* /'."\n\n";
                        //$content .= Javascript::Pack(FileInfo::ReadAll(_PATH.$script))."\n\n";
                        $content .= FileInfo::ReadAll(_PATH.$script)."\n\n";
                        $content .= '/ * script file end '.$script.'* /'."\n\n";
                    }
                }*/
                
                if ($include_release) {
                    $jp = _CACHE.'code/.js.release';
                    $content .= '/* script file begin .js.release*/'."\n\n";
                    $content .= FileInfo::ReadAll($jp)."\n\n";
                    $content .= '/* script file end .js.release*/'."\n\n";
                }
            
                FileInfo::WriteAll($path, $content, true, 0x777);
                
            }
            
            // взять дату обновления файла и прилепить как параметр
            $f = new FileInfo($path);
            $moddate = $f->attributes->modified;
                                                                 
            return '<script type="text/javascript" src="'.$link.'?ver='.$moddate.'"'.($defer ? ' defer' : '').'></script>';
        }
        
    }
        
    class Assets {
        
        public static function Compile($name, $exts, $path, $exception = array(), $preg = false) {
            $jpweb = _CACHE.'code/'.$name;
            if (!_DEBUG && FileInfo::Exists($jpweb)) {
                return str_replace(_PATH, '/', $jpweb);
            }
            
            $files = self::getChildAssets($path, $exts, $exception, $preg);
            
            $content = '';
            foreach($files as $file) {
                if(!FileInfo::Exists($file))                
                    throw new Exception('file not exists '.$file);
            
                $content .= '/* file begin '.$file.'*/'."\n\n";
                $c = FileInfo::ReadAll($file)."\n\n";
                $args = EventDispatcher::$i->Dispatch(new Event(null, 'assets.compile.file'), array('content' => $c));
                if(isset($args['content']))
                    $c = $args['content'];
                $content .= $c;
                $content .= '/* file end '.$file.'*/'."\n\n";
            }
            
            if(FileInfo::Exists($jpweb)) {
                FileInfo::Delete($jpweb);
            }
            
            FileInfo::WriteAll($jpweb, $content, true, 0x777);
            
            system('chmod 777 '.$jpweb);
            return str_replace(_PATH, '/', $jpweb);
        }
        
        protected static function getChildAssets($path, $exts, $exception = array(), $preg = false) {
            $files = array();

            $items = scandir($path);
            foreach ($items as $item) {
                if ($item === '.' or $item === '..') continue;
                $file = new FileInfo($path.$item);
                if ($file->extension && ! in_array($file->extension, $exts)) continue;

                if (FileInfo::IsDirectory($path.$item) && !in_array($item, $exception)) {
                    $files = array_merge($files, self::getChildAssets($path.$item.'/', $exts, $exception, $preg));
                } else if(!$preg || !preg_match($preg, $item)) {
                    $files[] = $path.$item;
                }
            }
            
            return $files;
        }
    }
?>
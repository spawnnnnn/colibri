<?php
    
    /**
    * Template class
    */
    class Template {
        
        private $_file;
        private $_livetime;
        private $_cacheStorage;
        
        private $_layout;
        
        public function __construct($layout, $file) {
              
            $this->_layout = $layout;
            if(FileInfo::Exists($file))
                $this->_file = $file;
            else
                $this->_file = ($this->_layout ? _PATH.$this->_layout->location : _DEFAULT_LOCATION).$file.'.layout';
            
            if(!_DEBUG) {
                if(!function_exists('tpl_'.md5($this->_file)))
                    require_once(_CACHE.'code/.tpl.release');
                if(!function_exists('tpl_'.md5($this->_file))) {
                    throw new BaseException('Template not found: '.$this->_file.': '.'tpl_'.md5($this->_file));
                }
            }
            else {
                if(!FileInfo::Exists($this->_file))
                    throw new BaseException('Template not found: '.$this->_file);
            }
            
            $this->_livetime = 0;
            
            $xml = Config::Load(_TEMPLATE_CACHE);
            
            $this->_cacheStorage = $xml->attributes->storage->value;
            $domain = $xml->Query('//domain[@name="'.($layout ? $layout->domain->attributes->name->value : Navigator::$i->domain->attributes->name->value).'"]');
            if($domain->count > 0) {
                $files = $domain->first->Query('./template[@name="'.$file.'"]');
                if($files->count > 0) 
                    $this->_livetime = $files->first->attributes->livetime->value;
            }
            
            
        }
        
        private function _realRender($args) {
            $layout = $this->_layout;
            $template = $this;
            
            ob_start();
            
            // require($this->_file);
            if(!_DEBUG) {
                if(!function_exists('tpl_'.md5($this->_file)))
                    require_once(_CACHE.'code/.tpl.release');
                if(!function_exists('tpl_'.md5($this->_file))) {
                    throw new BaseException('Template not found: '.$this->_file.': '.'tpl_'.md5($this->_file));
                }
                call_user_func('tpl_'.md5($this->_file), $args, $layout, $template);
            }
            else
                require($this->_file);
            
            $ret = ob_get_contents();
            ob_end_clean();
            
            return $ret;
        }
        
        private function _cacheFile($args) {
            return str_replace('.', '_', str_replace('/', '_', $this->_file)).'_'.md5($this->_file.serialize($args)).'.cache';
        }
        
        private function _createCache($args, $recache, $nocache) {
            $ret = '';
            switch($this->_cacheStorage) {
                
                case 'disk' :
                    
                    $cachePath = _CACHE.'layouts/';
                    $cacheFileName = $this->_cacheFile($args);
                    if(!$recache && FileInfo::Exists($cachePath.$cacheFileName)) {
                        $fi = new FileInfo($cachePath.$cacheFileName);
                        if($fi->attributes->created + $this->_livetime < time())
                            FileInfo::Delete($cachePath.$cacheFileName);
                    }
                    else if(FileInfo::Exists($cachePath.$cacheFileName))
                        FileInfo::Delete($cachePath.$cacheFileName);
                    
                    if(FileInfo::Exists($cachePath.$cacheFileName))
                        $ret = FileInfo::ReadAll($cachePath.$cacheFileName);
                    else {
                        $ret = $this->_realRender($args);
                        if(!Variable::IsEmpty($ret) && !$nocache)
                            FileInfo::WriteAll($cachePath.$cacheFileName, $ret, 0777);
                    }
                    
                    
                    break;
                case 'memory':
                
                    $cacheFileName = $this->_cacheFile($args);

                    $cacheData = Mem::$i->get('mc'.md5($cacheFileName));
                    if($cacheData && !$recache) {
                        $ret = $cacheData;
                        break;
                    }
                    
                    $ret = $this->_realRender($args);
                    
                    Mem::$i->add('mc'.md5($cacheFileName), $ret, false, $this->_livetime);
                    
                    break;
                
            }
            
            return $ret;
        }
        
        public function Render($args = array(), $recache = false, $nocache = false) {
            
            global $stats;
            //try { $stats[] = sysstats($stats, 'template start'.$this->_file); } catch( Exception $e) {}
            
            if($this->_livetime == 0) {
                $ret = $this->_realRender($args);
                
                //try { $stats[] = sysstats($stats, 'template end'.$this->_file.' reeal output '); } catch( Exception $e) {}
                return $ret;
            }
            
            $ret = $this->_createCache($args, $recache, $nocache);
            
            //try { $stats[] = sysstats($stats, 'template end'.$this->_file); } catch( Exception $e) {}
            return $ret;
        }
        
        public static function Create($layout, $file) {
            return new Template($layout, $file);
        }
        
        public function __get($prop) {
            switch(strtolower($prop)) {
                case 'layout':
                    return $this->_layout;
                case 'file':
                    return $this->_file;
                case 'name':
                    $file = pathinfo($this->_file);
                    return $file['filename'];
            }
        }
        
    }
    
?>
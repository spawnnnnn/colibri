<?php
    
    class Graphics2 {
        
        private $_img;
        private $_size;
        private $_type;
        private $_file;
        
        private $_history = array();
        
        public function __construct() {
            $this->_img = null;
            $this->_size = new Size(0, 0);
            $this->_type = 'unknown';
        }
        
        public function __destruct() {
            if(is_resource($this->_img))
                @imagedestroy($this->_img);
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case 'isvalid':
                    return !is_null($this->_img);
                case 'size':
                    return $this->_size;
                case 'type':
                    return $this->_type;
                case 'data':
                    return $this->_getImageData();
                case 'transparency':
                    return !is_null($this->_img) ? $this->_img->getImageAlphaChannel() : false;
                case 'name':
                    return $this->_file;
            }
        }
        
        public function __set($property, $value) {
            switch(strtolower($property)) {
                case 'type':
                    $this->_type = $value;
                    break;
            }
        }
        
        public function LoadFromData($data) {
            
            $this->_img = new Imagick();

            $this->_file = basename(Randomization::Character(20));
            $this->_img->readimageblob($data);
            $this->_size = new Size($this->_img->getImageWidth(), $this->_img->getImageHeight());
            $this->_history = array();
            $this->_safeAlpha();
        }
        
        public function LoadFromFile($file) {
            
            $this->_file = basename($file);
            $pp = explode('.', $file);
            $this->_type = strtolower($pp[count($pp) - 1]);
            
            $this->_img = new Imagick($file);
            
            $this->_size = new Size($this->_img->getImageWidth(), $this->_img->getImageHeight());
            $this->_history = array();
            $this->_safeAlpha();
        }
        
        public function LoadEmptyImage($size) {
            $this->_type = "unknown";
            $this->_img = new Imagick();
            $this->_img = $this->_img->newImage($size->width, $size->height, new ImagickPixel('white'));
            $this->_size = $size;
            $this->_history = array();
            $this->_safeAlpha();
        }
        
        public function Resize($size) {
            if($this->isValid) {
                
                $this->_img->resizeImage($size->width, $size->height, Imagick::FILTER_UNDEFINED, 1);
                $this->_size = $size;
                $this->_history[] = array('operation' => 'resize', 'postfix' => 'resized-'.$size->width.'x'.$size->height);
                
            }
        }
        
        public function Rotate($degree = 90) {
            $this->_img->rotateImage(new ImagickPixel('black'), $degree);
        }
        
        public function Crop($size, $start = null) {
            if($this->isValid) {
                if(is_null($start)) $start = new Point(0, 0);
                $this->_img->cropImage($size->width, $size->height, $start->x, $start->y);
                $this->size = $size;
                $this->_history[] = array('operation' => 'crop', 'postfix' => 'croped-'.$start->x.'x'.$start->y.'.'.$size->width.'x'.$size->height);
            }
        }

        public function ApplyFilter($filter, $arg1 = 0, $arg2 = 0, $arg3 = 0) {
            /*switch($filter) {  
                case IMG_FILTER_NEGATE:
                    $this->_history[] = array('operation' => 'filter', 'postfix' => 'negate');
                    return imagefilter($this->_img, $filter);
                case IMG_FILTER_GRAYSCALE:
                    $this->_history[] = array('operation' => 'filter', 'postfix' => 'grayscale');
                    return imagefilter($this->_img, $filter);
                case IMG_FILTER_BRIGHTNESS:
                    $this->_history[] = array('operation' => 'filter', 'postfix' => 'brightness-'.$arg1);
                    return imagefilter($this->_img, $filter, $arg1);
                case IMG_FILTER_CONTRAST:
                    $this->_history[] = array('operation' => 'filter', 'postfix' => 'contrast-'.$arg1);
                    return imagefilter($this->_img, $filter, $arg1);
                case IMG_FILTER_COLORIZE:
                    $this->_history[] = array('operation' => 'filter', 'postfix' => 'colorize-'.$arg1.'x'.$arg2.'x'.$arg3);
                    return imagefilter($this->_img, $filter, $arg1, $arg2, $arg3);
                case IMG_FILTER_EDGEDETECT:
                    $this->_history[] = array('operation' => 'filter', 'postfix' => 'edgedetect');
                    return imagefilter($this->_img, $filter);
                case IMG_FILTER_EMBOSS:
                    $this->_history[] = array('operation' => 'filter', 'postfix' => 'emboss');
                    return imagefilter($this->_img, $filter);
                case IMG_FILTER_GAUSSIAN_BLUR:
                    $this->_history[] = array('operation' => 'filter', 'postfix' => 'gausian-blur');
                    return imagefilter($this->_img, $filter);
                case IMG_FILTER_SELECTIVE_BLUR:
                    $this->_history[] = array('operation' => 'filter', 'postfix' => 'blur');
                    return imagefilter($this->_img, $filter);
                case IMG_FILTER_MEAN_REMOVAL:
                    $this->_history[] = array('operation' => 'filter', 'postfix' => 'mean-removal');
                    return imagefilter($this->_img, $filter);
                case IMG_FILTER_SMOOTH:
                    $this->_history[] = array('operation' => 'filter', 'postfix' => 'smooth-'.$arg1);
                    return imagefilter($this->_img, $filter, $arg1);
            }*/
            return false;
        }        
        
        public function Save($file, $format = false, $quality = false) {
            
            if($format)
                $this->_img->setImageFormat($format);
            
            if($quality)
                $this->_img->setImageCompressionQuality($quality);
            
            $this->_img->writeImage($file);
        }
        
        public function Cache() {
            global $core;
            $path = $this->_cacheFolder().md5($this->name).'.'.$this->_joinHistory().'.'.$this->_type;
            if(!$core->fs->FileExists($path))
                $this->Save($path);
            return $path;
        }
        
/* privates */
        
        private function _cacheFolder() {
            global $core;
            $path = $core->fs->mappath($core->sts->BLOB_CACHE_FOLDER)."/";
            return str_replace('//', '/', $path);
        }
        
        private function _safeAlpha() {
            // save alpha
            /*imagealphablending($this->_img, 1);
            imagesavealpha($this->_img, 1); */
        }
        
        private function _tryCreateTempFile() {
            $filename = tempnam("","");
            if(!$filename) $filename = $this->_cacheFolder().Randomization::Character(20);
            return $filename;
        }
        
        private function _getImageData() {
            return $this->_img->getImageBlob();
        }
        
        private function _joinHistory() {
            $ret = '';
            foreach($this->_history as $operation) {
                $ret .= '.'.$operation['postfix'];
            }
            return is_empty($ret) ? $ret : substr($ret, 1);
        }
        
        public static function Info($path) {
            list($width, $height, $type, $attr) = @getimagesize($path);
            $o = new Object();
            $o->size = new Size($width, $height);
            $o->type = $type;
            $o->attr = $attr;
            return $o;
        }
        
        public static function Create($data) {
            global $core;
            $g = new Graphics();
            
            if($data instanceOf Size)
                $g->LoadEmptyImage($data);
            else if(file_exists($core->fs->MapPath($data))) {
                $g->LoadFromFile($core->fs->MapPath($data));
            }
            else 
                $g->LoadFromData($data);
            
            return $g;
        }
        
    }
    
?>
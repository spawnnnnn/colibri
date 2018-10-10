<?php
    
    class Graphics {
        
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
                    return !is_null($this->_img) ? @imagecolortransparent($this->_img) : false;
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
            $this->_file = basename(Randomization::Mixed(20));
            $this->_img = @imagecreatefromstring($data);
            $this->_size = new Size(imagesx($this->_img), imagesy($this->_img));
            $this->_history = array();
            $this->_safeAlpha();
        }
        
        public function LoadFromFile($file) {
            $this->_file = basename($file);
            $pp = explode('.', $file);
            $this->_type = strtolower($pp[count($pp) - 1]);
            
            switch($this->_type) {
                case 'png':
                    $this->_img = imagecreatefrompng($file);
                    break;
                case 'gif':
                    $this->_img = imagecreatefromgif($file);
                    break;
                case 'jpg':
                case 'jpeg':
                    $this->_img = imagecreatefromjpeg($file);
                    break;
            }
            $this->_size = new Size(imagesx($this->_img), imagesy($this->_img));
            $this->_history = array();
            $this->_safeAlpha();
        }
        
        public function LoadEmptyImage($size) {
            $this->_type = "unknown";
            $this->_img = imagecreatetruecolor($size->width, $size->height);
            $this->_size = $size;
            $this->_history = array();
            $this->_safeAlpha();
        }
        
        public function Resize($size) {
            if($this->isValid) {
                // Spawn version
                //$newImage = @ImageCreateTrueColor($size->width, $size->height);
                //ImageColorTransparent($newImage, imagecolorallocate($newImage, 0, 0, 0)); 
                //ImageCopyResampled($newImage, $this->_img, 0, 0, 0, 0, $size->width, $size->height, $this->_size->width, $this->_size->height);
                //ImageDestroy($this->_img);
                
                // VipFanat version
                $newImage = imagecreatetruecolor($size->width, $size->height);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                ImageCopyResampled($newImage, $this->_img, 0, 0, 0, 0, $size->width, $size->height, $this->_size->width, $this->_size->height);
                ImageDestroy($this->_img);
                $this->_img = $newImage;
                $this->_size = $size;
                
                $this->_history[] = array('operation' => 'resize', 'postfix' => 'resized-'.$size->width.'x'.$size->height);
                
            }
        }
        
        public function Rotate($degree = 90) {
            $this->_img = imagerotate($this->_img, $degree, -1); 
            imagealphablending($this->_img, true); 
            imagesavealpha($this->_img, true);             
        }
        
        public function Crop($size, $start = null) {
            if($this->isValid) {
                if(is_null($start)) $start = new Point(0, 0);
                $newImage = ImageCreateTrueColor($size->width, $size->height);
                ImageCopyResampled($newImage, $this->_img, 0, 0, $start->x, $start->y,
                                   $size->width, $size->height, $size->width, $size->height);
                ImageDestroy($this->_img);
                $this->_img = $newImage;
                $this->size = $size;
                
                $this->_history[] = array('operation' => 'crop', 'postfix' => 'croped-'.$start->x.'x'.$start->y.'.'.$size->width.'x'.$size->height);
            }
        }

        public function ApplyFilter($filter, $arg1 = 0, $arg2 = 0, $arg3 = 0) {
            switch($filter) {
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
            }
            return false;
        }        
        
        public function Save($file) {
            switch($this->_type) {
                case 'png':
                    imagepng($this->_img, $file);
                    break;
                case 'gif':
                    imagegif($this->_img, $file);
                    break;
                case 'jpg':
                case 'jpeg':
                    imagejpeg($this->_img, $file);
                    break;
                default:
                    imagegd2($this->_img, $file);
                    break;
            }            
        }
        
/* privates */
        
        private function _safeAlpha() {
            // save alpha
            imagealphablending($this->_img, 1);
            imagesavealpha($this->_img, 1);
        }
        
        private function _getImageData() {
            $tempFile = tempnam(null, null);
            switch($this->_type) {
                case 'png':
                    imagepng($this->_img, $tempFile);
                    break;
                case 'gif':
                    imagegif($this->_img, $tempFile);
                    break;
                case 'jpg':
                case 'jpeg':
                    imagejpeg($this->_img, $tempFile);
                    break;
                default:
                    imagegd2($this->_img, $tempFile);
                    break;
            }            
            
            $c = file_get_contents($tempFile);
            unlink($tempFile);
            return $c;
        }
        
        private function _joinHistory() {
            $ret = '';
            foreach($this->_history as $operation) {
                $ret .= '.'.$operation['postfix'];
            }
            return is_empty($ret) ? $ret : substr($ret, 1);
        }
        
        public static function Info($path) {
            list($width, $height, $type, $attr) = getimagesize($path);
            $o = new ObjectEx();
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
            else if(FileInfo::Exists($data)) {
                $g->LoadFromFile($data);
            }
            else 
                $g->LoadFromData($data);
            
            return $g;
        }
        
    }
    
?>
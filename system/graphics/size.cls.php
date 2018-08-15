<?php
    
    class Size {

        public $width;
        public $height;

        public function __construct($width = 0, $height = 0) {
            $this->width = $width;
            $this->height = $height;
        }
        
        public function __get($nm) {
            switch($nm) {
                case "style":
                    return ($this->width != 0 ? "width:".intval($this->width)."px;" : "").($this->height != 0 ? "height:".intval($this->height)."px;" : "");
                case "attributes":
                    return ($this->width != 0 ? " width=\"".intval($this->width)."\"" : "").($this->height != 0 ? " height=\"".intval($this->height)."\"" : "");
                case "params":
                    return ($this->width != 0 ? "&w=".intval($this->width) : "").($this->height != 0 ? "&h=".intval($this->height) : "");
                case "isNull":
                    return ($this->width == 0 && $this->height == 0);
            }
            return null;
        }
        
        public function TransformTo($size) {
            
            $width = $size->width;
            $height = $size->height;
            
            $rheight = null; //
            $rwidth = null; //
            $originalwidth = $this->width;
            $originalheight = $this->height;
            
            if($width == 0 && $height == 0) {
                $originalwidth = $width;
                $originalheight = $height;
            }
            else if($width == 0) {
                $rheight = ($height <= $originalheight ? $height : $originalheight);
                $otnoshenie = $originalheight / $originalwidth;
                $rwidth = $rheight / $otnoshenie;
            }
            else if($height == 0) {
                $rwidth = ($width <= $originalwidth ? $width : $originalwidth);
                $otnoshenie = $originalwidth / $originalheight;
                $rheight = $rwidth / $otnoshenie;
            }
            else {
            if($originalwidth <= $width && $originalheight <= $height) {
                    $rwidth = $originalwidth;
                    $rheight = $originalheight;
                }
                else if($originalwidth / $width > $originalheight / $height) {
                    $rwidth = $width;
                    $rheight = $originalheight * ($width / $originalwidth);
                }
                else {
                    $rheight = $height;
                    $rwidth = $originalwidth * ($height / $originalheight);
                }
            }

            $originalwidth = $rwidth;
            $originalheight = $rheight;

            return new Size((int)$originalwidth, (int)$originalheight);
        
        }
        
        function TransformToFill($size) {
            $width = $size->width;
            $height = $size->height;
            
            $rheight = null; //
            $rwidth = null; //
            $originalwidth = $this->width;
            $originalheight = $this->height;
            if($width == 0 && $height == 0) { 
                $originalwidth = $width;
                $originalheight = $height;
            }
            else if($width == 0) {
                $rheight = ($height <= $originalheight ? $height : $originalheight);
                $otnoshenie = $originalheight / $originalwidth;
                $rwidth = $rheight / $otnoshenie;
            }
            else if($height == 0) {
                $rwidth = ($width <= $originalwidth ? $width : $originalwidth);
                $otnoshenie = $originalwidth / $originalheight;
                $rheight = $rwidth / $otnoshenie;
            }
            else {
            
                if($originalwidth <= $width && $originalheight <= $height) {
                    $rwidth = $originalwidth;
                    $rheight = $originalheight;
                }
                else if($originalwidth / $width > $originalheight / $height) {
                    $rheight = $height;
                    $rwidth = $originalwidth * ($height / $originalheight);
                }
                else {
                    $rwidth = $width;
                    $rheight = $originalheight * ($width / $originalwidth);
                }
            
            }

            $originalwidth = $rwidth;
            $originalheight = $rheight;

            return new Size((int)$originalwidth, (int)$originalheight);
        }
        
        
        public function Expand($w, $h) {
            $this->width += $w;
            $this->height += $h;
        }    
    }
    
?>
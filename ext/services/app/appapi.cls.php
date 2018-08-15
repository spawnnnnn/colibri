<?php
    
    Core::Using('Lib::Services', _PROJECT);
    
    class AdvApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('app'));
        }
        
        public function SearchFor($request = array()) {

            $opts = array(
            );

            $opts = Variable::Extend($opts, $request);
            $res = parent::_request('AdvAjaxHandler.Process', $opts);
            
            if(!$res)
                return false;
                
            return $res;
            
        }
        
        public function SetDevice($request = array()) {

            $opts = array(
            );

            $opts = Variable::Extend($opts, $request);
            $res = parent::_request('AdvAjaxHandler.SetDevice', $opts);
            
            if(!$res)
                return false;
                
            return $res;
            
        }
        
    }
    
    class AppApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('app'));
        }
        
        public function GetLandingVariants($cid, $post, $rejected) {

            $res = parent::_request('AppAjaxHandler.GetLandingVariants', [
                'cid' => $cid,
                'post' => $post,
                'rejected' => $rejected,
            ]);
            
            if(!$res)
                return false;
                
            return $res;
            
        }
        
    }
    
    class GeoApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('app'));
        }
        
        public function Cities($request = array()) {

            $opts = array(
            );

            $opts = Variable::Extend($opts, $request);
            $res = parent::_request('GeoAjaxHandler.Cities', $opts);
            
            if(!$res)
                return false;
                
            return $res;
            
        }
        
    }
    
?>
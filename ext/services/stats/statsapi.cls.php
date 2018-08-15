<?php
    
    /* Описания параметров */
    
    Core::Using('Lib::Services', _PROJECT);
    
    class StatsApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('stats'));
        }
        
        public function SaveStats($service, $cid, $date, $request, $results) {
            
            $res = parent::_request('StatsAjaxHandler.SaveStats', array(
                'service' => $service,
                'cid' => $cid,
                'date' => Date::ToDBString($date), 
                'request' => $request,
                'results' => $results,
                'async' => true,
            ));
            
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function SaveCurrency($id, $cid, $date, $rate) {
            
            $res = parent::_request('StatsAjaxHandler.SaveCurrency', array(
                'id' => $id,
                'cid' => $cid,
                'date' => $date, 
                'rate' => $rate,
            ));
            
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function GetRequestData($service, $cid) {
            
            $res = parent::_request('StatsAjaxHandler.GetRequestData', array(
                'service' => $service,
                'cid' => $cid,
            ));
            
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function GetLatestCalcs($service) {
            
            $res = parent::_request('StatsAjaxHandler.GetLatestData', array(
                'service' => $service,
            ));
            
            if(!$res)
                return false;
            
            return $res;
            
        }
        
    }
    
?>
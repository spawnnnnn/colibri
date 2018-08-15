<?php
    
    class Geo {
        
        public static function CountryByIP($ip) {
            
            if(is_numeric($ip))
                $ip = long2ip($ip);
            $output = new stdClass();
            /*$gi = geoip_open(_GEODB, GEOIP_STANDARD)*/;
            $output->code = geoip_country_code_by_name($ip);
            $output->name = geoip_country_name_by_name($ip);
            /*geoip_close($gi);*/
            return $output;
        }
        
        public static function RecordByIP($ip) {

            if(is_numeric($ip))
                $ip = long2ip($ip);
            
            /*$gi = geoip_open(_GEODBCITY, GEOIP_STANDARD);*/
            $output = geoip_record_by_name($ip);
            /*geoip_close($gi);*/
            return $output;
        }
        
        public static function TimezoneByIP($ip) {
            /*$gi = geoip_open(_GEODBCITY, GEOIP_STANDARD);*/
            $region = geoip_record_by_name($ip);
            $output = geoip_time_zone_by_country_and_region($region['country_code'], $region['region']);
            /*geoip_close($gi);*/
            return $output;
        }
        
    }    
    
?>

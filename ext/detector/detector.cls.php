<?
    /*
	    Detects the text language using Google Translate API
    */
    class Detector {

        public static function Detect($text) {
            
            $data = file_get_contents("http://ajax.googleapis.com/ajax/services/language/detect?v=1.0&q=".urlencode($text));
            
            $data = json_decode ( $data );
            
            if ( $data->responseStatus == 200 ) {
               return $data->responseData->language;
            }
            else {
               return false;
            }
     
        }
        
	    
    }
?>
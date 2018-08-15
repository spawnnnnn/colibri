<?
    /**
    * 
    * Ajax class definition
    * 
    * every ajax handler must be a singleton class
    * 
    */
    
    abstract class AjaxHandler {
        
        public static $i = null;
        
    }

    class Ajax {
        
        private $_allowed;
        
        private $_type;
        private $_object;
        private $_method;
        private $_data;
        
        const IncorrectCommandObject = 1;
        const UnknownMethodInObject = 2;
        
        const JSON = 1;
        const XML = 2;
        
        public function __construct($allowed, $type = Ajax::JSON) {
            $this->_allowed = $allowed;
            $this->_type = $type;
        }
        
        private function _responseWithError($message, $code = -1) {
            echo '<response>
                <error>
                    <code>'.$code.'</code>
                    <message>'.$message.'</message>
                </error>
            </response>';
        }
        
        public function Process($cmd, $data) {

            // cmd = object.method
            $cmds = explode(".", $cmd);
            $this->_object = $cmds[0];
            $this->_method = $cmds[1];
            $this->_data = $data;
            
            /*if(!in_array($this->_object, $this->_allowed)) {
                $this->_responseWithError('Unauthorized command object', Ajax::IncorrectCommandObject);
                return false;
            }*/
            
            return true;

        }
        
        public function Run() {
            
            $class = $this->_object;
            $method = $this->_method;
            $data = $this->_data;
            
            if(!ClassKit::HasMethod($class, $method)) {
                $this->_responseWithError('Unknown method in object '.$class, Ajax::UnknownMethodInObject);
                return false;
            }
            
            $obj = CodeModel::CreateSingletonObject($class);
            $ret = ClassKit::InvokeMethod($obj, $method, array($data));
            
            if($this->_type == Ajax::JSON)
                return json_encode($ret);
                //return Json::Serialize($ret);
            else if($this->_type == Ajax::XML)
                return Xml::Serialize($ret, 'result');
            
        }
        
            
    }   

?>
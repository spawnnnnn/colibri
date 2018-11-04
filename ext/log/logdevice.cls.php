<?
    Core::Using('Ext::Json');
    
    class LogDevice extends XMLNode {
        
        private $_device;
        private $_output;
        
        public function __construct($device, $output = false) {
            $this->_device = _CACHE.'log/'.$device;
            $this->_output = $output;
        }        
        
        public function WriteLine() {
            $args = func_get_args();
            $args[] = "\n";
            $args = Date::ToDbString(time())."\t".implode("\t", $args);
            
            if(!FileInfo::Exists($this->_device))
                FileInfo::Create($this->_device, true, 0777);
            
            $fi = new FileInfo($this->_device);
            if($fi->size > 1048576) {
                FileInfo::Move($this->_device, $this->_device.'.'.microtime(true));
                FileInfo::Create($this->_device, true, 0777);
            }
                
            file_put_contents($this->_device, $args, FILE_APPEND);
            if($this->_output)
                out($args);
            
        }
        
    }
    
    class MemoryLog {
        
        private $_device;
        
        public function __construct() {
            $this->_device = array();
        }        
        
        public function WriteLine() {
            $args = func_get_args();
            $args[] = "\n";
            $args = Date::ToDbString(time())."\t".implode("\t", $args);
            
            $this->_device[] = $args;
        }
        
        public function Content() {
            return $this->_device;
        }
        
    }
        
    /*class DBLog {
        
        static $dataPoint;
        static $table;
        
        public static function Create($datapoint, $table) {
            DBLog::$dataPoint = new DataPoint($datapoint);
            DBLog::$table = $table;
        }
        
        public static function Write($operation, $data) {
            $res = DBLog::$dataPoint->Insert(DBLog::$table, array(
                'date' => microtime(true),
                'user' => Security::$i && Security::$i->current ? Security::$i->current->name : 'log',
                'operation' => $operation,
                'data' => Json::EncodeUTF8($data),
            ));
        }
        
        
    }*/
    
?>

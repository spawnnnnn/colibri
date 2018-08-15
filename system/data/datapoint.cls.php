<?php
    
    Core::Using("System::Reflection");
    Core::Using('System::Data::SqlClient');
    
    class DataPoint {
        
        private static $_connectionPool = array();
        
        private $_dpinfo;
        private $_connection;
        
        public static function GetList() {
            $list = array();
            $xml = Config::Load(_DATAPOINTS);
            $points = $xml->children;
            foreach($points as $point) {
                $list[] = (object)array(
                    'name' => $point->name,
                    'using' => $point->attributes->using->value,
                    'connectioninfo' => (object)array(
                        'host' => $point->Item('connectioninfo')->attributes->host->value,
                        'user' => $point->Item('connectioninfo')->attributes->user->value,
                        'password' => $point->Item('connectioninfo')->attributes->password->value,
                        'database' => $point->Item('connectioninfo')->attributes->database->value
                    ),
                    'connection' => $point->Item('connection')->attributes->entry->value,
                    'command' => $point->Item('command')->attributes->entry->value,
                    'reader' => $point->Item('reader')->attributes->entry->value,
                    'querybuilder' => $point->Item('querybuilder')->attributes->entry->value
                );
            }   
            return $list;         
        }       
        
        public function __construct($name, $dpinfo = false) {
            if(array_key_exists($name, DataPoint::$_connectionPool)) {
                $this->_dpinfo = DataPoint::$_connectionPool[$name]['dpinfo'];
                $this->_connection = DataPoint::$_connectionPool[$name]['connection'];
                if ( ! $this->_connection->isAlive) {
                    $this->_connection->Reopen();
                }
            } 
            else {
                if(!$dpinfo) {
                    $xml = Config::Load(_DATAPOINTS);
                    
                    if(!$xml->Item($name))
                        throw new BaseException('Datapoint does not exists');
                    
                    $dtp = $xml->Item($name);
                    $this->_dpinfo = array(
                        'name' => $dtp->name,
                        'using' => $dtp->attributes->using->value,
                        'connectioninfo' => array(
                            'host' => $dtp->Item('connectioninfo')->attributes->host->value,
                            'user' => $dtp->Item('connectioninfo')->attributes->user->value,
                            'password' => $dtp->Item('connectioninfo')->attributes->password->value,
                            'database' => $dtp->Item('connectioninfo')->attributes->database->value
                        ),
                        'connection' => $dtp->Item('connection')->attributes->entry->value,
                        'command' => $dtp->Item('command')->attributes->entry->value,
                        'reader' => $dtp->Item('reader')->attributes->entry->value,
                        'querybuilder' => $dtp->Item('querybuilder')->attributes->entry->value
                    );
                }
                else {
                    $this->_dpinfo = $dpinfo;
                }
                
                if(!empty($this->_dpinfo['using'])) 
                    Core::Using($this->_dpinfo['using']);
                
                $ci = CodeModel::CreateObject('ConnectionInfo', $this->_dpinfo['connectioninfo']['host'], $this->_dpinfo['connectioninfo']['user'], $this->_dpinfo['connectioninfo']['password'], $this->_dpinfo['connectioninfo']['database']);
                $this->_connection = CodeModel::CreateObject($this->_dpinfo['connection'], $ci);
                $this->_connection->Open();
                
                DataPoint::$_connectionPool[$name] = array('dpinfo' => $this->_dpinfo, 'connection' => $this->_connection);
                
            }
            
            
            
        }
        
        public function __get($property) {
            switch($property) {
                case 'name':
                    return $this->_dpinfo['name'];
                case 'connection':
                    return $this->_connection;
                default: 
                    $cmd = CodeModel::CreateObject($this->_dpinfo['command'], 'select * from '.$property, $this->_connection);
                    // $reader = $cmd->ExecuteReader();
                    return $cmd->ExecuteReader();
                    // return new DataTable($reader);
            }
        }
        
        public function Query($query, $page = -1, $pagesize = 10) {
            $cmd = CodeModel::CreateObject($this->_dpinfo['command'], $query, $this->_connection);
            if($page > 0) {
                $cmd->page = $page;
                $cmd->pagesize = $pagesize;
            }
            // $reader = $cmd->ExecuteReader();
            return $cmd->ExecuteReader();
            //return new DataTable($reader);
        }
        
        public function QueryBigData($query, $page = -1, $pagesize = 10) {
            $cmd = CodeModel::CreateObject($this->_dpinfo['command'], $query, $this->_connection);
            if($page > 0) {
                $cmd->page = $page;
                $cmd->pagesize = $pagesize;
            }
            // $reader = $cmd->ExecuteReader();
            return $cmd->ExecuteReader(false);
            //return new DataTable($reader);
        }
        
        public function QueryNonInfo($query, $page = -1, $pagesize = 10) {
            $cmd = CodeModel::CreateObject($this->_dpinfo['command'], $query, $this->_connection);
            if($page > 0) {
                $cmd->page = $page;
                $cmd->pagesize = $pagesize;
            }
            // $reader = $cmd->ExecuteReader();
            return $cmd->ExecuteNonQuery();
            //return new DataTable($reader);
        }
        
        public function Insert($table, $row = array(), $returning = '' /* used only in postgres*/) {
            $cmd = CodeModel::CreateObject(
                $this->_dpinfo['command'], 
                CodeModel::CreateObject(
                    $this->_dpinfo['querybuilder'])->CreateInsert(
                        $table, $row
                    ), 
                $this->_connection
            );
            return $cmd->ExecuteNonQuery($returning);
        }

        public function InsertOrUpdate($table, $row = array(), $exceptFields = array(), $returning = '' /* used only in postgres*/) {
            $cmd = CodeModel::CreateObject(
                $this->_dpinfo['command'], 
                CodeModel::CreateObject(
                    $this->_dpinfo['querybuilder'])->CreateInsertOrUpdate(
                        $table, $row, $exceptFields
                    ),              
                $this->_connection
            );
            return $cmd->ExecuteNonQuery($returning);
        }

        public function InsertBatch($table, $rows = array() /* array of rows */) {
            
            $cmd = CodeModel::CreateObject(
                $this->_dpinfo['command'], 
                CodeModel::CreateObject(      
                    $this->_dpinfo['querybuilder'])->CreateBatchInsert(
                        $table, $rows
                    ), 
                $this->_connection
            );
            return $cmd->ExecuteNonQuery();
        }
        
        public function Update($table, $row, $condition) {
            
            $cmd = CodeModel::CreateObject(
                $this->_dpinfo['command'], 
                CodeModel::CreateObject(      
                    $this->_dpinfo['querybuilder'])->CreateUpdate(
                        $table, $condition, $row
                    ),
                $this->_connection);
                
            return $cmd->ExecuteNonQuery();
            
        }
        
        public function Delete($table, $condition = '') {
            
            $cmd = CodeModel::CreateObject(
                $this->_dpinfo['command'], 
                CodeModel::CreateObject(      
                    $this->_dpinfo['querybuilder'])->CreateDelete(
                        $table, $condition
                    ),
                $this->_connection);
                
            return $cmd->ExecuteNonQuery();
        }
        
        public function Tables() {
            $cmd = CodeModel::CreateObject(
                $this->_dpinfo['command'], 
                CodeModel::CreateObject(      
                    $this->_dpinfo['querybuilder'])->CreateShowTables(),
                $this->_connection
            );
            $reader = $cmd->ExecuteReader();
            if($reader->count == 0)
                return null;
                
            return $reader; // new DataTable($reader); 
        }
        
        public function TableExists($table) {
            $table = new DataTable($this->Tables());
            $dtrlist = $table->CacheAll();
            return $dtrlist->Find($dtrlist->Item(0)->GetName(0), $table)->count > 0; 
        }
    
    }
    

?>

<?php
    
    Core::Using("System::Reflection");
    Core::Using('System::Data::SqlClient');
    
    class DataStorage {
        
        private static $_connectionPool = array();
        
        private $_dpinfo;
        private $_connection;
        
        public function __construct($name) {
            if(array_key_exists($name, DataStorage::$_connectionPool)) {
                $this->_dpinfo = DataStorage::$_connectionPool[$name]['dpinfo'];
                $this->_connection = DataStorage::$_connectionPool[$name]['connection'];
                if ( ! $this->_connection->isAlive) {
                    $this->_connection->Reopen();
                }
            } 
            else {
                $xml = XMLNode::LoadNode(_DATASTORAGES);
                if(!$xml->Item($name))
                    throw new BaseException('Datastorage does not exists');
                
                $dtp = $xml->Item($name);
                $this->_dpinfo = array(
                    'using' => $dtp->attributes->using->value,
                    'connectioninfo' => array(
                        'host' => $dtp->Item('connectioninfo')->attributes->host->value,
                        'user' => $dtp->Item('connectioninfo')->attributes->user->value,
                        'password' => $dtp->Item('connectioninfo')->attributes->password->value,
                        'database' => $dtp->Item('connectioninfo')->attributes->database->value
                    ),
                    'connection' => $dtp->Item('connection')->attributes->entry->value,
                    'command' => $dtp->Item('command')->attributes->entry->value,
                    'reader' => $dtp->Item('reader')->attributes->entry->value
                );
                
                
                if(!empty($this->_dpinfo['using'])) 
                    Core::Using($this->_dpinfo['using']);
                
                $ci = CodeModel::CreateObject('StorageConnectionInfo', $this->_dpinfo['connectioninfo']['host'], $this->_dpinfo['connectioninfo']['user'], $this->_dpinfo['connectioninfo']['password'], $this->_dpinfo['connectioninfo']['database']);
                $this->_connection = CodeModel::CreateObject($this->_dpinfo['connection'], $ci);
                $this->_connection->Open();
                
                DataStorage::$_connectionPool[$name] = array('dpinfo' => $this->_dpinfo, 'connection' => $this->_connection);
                
            }
            
            
            
        }
        
        public function __get($property) {
            switch($property) {
                case 'connection':
                    return $this->_connection;
                default: 
                    return false;
            }
        }
        
        public function Query($table, $condition = array(), $order = array(), $page = -1, $pagesize = 10) {
            $cmd = CodeModel::CreateObject($this->_dpinfo['command'], StorageCommand::Select, array('collection' => $table, 'condition' => $condition, 'order' => $order), $this->_connection);
            if($page > 0) {
                $cmd->page = $page;
                $cmd->pagesize = $pagesize;
            }
            return $cmd->ExecuteReader();
        }
        
        public function Insert($table, $data = array()) {
            $cmd = CodeModel::CreateObject($this->_dpinfo['command'], StorageCommand::Insert, array('collection' => $table, 'data' => $data), $this->_connection);
            return $cmd->ExecuteNonQuery();
        }

        public function InsertBatch($table, $data = array()) {
            $cmd = CodeModel::CreateObject($this->_dpinfo['command'], StorageCommand::InsertBatch, array('collection' => $table, 'data' => $data), $this->_connection);
            return $cmd->ExecuteNonQuery();
        }
        
        public function Update($table, $data, $condition) {
            $cmd = CodeModel::CreateObject($this->_dpinfo['command'], StorageCommand::Update, array('collection' => $table, 'data' => $data,'condition' => $condition), $this->_connection);
            return $cmd->ExecuteNonQuery();
        }
        
        public function Delete($table, $condition) {
            $cmd = CodeModel::CreateObject($this->_dpinfo['command'], StorageCommand::Delete, array('collection' => $table, 'condition' => $condition), $this->_connection);
            return $cmd->ExecuteNonQuery();
        }
        
        public function Tables() {
            $cmd = CodeModel::CreateObject($this->_dpinfo['command'], StorageCommand::ListCollections, array('condition' => $condition), $this->_connection);
            return $cmd->ExecuteReader();
        }
        
        public function TableExists($table) {
            $tables = $this->Tables();
            return in_array($table, $tables);
        }
        
        // специальные функции
        public function MapReduce($table, $mapFunctionCode, $reduceFunctionCode, $condition = array(), $outputInformation = array()) {
            $cmd = CodeModel::CreateObject($this->_dpinfo['command'], StorageCommand::MapReduce, array(
                'collection' => $table, 
                'map' => $mapFunctionCode,
                'reduce' => $reduceFunctionCode,
                'query' => $condition,
                'out' => $outputInformation
            ), $this->_connection);
            return $cmd->ExecuteNonQuery();
        }
    
    }
    

?>

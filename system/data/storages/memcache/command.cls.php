<?php
    
    final class MemcacheNonQueryInfo extends Object {
        
        public function __construct($type, $insertid, $affected) {
            parent::__construct();
            $this->type = $type;
            $this->insertid = $insertid;
            $this->affected = $affected;
        }
        
    }
    
    final class MemcacheStorageCommand extends StorageCommand {
        
        public function ExecuteReader() {
            
            if(isset($result->resultType))  {
                if($result->resultType == 'collections')
                    return $this->connection->database->getCollectionNames();
            }
            
            $queryData = $this->Prepare();
            
            $collection = $this->connection->database->{$queryData->collection};
            if(isset($queryData->condition))
                $results = $collection->find($queryData->condition);
            else
                $results = $collection->find();
            
            if(isset($queryData->skip)) {
                $results->skip($queryData->skip)->limit($queryData->limit);
            }

            return new MemcacheDataReader($results);
        }
        
        public function ExecuteNonQuery($returning = '') {
            
            $queryData = $this->Prepare();
            
            $collection = $this->connection->database->{$queryData->collection};
            switch($this->_commandType) {
                case StorageCommand::Insert: {
                    $results = $collection->insert($queryData->data, Variable::Extend($queryData->options, array('w' => 1)));
                    return new MemcacheNonQueryInfo($this->type, $queryData->data['_id'], $results['n']);
                }   
                case StorageCommand::InsertBatch: {
                    $results = $collection->batchInsert($queryData->data, Variable::Extend($queryData->options, array('w' => 1)));
                    $ids = array();
                    foreach($queryData->data as $inserted) {
                        $ids[] = $inserted['_id'];
                    }
                    return new MemcacheNonQueryInfo($this->type, $ids, count($ids));
                }   
                case StorageCommand::Update: {
                    $results = $collection->update($queryData->condition, $queryData->data, Variable::Extend($queryData->options, array('w' => 1)) );
                    return new MemcacheNonQueryInfo($this->type, -1, $results['n']);
                }
                case StorageCommand::Delete: {
                    $results = $collection->remove($queryData->condition, $queryData->options);
                    return new MemcacheNonQueryInfo($this->type, -1, $results['n']);
                }
            }
                            
            return false;
        }
        
        public function Prepare() {
            
            $result = (object)array();
            
            switch($this->_commandType) {
                case StorageCommand::Select: {
                    
                    $result->collection = isset($this->_commandData['collection']) ? $this->_commandData['collection'] : false;
 
                    $result->condition = isset($this->_commandData['condition']) ? $this->_commandData['condition'] : array();
                    $result->order = isset($this->_commandData['order']) ? $this->_commandData['order'] : array();
 
                    if($this->_page > 0) {
                        $result->skip = ($this->_page - 1)*$this->_pagesize;
                        $result->limit = $this->_pagesize;
                    }
                    
                    if(!$result->collection)
                        throw new BaseException('collection must be profided in query data');

                    break;
                }
                case StorageCommand::Insert: {
                    
                    $result->collection = isset($this->_commandData['collection']) ? $this->_commandData['collection'] : false;
                    $result->options = isset($this->_commandType['options']) ? $this->_commandType['options'] : array();
                    $result->data = isset($this->_commandData['data']) ? $this->_commandData['data'] : false;
                    
                    if(!$result->collection)
                        throw new BaseException('collection must be profided in query data');

                    if(!$result->data)
                        throw new BaseException('you must provide data to insert');

                    break;
                }
                case StorageCommand::InsertBatch: {
                    
                    $result->collection = isset($this->_commandData['collection']) ? $this->_commandData['collection'] : false;
                    $result->options = isset($this->_commandType['options']) ? $this->_commandType['options'] : array();
                    $result->data = isset($this->_commandData['data']) ? $this->_commandData['data'] : false;
                    
                    if(!$result->collection)
                        throw new BaseException('collection must be profided in query data');

                    if(!$result->data)
                        throw new BaseException('you must provide data to insert');

                    if(!is_array($result->data))
                        throw new BaseException('your data must be an array');
                        
                    break;
                }
                case StorageCommand::Update: {
                    $result->collection = isset($this->_commandData['collection']) ? $this->_commandData['collection'] : false;
                    $result->condition = isset($this->_commandData['condition']) ? $this->_commandData['condition'] : array();
                    $result->options = isset($this->_commandType['options']) ? $this->_commandType['options'] : array();
                    $result->data = isset($this->_commandData['data']) ? $this->_commandData['data'] : false;
                    
                    if(!$result->collection)
                        throw new BaseException('collection must be profided in query data');

                    if(!$result->data)
                        throw new BaseException('you must provide data to insert');

                    break;
                }
                case StorageCommand::Delete: {
                    $result->collection = isset($this->_commandData['collection']) ? $this->_commandData['collection'] : false;
                    $result->condition = isset($this->_commandData['condition']) ? $this->_commandData['condition'] : array();
                    $result->options = isset($this->_commandType['options']) ? $this->_commandType['options'] : array();
                    
                    if(!$result->collection)
                        throw new BaseException('collection must be profided in query data');

                    break;
                }
                
                case StorageCommand::ListCollections: {
                    $result->resultType = 'collections';
                    break;
                }
            }
            
            
            return $result;
            
            
        }        
    }
    

?>

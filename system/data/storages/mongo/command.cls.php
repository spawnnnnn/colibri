<?php
    
    final class MongoNonQueryInfo extends Object {
        
        public function __construct($type, $insertid, $affected) {
            parent::__construct();
            $this->type = $type;
            $this->insertid = $insertid;
            $this->affected = $affected;
        }
        
    }
    
    final class MongoStorageCommand extends StorageCommand {
        
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
                
            if(isset($queryData->order))
                $results->sort($queryData->order);
            
            if(isset($queryData->skip)) {
                $results->skip($queryData->skip)->limit($queryData->limit);
            }

            return new MongoDataReader($results);
        }
        
        public function ExecuteNonQuery($returning = '') {
            
            $queryData = $this->Prepare();
            
            $collection = $this->connection->database->{$queryData->collection};
            switch($this->_commandType) {
                case StorageCommand::Insert: {
                    $results = $collection->insert($queryData->data, Variable::Extend($queryData->options, array('w' => 1)));
                    return new MongoNonQueryInfo($this->type, isset($queryData->data['_id']) ? $queryData->data['_id'] : false, isset($queryData->data['_id']) ? 1 : 0);
                }   
                case StorageCommand::InsertBatch: {
                    $results = $collection->batchInsert($queryData->data, Variable::Extend($queryData->options, array('w' => 1)));
                    $ids = array();
                    foreach($queryData->data as $inserted) {
                        if(isset($inserted['_id']))
                            $ids[] = $inserted['_id'];
                    }
                    return new MongoNonQueryInfo($this->type, $ids, count($ids));
                }   
                case StorageCommand::Update: {
                    $results = $collection->update($queryData->condition, array('$set' => $queryData->data), Variable::Extend($queryData->options, array('w' => 1)) );
                    return new MongoNonQueryInfo($this->type, -1, $results['n']);
                }
                case StorageCommand::Delete: {
                    $results = $collection->remove($queryData->condition, $queryData->options);
                    return new MongoNonQueryInfo($this->type, -1, $results['n']);
                }
                case StorageCommand::MapReduce: {
                    
                    $results = $this->connection->database->command(array(
                        'mapreduce' => $queryData->collection,
                        'map' => new MongoCode($queryData->mapFunction),
                        'reduce' => new MongoCode($queryData->reduceFunction),
                        'query' => $queryData->query,
                        'out' => $queryData->out,
                    ));
                    
                    // тут вопрос, что возвращает 
                    return new MongoNonQueryInfo($this->type, -1, $results['counts']['output']);
                }
            }
                            
            return false;
        }
        
        public function Prepare() {
            
            $result = (object)array();
            
            switch($this->_commandType) {
                case StorageCommand::Select: {
                    
                    $result->collection = isset($this->_commandData['collection']) ? $this->_commandData['collection'] : false;
 
                    if(isset($this->_commandData['condition']) && is_array($this->_commandData['condition']) && count($this->_commandData['condition']) > 0)
                        $result->condition = $this->_commandData['condition'];
                        
                    if(isset($this->_commandData['order']) && is_array($this->_commandData['order']) && count($this->_commandData['order']) > 0)
                        $result->order = $this->_commandData['order'];
 
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

                case StorageCommand::MapReduce: {
                    $result->collection = isset($this->_commandData['collection']) ? $this->_commandData['collection'] : false;
                    $result->query = isset($this->_commandData['query']) ? $this->_commandData['query'] : array();
                    $result->mapFunction = isset($this->_commandData['map']) ? $this->_commandData['map'] : array();
                    $result->reduceFunction = isset($this->_commandData['reduce']) ? $this->_commandData['reduce'] : array();
                    $result->out = isset($this->_commandData['out']) ? $this->_commandData['out'] : array();
                    $result->options = isset($this->_commandType['options']) ? $this->_commandType['options'] : array();
                    
                    if(!$result->collection)
                        throw new BaseException('collection must be profided in query data');

                    if(!$result->out)
                        throw new BaseException('output must be specified');
                        
                    if(!$result->mapFunction)
                        throw new BaseException('map function must be specified');
                        
                    if(!$result->reduceFunction)
                        throw new BaseException('reduce function must be specified');
                        
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

<?php
    
    Core::Using('System');
    Core::Using('System::Data::SqlClient');
    
    final class MySqlNonQueryInfo extends ObjectEx {
        
        public function __construct($type, $insertid, $affected, $error, $query) {
            parent::__construct();
            $this->type = $type;
            $this->insertid = $insertid;
            $this->affected = $affected;
            $this->error = $error;
            $this->query = $query;
        }
        
    }
    
    final class MySqlCommand extends SqlCommand {
        
        public function ExecuteReader($info = true) {     

            // выбираем базу данныx, с которой работает данный линк
            mysqli_select_db($this->_connection->resource, $this->_connection->database);
            $affected = null;
            if($this->page > 0 && $info) {
                $ares = mysqli_query($this->_connection->resource, 'select count(*) as affected from ('.$this->query.') tbl');
                if(mysqli_num_rows($ares) > 0)
                    $affected = mysqli_fetch_object($ares)->affected;
            }
                       
            $preparedQuery = $this->PrepareQueryString();
            $res = mysqli_query($this->connection->resource, $preparedQuery);
            if(!($res instanceOf mysqli_result)) {
                throw new BaseException(mysqli_error($this->_connection->resource), mysqli_errno($this->_connection->resource));
            }
                
            return new MySqlDataReader($res, $affected, $preparedQuery);
        }
        
        public function ExecuteNonQuery() {
            mysqli_select_db($this->_connection->resource, $this->_connection->database);
            mysqli_query($this->_connection->resource, $this->query);    
            return new MySqlNonQueryInfo($this->type, mysqli_insert_id($this->connection->resource), mysqli_affected_rows($this->connection->resource), mysqli_error($this->connection->resource), $this->query);
        }
        
        public function PrepareQueryString() {
            $query = $this->query;
            if($this->_page > 0)
                if(strstr($query, "limit") === false)
                    $query .= ' limit '.(($this->_page-1)*$this->_pagesize).', '.$this->_pagesize;
            return $query;
        }
        
    }
  
?>

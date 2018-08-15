<?php
    
    Core::Using('System');
    Core::Using('System::Data::SqlClient');
    
    final class PgSqlNonQueryInfo extends Object {
        
        public function __construct($type, $insertid, $affected) {
            parent::__construct();
            $this->type = $type;
            $this->insertid = $insertid;
            $this->affected = $affected;
        }
        
    }
    
    final class PgSqlCommand extends SqlCommand {
        
        public function ExecuteReader($info = true) {
            $affected = null;
            if($this->page > 0 && $info) {
                $ares = pg_query($this->connection->resource, 'select count(*) as affected from ('.$this->query.') tbl');
                if(pg_num_rows($ares) > 0) {
                    $affected = pg_fetch_object($ares)->affected;
                }
            }
            
            /*global $stats;
            $stats[] = sysstats($stats, $this->PrepareQueryString());*/
            $res = pg_query($this->connection->resource, $this->PrepareQueryString());    
            //$stats[] = sysstats($stats, $this->PrepareQueryString());
                
            return new PgSqlDataReader($res, $affected);
        }
        
        public function ExecuteNonQuery($returning = '') {
            global $stats;
            //$stats[] = sysstats($stats);
            $r = pg_query($this->connection->resource, $this->query.($returning != '' ? ' returning '.$returning : ''));
            //$stats[] = sysstats($stats);
            $insertid = -1;
            if($returning != '') {
                $ro = pg_fetch_object($r);
                $insertid = $ro->$returning;
            }
            return new PgSqlNonQueryInfo($this->type, $insertid, pg_affected_rows($r));
        }
        
        public function PrepareQueryString() {
            $query = $this->query;
            if($this->_page > 0)
                if(strstr($query, "limit") === false) {
                    if($this->_page <= 1)
                        return $query . " limit " . $this->_pagesize;
                    else
                        return $query . " limit " . $this->_pagesize . " offset " . (($this->_page - 1) * $this->_pagesize);
                }
            return $query;
        }        
    }
  
?>
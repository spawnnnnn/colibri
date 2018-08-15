<?php

    class PgSqlQueryBuilder extends SqlQueryBuilder {
        
        public function CreateInsert($table, $data, $returning = '') {

            $keys = array_keys($data);
            $fields = '("'.implode('", "', $keys).'")';
            
            $vals = array_values($data);
            // escape strings
            for ($i=0; $i<count($vals);$i++) { 
                if(is_string($vals[$i]))
                    $vals[$i] = pg_escape_string(str_replace('\'', '\\\'', $vals[$i])); 
                else if(is_null($vals[$i]))
                    $vals[$i] = 'null';
            }
            $values = "('".implode("', '", $vals)."')";
            $values = str_replace('\'null\'', 'null', $values);

            // file_put_contents(_CACHE.'log/'.str_replace('cache_', '', end(explode('.', $table))), "insert into ".$table.$fields.' values'.$values.(!empty($returning) ? ' returning "'.$returning.'"' : ''));
            
            return "insert into ".$table.$fields.' values'.$values.(!empty($returning) ? ' returning "'.$returning.'"' : '');
        }
        
        public function CreateInsertOrUpdate($table, $data, $exceptFields = array(), $returning = '') {
            $keys = array_keys($data);
            $fields = '('.join(", ", $keys).')';
            
            $vals = array_values($data);
            $values = "('".join("', '", $vals)."')";

            $updateStatement = '';
            foreach($data as $k => $v)
                if(!in_array($k, $exceptFields)) 
                    $updateStatement .= ','.$k.'=\''.addslashes($v).'\'';

            return "insert into ".$table.$fields.' values '.$values.' ON CONFLICT ON CONSTRAINT DO UPDATE '.substr($updateStatement, 1);
        }
        
        public function CreateBatchInsert($table, $data) {
            
            $keys = array_keys(reset($data));
            $fields = '("'.implode('", "', $keys).'")';
            
            $values = '';
            foreach($data as $row) {
                $vals = array_values($row); 
                
                for ($i=0; $i<count($vals);$i++) { 
                    if(is_string($vals[$i]))
                        $vals[$i] = pg_escape_string(str_replace('\'', '\\\'', $vals[$i])); 
                    else if(is_null($vals[$i]))
                        $vals[$i] = 'null';
                }
                $values .= ", ('".implode("', '", $vals)."')";
                
            }
            $values = str_replace('\'null\'', 'null', $values);
            $values = substr($values, 1);
            
            return "insert into ".$table.$fields.' values'.$values;
        }
        
        public function CreateUpdate($table, $condition, $data) {
            $q = '';
            foreach($data as $k=>$v) {
                if(Variable::IsNull($v))
                    $q .= ',"'.$k.'"=null';
                else
                    $q .= ',"'.$k.'"=\''.pg_escape_string($v).'\'';
            }
            return "update ".$table.' set '.substr($q, 1).' where '.$condition; 
        }
        
        public function CreateDelete($table, $condition) {
            if(!empty($condition))
                $condition = ' where '.$condition;
            return 'delete from '.$table.$condition;
        }
        
        public function CreateShowTables() {
            // 
        }
        
        public function CreateShowField($table) {
            // 
        }
        
        
    }

?>
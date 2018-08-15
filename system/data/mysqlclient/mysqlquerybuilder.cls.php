<?php

    class MySqlQueryBuilder extends SqlQueryBuilder {
        
        public function CreateInsert($table, $data, $returning = '') {
            
            foreach($data as $key => $value) {
                if(is_null($value))
                    $value = 'null';
                else
                    $value = '\''.addslashes($value).'\'';
                $data[$key] = $value;
            }
            
            $keys = array_keys($data);
            $fields = '(`'.join("`, `", $keys).'`)';
            
            $vals = array_values($data);
            $values = "(".join(", ", $vals).")";
            
            return "insert into ".$table.$fields.' values'.$values;
        }
        
        public function CreateInsertOrUpdate($table, $data, $exceptFields = array(), $returning = '') {
            $keys = array_keys($data);
            $fields = '(`'.join("`, `", $keys).'`)';
            
            $vals = array_values($data);
            $values = "('".join("', '", $vals)."')";

            $updateStatement = '';
            foreach($data as $k => $v)
                if(!in_array($k, $exceptFields)) 
                    $updateStatement .= ',`'.$k.'`=\''.addslashes($v).'\'';

            return "insert into ".$table.$fields.' values '.$values.' on duplicate key update '.substr($updateStatement, 1);
        }
        
        public function CreateBatchInsert($table, $data) {
            
            $keys = array_keys($data[0]);
            $fields = '(`'.join("`, `", $keys).'`)';
            
            $values = '';
            foreach($data as $row) {
                $vals = array_values($row);
                $values .= ",('".join("', '", $vals)."')";
            }
            $values = substr($values, 1);
            
            return "insert into ".$table.$fields.' values'.$values;
        }
        
        public function CreateUpdate($table, $condition, $data) {
            $q = '';
            foreach($data as $k=>$v) 
                $q .= ',`'.$k.'`='.(is_null($v) ? 'null' : '\''.addslashes($v).'\'');
            return "update ".$table.' set '.substr($q, 1).' where '.$condition; 
        }
        
        public function CreateDelete($table, $condition) {
            if(!empty($condition))
                $condition = ' where '.$condition;
            return 'delete from '.$table.$condition;
        }
        
        public function CreateShowTables() {
            return "show tables";
        }
        
        public function CreateShowField($table) {
            return "show columns from ".$table;
        }
        
        
    }

?>

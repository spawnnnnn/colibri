<?php

    abstract class SqlQueryBuilder {
        
        abstract function CreateInsert($table, $data, $returning = '');
        abstract function CreateBatchInsert($table, $data);
        abstract function CreateUpdate($table, $condition, $data);
        abstract function CreateDelete($table, $condition);
        
        abstract function CreateShowTables();
        abstract function CreateShowField($table);
        
        
    }

?>

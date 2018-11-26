<?php

    /**
     * Класс для работы с логами в памяти
     */
    class MemoryLog {
            
        /**
         * Массив строк лога
         *
         * @var array
         */
        private $_device;
        
        /**
         * Конструктор
         */
        public function __construct() {
            $this->_device = array();
        }        
        
        /**
         * Пишет в лог строку
         *
         * @param array ...$args 
         * @return void
         */
        public function WriteLine(...$args) {
            $args[] = "\n";
            $args = Date::ToDbString(time())."\t".implode("\t", $args);
            $this->_device[] = $args;
        }
        
        /**
         * Возвращает данные лога в виде массива
         *
         * @return array
         */
        public function Content() : array {
            return $this->_device;
        }
        
    }

?>
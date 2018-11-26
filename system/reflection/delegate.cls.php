<?php

    /**
     * Делегат обьекта/класса
     * нужен для передачи колбэков в другие процессы
     */    
    class Delegate {
        
        /**
         * Объект для запуска, либо название объекта
         *
         * @var mixed
         */
        protected $_object;

        /**
         * Метод для запуска
         *
         * @var string
         */
        protected $_method;
        
        /**
         * Конструктор
         *
         * @param mixed $object
         * @param string $method
         */
        public function __construct($object, string $method) {
            $this->_object = $object;
            $this->_method = $method;
        }
        
        /**
         * Создает Delegate по строке вида delega(object,method(...params))
         *
         * @param string $info
         * @return Delegate
         */
        public static function CreateByDelegateInfo(string $info) : Delegate {
            return ContentProvider::Parse($info);
        }
        
        /**
         * Вызывает метод с параметрами
         *
         * @return mixed ...$args
         */
        public function Invoke(...$args) {
            $handler = $this->_method;
            
            // get the needle object
            if(!is_null($this->_object) && !is_object($this->_object)) {
                eval('$this->_object='.$this->_object.';');
            } 

            if(is_null($this->_object)) {
                if(CodeKit::Exists($handler))
                    return CodeKit::Invoke($handler, $args);
            }
            else { 
                if(ClassKit::HasMethod($this->_object, $handler)) {
                    return ClassKit::InvokeMethod($this->_object, $handler, $args);
                }
                else {
                    return null;
                }
            }            
        }
        
    }
    
?>
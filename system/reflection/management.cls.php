<?php
    
    /**
     * Класс обертка для работы с наименованиями классов и методов
     */
    class ClassKit {
        
        /**
         * Проверяет есть ли класс с переданным наименованием
         *
         * @param string $class название класса, которое нужно проверить
         * @return boolean
         */
        static function Exists(string $class) : bool {
            return class_exists($class);
        }
        
        /**
         * Проверяет есть ли метод $method в объекте $class
         *
         * @param mixed $class объект для проверки
         * @param string $method метод для проверки
         * @return boolean
         */
        static function HasMethod($class, string $method) : bool {
            return method_exists($class, $method);
        }
        
        /**
         * Проверяет есть ли свойство $property в объекте $class
         *
         * @param mixed $class объект для проверки
         * @param string $property свойство для проверки
         * @return boolean
         */
        static function HasProperty($class, string $property) : bool {
            return property_exists($class, $property);
        }

        /**
         * Возвращает свойства объекта в виде массива
         *
         * @param mixed $object объект из которого нужно получить свойства
         * @return array
         */
        static function GetProperties($object) : array {
            return get_object_vars($object);
        }    
        
        /**
         * Возвращает наименование класса объекта
         *
         * @param mixed $object
         * @return string
         */
        static function GetName($object) : string {
            return get_class($object);
        }        
        
        /**
         * Вызывает метод объекта по текстовому названию
         *
         * @param mixed $object
         * @param string $method
         * @param array $args
         * @return mixed
         */
        static function InvokeMethod($object, string $method, array $args) {
            return call_user_func_array(array($object, $method), $args);
        }
        
    }
    
    /**
     * Класс обертка для работы в названиями функций
     */
    class CodeKit {

        /**
         * Проверяет есть ли функция с заданным названием
         *
         * @param string $func
         * @return boolean
         */
        static function Exists(string $func) : bool {
            return function_exists($func);
        }
        
        /**
         * Вызывает функцию по названию
         *
         * @param string $func
         * @param array $args
         * @return void
         */
        static function Invoke(string $func, array $args) {
            return call_user_func_array($func, $args);
        }
        
    }
    
    /**
     * Класс обертка для работы с кодом
     */
    class CodeModel {
    
        /**
         * Создает объект по заданному названию и параметрам
         *
         * @param mixed ...$args
         * @return mixed
         */
        public static function CreateObject(string $className, ...$args) {
            
            $eval = '$o = new '.$className.'(';
            for($i=0; $i<count($args); $i++) {
                $eval .= ($i != 0 ? ',' : '').'$args['.$i.']';
            }        
            $eval .= ');';
            
            eval($eval);
            return $o;
        }
        
        /**
         * Создает singleton объект по заданному названию и параметрам
         *
         * @param string $className
         * @param mixed ...$args
         * @return mixed
         */
        public static function CreateSingletonObject(string $className, ...$args) {

            $eval = 'if(!'.$className.'::$i) { '.$className.'::$i = new '.$className.'(';
            for($i=0; $i<count($args); $i++) {
                $eval .= ($i != 0 ? ',' : '').'$args['.$i.']';
            }        
            $eval .= '); } ';
            eval($eval);
            
            $eval = '$o = '.$className.'::$i;';
            eval($eval);
            
            return $o;
        }
        
        /**
         * Вызывает статический метод $methodName класса $className
         *
         * @param string $className
         * @param string $methodName
         * @return mixed
         */
        public static function CallStaticMethod(string $className, string $methodName, ...$args) {
            
            $eval = '$result = '.$className.'::'.$methodName.'(';
            for($i=0; $i<count($args); $i++) {
                $eval .= ($i != 0 ? ',' : '').'$args['.$i.']';
            }        
            $eval .= ');';

            eval($eval);
            return $result;
        }
        
        
        
        
    }
    

    
?>

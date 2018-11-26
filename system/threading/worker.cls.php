<?php

    /**
     * Перечисление ошибок для класса Worker
     */
    abstract class WorkerErrorCodes {

        const UnknownProperty = 1;

        public static function ToString($code) {
            switch($code) {
                case WorkerErrorCodes::UnknownProperty: return 'Unknown property';
            }
        }

    }

    /**
     * Класс исключения для Worker
     */
    class WorkerException extends Exception {

        public function __construct($code, $message) {
            return parent::__construct(WorkerErrorCodes::ToString($code).' '.$message, $code);
        }

    };

    /**
     * Класс работы в процессами, имитирует поток
     * Для работы необходимо наличие php-cli, memcached и ramdisk
     */
    abstract class Worker {

        /**
         * Лимит по времени на выполнение процесса
         *
         * @var integer
         */
        protected $_timeLimit = 0;

        /**
         * Приоритет процесса, требуется наличие nohup
         *
         * @var integer
         */
        protected $_prio = 0;

        /**
         * ID потока
         *
         * @var string
         */
        protected $_id = '';

        /**
         * Лог воркера
         *
         * @var LogDevice
         */
        protected $_log;

        /**
         * Переданные в воркер параметры
         *
         * @var mixed
         */
        protected $_params;
        
        /**
         * Создает обьект класса Worker
         *
         * @param integer $timeLimit лимит по времени для выполнения воркера
         * @param integer $prio приоритет, требуется наличие nohup 
         */
        public function __construct(int $timeLimit = 0, int $prio = 0) {    
            $this->_timeLimit = $timeLimit;
            $this->_prio = $prio;

            $this->_id = Randomization::Integer(0, 999999999);
            $this->_log = new LogDevice('worker_log_'.$this->_id, false, false); // лог файл не режется на куски
        }

        /**
         * Работа по процессу/потоку, необходимо переопределить
         *
         * @return void
         */
        abstract public function Run();

        /**
         * функция Getter для получения данных по потоку 
         *
         * @param string $prop
         * @return void
         */
        public function __get($prop) {
            $prop = strtolower($prop);
            switch($prop) {
                case 'id': return $this->_id;
                case 'timelimit': return $this->_timeLimit;
                case 'prio': return $this->_prio;
                case 'log': return $this->_log;
                default: throw new WorkerException(WorkerErrorCodes::UnknownProperty, $prop);
            }
        }

        /**
         * функция Setter для ввода данных в процесс
         *
         * @param string $prop
         * @param mixed $val
         */
        public function __set($prop, $val) {
            $prop = strtolower($prop);
            switch($prop) {
                case 'timelimit': $this->_timeLimit = $val; break;
                case 'prio': $this->_prio = $val; break;
                default: throw new WorkerException(WorkerErrorCodes::UnknownProperty, $prop); break;
            }
        }

        /**
         * Подготавливает параметры к отправке в поток
         *
         * @param mixed $params
         * @return void
         */
        public function PrepareParams($params) {
            return Variable::Serialize($params);
        }

        /**
         * Разбирает параметры из строки в объект
         *
         * @return void
         */
        public function Prepare($params) {
            $this->_params = Variable::Unserialize($params);
        }

        /**
         * Сериализует воркер
         *
         * @return void
         */
        public function Serialize() {
            return Variable::Serialize($this);
        }

        /**
         * Десериализует воркер
         *
         * @param string $workerString строка содержащая сериализованный воркер
         * @return Worker десериализованный воркер
         */
        public static function Unserialize(string $workerString) : Worker {
            return Variable::Unserialize($workerString);
        }

    }

?>
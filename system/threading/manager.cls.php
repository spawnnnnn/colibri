<?php

    /**
     * Singleton для обработки и создания процессов
     */
    class ProcessManager {

        static $i = false;

        /**
         * Конструктор, запускает обработку воркера, если задан
         */
        public function __construct() {
            $this->_processWorkers();
        }

        /**
         * Статическая функция создания Singleton
         *
         * @return void
         */
        public static function Create() {
            if (!self::$i){
                $c = __CLASS__;
                self::$i = new $c();
            }
            return self::$i;
        }

        /**
         * Запускает обработку воркера
         *
         * @return void
         */
        private function _processWorkers() {
            if(Request::$i->get->worker) {
                $worker = Worker::Unserialize(Request::$i->get->worker);
                $worker->Prepare(Request::$i->get->params);
                $worker->Run();
                exit;
            }
        }

        /**
         * Создает процесс для заданного воркера
         *
         * @param Worker $worker воркер, который нужно запустить
         * @return Process созданный процесс
         */
        public function CreateProcess(Worker $worker) : Process {
            return new Process($worker);
        }

    }

?>
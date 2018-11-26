<?php

    /**
     * Класс в котором собраны функции работы с процессами
     * для вызова со стороны клиента
     */
    class ProcessAjaxHandler extends AjaxHandler {

        /**
         * Запустить процесс
         *
         * @param object $data
         * @return void
         */
        public function StartProcess($data) {

            $params = json_decode($data->params);
            $worker = json_decode($data->worker);

            $workerName = $worker->name;

            $worker = new $workerName($worker->timelimit, $worker->prio);
            $process = new Process($worker);

            $process->Run($params);
            if(!$process->IsRunning()) {
                return $this->Result(true, 'Worker run error');
            }

            return $this->Result(false, 'Worker run success', array('process' => $process->pid, 'worker' => (object)array('id' => $worker->id, 'log' => $worker->log->device, 'position' => $worker->log->position)));

        }

        /**
         * Остановить процесс
         *
         * @param object $data
         * @return void
         */
        public function StopProcess($data) {
            $pid = $data->process;
            $res = Process::StopProcess($pid);
            return $this->Result(false, $res ? 'Process is stopped' : 'Process stop error', array('result' => $res));
        }

        /**
         * Проверяет запущен ли процесс
         *
         * @param object $data
         * @return void
         */
        public function ProcessIsRunning($data) {
            $pid = $data->process;
            $isrunning = Process::IsProcessRunning($pid);
            return $this->Result($res, 'Process is '.($isrunning ? ' running' : 'ended'), array('result' => $isrunning));
        }

        /**
         * Прочитать лог процесса
         *
         * @param object $data
         * @return void
         */
        public function ProcessLog($data) {
            $log = new LogDevice($data->log);
            $log->Open($data->position);
            $results = $log->Read();
            $position = $log->position;
            $log->Close();
            return $this->Result(false, 'Log read success', array('position' => $position, 'results' => $results, 'isrunning' => Process::IsProcessRunning($data->process)));
        }



    }

?>
<?php

    declare(ticks = 1);

    abstract class Process { 
        
        protected $_parentProcessId;
        protected $_processId;
                        
        protected $_data;
        protected $_stoping;
        
        protected $_eventHandlers = array();
        
        public $memory = null;
        
        public function __construct($data = false, $eventHandlers = array()) { 
            
            // нужно обязательно передать в конструктор
            $this->_eventHandlers = $eventHandlers;
            
            // все операции сделанные в этом месте создадут данные для обоиx процессов, и для парент процесса и для чайлд процесса
            $this->memory = new SharedMemory();
            
            $self = $this;
            pcntl_signal(SIGUSR1, function($sig) use ($self) {
                $event = $self->memory->event;
                $self->ProcessEventHandler($event);
            });
            
            $parentpid = posix_getpid();
            if ($pid = pcntl_fork()) {
                $this->_parentProcessId = $parentpid; // возвращаем парент процессу свой собственный PID
                $this->_processId = $pid; // возвращаем парент процессу пид чайлд процесса
                return;     // возвращаемся в парент процесс
            }
            
            if ($pid == -1) {
                new Exception('could not create new process');
            }
            else if ($pid) {
                pcntl_wait($status); // ждем когда детятку проснется
            } else {
                
                // $pid = 0
                $this->_processId = posix_getpid(); // берем пид чайлд процесса
                $this->_parentProcessId = $parentpid; // берем ранее взятый пид парент процесса
                
                // пишем переданные данные в переменну локальную
                $this->_data = !$data ? new ObjectEx() : $data;
                out($this->_data);

                $self = $this;
                pcntl_signal(SIGTERM, function($sig) use ($self) {
                    $self->StopProcessing();
                });
                
                $this->Process();
                
                exit($this->_processId);
                
            }
            
        }
        
        // в парент процессе
        public function ProcessEventHandler($event) { 
            if(isset($this->_eventHandlers[$event->event])) {
                $this->_eventHandlers[$event]($event->data);
            }
        }
        
        public function Abort() { // called from parent process
            posix_kill($this->_processId, SIGTERM);
        }
        
        public function StopProcessing() { //must be called from child process only
            $this->_stoping = true;
        }
        
        public function _signal($signo) {
            
            switch ($signo) {
                case SIGTERM:
                    echo 'aborting...';
                    $this->_stoping = true;
                    exit;
                    break;
                default:
                    $this->_customSignalHandlers[$signo];
                    break;
            }

        }
        
        protected function SendEventToParentProcess($event, $data) {
            
            // пишем информацию о событии
            $this->memory->event = (object)array('event' => $event, 'data' => $data);
            $this->memory->data = $this->_data;
            
            posix_kill($this->_parentProcessId, SIGUSR1);
            pcntl_signal_dispatch();
            
        }
        
        abstract function Process(); /*
            while(!$this->_stoping)  {
                do some work
            }
        */
        
    }

?>

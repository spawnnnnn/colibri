<?php

    class Event {
        
        private $_sender;
        private $_name;
        protected $_propagation;
        
        public function __construct($sender, $name){
            $this->_sender = $sender;
            $this->_name = $name;
            $this->_propagation = true;
        }
        
        public function __get($key){
            switch (strtolower($key)){
                case "name" :
                    return $this->_name;
                case "propagation" :
                    return $this->_propagation;
                case "sender" :
                    return $this->_sender;
                default : 
                    return $this->getProperty($key);
            }
        }
        
        public function __set($key, $value){
            switch (strtolower($key)){
                default : 
                    $this->setProperty($key, $value);
                    break;
            }
        }
        
        public function StopPropagation(){
            $this->_propagation = false;
        }
        
        public function Dispose(){
            unset($this);
        }
        
        protected function getProperty($key){
            return;
        }
        
        protected function setProperty($key, $value){
            return;
        }
    }
    
    class IEventDispatcher {
        
        public function DispatchEvent($event, $args){
            return EventDispatcher::$i->Dispatch(new Event($this, $event), $args);
        }
        
        public function HandleEvent($ename, $listener){
            EventDispatcher::$i->AddEventListener($ename, $listener, $this);
        }
        
        public function RemoveHandler($ename, $listener){
            EventDispatcher::$i->RemoveEventListener($ename, $listener);
        }
    }
    
    class EventDispatcher {
        
        public static $i;
        
        private $_events;
        
        private function __construct(){
            $this->_events = new Hashtable();
        }
        
        public static function Create() {
            if(!self::$i) {
                self::$i = new self();
            }
            return self::$i;
        }
        
        public function Dispose() {
            $this->_events->Clear();
        }
        
        public function __clone() {
            
        }

        function __get($key){
            switch (strtolower($key)){
                default : 
                    return;
            }
        }
        
        function __set($key, $value){
            switch (strtolower($key)){
                default : 
                    break;
            }
        }
        
        public function AddEventListener($ename, $listener = "Dispatch", $object = null){
            
            if (Variable::IsEmpty($listener))
                    return false;

//            changed by spawn
//            if (!$this->_events->exists($ename))
//                return false;

            if (is_object($object)){
                $minfo = new stdClass();
                $minfo->listener = $listener;
                $minfo->object = $object;
            } else {
                if (!is_string($listener))
                    return false;
                    
                $minfo = $listener;
            }

            $e = $this->_events->$ename;

            if ($e == null){
                $l = new arraylist();
                $l->add($minfo);
                $this->_events->add($ename, $l);

                return true;
            }

            if ($e->contains($minfo))
                return false;
            
            $e->add($minfo);
            
            return true;
        }
        
        public function RemoveEventListener($ename, $listener){
            if (!$this->_events->exists($ename))
                return false;
            
            $e = $this->_events->$ename;
            if ($e == null)
                return false;
            
            return $e->delete($listener);
        }
        
        public function Dispatch($event, $args = null){
            
            if (!($event instanceof Event))
                return false;
            
            if (!$this->_events->exists($event->name))
                return false;

            $e = $this->_events->Item($event->name);
            if ($e == null)
                return false;
            
            $result = $args;
            foreach ($e as $item){
                if (is_object($item)){
                    $object = $item->object;
                    $listener = $item->listener;
                    if (method_exists($object, strval($listener))) {
                        $result = $object->$listener($event, $result);
                    }
                } else {
                    if (function_exists(strval($item)))
                        $result = $item($event, $result);

                        
                }
                
                if (!$event->propagation)
                    break;
            }
            
            return $result;
        }
        
        public function HasEventListener($ename, $listener){
            if (!$this->_events->exists($ename))
                return false;
            
            $e = $this->_events->$ename;
            
            if ($e == null)
                return false;
            
            return $e->exists($listener);
        }
        
        public function RegisteredListeners($ename = ""){
            if ($this->_events->count() == 0)
                return false;
                
            if (is_empty($ename)){
                $keys = $this->_events->keys();
                $listeners = array();
                foreach ($keys as $k){
                    $l = $this->RegisteredListeners($k);
                    if ($l)
                        $listeners = array_merge($listeners, $l->ToArray());
                }
                
                return $listeners;
            } else {
                if (!$this->_events->exists($ename))
                    return false;
                
                $e = $this->_events->$ename;
                if ($e == null)
                    return false;
                
                return $e->Copy();
            }
        }
    }

?>
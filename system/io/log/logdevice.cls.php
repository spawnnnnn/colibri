<?
    
    class LogDevice {
        
        /**
         * Наименование лог файла
         *
         * @var string
         */
        private $_device;

        /**
         * Выворить ли лог на экран
         *
         * @var bool
         */
        private $_output;

        /**
         * Архивировать ли лог при достижении 10 кб
         *
         * @var bool
         */
        private $_archive;

        /**
         * Текущая позиция в логе при чтении, номер строки
         *
         * @var int
         */
        private $_currentPos;

        /**
         * Ридер лог файла
         *
         * @var resource
         */
        private $_handler;
        
        /**
         * Конструктор
         *
         * @param string $device
         * @param boolean $output
         * @param boolean $archive
         */
        public function __construct(string $device, bool $output = false, bool $archive = true) {
            $this->_device = _CACHE.'log/'.$device;
            $this->_output = $output;
            $this->_archive = $archive;
        }        

        /**
         * Getter
         *
         * @param string $prop
         * @return void
         */
        public function __get(string $prop) {
            $prop = strtolower($prop);
            switch($prop) {
                case 'device': return str_replace(_CACHE.'log/', '', $this->_device);
                case 'position': return $this->_currentPos;
            }
            return false;
        }

        /**
         * Setter
         *
         * @param string $prop
         * @param mixed $value
         */
        public function __set(string $prop, $value) {
            $prop = strtolower($prop);
            switch($prop) {
                case 'position': $this->_currentPos = $value; break;
            }
        }
        
        /**
         * Записывает в лог строку
         * Строка формируется из параметров функции
         *
         * Внимание: Лог файл будет перезаписан (создан новый с сохранением старого) при достижении размера 10 кб
         * 
         * @param string[] ...$args
         * @return void
         */
        public function WriteLine(...$args) {
            // $args = func_get_args();
            $args[] = "\n";
            $args = Date::ToDbString(time())."\t".implode("\t", $args);
            
            if(!FileInfo::Exists($this->_device))
                FileInfo::Create($this->_device, true, 0777);
            
            if($this->_archive) {
                $fi = new FileInfo($this->_device);
                if($fi->size > 1048576) {
                    FileInfo::Move($this->_device, $this->_device.'.'.microtime(true));
                    FileInfo::Create($this->_device, true, 0777);
                }
            }
                
            file_put_contents($this->_device, $args, FILE_APPEND);
            if($this->_output)
                out($args);
            
        }

        /**
         * Возвращает контент лог файла
         *
         * @return string
         */
        public function Content() : string {
            return FileInfo::ReadAll($this->_device);
        }

        /**
         * Открывает лог файл для последовательного чтения
         *
         * @param integer $position стартовая позиция для чтения
         * @return void
         */
        public function Open(int $position = 0) {
            if(!file_exists($this->_device)) {
                touch($this->_device);
            }
            $this->_handler = fopen($this->_device, 'r+');
            $this->_currentPos = $position;
        }

        /**
         * Закрывает лог файл
         *
         * @return void
         */
        public function Close() {
            fclose($this->_handler);
            $this->_handler = false;
            $this->_currentPos = 0;
        }

        /**
         * Читает последние сообщения в логе начиная с позиции последнего чтения, возвращает в виде массива строк
         *
         * @return array массив строк лога
         */
        public function Read() : array {
            $results = [];
            fseek($this->_handler, $this->_currentPos);
            while($string = fgets($this->_handler)) {
                $results[] = $string;
                $this->_currentPos+=strlen($string);
            }
            return $results;
        }
        
    }
    
?>
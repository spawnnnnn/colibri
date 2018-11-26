<?
    
    /**
     * Базовый класс для всех обработчиков Ajax запросов
     */
    abstract class AjaxHandler {
        // TODO понять зачем нужна эта переменная      
        static $i;

        /**
         * Функция сборки результата запроса
         *
         * @param boolean $error произошла ли ошибка
         * @param string $message сообщение
         * @param array $params дополнительные параметры
         * @return array готовый результат
         */
        public function Result(bool $error, string $message, array $params) : array {
            $res = new ObjectEx();
            $res->error = $error;
            $res->message = $message;
            foreach($params as $key => $value) {
                $res->$key = $value;
            }
            return $res->data;
        }
        
    }

    /**
     * Класс запросщик, вызывается в сервисе /.service
     */
    class Ajax {
        
        /**
         * Спусок разрешенных обработчиков/команд
         *
         * @var array
         */
        private $_allowed;
        
        /**
         * Тип ответа JSON/XML
         *
         * @var integer
         */
        private $_type;

        /**
         * Объект
         *
         * @var mixed
         */
        private $_object;

        /**
         * Метод, который надо запустить
         *
         * @var string
         */
        private $_method;

        /**
         * Данные запроса
         *
         * @var mixed
         */
        private $_data;
        
        /**
         * Список ошибок
         */
        const IncorrectCommandObject = 1;
        const UnknownMethodInObject = 2;
        
        /**
         * Список типов
         */
        const JSON = 1;
        const XML = 2;
        
        /**
         * Конструктор
         *
         * @param array $allowed
         * @param integer $type
         */
        public function __construct(array $allowed, int $type = Ajax::JSON) {
            $this->_allowed = $allowed;
            $this->_type = $type;
        }
        
        /**
         * Отправляет ответ об ошибке в виде XML
         *
         * @param string $message
         * @param integer $code
         * @return void
         */
        private function _responseWithError(string $message, int $code = -1) {
            echo '<response>
                <error>
                    <code>'.$code.'</code>
                    <message>'.$message.'</message>
                </error>
            </response>';
        }
        
        /**
         * Подготавливает команду
         *
         * @param string $cmd
         * @param mixed $data
         * @return void
         */
        public function Process(string $cmd, $data): bool {

            // cmd = object.method
            $cmds = explode(".", $cmd);
            $this->_object = $cmds[0];
            $this->_method = $cmds[1];
            $this->_data = $data;
            
            /*if(!in_array($this->_object, $this->_allowed)) {
                $this->_responseWithError('Unauthorized command object', Ajax::IncorrectCommandObject);
                return false;
            }*/
            
            return true;

        }
        
        /**
         * Запускает команду
         *
         * @return string Результат работы в виде строки JSON или XML
         */
        public function Run() : string {
            
            $class = $this->_object;
            $method = $this->_method;
            $data = $this->_data;
            
            if(!ClassKit::HasMethod($class, $method)) {
                $this->_responseWithError('Unknown method in object '.$class, Ajax::UnknownMethodInObject);
                return false;
            }
            
            $obj = CodeModel::CreateSingletonObject($class);
            $ret = ClassKit::InvokeMethod($obj, $method, array($data));
            
            if($this->_type == Ajax::JSON)
                return json_encode($ret);
                //return Json::Serialize($ret);
            else if($this->_type == Ajax::XML)
                return Xml::Serialize($ret, 'result');
            
        }
        
            
    }   

?>
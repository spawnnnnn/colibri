<?php
    class BanksConsumerCreditsScore {
        protected static $rules = array(
            // Райф
            2 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),
            // ОТП банк
            11 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),
            // Почта банк
            /*237 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_GREEN,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_YELLOW,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_GREEN,
            ),*/
            // МКБ
            26 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_GREEN,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_YELLOW,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_GREEN,
            ),
            
            // Юникредит
            7 => array(
                Score::SCORE_A_PLUS => Score::GROUP_YELLOW,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_RED,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_GRAY,
            ),
            // ВТБ 24
            1 => array(
                Score::SCORE_A_PLUS => Score::GROUP_YELLOW,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_RED,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),
            //ПСБ
            27 => array(
                Score::SCORE_A_PLUS => Score::GROUP_YELLOW,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_RED,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_GREEN,
            ),
            
            
            // Сити
            33 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_GREEN,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_YELLOW,
                Score::SCORE_E => Score::GROUP_YELLOW,
                Score::SCORE_N_A => Score::GROUP_DARKGREEN,
            ),
            // Вост Экспресс
            31 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_GREEN,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_YELLOW,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_GREEN,
            ),
            // Бинбанк
            34 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_GREEN,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_YELLOW,
                Score::SCORE_E => Score::GROUP_YELLOW,
                Score::SCORE_N_A => Score::GROUP_DARKGREEN,
            ),
            
            // Совкомбанк
            /*28 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_GREEN,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_GREEN,
            ),*/
            
            // Кредит Европа Банк
            /*17 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),*/
            // Газпром
            /*13 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),*/
            // Рус стандарт
            /*14 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),*/
            // Траст
            /*30 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),*/
            // Рус финанс
            /*15 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),*/
            // Тинькофф
            /*25 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_GREEN,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_YELLOW,
                Score::SCORE_E => Score::GROUP_YELLOW,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),*/
            // Открытие
            /*35 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_RED,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_RED,
            ),*/
            // Уралсиб
            /*23 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_YELLOW,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_RED,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_RED,
            ),*/
            // РоссельхозБанк
            /*21 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_GREEN,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_RED,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),*/
            // БМ
            /*12 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_GREEN,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_YELLOW,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),*/
            // Сбербанк
            3 => array(
                Score::SCORE_A_PLUS => Score::GROUP_GREEN,
                Score::SCORE_A => Score::GROUP_GREEN,
                Score::SCORE_B => Score::GROUP_YELLOW,
                Score::SCORE_C => Score::GROUP_YELLOW,
                Score::SCORE_D => Score::GROUP_YELLOW,
                Score::SCORE_E => Score::GROUP_RED,
                Score::SCORE_N_A => Score::GROUP_YELLOW,
            ),
            
        );
        
        public static function GetGroup($bank, $score, $request = [], $ad_ost = []) {
            if ( ! ($request instanceof ObjectEx)) {
                $request = new ObjectEx($request);
            }
            
            $group = Score::GROUP_GRAY;
            if (isset(self::$rules[$bank][$score])) {
                $group = self::$rules[$bank][$score];
            }
            elseif (28 == $bank) {
                if ( ! $request->age) {
                    $group = Score::GROUP_YELLOW;
                } elseif ($request->age >= 45) {
                    $group = Score::GROUP_GREEN;
                } elseif ($request->age >= 35) {
                    $group = Score::GROUP_YELLOW;
                } else {
                    $group = Score::GROUP_RED;
                }
            }
            
            if ($ad_ost && 
                !isset($ad_ost[$bank]) && 
                ($group == Score::GROUP_GREEN || $group == Score::GROUP_DARKGREEN)) {
                $group = Score::GROUP_YELLOW;
            }
            
            $raifBid = 2;
            $uniBid = 7;
            if ($bank == $raifBid) {
                $allScore = Score::i()->GetScore();
                $raifScore = isset($allScore->$raifBid) ? $allScore->$raifBid : null;
                $uniScore = isset($allScore->$uniBid) ? $allScore->$uniBid : null;
                if ($raifScore == Score::SCORE_A_PLUS) {
                    if ($uniScore == Score::SCORE_A_PLUS || $uniScore == Score::SCORE_A) {
                        $group = Score::GROUP_DARKGREEN;
                    }
                }
            }
            
            /*$raifBid = 2;
            $allScore = Score::i()->GetScore();
            $raifScore = isset($allScore->$raifBid) ? $allScore->$raifBid : null;
            if ($raifScore == Score::SCORE_A_PLUS || $raifScore == Score::SCORE_A) {
                if ($bank != $raifBid && $group == Score::GROUP_DARKGREEN) {
                    $group = Score::GROUP_GREEN;
                }
            }*/
            
            return $group;
        }
    }
    
    class Score {
        
        const GROUP_DARKGREEN = 'darkgreen';
        const GROUP_GREEN = 'green';
        const GROUP_YELLOW = 'yellow';
        const GROUP_RED = 'red';
        const GROUP_GRAY = 'gray';
        
        const SCORE_A_PLUS = 1;
        const SCORE_A = 2;
        const SCORE_B = 3;
        const SCORE_C = 4;
        const SCORE_D = 5;
        const SCORE_E = 6;
        const SCORE_N_A = 0;
        
        static $backConvert = array(
            Score::SCORE_A_PLUS => array(1),
            Score::SCORE_A => array(2,3),
            Score::SCORE_B => array(4),
            Score::SCORE_C => array(5),
            Score::SCORE_D => array(6,7),
            Score::SCORE_E => array(8,9,10),
        );
        
        protected $prescore = false;
        protected $postscore = false;
        protected $data = false;
        
        protected $postscoreRequests = 0;
        
        protected $banks = array(
            //'p1' => 26,
            'p2' => 11,
            'p3' => 27,
            'p4' => 2,
            //'p5' => 29,
            'p6' => 7,
            'p7' => 1,
        );
        
        protected static $instance;
        
        public static function i() {
            if ( is_null(self::$instance) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        
        public function __construct() {
            $score = json_decode(Request::$i->cookie->score);
            $this->prescore = (isset($score->prescore) && $score->prescore) ? (object) $score->prescore : false;
            $this->postscore = (isset($score->postscore) && $score->postscore) ? (object) $score->postscore : false;
            $this->data = (isset($score->data) && $score->data) ? (object) $score->data : false;
            if ($this->data) {
                $this->data = (array) $this->data;
                $this->data = (object) array_map('urldecode', $this->data);
            }
        }
        
        public function GetScoreText($score) {
            $array = array(
                self::SCORE_A_PLUS => 'Отлично',
                self::SCORE_A => 'Отлично',
                self::SCORE_B => 'Хорошо',
                self::SCORE_C => 'Средне',
                self::SCORE_D => 'Плохо',
                self::SCORE_E => 'Плохо',
            );
            
            return isset($array[$score]) ? $array[$score] : null;
        }
        
        public function GetScoreConst($score) {
            if ($score >= 0.8) {
                return Score::SCORE_A_PLUS;
            } elseif ($score >= 0.7) {
                return Score::SCORE_A;
            } elseif ($score >= 0.6) {
                return Score::SCORE_B;
            } elseif ($score >= 0.4) {
                return Score::SCORE_C;
            } elseif ($score >= 0.3) {
                return Score::SCORE_D;
            } elseif ($score >= 0) {
                return Score::SCORE_E;
            }
        }
        
        public function GetScoreLabel($score) {
            $array = array(
                self::SCORE_A_PLUS => 'A+',
                self::SCORE_A => 'A',
                self::SCORE_B => 'B',
                self::SCORE_C => 'C',
                self::SCORE_D => 'D',
                self::SCORE_E => 'E',
            );
            
            return isset($array[$score]) ? $array[$score] : null;
        }
        
        public function GetPreScore($bank = null) {
            if ($bank !== null) {
                $bank = 'b_'.$bank;
            }
            
            // Хук! Если скор не определен (АдБлок и.т.д.) то считаем что скор N/A
            /*if ($this->prescore === false || $this->GetMedian($this->prescore) == Score::SCORE_N_A) {
                $this->prescore = (object) $this->defaultScore;
            }*/
            if ($this->prescore === false) {
                return $bank ? Score::SCORE_N_A : (object) array(0 => Score::SCORE_N_A);
            }
            if ($bank) {
                return isset($this->prescore->$bank) ? $this->prescore->$bank : self::GetMedian($this->prescore);
            }
            if ( ! $this->prescore || ! (array)$this->prescore) {
                return false;
            }
            return $this->prescore;
        }
        
        public static function ConvertToMin($source) {
            $convert = array(
                1 => Score::SCORE_A_PLUS,
                2 => Score::SCORE_A,
                3 => Score::SCORE_A,
                4 => Score::SCORE_B,
                5 => Score::SCORE_C,
                6 => Score::SCORE_D,
                7 => Score::SCORE_D,
                8 => Score::SCORE_E,
                9 => Score::SCORE_E,
                10 => Score::SCORE_E,
            );
            
            if (is_object($source) || is_array($source)) {
                $source = (array) $source;
                foreach ($source as $k => $v) {
                    if (isset($convert[$v])) {
                        $source[$k] = $convert[$v];
                    }
                }
                return $source;
            } else {
                if (isset($convert[$source])) {
                    $source = $convert[$source];
                }
                return $source;
            }
        }
        
        public function GetPostScore($bank = null, $convertToMin = false) {
            if ($bank !== null) {
                $bank = 'b_'.$bank;
            }
            
            if ( ! $this->postscore || ! (array)$this->postscore) {
                return false;
            }
            
            if ($convertToMin) {
                $postscore = self::ConvertToMin($this->postscore);
            } else {
                $postscore = clone $this->postscore;
            }
            
            /*if ($this->postscore === false || $this->GetMedian($this->postscore) == Score::SCORE_N_A) {
                $this->postscore = (object) $this->defaultScore;
            }*/
            if ($bank) {
                return isset($postscore->$bank) ? $postscore->$bank : self::GetMedian($postscore);
            }
            return $postscore;
        }
        
        public function GetPostScoreRequests($bank = null, $convertToMin = false) {
            return $this->postscoreRequests;
        }
        
        public function GetPostScoreData() {
            return $this->data;
        }
        
        public function GetScore($bank = null, $convertToMin = false) {
            if ($this->GetPostScore($bank, $convertToMin)) {
                return $this->GetPostScore($bank, $convertToMin);
            } else {
                return $this->GetPreScore($bank);
            }
        }
        
        public function RequestPreScore($clientid) {
            $key = 'ooBiep8aeBeor5faaseoThi5imaediek';
            $partnerid = 78;
            $method = 'moneymatika_segments';
            $requestid = microtime(true)*10000;
            
            // Вычисление подписи
            $sign = '';
            $query = array(
                'partner_id' => $partnerid,
                'method' => $method,
                'request_id' => $requestid,
                'partner_user_id' => $clientid,
            );
            ksort($query);
            foreach ($query as $k => $v) {
                $sign .= $k.'='.$v;
            }
            $sign = Strings::ToLower(md5($sign.$key));
            
            $url = 'https://r9.mail.ru/api/v1/call/?partner_user_id='.$clientid.'&partner_id='.$partnerid.'&method='.$method.'&request_id='.$requestid.'&sign='.$sign;
            
            $request = new WebRequest($url);
            $result = $request->Request();
            
            $result = json_decode($result->data);

            $succes = $result && isset($result->data);
            
            $prescore = array();
            $log = array();
            if ($succes) {
                foreach ($result->data as $k => $v) {
                    $k = isset($this->banks[$k]) ? $this->banks[$k] : null;
                    if ($k) {
                        $prescore['b_'.$k] = $v;
                        $log[$k] = $v;
                    }
                }
            } else {
                foreach ($this->banks as $k => $v) {
                    $prescore['b_'.$v] = 0;
                    $log[$v] = 0;
                }
            }
            
            $prescore = (object) $prescore;

            $this->store($prescore, null);
            
            $this->logPreScore($clientid, $log, $requestid);
            
            return $prescore;
        }
        
        public function RequestPostScoreFromMail($cid, $clientid, $firstname, $lastname, $patronymic, $birthdate, $phone, $email, $credit_history, $work_type) {
            $url = 'https://r9.mail.ru/score/';
            
            $data = array(
                'ApplicationName' => 'MoneyMaticaBaseApp',
                'Method' => 'GetStreetClientSegment',
                'Param' => array(
                    'Model' => 'moneymatika_segment',
                    'Phones' => array($phone),
                    'Emails' => array($email),
                    'BD' => $birthdate ? date('Ymd', $birthdate) : 'None',
                    'FirstName' => $firstname,         
                    'MiddleName' => $patronymic,
                    'SecondName' => $lastname,
                ),
                'ProductName' => 'MoneyMaticaSegments',
                'Login' => 'MoneyMatica',
                'Token' => '93EB715DBC84BA938F5A1CD5823799F4',
            );
            $request = new WebRequest($url, RequestType::Post);
            $request->encryption = RequestEncryption::JsonEncoded;
            $result = $request->Request($data);

            $result = json_decode($result->data);
            
            $succes = $result && ! isset($result->error);

            $postscore = array();
            $log = array();
            if ($succes) {
                foreach ($result as $k => $v) {
                    $k = isset($this->banks[$k]) ? $this->banks[$k] : null;
                    if ($k) {
                        $postscore['b_'.$k] = $v;
                        $log[$k] = $v;
                    }
                }
            } else {
                foreach ($this->banks as $k => $v) {
                    $postscore['b_'.$v] = 0;
                    $log[$v] = 0;
                }
            }
            
            $postscore = (object) $postscore;
            
            return [$postscore, $log];
        }
            
        public function RequestPostScore($cid, $clientid, $firstname, $lastname, $patronymic, $birthdate, $phone, $email, $credit_history, $work_type) {
            $phone = preg_replace('/\D/', '', $phone);
            
            list($postscore, $log) = $this->RequestPostScoreFromMail($cid, $clientid, $firstname, $lastname, $patronymic, $birthdate, $phone, $email, $credit_history, $work_type);
            
            $this->store(null, array('postscore' => $postscore, 'data' => array('firstname' => $firstname)));
            
            $member = 0;
            try {
                $password = Randomization::Character(8);
                
                $auth = new AuthApi();
                $auth->Check();
                if ( ! $auth->current) {
                    $auth->Register((object) array(
                        'email' => $email,
                        'phone' => $phone,
                        'password' => $password,
                        'name' => $firstname,
                        'surname' => $lastname,
                        'patronymic' => $patronymic,
                        'birthdate' => $birthdate ? Date::ToDbString($birthdate) : null,
                        'profile' => json_encode([
                            'source' => 'postscore',
                            'credit_history' => $credit_history,
                            'work_type' => $work_type,
                        ]),
                        'additional' => json_encode([
                            'cid' => $cid,
                            'service' => 3,
                            'clientid' => Request::$i->cookie->clientid,
                            'traffictype' => Request::$i->cookie->traffictype,
                            'prescore' => Score::i()->GetPreScore(),
                            'postscore' => Score::i()->GetPostScore(),
                        ]),
                        'source' => array('type' => 'postscore'),
                    ));
                    $auth->Login($email, $password);
                }
                $member = $auth->current ? $auth->current->id : 0;
                if ( ! $member) {
                    $result = $auth->MemberExists((object)['email' => $email]);
                    $member = $result->id ? $result->id : 0;
                }
            } catch (BaseException $ex) {  }
            
            $this->logPostScore($member, $clientid, $log, $firstname, $lastname, $patronymic, $birthdate, $phone, $email, $credit_history, $work_type);
            
            $this->postscoreRequests++;
            
            return $postscore;
        }
        
        protected function store($prescore = null, $postscore = null) {
            $score = json_decode(Request::$i->cookie->score);
            $score = $score ? $score : (object) array();
            if ($prescore !== null) {
                $this->prescore = $prescore;
                $score->prescore = $this->prescore;
                setcookie('score_valid', 1, 0, '/');
            }
            if ($postscore !== null) {
                $data = array_map('urlencode', $postscore['data']);
                $postscore = $postscore['postscore'];
                
                $this->postscore = $postscore;
                $this->data = $data;
                $score->postscore = $this->postscore;
                $score->data = $this->data;
                setcookie('score_valid', 1, time()+Date::WEEK, '/');
            }
            setcookie('score', json_encode($score), time()+Date::MONTH, '/');
        }
        
        protected function logPreScore($clientid, $prescore, $requestid) {
            $dtp = new DataPoint('auth');
            $insert = array();
            $date = time();
            foreach ($prescore as $bid => $score) {
                $insert[] = array(
                    'clientid' => $clientid,
                    'date' => Date::ToDbString($date),
                    'bid' => $bid,
                    'requestid' => $requestid,
                    'score' => $score,
                );
            }
            $dtp->InsertBatch('prescores', $insert);
        }
        
        protected function logPostScore($member, $clientid, $postscore, $firstname, $lastname, $patronymic, $birthdate, $phone, $email, $credit_history, $work_type) {
            $dtp = new DataPoint('auth');
            $insert = array();
            $date = time();
            
            $data = array('firstname' => $firstname, 'lastname' => $lastname, 'patronymic' => $patronymic, 'date' => $birthdate, 'phone' => $phone, 'email' => $email, 'credit_history' => $credit_history, 'work_type' => $work_type);
            
            foreach ($postscore as $bid => $score) {
                $insert[] = array(
                    'member' => $member,
                    'clientid' => $clientid,
                    'date' => Date::ToDbString($date),
                    'bid' => $bid,
                    'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                    'score' => $score,
                );
            }
            $dtp->InsertBatch('postscores', $insert);
        }
        
        public static function GetMedian($array) {
            $array = (array) $array;
            $count = count($array);
            
            sort($array);
            $middle = floor($count/2);

            if ($count%2) 
                return $array[$middle];
            else 
                return $array[$middle-1];
        }
        
        public static function GetScoreGroup($subgroup) {
            $score_group = null;
            if ($subgroup == Score::GROUP_DARKGREEN) {
                $score_group = 'high';
            } elseif ($subgroup == Score::GROUP_GREEN) {
                $score_group = 'big';
            } elseif ($subgroup == Score::GROUP_YELLOW) {
                $score_group = 'middle';
            } elseif ($subgroup == Score::GROUP_RED) {
                $score_group = 'low';
            } else {
                $score_group = 'unknown';
            }
            return $score_group;
        }
        
        public static function GetPercent($tryBank, $scoreBank, $scores) {
            if (Variable::IsNumeric($tryBank)) {
                $tryBank = 'b_'.$tryBank;
            }
            if (Variable::IsNumeric($scoreBank)) {
                $scoreBank = 'b_'.$scoreBank;
            }
            
            $probabilities = [
                'psb' => [
                    'b_27' => [
                        0  => 0.55,
                        1  => 0.85,
                        2  => 0.80,
                        3  => 0.75,
                        4  => 0.65,
                        5  => 0.55,
                        6  => 0.45,
                        7  => 0.40,
                        8  => 0.30,
                        9  => 0.20,
                        10 => 0.15,
                    ],
                    'b_2' => [
                        0  => 0.30,
                        1  => 0.55,
                        2  => 0.50,
                        3  => 0.45,
                        4  => 0.40,
                        5  => 0.35,
                        6  => 0.30,
                        7  => 0.25,
                        8  => 0.15,
                        9  => 0.10,
                        10 => 0.05,
                    ],
                    'b_26' => [
                        0  => 0.50,
                        1  => 0.80,
                        2  => 0.70,
                        3  => 0.60,
                        4  => 0.50,
                        5  => 0.55,
                        6  => 0.45,
                        7  => 0.40,
                        8  => 0.30,
                        9  => 0.20,
                        10 => 0.15,
                    ],
                    'b_11' => [
                        0  => 0.15,
                        1  => 0.15,
                        2  => 0.15,
                        3  => 0.15,
                        4  => 0.15,
                        5  => 0.15,
                        6  => 0.15,
                        7  => 0.15,
                        8  => 0.15,
                        9  => 0.15,
                        10 => 0.15,
                    ],
                    'b_28' => [
                        0  => 0.40,
                        1  => 0.15,
                        2  => 0.35,
                        3  => 0.35,
                        4  => 0.35,
                        5  => 0.45,
                        6  => 0.45,
                        7  => 0.50,
                        8  => 0.50,
                        9  => 0.40,
                        10 => 0.25,
                    ],
                    'b_1' => [
                        0  => 0.35,
                        1  => 0.65,
                        2  => 0.60,
                        3  => 0.55,
                        4  => 0.45,
                        5  => 0.45,
                        6  => 0.40,
                        7  => 0.35,
                        8  => 0.30,
                        9  => 0.25,
                        10 => 0.10,
                    ],
                    'b_7' => [
                        0  => 0.35,
                        1  => 0.65,
                        2  => 0.65,
                        3  => 0.60,
                        4  => 0.50,
                        5  => 0.45,
                        6  => 0.40,
                        7  => 0.35,
                        8  => 0.30,
                        9  => 0.25,
                        10 => 0.10,
                    ],
                    'b_31' => [
                        0  => 0.60,
                        1  => 0.65,
                        2  => 0.65,
                        3  => 0.60,
                        4  => 0.60,
                        5  => 0.60,
                        6  => 0.60,
                        7  => 0.60,
                        8  => 0.30,
                        9  => 0.25,
                        10 => 0.10,
                    ],
                ],
                'own' => [
                    'b_27' => [
                        0  => 0.50,
                        1  => 0.85,
                        2  => 0.80,
                        3  => 0.75,
                        4  => 0.65,
                        5  => 0.50,
                        6  => 0.40,
                        7  => 0.35,
                        8  => 0.30,
                        9  => 0.20,
                        10 => 0.15,
                    ],
                    'b_2' => [
                        0  => 0.60,
                        1  => 0.85,
                        2  => 0.65,
                        3  => 0.60,
                        4  => 0.55,
                        5  => 0.50,
                        6  => 0.40,
                        7  => 0.30,
                        8  => 0.25,
                        9  => 0.15,
                        10 => 0.10,
                    ],
                    'b_26' => [
                        0  => 0.50,
                        1  => 0.83,
                        2  => 0.75,
                        3  => 0.70,
                        4  => 0.60,
                        5  => 0.50,
                        6  => 0.40,
                        7  => 0.35,
                        8  => 0.30,
                        9  => 0.20,
                        10 => 0.15,
                    ],
                    'b_11' => [
                        0  => 0.15,
                        1  => 0.15,
                        2  => 0.15,
                        3  => 0.15,
                        4  => 0.15,
                        5  => 0.15,
                        6  => 0.15,
                        7  => 0.15,
                        8  => 0.15,
                        9  => 0.15,
                        10 => 0.15,
                    ],
                    'b_28' => [
                        0  => 0.40,
                        1  => 0.55,
                        2  => 0.55,
                        3  => 0.50,
                        4  => 0.50,
                        5  => 0.50,
                        6  => 0.50,
                        7  => 0.40,
                        8  => 0.30,
                        9  => 0.20,
                        10 => 0.10,
                    ],
                    'b_1' => [
                        0  => 0.30,
                        1  => 0.55,
                        2  => 0.50,
                        3  => 0.45,
                        4  => 0.40,
                        5  => 0.45,
                        6  => 0.40,
                        7  => 0.35,
                        8  => 0.30,
                        9  => 0.25,
                        10 => 0.10,
                    ],
                    'b_7' => [
                        0  => 0.30,
                        1  => 0.60,
                        2  => 0.55,
                        3  => 0.50,
                        4  => 0.45,
                        5  => 0.50,
                        6  => 0.40,
                        7  => 0.35,
                        8  => 0.30,
                        9  => 0.25,
                        10 => 0.10,
                    ],
                    'b_31' => [
                        0  => 0.60,
                        1  => 0.65,
                        2  => 0.65,
                        3  => 0.60,
                        4  => 0.60,
                        5  => 0.60,
                        6  => 0.60,
                        7  => 0.60,
                        8  => 0.30,
                        9  => 0.25,
                        10 => 0.10,
                    ],
                ],
            ];
            
            $score = isset($scores->$scoreBank) ? $scores->$scoreBank : null;
            $psbScore = isset($scores->b_27) ? $scores->b_27 : null;
            
            if ($score === null || $psbScore === null) {
                return null;
            }
            
            $bank = isset($probabilities['psb'][$tryBank]) ? $tryBank : $scoreBank;
            
            if (isset($probabilities['psb'][$bank][$score]) && isset($probabilities['own'][$bank][$score])) {
                return round(
                    $probabilities['psb'][$bank][$psbScore]*0.8 + 
                    $probabilities['own'][$bank][$score]*0.2
                , 2);
            }
            
            return null;
        }
        
        public static function GetProbability($bank, $postscore) {
            if (Variable::IsNumeric($bank)) {
                $bank = 'b_'.$bank;
            }
            
            if (
                $bank == 'b_1' //ВТБ24
                || $bank == 'b_2' //Райффайзен
                || $bank == 'b_7' //ЮниКредит Банк
                || $bank == 'b_11' //ОТП Банк
                || $bank == 'b_27' //Промсвязьбанк
            ) {
                $score_probability = Score::GetPercent($bank, $bank, $postscore);
            } elseif (
                $bank == 'b_26' //МКБ
                || $bank == 'b_33' //Ситибанк
                || $bank == 'b_34' //Бинбанк
                || $bank == 'b_35' //Открытие
                || $bank == 'b_25' //Тинькофф Банк
            ) {
                $score_probability = Score::GetPercent($bank, 27, $postscore);
            } elseif (
                $bank == 'b_9' //Альфабанк
                || $bank == 'b_10' //Росбанк
            ) {
                $score_probability = Score::GetPercent($bank, 7, $postscore);
            } elseif (
                $bank == 'b_3' //Сбербанк
                || $bank == 'b_13' //Газпромбанк
                || $bank == 'b_21' //РоссельхозБанк
                || $bank == 'b_5' //Абсолют Банк
                || $bank == 'b_12' //Банк Москвы
                || $bank == 'b_23' //УралСиб
                || $bank == 'b_14' //Русский стандарт
                || $bank == 'b_17' //Кредит Европа банк
                || $bank == 'b_30' //Банк Траст
                || $bank == 'b_20' //Банк Жилищного Финансирования
                || $bank == 'b_78' //СКБ-Банк
                || $bank == 'b_18' //Возрождение
                || $bank == 'b_82' //Транскапиталбанк
            ) {
                $score_probability = Score::GetPercent($bank, 1, $postscore);
            } else {
                $score_probability = Score::GetPercent($bank, 11, $postscore);
            }
            
            return $score_probability ? $score_probability*100 : null;
        }
        
        public static function GetScoreInfo($bank, $request = [], $ad_ost = []) {
            if ( ! ($request instanceof ObjectEx)) {
                $request = new ObjectEx($request);
            }
            
            $prescore = Score::i()->GetPreScore($bank);
            $prescore_subgroup = BanksConsumerCreditsScore::GetGroup($bank, $prescore, $request, $ad_ost);
            $prescore_group = Score::GetScoreGroup($prescore_subgroup);
            
            $postscore = Score::i()->GetPostScore();
            $postscore_subgroup = null;
            $postscore_group = null;
            
            $score_probability = Score::GetProbability($bank, $postscore);
            
            /* Modifiers */
            if ($score_probability) {
                if (isset($ad_ost[$bank])) {
                    $score_probability += 5;
                } elseif ($ad_ost) {
                    $score_probability -= 5;
                }
                if ($bank == 1 || $bank == 3) {
                    $score_probability -= 10;
                }
                if ($bank == 26 && ($request->city == 1 || $request->city == 2)) {
                    $score_probability += 5;
                }
                if ($bank == 28) {
                    if ($request->age >= 45) { $score_probability += 15; }
                    else if ($request->age >= 35) { $score_probability += 5; }
                    else if ($request->age) { $score_probability -= 10; }
                }
                if ($score_probability > 90) { $score_probability = 90; }
                if ($score_probability < 5) { $score_probability = 5; }
            }
            /* ~ Modifiers */
            
            if ($postscore) {
                if ($score_probability >= 80) {
                    $postscore_subgroup = Score::GROUP_DARKGREEN;
                    $postscore_group = 'high';
                } elseif ($score_probability >= 60) {
                    $postscore_subgroup = Score::GROUP_GREEN;
                    $postscore_group = 'big';
                } elseif ($score_probability >= 40) {
                    $postscore_subgroup = Score::GROUP_YELLOW;
                    $postscore_group = 'middle';
                } elseif (Variable::IsNumeric($score_probability)) {
                    $postscore_subgroup = Score::GROUP_RED;
                    $postscore_group = 'low';
                } else {
                    $postscore_subgroup = Score::GROUP_GRAY;
                    $postscore_group = 'unknown';
                }
            }
            
            return (object) array(
                'prescore_subgroup' => $prescore_subgroup,
                'prescore_group' => $prescore_group,
                'postscore' => $postscore,
                'postscore_subgroup' => $postscore_subgroup,
                'postscore_group' => $postscore_group,
                'score_probability' => $score_probability,
            );
        }
    }
?>

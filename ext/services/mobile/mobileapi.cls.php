<?php
    
    class MobileOperators {
        const Beeline = 0;
        const Megafon = 1;
        const MTS = 2;
        const MGTS = 3;
        const Yota = 4;
        const Tele2 = 5;
        const Any = -1;
        
    }
    
    /* Описания параметров */
    class MobileConstants {
        public static $operators = array(
            MobileOperators::Any => 'любой',
            MobileOperators::Beeline => 'Билайн',
            MobileOperators::Megafon => 'МегаФон',
            MobileOperators::MGTS => 'МГТС',
            MobileOperators::MTS => 'МТС',
            MobileOperators::Yota => 'Yota',
            MobileOperators::Tele2 => 'Теле2',
        );
        
        public static $operatorsLat = array(
            MobileOperators::Beeline  => 'BEELINE',
            MobileOperators::Megafon  => 'MEGAFON',
            MobileOperators::MTS      => 'MTS',
            MobileOperators::Yota  => 'YOTA',
            MobileOperators::Tele2  => 'TELE2',
        );
        
        public static $number_types = array(
            'Федеральный',
            'Прямой'
        );
        
        public static $tarif_types = array(
            'Предоплатный',
            'Постоплатный'
        );
        
        public static $regions = array(
            Regions::MoscowAndRegion => 'Москва и область',
            Regions::StPiterburgAndRegion => 'Санкт-Петербург и область',
            Regions::KrasnodarAndRegion => 'Краснодар, Краснодарский край и Адыгея',
            Regions::NovosibirskAndRegion => 'Новосибирск и область',
        );
        
        public static $others = array(
            'mms' => 'MMS',
            'intercity' => 'минут межгород',
            'cis' => 'минут в СНГ',
            'international' => 'минут международные',
            'traffic' => ' МБ интернет'
        );
        
        //Ключи массива равны zone_id
        public static $cities = array(
            0 => array('rus' => 'Москва', 'lat' => 'moskva'),
            1 => array('rus' => 'Санкт-Петербург', 'lat' => 'sankt-peterburg'),
            2 => array('rus' => 'Краснодар', 'lat' => 'krasnodar'),
            3 => array('rus' => 'Новосибирск', 'lat' => 'novosibirsk'),
        );
        
        //Возвращает дефолтный регион для оператора
        public static function getDefaultCity($oid){
            if($oid == 5)
                return self::$cities[0]['lat'];
            return self::$cities[0]['lat'];
        }
        
        public static $tele2ApiTypesAliases = array(
            PhoneNumberTypes::Bronze => 'simple',
            PhoneNumberTypes::Argentum => 'silver',
            PhoneNumberTypes::Aurum => 'gold',
            PhoneNumberTypes::Platinum => 'platina'
        );
    }
    
    // Тип ценообразования
    class MobilePricing {
        const Fixed = 0;
        const InDays = 1;
        const InTime = 2;
        const InInterChange = 3;
        const Unit = 4;
    }
    
    class CounterMerging {
        const Merged = '1 за 1';
    }
    
    // Тип счетчика
    class CounterTypes {
        const Call = 1;
        const Message = 2;
        const Traffic = 3;
    }
    
    // Тип сообщения
    class MessageTypes {
        const Any = -1;
        const SMS = 0;
        const MMS = 1;
    }
    
    // Откуда - старт звонка
    class DirectionStarts {
        const Any = -1;
        const FromTown = 2;
        const FromRegion = 4;
        
        public static function ToString($arr) {
            $ret = '';
            foreach($arr as $k => $v) {
                switch($k) {
                    case DirectionStarts::Any:
                        $ret .= ', не важно';
                        break;
                    case DirectionStarts::FromTown:
                        $ret .= ', город: '.$v.'%';
                        break;
                    case DirectionStarts::FromRegion:
                        $ret .= ', область: '.$v.'%';
                        break;
                }
            }
            return substr($ret, 1);
        }        
    }
    
    // Приземление (куда - регион)
    class DirectionEnds {
        const Any = -1;
        
        // russia
        const Local = 2;
        const Intercity = 4;
        
        // СНГ
        const CISCountries = 8;
        
        // международные
        const Europe = 16;
        const USCanada = 32;
        const OtherCountries = 64;

        const International = 128;
        
        public static function ToString($arr, $c = '%') {
            $ret = '';
            foreach($arr as $k => $v) {
                switch($k) {
                    case DirectionEnds::Any:
                        $ret .= ', не важно';
                        break;
                    case DirectionEnds::Local:
                        $ret .= ', локальные: '.$v.$c;
                        break;
                    case DirectionEnds::Intercity:
                        $ret .= ', межгород: '.$v.$c;
                        break;
                    case DirectionEnds::CISCountries:
                        $ret .= ', СНГ: '.$v.$c;
                        break;
                    case DirectionEnds::International:
                        $ret .= ', международные: '.$v.$c;
                        break;
                }
            }
            return substr($ret, 1);
        }        
    }
    
    class Operators {
        const Any = -1;
        const Beeline = 0;
        const Megafon = 1;
        const MTS = 2;
        const MGTS = 3;
        const Yota = 4;
        const Tele2 = 5;
        
        public static function ToString($arr) {
            $ret = '';
            foreach($arr as $k => $v) {
                switch($k) {
                    case Operators::Beeline:
                        $ret .= ', Beeline: '.$v.'%';
                        break;
                    case Operators::Megafon:
                        $ret .= ', Megafon: '.$v.'%';
                        break;
                    case Operators::MTS:
                        $ret .= ', MTS: '.$v.'%';
                        break;
                    case Operators::MGTS:
                        $ret .= ', MGTS: '.$v.'%';
                        break;
                    case Operators::Yota:
                        $ret .= ', Yota: '.$v.'%';
                        break;
                    case Operators::Tele2:
                        $ret .= ', Tele2: '.$v.'%';
                        break;
                }
            }
            return substr($ret, 1);
        }
        
    }
    
    // Куда, оператор
    class DirectionGoals {
        const Any = -1;
        const ToSameMobile = 2;
        const ToOtherMobile = 4;
        const ToCityPhone = 8;
    }
    
    // время звонка
    class TimePeriods {
        const Any = -1;
        const Night = 0;
        const Day = 1;
        const Evening = 2;
        
        public static function ToString($arr) {
            $ret = '';
            foreach($arr as $k => $v) {
                switch($k) {
                    case TimePeriods::Any:
                        $ret .= ', Не важно';
                        break;
                    case TimePeriods::Day:
                        $ret .= ', Днем: '.$v.'%';
                        break;
                    case TimePeriods::Evening:
                        $ret .= ', Вечером: '.$v.'%';
                        break;
                    case TimePeriods::Night:
                        $ret .= ', Ночью: '.$v.'%';
                        break;
                }
            }
            return substr($ret, 1);
        }        
    }
    
    // Валюта
    class Currencies {
        const Any = -1;
        const RUR = 0;
        const USD = 1;
        const EUR = 2;                                             
    }
    
    // Регионы (зоны)
    class Regions {
        const Any = -1;
        const MoscowAndRegion = 0;
        const StPiterburgAndRegion = 1;
        const KrasnodarAndRegion = 2;
        const NovosibirskAndRegion = 3;
    }
    
    // Периоды
    class PeriodTypes {
        const Any = -1;
        const Day = 0;
        const Month = 1;
    }
    
    class NumberTypes {
        const Any = -1;
        const Federal = 0;
        const Direct = 1;
    }
    
    class PhoneNumberTypes {
        const Bronze = 1;
        const Argentum = 2;
        const Aurum = 3;
        const Platinum = 4;
        const Brilliant = 5;
    }
    
    class PaymentTypes {
        const Any = -1;
        const Prepaid = 0;
        const Postpaid = 1;
    }
    
    class TariffNeeds {
        const NeedIPhone = 1;
    }
    
    class ServiceGroups {
        const Calls = 2;
        const Messages = 4;
        const Internet = 8;
        
        public static $groups = array(
            ServiceGroups::Calls    => 'Звонки',
            ServiceGroups::Messages => 'СМС',
            ServiceGroups::Internet => 'Интернет',
        );
        
        public static function GetGroups($gr) {
            $groups = array();
            foreach (array(self::Calls, self::Messages, self::Internet) as $item) {
                if (($gr & $item) == $item) {
                    $groups[$item] = self::$groups[$item];
                }
            }
            return $groups;
        }
    }
    
    class ServiceTypes {
        const DisableRecommended = 2;
        const BonusAction = 4;
        const EnableRecommended = 8;
    }
    
    class SocialGroups {
        const Students = 1;
        const Militaries = 2;
        const HasChildLessThan2Years = 4;
        const Pensioner = 8;
        const Voiceless = 16;
    }
    
    Core::Using('Lib::Services', _PROJECT);
    
    class MobileApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('mobile'));
        }
		
        protected function PostProcessing($res) {
            if (isset($res->found) && $res->found) {
                
                $res->found = (array) $res->found;
                uasort($res->found, function($a, $b) {
                    if ($a->price == $b->price) {
                        return 0;
                    }
                    return ($a->price < $b->price) ? -1 : 1;
                });

				$mytariff = array();
                $merge = array();
                $rows = array();

                foreach ($res->found as $k => $row) {     
                    if ($row->key == 'mytariff') {   
                        $mytariff[$k.'_'] = $row;
                        unset($res->found->$k);
                    }
                    //elseif ((isset($row->sale) && $row->sale) && count($merge) < 2) {
                    //    $merge[$k.'_'] = $row;
                    //    unset($res->found->$k);
                    //}
                    else {
                        $rows[$k.'_'] = $row;
                    }
                }

                $res->found = array_merge($mytariff, $merge, $rows);
            }
            return $res;
        }
        
        public function FromCache($cacheId) {
            
            $res = parent::_request('MobileAjaxHandler.FromCache', array('cid' => $cacheId));
            if(!$res)
                return false;
            
            return $this->PostProcessing($res);
            
        }
        
        public function GetCleanData() {
            
            $res = parent::_request('MobileAjaxHandler.GetCleanData');
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function SearchFor($searchOptions = array()) {
            
            // ******************************************************
            // МАКСИМ!!!!!! УБРАТЬ ОТСЮДА этот код!
            // ******************************************************
            
            $searchOptions = (array)$searchOptions;
            if(isset($searchOptions['callsgoals_temp'])) {
                unset($searchOptions['callsgoals_temp']);
            }  
            if(isset($searchOptions['first_minutes'])) {
                unset($searchOptions['first_minutes']);
            }  
            if(isset($searchOptions['first_trafficcount'])) {
                unset($searchOptions['first_trafficcount']);
            }
            // ******************************************************
            
            $opts = array(
                'reload' => '0',
                'city' => '1',
                'zone' => '1',
                'numbertype' => '0',
                'paymenttype' => '0',
                'calls' => '4',
                'callsavg' => '3',
                'callsgoals' => '{"0": "17","1": "17","2": "17","3": "15","4": "17","5": "17"}',                                                             
                'callsfrom' => json_encode(array('2' => '90', '4' => '10')),
                'callswhen' => json_encode(array('1' => '50', '2' => '40', '0' => '10')),
                'callswhere' => json_encode(array('4' => '0', '8' => '0', '128' => '0')),
                'smscount' => '0',
                'smsperiod' => '1',
                'mmscount' => '0',
                'mmsperiod' => '0',
                'traffic' => 'true',
                'trafficcount' => '2000',
                'trafficwhen' => '{"1":"50","2":"40","0":"10"}',
                'forced' => '{}' ,
                'minutes' => '400',
                'minutesperiod' => '1',
                'myoperator' => '',
                'mytariff' => ''                   
            );
            
            $searchOptions = Variable::Extend($opts, $searchOptions); 
            $res = parent::_request('MobileAjaxHandler.Process', $searchOptions);
            if(!$res)
                return false;
              
            return $this->PostProcessing($res);
            
        }
        
        public function Info(/*array/numeric*/$operatorId = null, /*array/numeric*/$tariffId = null, /*array/numeric*/$serviceId = null, /*array/numeric*/ $roamingId = null, $downloadCounters = false, $downloadAllTariffs = false) {
            
            if (!is_null($operatorId) && is_array($operatorId)) {
                $operatorId = Json::Encode($operatorId);
            }
            
            if (!is_null($tariffId) && is_array($tariffId)) {
                $tariffId = Json::Encode($tariffId);
            }
            
            if (!is_null($serviceId) && is_array($serviceId)) {
                $serviceId = Json::Encode($serviceId);
            }
            
            if (!is_null($roamingId) && is_array($roamingId)) {
                $roamingId = Json::Encode($roamingId);
            }                             
            $res = parent::_request('MobileAjaxHandler.Info', array(
                'operator' => $operatorId, 
                'tariff' => $tariffId,
                'service' => $serviceId,
                'roaming' => $roamingId,
                'dnldcnt' => $downloadCounters ? 1 : 0,
                'dnldall' => $downloadAllTariffs ? 1 : 0
            ));
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function PrecompiledTariffInfo($tariffId) {
            
            $res = parent::_request('MobileAjaxHandler.TariffInfo', array(
                'tariff' => $tariffId
            ));
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function PopularProducts() {
            
            $packets = array();

            $operators = $this->Info(array(MobileOperators::MTS, MobileOperators::Beeline, MobileOperators::Megafon));
            foreach ($operators->operators as $operator) {
                if (isset($operator->departments[0])) {
                    $department = $operator->departments[0];
                    $added = 0;
                    foreach ($department->tariffs as $tarif) {
                        if ($tarif->isonfront == 't') {
                            $descriptions = JSon::Unserialize($tarif->descriptions);
                            $meaning = GetProperty($descriptions, 'meaning');
                            if ($meaning) {
                                $packets[] = (object) array(
                                    'id' => $tarif->iid,
                                    'name' => $tarif->name.' от '.$operator->name,
                                    'company' => $operator->name,
                                    'packet' => $tarif->name,
                                    'opinion' => $meaning,
                                    'logo' => '/project/res/img/ico/'.(isset(MobileConstants::$operatorsLat[$operator->ident]) ? Strings::ToLower(MobileConstants::$operatorsLat[$operator->ident]) : '').'.png',
                                    'company_href' => MobileHandler::Url('tarifs', array('operator' => $operator->ident)),
                                    'packet_href' => MobileHandler::Url('tarif', array('operator' => $operator->ident, 'tarif' => $tarif->name, 'id' => $tarif->iid))
                                );
                                $added++;
                            }
                        }
                        
                        if ($added >= 3) {
                            break;
                        }
                    }
                    
                }
            }
            
            return array(
                'title' => 'Популярные мобильные тарифы',
                'packets' => $packets,
            );
        }
        
    }
    
?>
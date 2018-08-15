<?php
    
    Core::Using('Lib::Services', _PROJECT);
    
    class MortgagePledge {
        const NOT_REQUIRED = '0';
        const CREDIT_OBJECT = '1';
        const OTHER = '2';
    }
    class MortgageSurety {
        const POSSIBLY = '0';
        const REQUIRED = '1';
        const NOT_REQUIRED = '2';
    }
    class MortgageVerification {
        const NOT_REQUIRED = '0';
        const NDFL = '1';
        const BANK_FORM = '2';
    }
    class MortgagePricing {
        const FIXED = '0';
        const FLOATING = '1';
        const COMBI = '2';
    }
    class MortgageConstants {
        public static $verification = array(
            MortgageVerification::NOT_REQUIRED => 'Не требуется',
            MortgageVerification::NDFL => '2-НДФЛ',
            MortgageVerification::BANK_FORM => 'Банковская форма',
        );
        public static $pledge = array(
            MortgagePledge::NOT_REQUIRED => 'Не требуется', 
            MortgagePledge::CREDIT_OBJECT => 'Кредитуемый объект',
            MortgagePledge::OTHER => 'Другая недвижимость',
        );
        public static $surety = array(
            MortgageSurety::POSSIBLY => 'Возможно', 
            MortgageSurety::REQUIRED => 'Требуется',
            MortgageSurety::NOT_REQUIRED => 'Не требуется',
        );
        public static $pricing_method = array(
            MortgagePricing::FIXED => 'Фиксированная',
            MortgagePricing::FLOATING => 'Плавающая',
            MortgagePricing::COMBI => 'Комбинированная',
        );
    }
    
    
    class BanksHypothecCreditsPledge {
        const NOT_REQUIRED = '0';
        const CREDIT_OBJECT = '1';
        const OTHER = '2';
    }
    class BanksHypothecCreditsSurety {
        const POSSIBLY = '0';
        const REQUIRED = '1';
        const NOT_REQUIRED = '2';
    }
    class BanksHypothecCreditsVerification {
        const NOT_REQUIRED = '0';
        const NDFL = '1';
        const BANK_FORM = '2';
    }
    class BanksHypothecCreditsPricing {
        const FIXED = '0';
        const FLOATING = '1';
        const COMBI = '2';
    }
    class BanksHypothecCreditsConstants {
        public static $verification = array(
            BanksHypothecCreditsVerification::NOT_REQUIRED => 'Не требуется',
            BanksHypothecCreditsVerification::NDFL => '2-НДФЛ',
            BanksHypothecCreditsVerification::BANK_FORM => 'Банковская форма',
        );
        public static $pledge = array(
            BanksHypothecCreditsPledge::NOT_REQUIRED => 'Не требуется', 
            BanksHypothecCreditsPledge::CREDIT_OBJECT => 'Кредитуемый объект',
            BanksHypothecCreditsPledge::OTHER => 'Другая недвижимость',
        );
        public static $surety = array(
            BanksHypothecCreditsSurety::POSSIBLY => 'Возможно', 
            BanksHypothecCreditsSurety::REQUIRED => 'Требуется',
            BanksHypothecCreditsSurety::NOT_REQUIRED => 'Не требуется',
        );
        public static $pricing_method = array(
            BanksHypothecCreditsPricing::FIXED => 'Фиксированная',
            BanksHypothecCreditsPricing::FLOATING => 'Плавающая',
            BanksHypothecCreditsPricing::COMBI => 'Комбинированная',
        );
    }
    
    class BanksAutoCreditsPricing {
        const FIXED = '0';
        const FLOATING = '1';
        const COMBI = '2';
    }
    class BanksAutoCreditsVerification {
        const NOT_REQUIRED = '0';
        const NDFL = '1';
        const BANK_FORM = '2';
    }
    class BanksAutoCreditsPledge {
        const NOT_REQUIRED = '0';
        const CREDIT_OBJECT = '1';
        const OTHER = '2';
    }
    class BanksAutoCreditsSurety {
        const POSSIBLY = '0';
        const REQUIRED = '1';
        const NOT_REQUIRED = '2';
    }
    class BanksAutoCreditsConstants {
        public static $pricing_method = array(
            BanksAutoCreditsPricing::FIXED => 'Фиксированная',
            BanksAutoCreditsPricing::FLOATING => 'Плавающая',
            BanksAutoCreditsPricing::COMBI => 'Комбинированная',
        );
        public static $verification = array(
            BanksAutoCreditsVerification::NOT_REQUIRED => 'Не требуется',
            BanksAutoCreditsVerification::NDFL => '2-НДФЛ',
            BanksAutoCreditsVerification::BANK_FORM => 'Банковская форма',
        );
        public static $pledge = array(
            BanksAutoCreditsPledge::NOT_REQUIRED => 'Не требуется', 
            BanksAutoCreditsPledge::CREDIT_OBJECT => 'Кредитуемый объект',
            BanksAutoCreditsPledge::OTHER => 'Другая недвижимость',
        );
        public static $surety = array(
            BanksAutoCreditsSurety::POSSIBLY => 'Возможно', 
            BanksAutoCreditsSurety::REQUIRED => 'Требуется',
            BanksAutoCreditsSurety::NOT_REQUIRED => 'Не требуется',
        );
    }    
    
    class CardsVerification {
        const NOT_REQUIRED = '0';
        const NDFL = '1';
        const BANK_FORM = '2';
    }
    class CardsPaytype {
        const VISA = '0';
        const MASTERCARD = '1';
        const AMEX = '2';
    }
    class CardsCardtype {
        const STANDARD = '0';
        const GOLD = '1';
        const PLATINUM = '2';
        const BLACK = '3';
    }
    class CardsTechnology {
        const CHIP = '0';
        const MAGNET = '1';
    }
    class CardsConstants {
        public static $verification = array(
            BanksHypothecCreditsVerification::NOT_REQUIRED => 'Не требуется',
            BanksHypothecCreditsVerification::NDFL => '2-НДФЛ',
            BanksHypothecCreditsVerification::BANK_FORM => 'Банковская форма',
        );
        public static $paytype = array(
            CardsPaytype::VISA => 'Visa',
            CardsPaytype::MASTERCARD => 'MasterCard',
            CardsPaytype::AMEX => 'American Express',
        );
        public static $cardtype = array(
            CardsCardtype::STANDARD => 'Standard/Classic',
            CardsCardtype::GOLD => 'Gold',
            CardsCardtype::PLATINUM => 'Platinum',
        );
        public static $technology = array(
            CardsTechnology::CHIP => 'Электронный чип',
            CardsTechnology::MAGNET => 'Магнитная полоса',
        );
    }
    
    class ConsumerVerification {
        const NOT_REQUIRED = '0';
        const NDFL = '1';
        const BANK_FORM = '2';
    }
    class ConsumerPledge {
        const NOT_REQUIRED = '0';
        const REALTY = '1';
        const AUTO = '2';
        const OTHER = '3';
    }
    class ConsumerSurety {
        const POSSIBLY = '0';
        const REQUIRED = '1';
        const NOT_REQUIRED = '2';
    }
    class ConsumerPricing {
        const FIXED = '0';
        const FLOATING = '1';
        const COMBI = '2';
    }
    class ConsumerCreditHistory {
        const HAVE_CREDIT_NO_DELAY = '1';
        const HAVE_CREDIT_HAD_DELAY = '2';
        const HAD_CREDIT_NO_DELAY = '3';
        const HAD_CREDIT_HAD_DELAY = '4';
        const NEVER = '5';
    }
    class ConsumerWorkType {
        const WORK_NDFL = '1';
        const WORK_NO_NDFL = '2';
        const WORK_GOVERMENT = '3';
        const WORK_PENSIONER = '4';
        const WORK_IP = '5';
        const NOT_WORKING = '6';
    }
    class ConsumerConstants {
        public static $verification = array(
            ConsumerVerification::NOT_REQUIRED => 'Не требуется',
            ConsumerVerification::NDFL => '2-НДФЛ',
            ConsumerVerification::BANK_FORM => 'Банковская форма',
        );
        public static $pledge = array(
            ConsumerPledge::NOT_REQUIRED => 'Не требуется', 
            ConsumerPledge::REALTY => 'Недвижимость',
            ConsumerPledge::AUTO => 'Автомобиль',
            ConsumerPledge::OTHER => 'Другой',
        );
        public static $surety = array(
            ConsumerSurety::POSSIBLY => 'Возможно', 
            ConsumerSurety::REQUIRED => 'Требуется',
            ConsumerSurety::NOT_REQUIRED => 'Не требуется',
        );
        public static $pricing_method = array(
            ConsumerPricing::FIXED => 'Фиксированная',
            ConsumerPricing::FLOATING => 'Плавающая',
            ConsumerPricing::COMBI => 'Комбинированная',
        );
        public static $credit_history = array(
            ConsumerCreditHistory::HAVE_CREDIT_NO_DELAY => 'Имею кредит, погашаю исправно',
            ConsumerCreditHistory::HAVE_CREDIT_HAD_DELAY => 'Имею кредит, были просрочки платежа',
            ConsumerCreditHistory::HAD_CREDIT_NO_DELAY => 'Брал кредит(ы), просрочек не было',
            ConsumerCreditHistory::HAD_CREDIT_HAD_DELAY => 'Брал кредит(ы), были просрочки',
            ConsumerCreditHistory::NEVER => 'Никогда не брал кредиты',
        );
        public static $work_type = array(
            ConsumerWorkType::WORK_NDFL => 'Наемный работник, есть НДФЛ-2',
            ConsumerWorkType::WORK_NO_NDFL => 'Наемный работник, нет НДФЛ-2',
            ConsumerWorkType::WORK_GOVERMENT => 'Госслужащий',
            ConsumerWorkType::WORK_PENSIONER => 'Пенсионер',
            ConsumerWorkType::WORK_IP => 'Индивидуальный предприниматель',
            ConsumerWorkType::NOT_WORKING => 'Не работаю',
        );
    }
    
    class BanksConsumerCreditsVerification {
        const NOT_REQUIRED = '0';
        const NDFL = '1';
        const BANK_FORM = '2';
    }
    class BanksConsumerCreditsPledge {
        const NOT_REQUIRED = '0';
        const REALTY = '1';
        const AUTO = '2';
        const OTHER = '3';
    }
    class BanksConsumerCreditsSurety {
        const POSSIBLY = '0';
        const REQUIRED = '1';
        const NOT_REQUIRED = '2';
    }
    class BanksConsumerCreditsPricing {
        const FIXED = '0';
        const FLOATING = '1';
        const COMBI = '2';
    }
    class BanksConsumerCreditsCreditHistory {
        const HAVE_CREDIT_NO_DELAY = '1';
        const HAVE_CREDIT_HAD_DELAY = '2';
        const HAD_CREDIT_NO_DELAY = '3';
        const HAD_CREDIT_HAD_DELAY = '4';
        const NEVER = '5';
    }
    class BanksConsumerCreditsWorkType {
        const WORK_NDFL = '1';
        const WORK_NO_NDFL = '2';
        const WORK_GOVERMENT = '3';
        const WORK_PENSIONER = '4';
        const WORK_IP = '5';
        const NOT_WORKING = '6';
    }
    class BanksConsumerCreditsConstants {
        public static $verification = array(
            BanksConsumerCreditsVerification::NOT_REQUIRED => 'Не требуется',
            BanksConsumerCreditsVerification::NDFL => '2-НДФЛ',
            BanksConsumerCreditsVerification::BANK_FORM => 'Банковская форма',
        );
        public static $pledge = array(
            BanksConsumerCreditsPledge::NOT_REQUIRED => 'Не требуется', 
            BanksConsumerCreditsPledge::REALTY => 'Недвижимость',
            BanksConsumerCreditsPledge::AUTO => 'Автомобиль',
            BanksConsumerCreditsPledge::OTHER => 'Другой',
        );
        public static $surety = array(
            BanksConsumerCreditsSurety::POSSIBLY => 'Возможно', 
            BanksConsumerCreditsSurety::REQUIRED => 'Требуется',
            BanksConsumerCreditsSurety::NOT_REQUIRED => 'Не требуется',
        );
        public static $pricing_method = array(
            BanksConsumerCreditsPricing::FIXED => 'Фиксированная',
            BanksConsumerCreditsPricing::FLOATING => 'Плавающая',
            BanksConsumerCreditsPricing::COMBI => 'Комбинированная',
        );
        public static $credit_history = array(
            BanksConsumerCreditsCreditHistory::HAVE_CREDIT_NO_DELAY => 'Имею кредит, погашаю исправно',
            BanksConsumerCreditsCreditHistory::HAVE_CREDIT_HAD_DELAY => 'Имею кредит, были просрочки платежа',
            BanksConsumerCreditsCreditHistory::HAD_CREDIT_NO_DELAY => 'Брал кредит(ы), просрочек не было',
            BanksConsumerCreditsCreditHistory::HAD_CREDIT_HAD_DELAY => 'Брал кредит(ы), были просрочки',
            BanksConsumerCreditsCreditHistory::NEVER => 'Никогда не брал кредиты',
        );
        public static $work_type = array(
            BanksConsumerCreditsWorkType::WORK_NDFL => 'Наемный работник, есть НДФЛ-2',
            BanksConsumerCreditsWorkType::WORK_NO_NDFL => 'Наемный работник, нет НДФЛ-2',
            BanksConsumerCreditsWorkType::WORK_GOVERMENT => 'Госслужащий',
            BanksConsumerCreditsWorkType::WORK_PENSIONER => 'Пенсионер',
            BanksConsumerCreditsWorkType::WORK_IP => 'Индивидуальный предприниматель',
            BanksConsumerCreditsWorkType::NOT_WORKING => 'Не работаю',
        );
    }
    
    class BanksDepositsPricing {
        const FIXED = '0';
        const FLOATING = '1';
        const COMBI = '2';
    }
    class BanksDepositsEnter {
        const CASH = '0';
        const TRANSFER = '1';
    }
    class BanksDepositsPercentPeriod {
        const EVERY_MONTH = '0';
        const END_OF_TERM = '1';
    }
    class BanksDepositsConstants {
        public static $percent_period = array(
            BanksDepositsPercentPeriod::EVERY_MONTH => 'Ежемесячно', 
            BanksDepositsPercentPeriod::END_OF_TERM => 'В конце срока'
        );
        public static $enter_method = array(
            BanksDepositsEnter::CASH => 'Наличные',
            BanksDepositsEnter::TRANSFER => 'Безналичный перевод',
        );
        public static $pricing_method = array(
            BanksDepositsPricing::FIXED => 'Фиксированная',
            BanksDepositsPricing::FLOATING => 'Плавающая',
            BanksDepositsPricing::COMBI => 'Комбинированная',
        );
    }
    
    class BanksMicroCreditsPaymentPeriod {
        const EVERY_WEEK = '0';
        const EVERY_TWO_WEEKS = '1';
        const END_OF_TERM = '2';
    }
    class BanksMicroCreditsPledge {
        const NOT_REQUIRED = '0';
        const REALTY = '1';
        const AUTO = '2';
        const OTHER = '3';
    }
    class BanksMicroCreditsConstants {
        public static $payment_period = array(
            BanksMicroCreditsPaymentPeriod::EVERY_WEEK => 'Раз в неделю', 
            BanksMicroCreditsPaymentPeriod::EVERY_TWO_WEEKS => 'Раз в 2 недели',
            BanksMicroCreditsPaymentPeriod::END_OF_TERM => 'В конце срока'
        );
        public static $pledge = array(
            BanksMicroCreditsPledge::NOT_REQUIRED => 'Не требуется', 
            BanksMicroCreditsPledge::REALTY => 'Недвижимость',
            BanksMicroCreditsPledge::AUTO => 'Автомобиль',
            BanksMicroCreditsPledge::OTHER => 'Другой',
        );
    }
    
    class BanksConstants {
        public static $currency = array(
            '1' => '$',
            '2' => '€',
            '3' => 'руб.',
        );
    }
    
    
    function RiseBankOnTop($count, $bankid, & $res, $secondary_param = false) {
        $search = array();
        
        foreach ($res as $index => $row) {
            if ($row->bank == $bankid) {
                
                $key = ($secondary_param) ? $row->$secondary_param : 'common';
                
                if ( ! isset($search[$key]) || count($search[$key]) < $count) {
                    $row->color = true;
                    $search[$key][] = $row;
                    unset($res[$index]);
                }
            }
        }
        
        foreach ($search as $index => $found) {
            for ($i = count($found) - 1; $i >= 0; $i--) {
                array_unshift($res, $found[$i]);
            }
        }
    }
    
    class MortgageApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('banks'));
        }
        
        public function SearchFor($searchOptions = array()) {

            $opts = array(
                'housing' => '2',
                'city' => '1',
                'currency' => '3',
                'object_price' => 6000000,
                'price' => 2700000,
                'years' => 10,
                'verification' => MortgageVerification::NDFL,
                'age' => 28,
            );

            $searchOptions = Variable::Extend($opts, $searchOptions);
            $res = parent::_request('HypothecBanksAjaxHandler.Process', $searchOptions);
            
            if(!$res)
                return false;
                
            return $res;
            
        }
        
        public function GetCleanData() {
            
            $res = parent::_request('HypothecBanksAjaxHandler.GetCleanData');
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function FromCache($cacheId) {
            
            $res = parent::_request('HypothecBanksAjaxHandler.FromCache', array('cid' => $cacheId));
            if(!$res)
                return false;
            
            return $res;
            
        }
        
    }

    class BanksHypothecCreditsApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('banks'));
        }
        
        public function SearchFor($searchOptions = array()) {

            $opts = array(
                'housing' => '2',
                'city' => '1',
                'currency' => '3',
                'object_price' => 6000000,
                'price' => 2700000,
                'years' => 10,
                'verification' => BanksHypothecCreditsVerification::NDFL,
                'age' => 28,
            );

            $searchOptions = Variable::Extend($opts, $searchOptions);
            $res = parent::_request('HypothecBanksAjaxHandler.Process', $searchOptions);
            
            if(!$res)
                return false;
                
            return $res;
            
        }
        
        public function GetCleanData() {
            
            $res = parent::_request('HypothecBanksAjaxHandler.GetCleanData');
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function FromCache($cacheId) {
            
            $res = parent::_request('HypothecBanksAjaxHandler.FromCache', array('cid' => $cacheId));
            if(!$res)
                return false;
            
            return $res;
            
        }
        
    }

    class BanksAutoCreditsApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('banks'));
        }
        
        public function SearchFor($searchOptions = array()) {
            
            $opts = array(
                'age' => 28,
                'brand' => '0',
                'brand_origin' => 'car_for',
                'city' => '1',          
                'currency' => '3',
                'kasko' => '1',
                'is_new' => 'car_new',
                'months' => 36,   
                'object_price' => 800000,
                'price' => 500000, 
                'unofficial_dealer' => '0',      
                'verification' => BanksAutoCreditsVerification::NDFL,
                                            
                //к удалению
                'annuitet' => 1,
                'collateral' => '',
                'insurance' => 'complex',
                'pricing_method' => '',
                'subsidy' => '1',
            );
            
            /*out($searchOptions);
            out($opts);
            exit;*/
            
            $searchOptions = Variable::Extend($opts, $searchOptions);
                                    
            $res = parent::_request('AutocreditsBanksAjaxHandler.Process', $searchOptions);
            if(!$res)
                return false;
            
            $this->ProcessResults($res);
            
            return $res;
            
        }
        
        public function GetCleanData() {
            
            $res = parent::_request('AutocreditsBanksAjaxHandler.GetCleanData');
            if(!$res)
                return false;
            
            $this->ProcessResults($res);
            
            return $res;
            
        }
        
        public function FromCache($cacheId) {
            
            $res = parent::_request('AutocreditsBanksAjaxHandler.FromCache', array('cid' => $cacheId));
            if(!$res)
                return false;
            
            $this->ProcessResults($res);
            
            return $res;
            
        }
        
        protected function ProcessResults( & $result) {
            if (isset($result->results) && $result->results) {
                //$this->_cleanAutoResults($result->results);
				//RiseBankOnTop(1, 1, $result->results);
            }
        }
        
        protected function _cleanAutoResults(& $res) {
            $template = array(
                'RUR' => array(
                    't' => false,
                    'f' => false,
                ),
                'CUR' => array(
                    't' => false,
                    'f' => false,
                ),
            );
            $fill = array();
            
            foreach ($res as $index => $row) {
                $currency = $row->currency == 3 ? 'RUR' : 'CUR';
                $key = $row->bank . ' ' . $row->name;
                
                if ( ! isset($fill[$key])) {
                    $fill[$key] = $template;
                }
                
                if ( ! $fill[$key][$currency][$row->need_liveinsurance]) {
                    $fill[$key][$currency][$row->need_liveinsurance] = true;
                }
                else {
                    unset($res[$index]);
                }
            }
        }

    }
    
    class BanksDepositsApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('banks'));
        }
        
        public function SearchFor($searchOptions = array()) {
            
            $opts = array(
                'city' => '1',
                'currency' => '3',
                'months' => 12,
                'price' => 100000,
                'refill' => 0,
                'pay_percent_period' => BanksDepositsPercentPeriod::END_OF_TERM,
                'multicurrency' => 'N',
                'pensioner' => 'N',
                'may_update' => 'N',
                'may_take_part' => 'N',
            );
            
            $searchOptions = Variable::Extend($opts, $searchOptions);
            
            $res = parent::_request('DepositsBanksAjaxHandler.Process', $searchOptions);
            if(!$res)                           
                return false;
            
            $this->ProcessResults($res);
            
            return $res;
            
        }
        
        public function FromCache($cacheId) {
            
            $res = parent::_request('DepositsBanksAjaxHandler.FromCache', array('cid' => $cacheId));
            if(!$res)
                return false;
            
            $this->ProcessResults($res);
            
            return $res;
            
        }
        
        protected function ProcessResults( & $result) {
            if (isset($result->results) && $result->results) {
                //RiseBankOnTop(1, 5, $result->results);
            }
        }
        
        public function GetCleanData() {
            $res = parent::_request('DepositsBanksAjaxHandler.GetCleanData');
            if(!$res)
                return false;
            
            return $res;
            
        }
        
    }
    
    class ConsumerApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('banks'));
        }
        
        public function SearchFor($searchOptions = array()) {
            
            $opts = array(
                'refinance' => '0',
                'city' => '1',
                'currency' => '3',
                'price' => 500000,
                'months' => 24,
                'age' => 28,
                'verification' => BanksConsumerCreditsVerification::NDFL,
                'pledge' => 'any',
                'client' => '0',
            );
            
            $searchOptions = Variable::Extend($opts, $searchOptions);
                                    
            $res = parent::_request('ConsumerBanksAjaxHandler.Process', $searchOptions);
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function FromCache($cacheId) {
            
            $res = parent::_request('ConsumerBanksAjaxHandler.FromCache', array('cid' => $cacheId));
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function GetCleanData() {
            
            $res = parent::_request('ConsumerBanksAjaxHandler.GetCleanData');
            if(!$res)
                return false;
            
            return $res;
            
        }

    }
    
    class BanksConsumerCreditsApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('banks'));
        }
        
        public function SearchFor($searchOptions = array()) {
            
            $opts = array(
                'refinance' => '0',
                'city' => '1',
                'currency' => '3',
                'price' => 500000,
                'months' => 24,
                'age' => 28,
                'verification' => BanksConsumerCreditsVerification::NDFL,
                'pledge' => 'any',
                'client' => '0',
            );
            
            $searchOptions = Variable::Extend($opts, $searchOptions);
                                    
            $res = parent::_request('ConsumerBanksAjaxHandler.Process', $searchOptions);
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function FromCache($cacheId) {
            
            $res = parent::_request('ConsumerBanksAjaxHandler.FromCache', array('cid' => $cacheId));
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function GetCleanData() {
            
            $res = parent::_request('ConsumerBanksAjaxHandler.GetCleanData');
            if(!$res)
                return false;
            
            return $res;
            
        }

    }
    
    class CardsApi extends ServiceApi {

        public static $sorting = null;
        
        public function __construct() {
            parent::__construct($this->_serviceUrl('banks'));
        }
        
        public function SearchFor($searchOptions = array(), $filter = array(), $savestatsOnly = false) {
            
            $opts = array(
                'city' => '1',
                'currency' => '3',
                'price' => 100000,
                'target' => '2',
                'bank' => null,
                'verification' => CardsVerification::NDFL,
                'cardtype' => '',
                'isvisa' => 'N',
                'ismastercard' => 'N',
                'isreqbonus1' => 'N',
                'isreqbonus2' => 'N',
                'isreqbonus3' => 'N',
                'isreqbonus4' => 'N',
                'iselectronic' => 'N',
                'ispaywaypaypass' => 'N',
                'color' => '',
                'order' => 'prices',
                'type' => 'all'
            );
            
            $searchOptions = Variable::Extend($opts, $searchOptions);
            $searchOptions['savestatsonly'] = $savestatsOnly;
                                    
            $res = parent::_request('CardsBanksAjaxHandler.Process', $searchOptions);
            if(!$res)
                return false;

            $res->unfiltered_count = isset($res->results) && $res->results ? count($res->results) : 0;
            $res = $this->ApplyFilter($res, $filter);
            $res->filter = (object) array();
            
            return $res;
            
        }
        
        public function FromCache($cacheId, $filter = array()) {
            
            $res = parent::_request('CardsBanksAjaxHandler.FromCache', array(
                'cid' => $cacheId,
            ));
            if( ! $res)
                return false;
            
            /*$res->unfiltered_count = isset($res->results) && $res->results ? count($res->results) : 0;
            $res = $this->ApplyFilter($res, $filter);
            $res->filter = (object) $filter; */
            
            return $res;
            
        }
        
        public function ApplyFilter($res, $filter) {

            if ( ! isset($res->results) || ! $res->results) {
                return $res;
            }
            
            $res->filtered = $res->results;
            if ($filter && isset($res->filtered) && $res->filtered) {
                
                CardsApi::$sorting = isset($filter['sorting']) && $filter['sorting'] ? $filter['sorting'] : 'effective_rate';
                uasort($res->filtered, function($a, $b) {
                    $sorting = CardsApi::$sorting;
                    if ($a->$sorting == $b->$sorting) {
                        if ($a->effective_rate == $b->effective_rate) {
                            return 0;
                        }
                        return ($a->effective_rate < $b->effective_rate) ? -1 : 1;
                    }
                    if ($sorting == 'grace_period') {
                        return ($a->$sorting > $b->$sorting) ? -1 : 1;
                    }
                    elseif ($sorting == 'maintenance') {
                        return (
                                ($a->maintenance * $a->maintenance_rate) + ($a->account_maintenance * $a->account_maintenance_rate)
                                <
                                ($b->maintenance * $b->maintenance_rate) + ($b->account_maintenance * $b->account_maintenance_rate)
                            ) ? -1 : 1;
                    }
                    else {
                        return ($a->$sorting < $b->$sorting) ? -1 : 1;
                    }
                    
                });
                
                // Формирование фильтра
                $expression = ' && true';
                /*if (isset($filter['paytype']) && $filter['paytype']) {
                    $expr = '';
                    foreach ($filter['paytype'] as $paytype) {
                        $expr .= ' || $row->paytype == \''.$paytype.'\'';
                    }
                    $expression .= ' && ('.substr($expr, 4).')';
                } else {
                    $expression .= ' && false';
                }*/
                /*if (isset($filter['cardtype']) && $filter['cardtype']) {
                    $expr = '';
                    foreach ($filter['cardtype'] as $cardtype){
                        $expr .= ' || $row->cardtype == \''.$cardtype.'\'';
                    }
                    $expression .= ' && ('.substr($expr, 4).')';
                } else {
                    $expression .= ' && false';
                }*/
                
                if (isset($filter['miles']) && $filter['miles']) $expression .= ' && $row->miles == \'t\'';
                if (isset($filter['discounts']) && $filter['discounts']) $expression .= ' && $row->discounts == \'t\'';
                if (isset($filter['cashback']) && $filter['cashback']) $expression .= ' && $row->cashback == \'t\'';
                if (isset($filter['chip']) && $filter['chip']) $expression .= ' && $row->technology == \''.CardsTechnology::CHIP.'\'';
                //if (isset($filter['paysec']) && $filter['paysec']) $expression .= ' && $row->paywavepass == \'t\'';
                
                if (isset($filter['color']) && $filter['color']) {
                    $expr = '';
                    foreach ($filter['color'] as $color){
                        $expr .= ' || (strpos(\',\'.$row->color.\',\', \','.$color.',\') !== false)';
                    }
                    $expression .= ' && ('.substr($expr, 4).')';
                }
                if (isset($filter['theme']) && $filter['theme']) {
                    $expr = '';
                    foreach ($filter['theme'] as $theme){
                        $expr .= ' || (strpos(\',\'.$row->theme.\',\', \','.$theme.',\') !== false)';
                    }
                    $expression .= ' && ('.substr($expr, 4).')';
                }
                if (isset($filter['bank']) && $filter['bank']) {
                    $expression .= ' && in_array($row->bank, array('. implode(',', $filter['bank']) .'))';
                }
                
                $expression = substr($expression, 4);
                
                // Сама фильтрация
                foreach ($res->filtered as $i => $row) {
                    eval('$bool = '.$expression.';');
                    if ( ! $bool) {
                        unset($res->filtered[$i]);
                    }
                }
            }
            
            return $res;
        }
        
        public function GetCleanData() {
            
            $res = parent::_request('CardsBanksAjaxHandler.GetCleanData');
            if(!$res)
                return false;
            
            return $res;
            
        }

    }
    
    class BanksMicroCreditsApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('banks'));
        }
        
        public function SearchFor($searchOptions = array()) {
            
            $opts = array(
                'city' => '1',
                'price' => 5000,
                'currency' => 3,
                'weeks' => 2,
                'online' => 'Y',
                'verification' => 'Y',
                'age' => 28,
            );
            
            $searchOptions = Variable::Extend($opts, $searchOptions);
                                    
            $res = parent::_request('MicrocreditsBanksAjaxHandler.Process', $searchOptions);
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function FromCache($cacheId) {
            
            $res = parent::_request('MicrocreditsBanksAjaxHandler.FromCache', array('cid' => $cacheId));
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function GetCleanData() {
            
            $res = parent::_request('MicrocreditsBanksAjaxHandler.GetCleanData');
            if(!$res)
                return false;
            
            return $res;
            
        }

    }
    
    class BankApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('banks'));
        }
        
        public function Info($bank_id = null, $type = null, $pid = null) {
            
            $res = parent::_request('InfoBanksAjaxHandler.Info', array(
                'bank_id' => $bank_id,
                'type' => $type,
                'pid' => $pid,
            ));
            
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        /*
        * @type ('hypothec', 'autocredits', 'consumer', 'deposits', 'cards')
        */
        public function PopularProducts($type, $flag = null, $offset = 0) {
            
            $href_types = array(
                'hypothec' => array(
                    'href' => 'bank_mortgage',
                    'title' => 'Популярные ипотечные программы',
                ),
                'autocredits' => array(
                    'href' => 'bank_auto',
                    'title' => 'Популярные автокредиты',
                ),
                'cards' => array(
                    'href' => 'bank_card',
                    'title' => 'Популярные кредитные карты',
                ),
                'consumer' => array(
                    'href' => 'bank_consumer',
                    'title' => 'Популярные кредиты наличными',
                ),
                'deposits' => array(
                    'href' => 'bank_deposits',
                    'title' => 'Популярные депозиты',
                ),
            );
            
            if ( ! isset($href_types[$type]))
                return array();
            
            $res = parent::_request('InfoBanksAjaxHandler.PopularProducts', array(
                'type' => $type,
                'flag' => $flag,
            ));
            
            if ( ! $res)
                return array();
            
            $return = array(
                'title' => $href_types[$type]['title'],
                'packets' => array(),
            );
            
            $items = Navigator::$i->domain->Query('./page[@name=\'bank\']/page[@name=\'kompaniya\']/page[@oid]');
            $offset_count = 1;
            $i = 1;
            foreach ($items as $item) {
                if ($offset_count < $offset) {
                    $offset_count++;
                    continue;
                }
                
                $oid = $item->attributes->oid->value;
                $bank_alias = $item->attributes->name->value;
                if (isset($res->{$oid}) ) {
                    $packet = $res->{$oid};
                    $packet->name = $packet->packet . ' в ' . $packet->company;
                    $packet->logo = GetStaticImg($packet->logo, 120);
                    $packet->company_href = BankHandler::Url($href_types[$type]['href'], array('bank' => $bank_alias));
                    $packet->packet_href = BankHandler::Url($href_types[$type]['href'], array('bank' => $bank_alias, 'program' => Strings::CreateHID($packet->packet.'-'.$packet->id)));
                    $return['packets'][] = $packet;
                    $i++;
                }

                if ($i > 9) {
                    break;
                }
            }
            
            return $return;

        }
        
        public function AllProducts($type) {
            
            $href_types = array(
                'hypothec' => array(
                    'href' => 'bank_mortgage',
                ),
                'autocredits' => array(
                    'href' => 'bank_auto',
                ),
                'cards' => array(
                    'href' => 'bank_card',
                ),
                'consumer' => array(
                    'href' => 'bank_consumer',
                ),
                'deposits' => array(
                    'href' => 'bank_deposits',
                ),
            );
            
            if ( ! isset($href_types[$type]))
                return array();
            
            $res = parent::_request('InfoBanksAjaxHandler.AllProducts', array(
                'type' => $type,
            ));
            
            if ( ! $res)
                return array();
            
            foreach ($res as $company) {
                foreach ($company as $packet) {
                    $packet->company_href = BankHandler::Url($href_types[$type]['href'], array('bank' => $packet->company_hid));
                    $packet->packet_href = BankHandler::Url($href_types[$type]['href'], array('bank' => $packet->company_hid, 'program' => Strings::CreateHID($packet->name.'-'.$packet->pid)));
                }
            }
            
            return $res;
        }
        
        public function GetLatestCalcs($type) {
            
            $res = parent::_request('InfoBanksAjaxHandler.GetLatestCalcs', array(
                'type' => $type,
            ));
            
            if ( ! $res)
                return array();
            
            return $res;
            
        }
        
        public function GetProductList() {
            
            $res = parent::_request('InfoBanksAjaxHandler.ProductList', array());
            
            if ( ! $res)
                return array();
            
            return $res;
            
        }
        
        public function GetBankUnits($filter) {
            $res = parent::_request('InfoBanksAjaxHandler.BankUnits', array('filter' => $filter));
            
            if (!$res)
                return array();
            
            return $res;
        }
        
        public function GetBaseCalcs($type) {
            return parent::_request('InfoBanksAjaxHandler.GetBaseCalcs', array(
                'type' => $type,
            ));
        }
        
        public function RawInfo($bank = false, $pid = false, $downloadPids = false) {
            return parent::_request('InfoBanksAjaxHandler.PartnersRawInfo', array(
                'bank' => $bank,
                'pid' => $pid,
                'downloadpids' => $downloadPids,
            ));
        }
        
        public function UnitInfo($bank = false, $downloadAtms = true, $downloadOffices = true, $region = false, $unit = null, $offset = 0, $limit = 10) {
            return parent::_request('InfoBanksAjaxHandler.PartnersUnitInfo', array(
                'bank' => $bank,
                'downloadAtms' => $downloadAtms,
                'downloadOffices' => $downloadOffices,
                'region' => $region,
                'unit' => $unit,
                'offset' => $offset,
                'limit' => $limit,
            ));
        }
        
        public function BestDeposits() {
            return parent::_request('InfoBanksAjaxHandler.BestDeposits', array());
        }
        
    }
?>
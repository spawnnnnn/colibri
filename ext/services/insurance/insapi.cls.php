<?php
    
    Core::Using('Lib::Services', _PROJECT);
    
    class CascoApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('insurance'));
        }
        
        public function SearchFor($searchOptions = array()) {

            $opts = array(
                'vehbrand' => 'acura',
                'vehmodel' => 'mdx',
                'vehyear' => 2009,
                'vehpower' => 150,
                'vehregion' => 'moskva',
                'vehcost' => 1500000,
                'vehwheel' => 0,
                'vehcredit' => 0,
                'vehbank' => '',
                'juridical' => 0,
                'drvcount' => 1,
                'drvgender' => 'M',
                'drvage' => '30',
                'drvexp' => '10',
                'drvfamily' => '0',
                'drvchild' => '0',
                'pubrand' => -1,
                'pumodel' => -1,
                'psbrand' => -1,
                'psmodel' => -1,
                'franch' => 0,
                'riskcover' => 'full',
                'avarkom' => 0,
                'refund' => 1,
                'evacuation' => 0,
                'techhelp' => 0,
                'norefpay' => 0,
                'dagosum' => 0,
                'nssum' => 0,
                'docost' => 0,
                'paymenttype' => 1
            );

            $searchOptions = Variable::Extend($opts, $searchOptions);
            
            $res = parent::_request('CascoAjaxHandler.Calculate', $searchOptions);
            if(!$res)
                return false;

            if (isset($res->casco)) {
                $discount = new DiscountCasco();
                $discount->Process($res->casco, array(
                    'riskcover' => $searchOptions['riskcover']
				));
                
                $this->_resultsModify($res->casco, array(
                    'vehregion' => $searchOptions['vehregion']
                ));
                
                if (isset($res->score)) {
                    $this->_homingRenes($res->casco, $res->score, $res->cid);
                }
            }

            return $res;
            
        }

        public function GetVehModels($vehbrand) {
            $res = parent::_request('CascoAjaxHandler.GetVehModels', array('vehbrand' => $vehbrand));
            
            if (!$res) return false;
            
            return $res;
        }
        
        public function GetVehCost($vehbrand, $vehmodel, $vehpower) {
            $res = parent::_request('CascoAjaxHandler.GetVehCost', array(
                'vehbrand' => $vehbrand,
                'vehmodel' => $vehmodel,
                'vehpower' => $vehpower
            ));

            if (!$res) return false;
            
            return $res;
        }
        
        public function GetCleanData($vehbrand = '') {
            return $this->_request('CascoAjaxHandler.GetCleanData', array(
                'vehbrand' => $vehbrand,
            ));
        }
        
        public function GetCachedData($cid) {
		    $result = $this->_request('CascoAjaxHandler.GetCachedData', array(
                'cid' => $cid
            ));

			if (isset($result->results->casco)) {
                $discount = new DiscountCasco();
                $discount->Process($result->results->casco, array(
                    'riskcover' => $result->params->riskcover
				));
                
                $this->_resultsModify($result->results->casco, array(
                    'vehregion' => $result->params->vehregion
                ));
                
                if (isset($result->results->score)) {
                    $this->_homingRenes($result->results->casco, $result->results->score, $result->results->cid);
                }
            }

            return $result;
        }
        
        /*public function Details($cid, $company_id, $packet_id) {
            $result = $this->_request('CascoAjaxHandler.Details', array(
                'cid' => $cid,
                'company_id' => $company_id,
                'packet_id' => $packet_id,
            ));

			if (isset($result->details)) {
                $discount = new DiscountCasco();
                $discount->ProcessItem($result->details);
			}

            return $result;
        }
        
        public function Comparison($cid, $ids) {
            $result = $this->_request('CascoAjaxHandler.Comparison', array(
                'cid' => $cid,
                'ids' => $ids
            ));

            if (count($result) > 0) {
                $discount = new DiscountCasco();
                $discount->Process($result);
            }

			return $result;
        }*/
        
        public function PartnersInfo() {
            return $this->_request('CascoAjaxHandler.PartnersInfo');
        }
        
        public function BaseCalcInfo() {
            return $this->_request('CascoAjaxHandler.BaseCalcInfo');
        }
        
        protected function _resultsModify( & $rows, $params = null) {
            //return null;
            $rows = (array) $rows;
            if (isset($params['vehregion'])) {
                if ($params['vehregion'] == 'ekaterinburg' || $params['vehregion'] == 'novosibirsk' || $params['vehregion'] == 'samara' || $params['vehregion'] == 'murmansk' || $params['vehregion'] == 'novokuzneck' || $params['vehregion'] == 'cheljabinsk' || $params['vehregion'] == 'perm' || $params['vehregion'] == 'volgograd' || $params['vehregion'] == 'vladivostok' || $params['vehregion'] == 'ufa' || $params['vehregion'] == 'krasnojarsk' || $params['vehregion'] == 'krasnodar' || $params['vehregion'] == 'lipeck' || $params['vehregion'] == 'barnaul' || $params['vehregion'] == 'tjumen' || $params['vehregion'] == 'habarovsk' || $params['vehregion'] == 'omsk' || $params['vehregion'] == 'tomsk') {
                    $to_kill = array('renes' => true, 'soglasie' => true);
                    foreach ($rows as $key => $row) {
                        if (isset($to_kill[$row->company_key])) {
                            unset($rows[$key]);
                            continue;
                        }
                        else {
                            break;
                        }
                    }
                }
            }
        }
        
        protected function _homingRenes( & $rows, $score, $cid) {
            if (Request::$i->get->reninstest == true) {
                return null;
            }
            
            $koef = 0;
            trY {
                $discount = $this->_getAvailRenesDiscount($score);
                // TODO DELETE
                //$discount = 10;
                // TODO DELETE
                // TODO DELETE
                //$rows['renes::confident'] = $rows['renes::packet_1'];
                //$rows['renes::confident']->total = 47000;
                //unset($rows['renes::packet_1']);
                // TODO DELETE
                
                $scheme = $this->_getRenesScheme($rows);
                
                $rows = (array) $rows;
                
                $pos = $index = 0;
                
                foreach ($rows as $row) {
                    $index++;
                    
                    if ( ! $scheme[$index]->type) { continue; }
                    
                    $pos++;
                    
                    if ($row->company_key == 'renes') {
                        $key = ($row->packet_key == 'confident') ? 'trim' : 'full';
                        if ($pos == 1) {
                            //$koef = $this->_getRenesDiscountFirst($row->total, $key, $scheme, $index);
                            break;
                        }
                        elseif ($pos >= 2) {
                            $koef = $this->_getRenesDiscountSecond($row->total, $key, $scheme, $index, $discount);
                        }
                        
                        if ($koef) { break; }
                    }
                }
            }
            catch (BaseException $ex) {
                $koef = 0;
            }
            
            if ($koef) {
                foreach (array('renes::confident', 'renes::packet_1') as $key) {
                    if (isset($rows[$key])) {
                        $rows[$key]->discount += $koef;
                        $rows[$key]->discounts->homing = $koef;
                        $rows[$key]->total = round($rows[$key]->total - ($rows[$key]->total * $koef / 100));
                    }
                }
                
                $cookie_name = 'homing_'.$cid;
                $cookie_value = Encryption::Encrypt('renes', (string) $koef);
                if (Request::$i->cookie->$cookie_name != $cookie_value) {
                    $dtp = new DataPoint('postgres');
                    $dtp->QueryNonInfo('INSERT INTO homing (cid, homing) VALUES ('.$cid.', '.$koef.')');
                }
                setcookie($cookie_name, $cookie_value, 0, '/');
                
                uasort($rows, function($a, $b) {
                    if ($a->total == $b->total) {
                        return 0;
                    }
                    return ($a->total < $b->total) ? -1 : 1;
                });
            }
        }
        
        protected function _getAvailRenesDiscount($score) {
            if ($score > 21) { return 10; }
            elseif ($score > 19) { return 9; }
            elseif ($score > 17) { return 8; }
            elseif ($score > 15) { return 7; }
            elseif ($score > 13) { return 6; }
            elseif ($score > 11) { return 5; }
            elseif ($score > 7) { return 3; }
            else { return 0; }
        }
        
        protected function _getRenesScheme( & $rows) {
            
            $scheme = array();
            
            $rows = (array) $rows;
            
            $i = 0;
            foreach ($rows as $key => $row) {
                if ($row->company_key == 'toi' ||
                    $row->packet_key == 'pragmatist' ||
                    $row->packet_key == 'pragmatist_plus' ||
                    $row->packet_key == 'ing_byudzhet' ||
                    $row->packet_key == 'ing_bronze' ||
                    $row->packet_key == 'packet_50x50' ||
                    $row->packet_key == 'rgs_econom'
                ) {
                    $key = '';
                }
                elseif ($row->packet_key == 'uralsib_smart' || 
                        $row->packet_key == 'premium_profi' ||
                        $row->packet_key == 'premium_profi_econom' ||
                        $row->packet_key == 'optimal_profi' ||
                        $row->packet_key == 'optimal_profi_econom' ||
                        $row->packet_key == 'optimal_profi_econom' ||
                        $row->packet_key == 'ing_vygoda' ||
                        $row->packet_key == 'ing_econom' ||
                        $row->packet_key == 'ing_profi' ||
                        $row->packet_key == 'confident'
                ) {
                    $key = 'trim';
                }
                else {
                    $key = 'full';
                }
                
                $i++;
                
                $scheme[$i] = (object) array('type' => $key, 'total' => $row->total);
            }
            
            return $scheme;
        }
        
        protected function _getRenesDiscountFirst($price, $key, $scheme, $index) {
            $koef = 0;
            $count = count($scheme);
            for ($i = $index+1; $i <= $count; $i++) {
                if ($scheme[$i]->type == $key) {
                    $new_price = round(max($price, $scheme[$i]->total - ($scheme[$i]->total*2/100)));
                    $koef = (round($price / $new_price, 2) - 1) * 100;
                    break;
                }
            }
            
            return $koef;
        }
        
        protected function _getRenesDiscountSecond($price, $key, $scheme, $index, $discount) {
            $koef = 0;
            
            if ($discount <= 0) {
                return $koef;
            }
            
            $second = 0;
            for ($i = 1; $i < $index; $i++) {
                if ($scheme[$i]->type == $key) {
                    $second++;
                    if ($second == 1) {
                        $new_price = max($price - ($price*$discount/100), $scheme[$i]->total - ($scheme[$i]->total*2/100));
                        $koef = (1 - round($new_price / $price, 2))*100;
                        break;
                    } 
                    
                }
            }
            
            return $koef;
        }
    }
    
    class OsagoApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('insurance'));
        }
        
        public function GetCleanData($vehbrand = '') {
            return $this->_request('OsagoAjaxHandler.GetCleanData', array(
                'vehbrand' => $vehbrand,
            ));
        }
        
        public function GetCachedData($cid) {
            return $this->_request('OsagoAjaxHandler.GetCachedData', array(
                'cid' => $cid
            ));
        }
        
        public function SearchFor($searchOptions = array()) {

            $opts = array(
                "vehtype" => "1",
                "vehpower" => "100",
                "vehregion" => "moskva",
                "juridical" => "0",
                "drvcount" => "1",
                "drvage" => "18",
                "drvexp" => "0",
                "bonusmalus" => "0",
                "term" => "10",
            );

            $searchOptions = Variable::Extend($opts, $searchOptions);
            
            $res = parent::_request('OsagoAjaxHandler.Calculate', $searchOptions);
            if(!$res)
                return false;
            
            return $res;
            
        }

    }
    
    class GreencardApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('insurance'));
        }
        
        public function GetCleanData() {
            return $this->_request('GreencardAjaxHandler.GetCleanData');
        }
        
        public function GetCachedData($cid) {
            return $this->_request('GreencardAjaxHandler.GetCachedData', array(
                'cid' => $cid
            ));
        }
        
        public function SearchFor($searchOptions = array()) {

            $opts = array(
                'zone' => 'Вся Европа',
                'vehtype' => 'Легковые автомобили',
                'term' => '12',
            );

            $searchOptions = Variable::Extend($opts, $searchOptions);
            
            $res = parent::_request('GreencardAjaxHandler.Calculate', $searchOptions);
            if(!$res)
                return false;
            
            return $res;
            
        }

    }
    
    class VzrApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('insurance'));
        }
        
        public function GetCleanData() {
            return $this->_request('VzrAjaxHandler.GetCleanData');
        }
        
        public function GetCachedData($cid) {
            return $this->_request('VzrAjaxHandler.GetCachedData', array(
                'cid' => $cid
            ));
        }
        
        public function SearchFor($searchOptions = array()) {

            $opts = array(
                'currency' => 'EUR',
                'triptype' => 'однократная',
                'tripgoal' => 'туризм',
                'countries' => 'world_germanija',
                'datebeg' => time() + Date::DAY ,
                'dateend' => time() + Date::DAY + (Date::DAY * 10),
                'medicalsum' => 30000,
                'accidentsum' => 0,
                'luggagesum' => 0,
                'gosum' => 0,
                'inscount' => 1,
                'tourbirth1' => mktime(0, 0, 0, 8, 15, 1990),
            );

            $searchOptions = Variable::Extend($opts, $searchOptions);
            
            $res = parent::_request('VzrAjaxHandler.Calculate', $searchOptions);
            if(!$res)
                return false;
            
            return $res;
            
        }

    }
    
    class InsuranceApi extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('insurance'));
        }
        
        public function Info($company_id = null) {
            return $this->_request('InsuranceAjaxHandler.Info', array(
                'company_id' => $company_id
            ));
        }
        
        public function AddTask($executor, $settings) {
            return $this->_request('TasksAjaxHandler.AddTask', array(
                'executor' => $executor,
                'settings' => $settings,
            ));
        }
        
        public function DoTask($executor, $settings) {
            return $this->_request('TasksAjaxHandler.DoTask', array(
                'executor' => $executor,
                'settings' => $settings,
            ));
        }
        
        public function PopularOsagoProducts() {
            
            $packets = array();

            $companies = $this->Info();  

            foreach ($companies as $company_id => $company) {
                if (isset($company->packets->osago)) {
                    foreach ($company->packets->osago as $packet) {
                        if ($packet->alias == 'osago') {
                            continue;
                        }
                        $packets[] = (object) array(
                            'id' => $packet->id,
                            'name' => $packet->name . ' от ' . $company->name,
                            'company' => $company->name,
                            'packet' => $packet->name,
                            'opinion' => $packet->meaning,
                            'logo' => ServiceApi::_serviceUrl('insurance').$company->docs->logo,
                            'packet_href' => InsuranceHandler::Url('company_osago', array('company' => $company->alias, 'packet' => $packet->alias)),
                            'company_href' => InsuranceHandler::Url('company_osago', array('company' => $company->alias)),
                        );
                    }
                }
            }
            
            return array(
                'title' => 'Популярные пакеты ОСАГО',
                'packets' => $packets,
            );
        }
        
        public function PopularProducts() {
            
            $top = array(
                'premium_profi', 'premium',
                'umnoe_kasko',
                'lait', 'klassik',
                'avto',
                'optimal', 'optimal_profi',
                'bazovyj',
                'bazovyj',
            );
            
            $packets = array();

            $companies = $this->Info();   

            foreach ($companies as $company_id => $company) {
                if (isset($company->packets->kasko)) {
                    foreach ($company->packets->kasko as $packet) {
                        if (($sorting = array_search($packet->alias, $top)) !== false) {
                            $packets[] = (object) array(
                                'id' => $packet->id,
                                'name' => $packet->name . ' от ' . $company->name,
                                'company' => $company->name,
                                'packet' => $packet->name,
                                'opinion' => $packet->meaning,
                                'logo' => ServiceApi::_serviceUrl('insurance').$company->docs->logo,
                                'packet_href' => InsuranceHandler::Url('company_kasko', array('company' => $company->alias, 'packet' => $packet->alias)),
                                'company_href' => InsuranceHandler::Url('company_kasko', array('company' => $company->alias)),
                                'sorting' => $sorting,
                            );
                        }
                    }
                }
            }
            
            uasort($packets, function($a, $b) {
                if ($a->sorting == $b->sorting) {
                    return 0;
                }
                return ($a->sorting < $b->sorting) ? -1 : 1;
            });
            
            return array(
                'title' => 'Популярные пакеты КАСКО',
                'packets' => $packets,
            );
        }
        
    }

    class Casco2Api extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('insurance2'));
        }
        
        public function SearchFor($searchOptions = array()) {

            $opts = array(

            );

            $searchOptions = Variable::Extend($opts, $searchOptions);
            
            $res = parent::_request('CascoAjaxHandler.Process', $searchOptions);
            if(!$res)
                return false;

            if (isset($res->additional->score)) {
                $this->_homingRenes($res->results, $res->additional->score, $res->cid);
            }
            
            return $res;
            
        }
        
        public function FromCache($cacheId) {
            $res = parent::_request('CascoAjaxHandler.FromCache', array('cid' => $cacheId));
            if(!$res)
                return false;
            
            if (isset($res->additional->score)) {
                $this->_homingRenes($res->results, $res->additional->score, $res->cid);
            }
            
            return $res;
        }
        
        public function GetCleanData() {
            
            $res = parent::_request('CascoAjaxHandler.GetCleanData');
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        protected function _homingRenes( & $rows, $score, $cid) {
            if (Request::$i->get->reninstest == true) {
                return null;
            }
            
            if ( ! $rows) {
                return null;
            }
            
            //$score = 10;
            //unset($rows[1]);
            //iout($rows);
            //exit;
            
            uasort($rows, function($a, $b) {
                if ($a->bonus == $b->bonus) {
                    return 0;
                }
                return ($a->bonus < $b->bonus) ? -1 : 1;
            });

            $koef = 0;
            trY {
                $discount = $this->_getAvailRenesDiscount($score);
                
                $scheme = $this->_getRenesScheme($rows);
                
                $rows = (array) $rows;
                
                $pos = $index = 0;
                
                foreach ($rows as $row) {
                    $index++;
                    
                    if ( ! $scheme[$index]->type) { continue; }
                    
                    $pos++;
                    
                    if ($row->company_key == 'renes') {
                        $key = ($row->packet_key == 'confident') ? 'trim' : 'full';
                        if ($pos == 1) {
                            //$koef = $this->_getRenesDiscountFirst($row->bonus, $key, $scheme, $index);
                            break;
                        }
                        elseif ($pos >= 2) {
                            $koef = $this->_getRenesDiscountSecond($row->bonus, $key, $scheme, $index, $discount);
                        }
                        
                        if ($koef) { break; }
                    }
                }
            }
            catch (BaseException $ex) {
                $koef = 0;
            }
            
            if ($koef) {
                foreach ($rows as $key => $row) {
                    $rows[$key]->discount = 0;
                    $rows[$key]->discounts = (object) array();
                    if ($row->company_key == 'renes') {
                        $rows[$key]->discount += $koef;
                        $rows[$key]->discounts->homing = $koef;
                        $rows[$key]->bonus = round($rows[$key]->bonus - ($rows[$key]->bonus * $koef / 100));
                    }
                }
                
                $cookie_name = 'homing_'.$cid;
                $cookie_value = Encryption::Encrypt('renes', (string) $koef);
                if (Request::$i->cookie->$cookie_name != $cookie_value) {
                    $dtp = new DataPoint('postgres');
                    $dtp->QueryNonInfo('INSERT INTO homing (cid, homing) VALUES ('.$cid.', '.$koef.')');
                }
                setcookie($cookie_name, $cookie_value, 0, '/');
                
                uasort($rows, function($a, $b) {
                    if ($a->bonus == $b->bonus) {
                        return 0;
                    }
                    return ($a->bonus < $b->bonus) ? -1 : 1;
                });
            }
        }
        
        protected function _getAvailRenesDiscount($score) {
            if ($score > 21) { return 10; }
            elseif ($score > 19) { return 9; }
            elseif ($score > 17) { return 8; }
            elseif ($score > 15) { return 7; }
            elseif ($score > 13) { return 6; }
            elseif ($score > 11) { return 5; }
            elseif ($score > 7) { return 3; }
            else { return 0; }
        }
        
        protected function _getRenesScheme( & $rows) {
            
            $scheme = array();
            
            $rows = (array) $rows;
            
            $i = 0;
            foreach ($rows as $key => $row) {
                if ($row->company_key == 'toi' ||
                    $row->packet_key == 'pragmatist' ||
                    $row->packet_key == 'pragmatist_plus' ||
                    $row->packet_key == 'ing_byudzhet' ||
                    $row->packet_key == 'ing_bronze' ||
                    $row->packet_key == 'packet_50x50' ||
                    $row->packet_key == 'econom' ||
                    $row->packet_key == 'steal' ||
                    $row->packet_key == 'rgs_econom'
                ) {
                    $key = '';
                }
                elseif ($row->packet_key == 'uralsib_smart' || 
                    $row->packet_key == 'premium_profi' ||
                    $row->packet_key == 'premium_profi_econom' ||
                    $row->packet_key == 'optimal_profi' ||
                    $row->packet_key == 'optimal_profi_econom' ||
                    $row->packet_key == 'optimal_profi_econom' ||
                    $row->packet_key == 'ing_vygoda' ||
                    $row->packet_key == 'ing_econom' ||
                    $row->packet_key == 'ing_profi' ||
                    $row->packet_key == 'confident'
                ) {
                    $key = 'trim';
                }
                else {
                    $key = 'full';
                }
                
                $i++;
                
                $scheme[$i] = (object) array('type' => $key, 'bonus' => $row->bonus);
            }
            
            return $scheme;
        }
        
        protected function _getRenesDiscountFirst($price, $key, $scheme, $index) {
            $koef = 0;
            $count = count($scheme);
            for ($i = $index+1; $i <= $count; $i++) {
                if ($scheme[$i]->type == $key) {
                    $new_price = round(max($price, $scheme[$i]->bonus - ($scheme[$i]->bonus*2/100)));
                    $koef = (1 - round($new_price / $price, 2))*100;
                    break;
                }
            }
            
            return $koef;
        }
        
        protected function _getRenesDiscountSecond($price, $key, $scheme, $index, $discount) {
            $koef = 0;
            
            if ($discount <= 0) {
                return $koef;
            }
            
            $second = 0;
            for ($i = 1; $i < $index; $i++) {
                if ($scheme[$i]->type == $key) {
                    $second++;
                    if ($second == 1) {
                        $new_price = max($price - ($price*$discount/100), $scheme[$i]->bonus - ($scheme[$i]->bonus*2/100));
                        $koef = (1 - round($new_price / $price, 2))*100;
                        break;
                    } 
                    
                }
            }
            
            return $koef;
        }
    }
    
    class Osago2Api extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('insurance2'));
        }
        
        public function SearchFor($searchOptions = array()) {

            $opts = array(
                'vehbrand' => 'audi',
                'vehmodel' => 'audi_a4',
                'vehpower' => '100',
                'vehyear' => '2015',
                'city' => '1',
                'term' => '10',
                'bonusmalus' => '0',
                'juridical' => '0',
                'multidrive' => '0',
                'drvactive' => '',
                'drvgender' => 'M',
                'drvage' => '35',
                'drvexp' => '15',
            );

            $searchOptions = Variable::Extend($opts, $searchOptions);
            
            $res = parent::_request('OsagoAjaxHandler.Process', $searchOptions);
            if(!$res)
                return false;
            
            return $res;
            
        }
        
        public function FromCache($cacheId) {
            $res = parent::_request('OsagoAjaxHandler.FromCache', array('cid' => $cacheId));
            if(!$res)
                return false;
            
            return $res;
        }
        
        public function GetCleanData() {
            
            $res = parent::_request('OsagoAjaxHandler.GetCleanData');
            if(!$res)
                return false;
            
            return $res;
            
        }
    }
    
    class Insurance2Api extends ServiceApi {

        public function __construct() {
            parent::__construct($this->_serviceUrl('insurance2'));
        }
        
        public function AddTask($executor, $settings) {
            return $this->_request('TasksAjaxHandler.AddTask', array(
                'executor' => $executor,
                'settings' => $settings,
            ));
        }
        
        public function DoTask($executor, $settings) {
            return $this->_request('TasksAjaxHandler.DoTask', array(
                'executor' => $executor,
                'settings' => $settings,
            ));
        }
        
        public function GetBaseCalcs($type, $cid = null) {
            return $this->_request('InsuranceAjaxHandler.GetBaseCalcs', array(
                'type' => $type,
                'cid' => $cid,
            ));
        }
        
        public function RawInfo($company = false, $pid = false) {
            return parent::_request('InsuranceAjaxHandler.PartnersRawInfo', array(
                'company' => $company,
                'pid' => $pid,
            ));
        }
    }
?>
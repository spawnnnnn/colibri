<?php

    function strftimeu($format, $microtime) {
        if (preg_match('/^[0-9]*\\.([0-9]+)$/', $microtime, $reg)) {
            $decimal = substr(str_pad($reg[1], 6, "0"), 0, 6);
        } else {
            $decimal = "000000";
        }
        $format = preg_replace('/(%f)/', $decimal, $format);
        return strftime($format, $microtime);
    }
    
    class Date {
        
        const YEAR  = 31556926;
        const MONTH = 2629744;
        const WEEK  = 604800;
        const DAY   = 86400;
        const HOUR  = 3600;
        const MINUTE= 60;
        
        static function RFC($time = null) {
            $tz = date('Z');
            $tzs = ($tz < 0) ? '-' : '+';
            $tz = abs($tz);
            $tz = (int)($tz/3600)*100 + ($tz%3600)/60;
            return sprintf("%s %s%04d", date('D, j M Y H:i:s', Variable::IsNull($time) ? time() : $time), $tzs, $tz);
        }
        
        static function ToDbString($time = null, $milliseconds = false) {
            return strftimeu('%Y-%m-%d %H:%M:%S'.($milliseconds ? '.%f' : ''), Variable::IsNull($time) ? time() : (Variable::IsNumeric($time) ? $time : strtotime($time)));
        }
        
        static function ToUnixTime($datestring) {
            return strtotime($datestring);
        }
        
        static function Age($time) {
            
            $time = time() - $time; // to get the time since that moment

            $tokens = array (
                31536000 => array('год', 'года', 'лет'),
                2592000 => array('месяц', 'месяца', 'месяцев'),
                604800 => array('неделю', 'недели', 'недель'),
                86400 => array('день', 'дня', 'дней'),
                3600 => array('час', 'часа', 'часов'),
                60 => array('минуту', 'минуты', 'минут'),
                1 => array('секунду', 'секунды', 'секунд')
            );

            foreach ($tokens as $unit => $labels) {
                if ($time < $unit) continue;
                $numberOfUnits = floor($time / $unit);
                $ret = ($numberOfUnits > 1 ? $numberOfUnits.' ' : '').Strings::FormatSequence($numberOfUnits, $labels).' назад';
                if($ret == 'день назад')
                    $ret = 'вчера';
                return $ret;
            }
            
            return 'только что';
            
        }
        
        static function AgeYears($time) {
            $day = date('j', $time);
            $month = date('n', $time);
            $year = date('Y', $time);
            
            //var dateParts = val.split("/");
            //var today = new Date();
            
            //var birthDate = new Date(dateParts[2], dateParts[1], dateParts[0]);
            
            $age = date('Y') - $year;
            $m = date('n') - $month;
            if ($m < 0 || ($m === 0 && date('j') < $day)) {
                $age--;
            }
            
            return $age;
        }      
        
        public static function TimeToString($number, $range = 60, $postfixes = array("sec.", "min.", "hours")){

            $hours = 0;
            $mins = 0;
            $secs = 0;

            if($number >= 60) {
                $secs = $number % 60; $number = (int)($number / 60);
                if($number >= 60) {
                    $mins = $number % 60; $number = (int)($number / 60);
                    if($number >= 60) {
                        $hours = $number % 60; $number = (int)($number / 60);
                    }
                    else
                        $hours = $number;
                }
                else
                    $mins = $number;
            }
            else {
                $secs = $number;
            }

            $txt = "";
            $txt .= Strings::Expand($hours, 2, "0").":";
            $txt .= Strings::Expand($mins, 2, "0").":";
            $txt .= Strings::Expand($secs, 2, "0").":";

            $txt = ltrim($txt, "0");
            $txt = ltrim($txt, ":");

            return substr($txt, 0, strlen($txt)-1);
        }  
        
    }
    
?>

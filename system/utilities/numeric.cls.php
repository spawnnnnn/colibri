<?php
    class Numeric {

        public static function ToPercent($number, $delimiter = ',', $decimals = 2) {
            return number_format($number, $decimals, $delimiter, '');
        }
        
        public static function ToMoney($number) {
            return number_format($number, 0, '', '&nbsp;');
        }
        
        public static function ToPrice($price, $format = '%s') {
            return sprintf($format, self::Humanize($price));
        }
        
        public static function Humanize($price) {
            $rprice = "";
            
            $price = (string)$price;
            $prices = preg_split("/\.|\,/", $price);
            $price = $prices[0];
            $dec =  isset($prices[1]) ? $prices[1] : '';
            
            
            $len = strlen($price);
            $count = (int)($len/3);
            for($i=0; $i<$count; $i++) {
                $rprice = "&nbsp;".substr($price, $len-($i+1)*3, 3).$rprice;
            }
            $rprice = substr($price, 0, $len-$count*3).$rprice;
            
            return trim($rprice, "&nbsp;").($dec ? ",".$dec : '');
        }
    }
?>

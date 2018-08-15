<?php

    class Randomization {
        
        static function Seed() {
           list($usec, $sec) = explode(' ', microtime());
           return (float) $sec + ((float) $usec * 100000);
        }

        
        static function Integer($min, $max) {
            // srand(Randomization::Seed());
            return rand($min, $max);
        }

        static function Mixed($l) {
            $j = 0;
            $tmp = "";
            $c = array();
            $i = 0;
            
            for($j = 1; $j < $l; $j++) {
                $i = (int) Randomization::Integer(0, 2.999999);
                $c[0] = chr((int) Randomization::Integer(ord("A"), ord("Z")));
                $c[1] = chr((int) Randomization::Integer(ord("a"), ord("z")));
                $c[2] = chr((int) Randomization::Integer(ord("0"), ord("9")));
                $tmp = $tmp.$c[$i];
            }

            return $tmp;
        }

        static function Numeric($l) {
            $j = 0;
            $tmp = "";
            $c = array();
            $i = 0;
            
            for($j = 1; $j <= $l; $j++) {
                $i = (int) Randomization::Integer(0, 2.999999);
                $c[0] = chr((int) Randomization::Integer(ord("0"), ord("9")));
                $c[1] = chr((int) Randomization::Integer(ord("0"), ord("9")));
                $c[2] = chr((int) Randomization::Integer(ord("0"), ord("9")));
                $tmp = $tmp.$c[$i];
            }

            return $tmp;
        }

        static function Character($l) {
            $tmp = "";
            $c = array();
            
            for($i = 0; $i < $l; $i++) {
                $j = (int) rand(0, 1);
                $c[0] = chr((int) Randomization::Integer(ord("A"), ord("Z")));
                $c[1] = chr((int) Randomization::Integer(ord("a"), ord("z")));
                $tmp = $tmp.$c[$j];
            }

            return $tmp;
        }        
        
    }

?>

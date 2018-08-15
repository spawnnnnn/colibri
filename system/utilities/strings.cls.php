<?php

    class Strings {
        
        const LE = "\n";
        const LF = "\n";
        const CR = "\r";
        const CRLF = "\r\n";
        
        public static $seq_years = array('год', 'года', 'лет');
        public static $seq_months = array('месяц', 'месяца', 'месяцев');
        public static $seq_weeks = array('неделя', 'недели', 'недель');
        public static $seq_days = array('день', 'дня', 'дней');
        
        public static function Randomize($length) {
            return Randomization::Mixed($length);
        }
        
        public static function PrepareAttribute($string, $quoters = false) {
            if ($quoters)
                $string = preg_replace("/\'/", "&rsquo;", $string);
            $string = preg_replace("/&amp;/", "&", $string);
            $string = preg_replace("/&nbsp;/", " ", $string);
            $string = preg_replace("/&/", "&amp;", $string);
            $string = preg_replace("/\n/", '', $string);
            $string = preg_replace("/\"/", "&quot;", $string);
            return $string; //Strings::Unescape($string);
        }
        
        public static function Unescape($s) {
            return preg_replace_callback(
                '/% (?: u([A-F0-9]{1,4}) | ([A-F0-9]{1,2})) /sxi',
                'Strings::_unescapeCallback',
                $s
            );
        }
        
        public static function _unescapeCallback($p) {
            $c = '';
            if ($p[1]) {
                $u = pack('n', $dec=hexdec($p[1]));
                $c = @iconv('UCS-2BE', 'windows-1251', $u);
            }
            return $c;
        }

        public static function ToLower($s) {
            return mb_strtolower($s, "UTF-8");
        }
                                                           
        public static function ToUpper($s) {
            return mb_strtoupper($s, "UTF-8");
        }
        
        public static function ToUpperFirst($str) {
            return mb_strtoupper(mb_substr($str, 0, 1, "UTF-8"), "UTF-8").mb_substr($str, 1, mb_strlen($str, "UTF-8"), "UTF-8");
        }
        
        public static function ToCamelCaseAttr($str, $firstCapital = false) {
            if($firstCapital)
                $str[0] = strtoupper($str[0]);
            
            return preg_replace_callback('/\-([a-z])/', function($c) {
                return Strings::ToUpper(substr($c[1], 0, 1)).Strings::ToLower(substr($c[1], 1));
            }, $str);
        }
        
        public static function FromCamelCaseAttr($str) {
            return preg_replace_callback('/([A-Z])/', function($c) {
                return '-'.Strings::ToUpper($c[1]);
            }, $str);
        }
        
        public static function ToCamelCaseVar($str, $firstCapital = false) {
            if($firstCapital)
                $str[0] = strtoupper($str[0]);
            
            return preg_replace_callback('/_([a-z])/', function($c) {
                return Strings::ToLower($c[1]);
            }, $str);
        }
        
        public static function FromCamelCaseVar($str) {
            return preg_replace_callback('/([A-Z])/', function($c) {
                return '_'.Strings::ToUpper($c[1]);
            }, $str);
        }
        
        public static function IsEmail($address) {
            if (function_exists('filter_var')) 
                return filter_var($address, FILTER_VALIDATE_EMAIL) !== false;
            else
                return preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $address);
        }
        
        public static function EndsWith($string, $end) {
            return substr($string, strlen($string) - strlen($end)) == $end;
        }
        
        public static function StartsWith($string, $start) {
            return substr($string, 0, strlen($start)) == $start;
        }
        
        public static function FixEOL($line, $char = Strings::LE) {
            $line = str_replace(Strings::CRLF, $char, $line);
            $line = str_replace(Strings::CR, $char, $line);
            $line = str_replace(Strings::LF, $char, $line);
            return $line;
        }

        public static function Substring($string, $start, $length = false) {
            $enc = mb_detect_encoding($string);
            if(!$length)
                $length = mb_strlen($string, $enc);
            return mb_substr($string, $start, $length, $enc);
        }
        
        public static function Length($string) {
            $encoding = mb_detect_encoding($string);
            if(!$encoding) $encoding = 'utf-8';
            return mb_strlen($string, $encoding);
        }
        
        public static function CheckForMultbyte($string, $encoding) {
            if (function_exists('mb_strlen'))
                return (strlen($string) > mb_strlen($string, $encoding));
            else 
                return false;
        }        
        
        public static function StripNewLines($string) {
            return trim(str_replace(Strings::LF, '', str_replace(Strings::CR, '', $string)));
        }

        public static function FormatSequence($secuence, $labels = array("год", "года", "лет"), $viewnumber = false) {

            $isfloat = intval($secuence) != floatval($secuence);
            $floatPoint = floatval($secuence) - intval($secuence);
            $floatPoint = $floatPoint.'';
            $floatPoint = str_replace('0.', '', $floatPoint);
            $floatLength = strlen($floatPoint);

            $s = "";
            if($viewnumber)
                $s = $secuence." ";
            $ssecuence = strval($secuence);
            $sIntervalLastChar = substr($ssecuence, strlen($ssecuence)-1, 1);
            if((int)$secuence > 10 && (int)$secuence < 20)
                return $s.$labels[2]; //"лет"
            else {
                if(!$isfloat || $floatLength > 1) {
                    switch(intval($sIntervalLastChar)) {
                        case 1:
                            return $s.$labels[0];
                        case 2:
                        case 3:
                        case 4:
                            return $s.$labels[1];
                        case 5:
                        case 6:
                        case 7:
                        case 8:
                        case 9:
                        case 0:
                            return $s.$labels[2];
                    }
                }
                else {
                    switch(intval($sIntervalLastChar)) {
                        case 1:
                            return $s.$labels[0];
                        case 2:
                        case 3:
                        case 4:
                        case 5:
                            return $s.$labels[1];
                        case 6:
                        case 7:
                        case 8:
                        case 9:
                        case 0:
                            return $s.$labels[2];
                    }
                }
            }

        }
        
        public static function FormatFileSize($number, $range = 1024, $postfixes = array("bytes", "Kb", "Mb", "Gb", "Tb")){
            for($j=0; $j < count($postfixes); $j++) {
                if($number <= $range)
                    break;
                else
                    $number = $number/$range;
            }
            $number = round($number, 2);
            return $number." ".$postfixes[$j];
        }

        public static function StripStrongHTML($html) {

            $search = array ('@([\r\n])[\s]+@',            
                            '@&(quot|#34);@i',            
                            '@&(amp|#38);@i',
                            '@&(nbsp|#160);@i',
                            '@&(iexcl|#161);@i',
                            '@&(cent|#162);@i',
                            '@&(pound|#163);@i',
                            '@&(copy|#169);@i');               

            $replace = array ('\1',
                            '"',
                            '&',
                            ' ',
                            chr(161),
                            chr(162),
                            chr(163),
                            chr(169));

            $html = preg_replace($search, $replace, $html);
            $html = preg_replace_callback('@&#(\d+);@', function($v) { return chr($v); }, $html);

            $html = preg_replace('@<script[^>]*?'.'>.*?</script>@sim', '', $html);
            $html = preg_replace('@<script[^>]*?'.'>@sim', '', $html);
            $html = preg_replace('@</script>@sim', '', $html);
            $html = preg_replace('@<style[^>]*?'.'>.*?</style>@sim', '', $html);
            $html = preg_replace('@<iframe[^>]*?'.'>.*?</iframe>@sim', '', $html);
            $html = preg_replace('@<iframe[^>]*?'.'>@sim', '', $html);
            $html = preg_replace('@<p[^>]*>(.*?)</p>@sim', '<p>\1</p>', $html);
            $html = preg_replace('@<a[^>]*?>(.*?)</a>@sim', '\1', $html);
            $html = preg_replace('@<font[^>]*?>(.*?)</font>@sim', '\1', $html);
            $html = preg_replace('@<span[^>]*?>(.*?)</span>@sim', '\1', $html);
            $html = preg_replace('@<div[^>]*?'.'>@sim', '<p>', $html);
            $html = preg_replace('@</div>@sim', '</p>', $html);
            $html = preg_replace('@<img[^>]*?'.'>@sim', '', $html);
            $html = preg_replace('@style="[^"+]"@sim', '', $html);
            $html = preg_replace('@<br[^>+]>@sim', '<br />', $html);
            $html = preg_replace('@<noindex[^>]*?'.'>(.*?)</noindex>@sim', '\1', $html);
            /*$html = preg_replace("@\n.+?\s@sim", ' ', $html);
            $html = preg_replace("@@sim", ' ', $html);
            $html = preg_replace("@\n@sim", ' ', $html);*/
            /*$html = preg_replace("@^[\s\S\SA-zА-з0-9]@sim", ' ', $html);*/
            
            $html = str_replace('<p> </p>', '', $html);  

            return $html;

        }
        
        public static function AddBrakes($text) {
            return str_replace("\n", "<br />", str_replace("\r", "<br />", str_replace("\r\n", "<br />", $text)));
        }
        
        public static function StripHTML($html) {
            
            $search = array ('@<script[^>]*?>.*?</script>@sim',
                            '@<\!--(.*?)-->@sim',          
                            '@<[\/\!]*?[^<>]*?>@sim',      
                            '@([\r\n])[\s]+@',            
                            '@&(quot|#34);@i',            
                            '@&(amp|#38);@i',
                            '@&(nbsp|#160);@i',
                            '@&(iexcl|#161);@i',
                            '@&(cent|#162);@i',
                            '@&(pound|#163);@i',
                            '@&(copy|#169);@i');               

            $replace = array (' ',
                            ' ',
                            ' ',
                            '\1',
                            '"',
                            '&',
                            ' ',
                            chr(161),
                            chr(162),
                            chr(163),
                            chr(169));

            $html = preg_replace($search, $replace, $html);
            $html = preg_replace_callback('@&#(\d+);@', function($v) { return chr($v); }, $html);
            
            return $html;
            
        }
        
        public static function TrimLength($str, $length, $ellipsis = "...") {
            if(mb_strlen($str, "utf-8") > $length)
                return mb_substr($str, 0, $length-3, "UTF-8").$ellipsis;
            else
                return $str;
        }
        
        public static function Words($text, $n, $ellipsis = "...") {
                
            $text = Strings::StripHTML(trim($text));
            $a = preg_split("/ |,|\.|-|;|:|\(|\)|\{|\}|\[|\]/", $text);
            
            if (count($a) > 0) {
                if (count($a) == 1)
                    return $text;
                else if(count($a) < $n)
                    return $text;
                /*else 
                    $n = $n-1;*/
                
                $l = 0;
                for($j=0; $j<$n;$j++) {
                    $l = $l + mb_strlen($a[$j])+1;
                }
                
                // $ellipsis = (count($a) > $n + 1) ? $ellipsis : "";
                return mb_substr($text, 0, $l).$ellipsis;
            }
            else {
                return mb_substr($text, 0, $n);
            }

        }
        
        public static function WordsRandomStart($text, $somelen, $ellipsis = "...") {
            $maxlen = 500;

            if($text == '') {
                return '';
            }
            
            $seed = preg_replace('/\D/', '', md5(Request::$i->requesteduri));
            $seed = Strings::Substring($seed, 0, 6);

            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            $text = str_replace('\xA0', ' ', $text); // Non-breaking space
            $text = strip_tags($text);
            $text = preg_replace('/\s{2,}/isu', ' ', trim($text));

            $pre = preg_split('/[.?!]\s([^\.0-9]{3,}.*?)[.?!]\s/isu', $text, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
            $pre = array_map('trim', $pre);
            $pre = array_filter($pre, create_function('$x', 'return mb_strlen($x, "UTF-8") > 50;'));

            if(!$pre) {
                return Strings::Words($text, $somelen, $ellipsis);
            }

            srand($seed);
            $arr = array_rand($pre, min(5, count($pre)));
            $arr = (array) $arr;
            srand(microtime(true)*1000);
            asort($arr);

            $res = '';
            foreach($arr as &$i) {
                $res.= $pre[$i] . '. ';
                if(mb_strlen($res, 'UTF-8') > $maxlen) {
                    break;
                }
            }

            return Strings::Words($res.'.', $somelen, $ellipsis);
        }
        
        public static function UniqueWords($string, $minlen = 3) {
            $string = Strings::StripHTML(trim($string));
            $a = preg_split("/ |,|\.|-|;|:|\(|\)|\{|\}|\[|\]/", $string);
            $a = array_unique($a);
            
            $b = array();
            foreach($a as $w) {
                if(Strings::Length($w) < $minlen)
                    continue;
                $b[] = Strings::ToLower($w);
            }
            
            return $b;
        }   
        
        public static function Split($regexp, $string) {
            return preg_split($regexp, $string);
        }
        
        public static function Replace($regexp, $string, $replacement = '') {
            return preg_replace($regexp, $replacement, $string);
        }
        
        public static function Match($regexp, $string) {
            preg_match($regexp, $string, $matches);
            return $matches;
        }
        
        public static function MatchAll($regexp, $string) {
            preg_match_all($regexp, $string, $matches, PREG_SET_ORDER);
            return $matches;
        }
        
        public static function Entities($string, $double_encode = true) {
            return htmlentities($string."\0", ENT_QUOTES, 'UTF-8', $double_encode);
        }
        
        public static function Chars($value, $double_encode = true) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $double_encode);
        }
        
        public static function HtmlToAttr($text) {
            $text = str_replace(array("\n", "\r"), '', $text);
            $text = preg_replace("/(\ )+</", "<", $text);
            $text = Strings::Chars($text);
            return $text;
        }
        
        public static function AddSlashes($string) {
            $ret = addslashes($string);
            $ret = preg_replace("/\n/", "\\n", $ret);
            return $ret;
        }
        
        public static function StripSlashes($string) {
            $ret = preg_replace("/\\n/", "\n", $string);
            $ret = stripslashes($ret);
            return $ret;
        }
        
        public static function Transliterate($string) { # Задаём функцию перекодировки кириллицы в транслит. 
            /*$string = mb_ereg_replace("ж","zh",$string);
            $string = mb_ereg_replace("ё","yo",$string);
            $string = mb_ereg_replace("й","i",$string);
            $string = mb_ereg_replace("ю","yu",$string);
            $string = mb_ereg_replace("ь","'",$string);
            $string = mb_ereg_replace("ч","ch",$string);
            $string = mb_ereg_replace("щ","sh",$string);
            $string = mb_ereg_replace("ц","c",$string);
            $string = mb_ereg_replace("у","u",$string);
            $string = mb_ereg_replace("к","k",$string);
            $string = mb_ereg_replace("е","e",$string);
            $string = mb_ereg_replace("н","n",$string);
            $string = mb_ereg_replace("г","g",$string);
            $string = mb_ereg_replace("ш","sh",$string);
            $string = mb_ereg_replace("з","z",$string);
            $string = mb_ereg_replace("х","h",$string);
            $string = mb_ereg_replace("ъ","''",$string);
            $string = mb_ereg_replace("ф","f",$string);
            $string = mb_ereg_replace("ы","y",$string);
            $string = mb_ereg_replace("в","v",$string);
            $string = mb_ereg_replace("а","a",$string);
            $string = mb_ereg_replace("п","p",$string);
            $string = mb_ereg_replace("р","r",$string);
            $string = mb_ereg_replace("о","o",$string);
            $string = mb_ereg_replace("л","l",$string);
            $string = mb_ereg_replace("д","d",$string);
            $string = mb_ereg_replace("э","yе",$string);
            $string = mb_ereg_replace("я","ya",$string);
            $string = mb_ereg_replace("с","s",$string);
            $string = mb_ereg_replace("м","m",$string);
            $string = mb_ereg_replace("и","i",$string);
            $string = mb_ereg_replace("т","t",$string);
            $string = mb_ereg_replace("б","b",$string);
            $string = mb_ereg_replace("Ё","yo",$string);
            $string = mb_ereg_replace("Й","I",$string);
            $string = mb_ereg_replace("Ю","YU",$string);
            $string = mb_ereg_replace("Ч","CH",$string);
            $string = mb_ereg_replace("Ь","'",$string);
            $string = mb_ereg_replace("Щ","SH'",$string);
            $string = mb_ereg_replace("Ц","C",$string);
            $string = mb_ereg_replace("У","U",$string);
            $string = mb_ereg_replace("К","K",$string);
            $string = mb_ereg_replace("Е","E",$string);
            $string = mb_ereg_replace("Н","N",$string);
            $string = mb_ereg_replace("Г","G",$string);
            $string = mb_ereg_replace("Ш","SH",$string);
            $string = mb_ereg_replace("З","Z",$string);
            $string = mb_ereg_replace("Х","H",$string);
            $string = mb_ereg_replace("Ъ","''",$string);
            $string = mb_ereg_replace("Ф","F",$string);
            $string = mb_ereg_replace("Ы","Y",$string);
            $string = mb_ereg_replace("В","V",$string);
            $string = mb_ereg_replace("А","A",$string);
            $string = mb_ereg_replace("П","P",$string);
            $string = mb_ereg_replace("Р","R",$string);
            $string = mb_ereg_replace("О","O",$string);
            $string = mb_ereg_replace("Л","L",$string);
            $string = mb_ereg_replace("Д","D",$string);
            $string = mb_ereg_replace("Ж","Zh",$string);
            $string = mb_ereg_replace("Э","Ye",$string);
            $string = mb_ereg_replace("Я","Ja",$string);
            $string = mb_ereg_replace("С","S",$string);
            $string = mb_ereg_replace("М","M",$string);
            $string = mb_ereg_replace("И","I",$string);
            $string = mb_ereg_replace("Т","T",$string);
            $string = mb_ereg_replace("Б","B",$string);*/
            $string = mb_ereg_replace("ый", "yj",$string); 
            $string = mb_ereg_replace("а" , "a",$string);  
            $string = mb_ereg_replace("б" , "b",$string);  
            $string = mb_ereg_replace("в" , "v",$string);   
            $string = mb_ereg_replace("г" , "g",$string); 
            $string = mb_ereg_replace("д" , "d",$string); 
            $string = mb_ereg_replace("е" , "e",$string);  
            $string = mb_ereg_replace("ё" , "yo",$string); 
            $string = mb_ereg_replace("ж" , "zh",$string); 
            $string = mb_ereg_replace("з" , "z",$string);   
            $string = mb_ereg_replace("и" , "i",$string); 
            $string = mb_ereg_replace("й" , "y",$string);
            $string = mb_ereg_replace("к" , "k",$string);  
            $string = mb_ereg_replace("л" , "l",$string);  
            $string = mb_ereg_replace("м" , "m",$string);  
            $string = mb_ereg_replace("н" , "n",$string);   
            $string = mb_ereg_replace("о" , "o",$string); 
            $string = mb_ereg_replace("п" , "p",$string);
            $string = mb_ereg_replace("р" , "r",$string);  
            $string = mb_ereg_replace("с" , "s",$string);  
            $string = mb_ereg_replace("т" , "t",$string);  
            $string = mb_ereg_replace("у" , "u",$string);  
            $string = mb_ereg_replace("ф" , "f",$string); 
            $string = mb_ereg_replace("х" , "h",$string);
            $string = mb_ereg_replace("ц" , "c",$string);  
            $string = mb_ereg_replace("ч" , "ch",$string); 
            $string = mb_ereg_replace("ш" , "sh",$string); 
            $string = mb_ereg_replace("щ" , "sch",$string); 
            $string = mb_ereg_replace("ъ" , "j",$string); 
            $string = mb_ereg_replace("ы" , "y",$string);
            $string = mb_ereg_replace("ь" , "",$string);  
            $string = mb_ereg_replace("э" , "e",$string);  
            $string = mb_ereg_replace("ю" , "yu",$string); 
            $string = mb_ereg_replace("я" , "ya",$string); 

            $string = mb_ereg_replace("ЫЙ", "YJ",$string); 
            $string = mb_ereg_replace("ыЙ", "yJ",$string); 
            $string = mb_ereg_replace("Ый", "Yj",$string); 
            $string = mb_ereg_replace("А" , "A",$string);  
            $string = mb_ereg_replace("Б" , "B",$string);  
            $string = mb_ereg_replace("В" , "V",$string);   
            $string = mb_ereg_replace("Г" , "G",$string); 
            $string = mb_ereg_replace("Д" , "D",$string); 
            $string = mb_ereg_replace("Е" , "E",$string);  
            $string = mb_ereg_replace("Ё" , "Yo",$string); 
            $string = mb_ereg_replace("Ж" , "Zh",$string); 
            $string = mb_ereg_replace("З" , "Z",$string);   
            $string = mb_ereg_replace("И" , "I",$string); 
            $string = mb_ereg_replace("Й" , "Y",$string);
            $string = mb_ereg_replace("К" , "K",$string);  
            $string = mb_ereg_replace("Л" , "L",$string);  
            $string = mb_ereg_replace("М" , "M",$string);  
            $string = mb_ereg_replace("Н" , "N",$string);   
            $string = mb_ereg_replace("О" , "O",$string); 
            $string = mb_ereg_replace("П" , "P",$string);
            $string = mb_ereg_replace("Р" , "R",$string);  
            $string = mb_ereg_replace("С" , "S",$string);  
            $string = mb_ereg_replace("Т" , "T",$string);  
            $string = mb_ereg_replace("У" , "U",$string);  
            $string = mb_ereg_replace("Ф" , "F",$string); 
            $string = mb_ereg_replace("Х" , "H",$string);
            $string = mb_ereg_replace("Ц" , "C",$string);  
            $string = mb_ereg_replace("Ч" , "Ch",$string); 
            $string = mb_ereg_replace("Ш" , "Sh",$string); 
            $string = mb_ereg_replace("Щ" , "Sch",$string); 
            $string = mb_ereg_replace("Ъ" , "J",$string); 
            $string = mb_ereg_replace("Ы" , "Y",$string);
            $string = mb_ereg_replace("Ь" , "",$string);  
            $string = mb_ereg_replace("Э" , "E",$string);  
            $string = mb_ereg_replace("Ю" , "Yu",$string); 
            $string = mb_ereg_replace("Я" , "Ya",$string);
            return $string;
        }
        
        public static function TransliterateBack($string) { # Задаём функцию перекодировки кириллицы в транслит. 
            //$string = mb_ereg_replace(  "I",    "Й",    $string);
            /*$string = mb_ereg_replace(  "Zh",   "Ж",     $string);
            $string = mb_ereg_replace(  "Ye",   "Э",     $string); 
            $string = mb_ereg_replace(  "Ja",   "Я",     $string);     
            $string = mb_ereg_replace(  "Yu",   "Ю",     $string);
            $string = mb_ereg_replace(  "Ch",   "Ч",     $string);
            $string = mb_ereg_replace(  "Sh'",  "Щ",      $string);
            $string = mb_ereg_replace(  "Sh",   "Ш",     $string);
            $string = mb_ereg_replace(  "Yo",   "Ё",     $string);
            $string = mb_ereg_replace(  "Ej",   "Ей",     $string);
            // $string = mb_ereg_replace(  "'",    "Ь",    $string);
            $string = mb_ereg_replace(  "C",    "Ц",    $string);
            $string = mb_ereg_replace(  "U",    "У",    $string);
            $string = mb_ereg_replace(  "K",    "К",    $string);
            $string = mb_ereg_replace(  "E",    "Е",    $string);
            $string = mb_ereg_replace(  "N",    "Н",    $string);
            $string = mb_ereg_replace(  "G",    "Г",    $string);
            $string = mb_ereg_replace(  "Z",    "З",    $string);
            $string = mb_ereg_replace(  "H",    "Х",    $string);
            $string = mb_ereg_replace(  "''",   "Ъ",     $string);
            $string = mb_ereg_replace(  "F",    "Ф",    $string);
            $string = mb_ereg_replace(  "Y",    "Ы",    $string);
            $string = mb_ereg_replace(  "V",    "В",    $string);
            $string = mb_ereg_replace(  "A",    "А",    $string);
            $string = mb_ereg_replace(  "P",    "П",    $string);
            $string = mb_ereg_replace(  "R",    "Р",    $string);
            $string = mb_ereg_replace(  "O",    "О",    $string);
            $string = mb_ereg_replace(  "L",    "Л",    $string);
            $string = mb_ereg_replace(  "D",    "Д",    $string);
            $string = mb_ereg_replace(  "S",    "С",    $string);
            $string = mb_ereg_replace(  "M",    "М",    $string);
            $string = mb_ereg_replace(  "I",    "И",    $string);
            $string = mb_ereg_replace(  "T",    "Т",    $string);
            $string = mb_ereg_replace(  "B",    "Б",    $string);

            $string = mb_ereg_replace(  "zh",   "ж",     $string);
            $string = mb_ereg_replace(  "yo",   "ё",     $string);
            $string = mb_ereg_replace(  "ij",    "ий",    $string);
            $string = mb_ereg_replace(  "yu",   "ю",     $string);
            $string = mb_ereg_replace(  "'",    "ь",    $string);
            $string = mb_ereg_replace(  "ch",   "ч",     $string);
            $string = mb_ereg_replace(  "ey",   "ей",     $string);
            // $string = mb_ereg_replace(  "sh",   "щ",     $string);
            $string = mb_ereg_replace(  "c",    "ц",    $string);
            $string = mb_ereg_replace(  "u",    "у",    $string);
            $string = mb_ereg_replace(  "k",    "к",    $string);
            $string = mb_ereg_replace(  "e",    "е",    $string);
            $string = mb_ereg_replace(  "n",    "н",    $string);
            $string = mb_ereg_replace(  "g",    "г",    $string);
            $string = mb_ereg_replace(  "sh",   "ш",     $string);
            $string = mb_ereg_replace(  "z",    "з",    $string);
            $string = mb_ereg_replace(  "j",    "ж",    $string);
            $string = mb_ereg_replace(  "h",    "х",    $string);
            $string = mb_ereg_replace(  "''",   "ъ",     $string);
            $string = mb_ereg_replace(  "f",    "ф",    $string);
            $string = mb_ereg_replace(  "y",    "ы",    $string);
            $string = mb_ereg_replace(  "v",    "в",    $string);
            $string = mb_ereg_replace(  "a",    "а",    $string);
            $string = mb_ereg_replace(  "p",    "п",    $string);
            $string = mb_ereg_replace(  "r",    "р",    $string);
            $string = mb_ereg_replace(  "o",    "о",    $string);
            $string = mb_ereg_replace(  "l",    "л",    $string);
            $string = mb_ereg_replace(  "d",    "д",    $string);
            $string = mb_ereg_replace(  "yе",   "э",     $string);
            $string = mb_ereg_replace(  "ya",   "я",     $string);
            $string = mb_ereg_replace(  "s",    "с",    $string);
            $string = mb_ereg_replace(  "m",    "м",    $string);
            $string = mb_ereg_replace(  "i",    "и",    $string);
            $string = mb_ereg_replace(  "t",    "т",    $string);
            $string = mb_ereg_replace(  "b",    "б",    $string);
            $string = mb_ereg_replace(  "w",   "в",     $string);*/

            $string = mb_ereg_replace("yj", "ый", $string);
            $string = mb_ereg_replace("a", "а", $string);
            $string = mb_ereg_replace("b", "б", $string);
            $string = mb_ereg_replace("v", "в", $string);
            $string = mb_ereg_replace("g", "г", $string);
            $string = mb_ereg_replace("d", "д", $string);
            $string = mb_ereg_replace("e", "е", $string);
            $string = mb_ereg_replace("yo", "ё", $string);
            $string = mb_ereg_replace("zh", "ж", $string);
            $string = mb_ereg_replace("z", "з", $string);
            $string = mb_ereg_replace("i", "и", $string);
            $string = mb_ereg_replace("y", "й", $string);
            $string = mb_ereg_replace("k", "к", $string);
            $string = mb_ereg_replace("l", "л", $string);
            $string = mb_ereg_replace("m", "м", $string);
            $string = mb_ereg_replace("n", "н", $string);
            $string = mb_ereg_replace("o", "о", $string);
            $string = mb_ereg_replace("p", "п", $string);
            $string = mb_ereg_replace("r", "р", $string);
            $string = mb_ereg_replace("s", "с", $string);
            $string = mb_ereg_replace("t", "т", $string);
            $string = mb_ereg_replace("u", "у", $string);
            $string = mb_ereg_replace("f", "ф", $string);
            $string = mb_ereg_replace("h", "х", $string);
            $string = mb_ereg_replace("c", "ц", $string);
            $string = mb_ereg_replace("ch", "ч", $string);
            $string = mb_ereg_replace("sh", "ш", $string);
            $string = mb_ereg_replace("sch", "щ", $string);
            $string = mb_ereg_replace("j", "ъ", $string);
            $string = mb_ereg_replace("y", "ы", $string);
            $string = mb_ereg_replace("e", "э", $string);
            $string = mb_ereg_replace("yu", "ю", $string);
            $string = mb_ereg_replace("ya", "я", $string);
            $string = mb_ereg_replace("YJ", "ЫЙ", $string);
            $string = mb_ereg_replace("yJ", "ыЙ", $string);
            $string = mb_ereg_replace("Yj", "Ый", $string);
            $string = mb_ereg_replace("A", "А", $string);
            $string = mb_ereg_replace("B", "Б", $string);
            $string = mb_ereg_replace("V", "В", $string);
            $string = mb_ereg_replace("G", "Г", $string);
            $string = mb_ereg_replace("D", "Д", $string);
            $string = mb_ereg_replace("E", "Е", $string);
            $string = mb_ereg_replace("Yo", "Ё", $string);
            $string = mb_ereg_replace("Zh", "Ж", $string);
            $string = mb_ereg_replace("Z", "З", $string);
            $string = mb_ereg_replace("I", "И", $string);
            $string = mb_ereg_replace("Y", "Й", $string);
            $string = mb_ereg_replace("K", "К", $string);
            $string = mb_ereg_replace("L", "Л", $string);
            $string = mb_ereg_replace("M", "М", $string);
            $string = mb_ereg_replace("N", "Н", $string);
            $string = mb_ereg_replace("O", "О", $string);
            $string = mb_ereg_replace("P", "П", $string);
            $string = mb_ereg_replace("R", "Р", $string);
            $string = mb_ereg_replace("S", "С", $string);
            $string = mb_ereg_replace("T", "Т", $string);
            $string = mb_ereg_replace("U", "У", $string);
            $string = mb_ereg_replace("F", "Ф", $string);
            $string = mb_ereg_replace("H", "Х", $string);
            $string = mb_ereg_replace("C", "Ц", $string);
            $string = mb_ereg_replace("Ch", "Ч", $string);
            $string = mb_ereg_replace("Sh", "Ш", $string);
            $string = mb_ereg_replace("Sch", "Щ", $string);
            $string = mb_ereg_replace("J", "Ъ", $string);
            $string = mb_ereg_replace("Y", "Ы", $string);
            $string = mb_ereg_replace("E", "Э", $string);
            $string = mb_ereg_replace("Yu", "Ю", $string);
            $string = mb_ereg_replace("Ya", "Я", $string);
            
            return $string;
        }  
        
        public static function CreateHID($text, $trans = true) {
            
            if($trans) {
                $hid = preg_replace('/\-+/', '-',substr(preg_replace('/[^\w]/i', '-', 
                    str_replace(
                        '«', '', 
                        str_replace(
                            '»', '', 
                            strtolower(Strings::Transliterate(trim($text, "\n\r ")))
                        )
                    )
                ), 0, 200));
            }
            else {
                $hid = iconv('cp1251','UTF-8',preg_replace('/\-+/', '-',substr(preg_replace('/[^\w\x7F-\xFF]/i', '-', 
                    str_replace(
                        '«', '', 
                        str_replace(
                            '»', '', 
                            strtolower(trim(iconv('UTF-8','cp1251',$text), "\n\r "))
                        )
                    )
                ), 0, 200)));
            }
             
            return $hid;
            
        }      
        
        public static function Translate($text) {
            $text = urlencode($text);
            $ch = curl_init('http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=' . $text . '&langpair=en%7Cru&callback=foo&context=bar');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
            $text = curl_exec($ch);
            preg_match('|"translatedText":"(.*?)"|is', $text, $result);
            curl_close($ch);
            return $result['1'];
        }
        
        /**
        * Finds last character boundary prior to maxLength in a utf-8
        * quoted (printable) encoded string.
        * Original written by Colin Brown.
        * @access public
        * @param string $encodedText utf-8 QP text
        * @param int    $maxLength   find last character boundary prior to this length
        * @return int
        */
        public static function UTF8CharBoundary($encodedText, $maxLength) {
            $foundSplitPos = false;
            $lookBack = 3;
            while (!$foundSplitPos) {
                $lastChunk = substr($encodedText, $maxLength - $lookBack, $lookBack);
                $encodedCharPos = strpos($lastChunk, "=");
                if ($encodedCharPos !== false) {
                    // Found start of encoded character byte within $lookBack block.
                    // Check the encoded byte value (the 2 chars after the '=')
                    $hex = substr($encodedText, $maxLength - $lookBack + $encodedCharPos + 1, 2);
                    $dec = hexdec($hex);
                    if ($dec < 128) { // Single byte character.
                        // If the encoded char was found at pos 0, it will fit
                        // otherwise reduce maxLength to start of the encoded char
                        $maxLength = ($encodedCharPos == 0) ? $maxLength :
                        $maxLength - ($lookBack - $encodedCharPos);
                        $foundSplitPos = true;
                    } elseif ($dec >= 192) { // First byte of a multi byte character
                        // Reduce maxLength to split at start of character
                        $maxLength = $maxLength - ($lookBack - $encodedCharPos);
                        $foundSplitPos = true;
                    } elseif ($dec < 192) { // Middle byte of a multi byte character, look further back
                        $lookBack += 3;
                    }
                } 
                else {
                    // No encoded character found
                    $foundSplitPos = true;
                }
            }
            return $maxLength;
        }    
        
        /**
        * Wraps message for use with mailers that do not
        * automatically perform wrapping and for quoted-printable.
        * Original written by philippe.
        * @param string $message The message to wrap
        * @param integer $length The line length to wrap to
        * @param boolean $qp_mode Whether to run in Quoted-Printable mode
        * @access public
        * @return string
        */
        public static function WrapText($message, $length, $charset, $qp_mode = false) {
            
            $soft_break = ($qp_mode) ? sprintf(" =%s", Strings::LE) : Strings::LE;
            
            // If utf-8 encoding is used, we will need to make sure we don't
            // split multibyte characters when we wrap
            $is_utf8 = (strtolower($charset) == "utf-8");

            $message = Strings::FixEOL($message);
            if (substr($message, -1) == Strings::LE)
                $message = substr($message, 0, -1);

            $line = explode(Strings::LE, $message);
            $message = '';
            for ($i=0 ;$i < count($line); $i++) {
                
                $line_part = explode(' ', $line[$i]);
                $buf = '';
                
                for ($e = 0; $e<count($line_part); $e++) {
                    
                    $word = $line_part[$e];
                    
                    if ($qp_mode and (strlen($word) > $length)) {
                        
                        $space_left = $length - strlen($buf) - 1;
                        
                        if ($e != 0) {
                            if ($space_left > 20) {
                                
                                $len = $space_left;
                                if ($is_utf8)
                                    $len = Strings::UTF8CharBoundary($word, $len);
                                else if (substr($word, $len - 1, 1) == "=")
                                    $len--;
                                else if (substr($word, $len - 2, 1) == "=")
                                    $len -= 2;
                                
                                $part = substr($word, 0, $len);
                                $word = substr($word, $len);
                                $buf .= ' ' . $part;
                                $message .= $buf . sprintf("=%s", Strings::LE);
                                
                            } 
                            else
                                $message .= $buf . $soft_break;
                            
                            $buf = '';
                        }
                        
                        while (strlen($word) > 0) {
                            
                            $len = $length;
                            if ($is_utf8)
                                $len = Strings::UTF8CharBoundary($word, $len);
                            else if (substr($word, $len - 1, 1) == "=")
                                $len--;
                            else if (substr($word, $len - 2, 1) == "=")
                                $len -= 2;
                            
                            $part = substr($word, 0, $len);
                            $word = substr($word, $len);

                            if (strlen($word) > 0)
                                $message .= $part . sprintf("=%s", Strings::LE);
                            else
                                $buf = $part;
                                
                        }
                    } else {
                        
                        $buf_o = $buf;
                        $buf .= ($e == 0) ? $word : (' ' . $word);

                        if (strlen($buf) > $length and $buf_o != '') {
                            $message .= $buf_o . $soft_break;
                            $buf = $word;
                        }
                    }
                }
                $message .= $buf . Strings::LE;
            }

            return $message;
        }
        
        /**
        * Correctly encodes and wraps long multibyte strings for mail headers
        * without breaking lines within a character.
        * Adapted from a function by paravoid at http://uk.php.net/manual/en/function.mb-encode-mimeheader.php
        * @access public
        * @param string $str multi-byte text to wrap encode
        * @return string
        */
        public static function Base64EncodeWrapMB($str, $charset) {
            $start = "=?".$charset."?B?";
            $end = "?=";
            $encoded = "";

            $mb_length = mb_strlen($str, $charset);
            // Each line must have length <= 75, including $start and $end
            $length = 75 - strlen($start) - strlen($end);
            // Average multi-byte ratio
            $ratio = $mb_length / strlen($str);
            // Base64 has a 4:3 ratio
            $offset = $avgLength = floor($length * $ratio * .75);

            for ($i = 0; $i < $mb_length; $i += $offset) {
                $lookBack = 0;

                do {
                    $offset = $avgLength - $lookBack;
                    $chunk = mb_substr($str, $i, $offset, $charset);
                    $chunk = base64_encode($chunk);
                    $lookBack++;
                } while (strlen($chunk) > $length);

                $encoded .= $chunk . Strings::LE;
            }

            // Chomp the last linefeed
            $encoded = substr($encoded, 0, -strlen(Strings::LE));
            return $encoded;
        }

        /**
        * Encode string to quoted-printable.
        * Only uses standard PHP, slow, but will always work
        * @access public
        * @param string $string the text to encode
        * @param integer $line_max Number of chars allowed on a line before wrapping
        * @return string
        */
        public static function EncodeQPphp( $input = '', $line_max = 76, $space_conv = false) {
            $hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
            $lines = preg_split('/(?:\r\n|\r|\n)/', $input);
            $eol = "\r\n";
            $escape = '=';
            $output = '';
            while( list(, $line) = each($lines) ) {
                $linlen = strlen($line);
                $newline = '';
                for($i = 0; $i < $linlen; $i++) {
                    $c = substr( $line, $i, 1 );
                    $dec = ord( $c );
                    if ( ( $i == 0 ) && ( $dec == 46 ) ) { // convert first point in the line into =2E
                        $c = '=2E';
                    }
                    if ( $dec == 32 ) {
                        if ( $i == ( $linlen - 1 ) ) { // convert space at eol only
                            $c = '=20';
                        } else if ( $space_conv ) {
                            $c = '=20';
                        }
                    } else if ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
                        $h2 = floor($dec/16);
                        $h1 = floor($dec%16);
                        $c = $escape.$hex[$h2].$hex[$h1];
                    }
                    if ( (strlen($newline) + strlen($c)) >= $line_max ) { // CRLF is not counted
                        $output .= $newline.$escape.$eol; //  soft line break; " =\r\n" is okay
                        $newline = '';
                        // check if newline first character will be point or not
                        if ( $dec == 46 ) {
                            $c = '=2E';
                        }
                    }
                    $newline .= $c;
                } // end of for
                $output .= $newline.$eol;
            } // end of while
            return $output;
        }        

        /**
        * Encode string to RFC2045 (6.7) quoted-printable format
        * Uses a PHP5 stream filter to do the encoding about 64x faster than the old version
        * Also results in same content as you started with after decoding
        * @see EncodeQPphp()
        * @access public
        * @param string $string the text to encode
        * @param integer $line_max Number of chars allowed on a line before wrapping
        * @param boolean $space_conv Dummy param for compatibility with existing EncodeQP function
        * @return string
        * @author Marcus Bointon
        */
        public static function EncodeQP($string, $line_max = 76, $space_conv = false) {
            
            if(CodeKit::Exists('quoted_printable_encode')) { //Use native function if it's available (>= PHP5.3)
                return quoted_printable_encode($string);
            }
            
            $filters = stream_get_filters();
            if (!in_array('convert.*', $filters)) { //Got convert stream filter?
                return Strings::EncodeQPphp($string, $line_max, $space_conv); //Fall back to old implementation
            }
            
            $fp = fopen('php://temp/', 'r+');
            $string = preg_replace('/\r\n?/', Strings::LE, $string); //Normalise line breaks
            $params = array('line-length' => $line_max, 'line-break-chars' => Strings::LE);
            $s = stream_filter_append($fp, 'convert.quoted-printable-encode', STREAM_FILTER_READ, $params);
            fputs($fp, $string);
            rewind($fp);
            $out = stream_get_contents($fp);
            stream_filter_remove($s);
            $out = preg_replace('/^\./m', '=2E', $out); //Encode . if it is first char on a line, workaround for bug in Exchange
            fclose($fp);
            return $out;
        }
      
        /**
        * Encode string to q encoding.
        * @link http://tools.ietf.org/html/rfc2047
        * @param string $str the text to encode
        * @param string $position Where the text is going to be used, see the RFC for what that means
        * @access public
        * @return string
        */      
        public static function EncodeQ ($str, $position = 'text') {
            // There should not be any EOL in the string
            $encoded = preg_replace('/[\r\n]*/', '', $str);

            switch (strtolower($position)) {
                case 'phrase':
                    $encoded = preg_replace("/([^A-Za-z0-9!*+\/ -])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
                    break;
                case 'comment':
                    $encoded = preg_replace("/([\(\)\"])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
                case 'text':
                default:
                    // Replace every high ascii, control =, ? and _ characters
                    //TODO using /e (equivalent to eval()) is probably not a good idea
                    $encoded = preg_replace('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/e', "'='.sprintf('%02X', ord('\\1'))", $encoded);
                    break;
            }

            // Replace every spaces to _ (more readable than =20)
            return str_replace(' ', '_', $encoded);
        }
      
        /**
        * Encodes string to requested format.
        * Returns an empty string on failure.
        * @param string $str The text to encode
        * @param string $encoding The encoding to use; one of 'base64', '7bit', '8bit', 'binary', 'quoted-printable'
        * @access public
        * @return string
        */
        public static function EncodeString($str, $encoding = 'base64') {
            $encoded = '';
            switch(strtolower($encoding)) {
                case 'base64':
                    $encoded = chunk_split(base64_encode($str), 76, Strings::LE);
                    break;
                case '7bit':
                case '8bit':
                    $encoded = Strings::FixEOL($str);
                    //Make sure it ends with a line break
                    if (substr($encoded, -(strlen(Strings::LE))) != Strings::LE)
                        $encoded .= Strings::LE;
                    break;
                case 'binary':
                    $encoded = $str;
                    break;
                case 'quoted-printable':
                    $encoded = Strings::EncodeQP($str);
                    break;
                default:
                    // $this->SetError($this->Lang('encoding') . $encoding);
                    break;
            }
            return $encoded;
        }

        /**
        * Encode a header string to best (shortest) of Q, B, quoted or none.
        * @access public
        * @return string
        */
        public static function EncodeHeader($str, $position = 'text', $charset = 'utf-8') {
            $x = 0;

            switch (strtolower($position)) {
                case 'phrase':
                    if (!preg_match('/[\200-\377]/', $str)) {
                    // Can't use addslashes as we don't know what value has magic_quotes_sybase
                        $encoded = addcslashes($str, "\0..\37\177\\\"");
                        if (($str == $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str)) {
                            return ($encoded);
                        } else {
                            return ("\"$encoded\"");
                        }
                    }
                    $x = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
                    break;
                case 'comment':
                    $x = preg_match_all('/[()"]/', $str, $matches);
                    // Fall-through
                case 'text':
                default:
                    $x += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
                    break;
            }

            if ($x == 0) {
                return ($str);
            }

            $maxlen = 75 - 7 - strlen($charset);
            // Try to select the encoding which should produce the shortest output
            if (strlen($str)/3 < $x) {
                $encoding = 'B';
                if (CodeKit::Exists('mb_strlen') && Strings::CheckForMultbyte($str, $charset)) {
                // Use a custom function which correctly encodes and wraps long
                // multibyte strings without breaking lines within a character
                    $encoded = Strings::Base64EncodeWrapMB($str, $charset);
                } else {
                    $encoded = base64_encode($str);
                    $maxlen -= $maxlen % 4;
                    $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
                }
            } else {
                $encoding = 'Q';
                $encoded = Strings::EncodeQ($str, $position);
                $encoded = Strings::WrapText($encoded, $maxlen, $charset, true);
                $encoded = str_replace('='.Strings::LE, "\n", trim($encoded));
            }

            $encoded = preg_replace('/^(.*)$/m', " =?".$charset."?$encoding?\\1?=", $encoded);
            $encoded = trim(str_replace("\n", Strings::LE, $encoded));

            return $encoded;
        }

        /**
        * Encodes attachment in requested format.
        * Returns an empty string on failure.
        * @param string $path The full path to the file
        * @param string $encoding The encoding to use; one of 'base64', '7bit', '8bit', 'binary', 'quoted-printable'
        * @see EncodeFile()
        * @access private
        * @return string
        */
        public static function EncodeFile($path, $encoding = 'base64') {
            try {
                
                if (!is_readable($path))
                    throw new BaseException("Can not open file ".$path);
                
                if (CodeKit::Exists('get_magic_quotes')) {
                    function get_magic_quotes() { return false; }
                }
                
                $file_buffer  = file_get_contents($path);
                $file_buffer  = Strings::EncodeString($file_buffer, $encoding);
                
                return $file_buffer;
                
            } catch (BaseException $e) {
                // $this->SetError($e->message);
                return '';
                
            }
        }
      
        /**
        * Returns the start of a message boundary.
        * @access private
        */
        public static function GetBoundaryBegin($boundary, $charSet, $contentType, $encoding) {
            $result = '';
            
            $result .= Strings::TextLine('--' . $boundary);
            $result .= sprintf("Content-Type: %s; charset = \"%s\"", $contentType, $charSet);
            $result .= Strings::LE;
            $result .= Strings::HeaderLine('Content-Transfer-Encoding', $encoding);
            $result .= Strings::LE;

            return $result;
        }

        /**
        * Returns the end of a message boundary.
        * @access private
        */
        public static function GetBoundaryEnd($boundary) {
            return Strings::LE . '--' . $boundary . '--' . Strings::LE;
        }

        /**
        *  Returns a formatted header line.
        * @access public
        * @return string
        */
        public static function HeaderLine($name, $value) {
            return $name . ': ' . $value . Strings::LE;
        }

        /**
        * Returns a formatted mail line.
        * @access public
        * @return string
        */
        public static function TextLine($value) {
            return $value . Strings::LE;
        }
        
        public static function Expand($s, $l, $c) {
            if( strlen($s) >= $l )
                return $s;
            else
                return str_repeat($c, $l - strlen($s)).$s;
        }
        
        public static function ExplodeByLengthSentences($text, $bound = 450) {
            //$text = preg_split("/(?<![A-ZА-Я])[\.?!]+\s*(?=[A-ZА-Я][^\.]|$)/", $text);
            //$text = preg_split('#(?<=(\.|\!|\?))\s#s', $text);
            $text = preg_split("/(?<=[.?!])\s+(?=[A-ZА-Я0-9])/", $text);
            $second = array_map("trim", $text);
            $first = array();
            
            $length = 0;
            while ($length < $bound && $tmp = array_shift($second)) {
                $first[] = $tmp;
                $length += Strings::Length($tmp);
            }
            
            return array(
                implode(' ', $first),
                implode(' ', $second),
            );
        }
        
        public static function Replacements($string, $planeObject) {
            
            $string = preg_replace_callback('/\[([^\]]*)\]/im', function($match) use ($planeObject) {
                $planeObject = (array)$planeObject;
                $v = $match[1];
                $res = preg_match('/\(([^\)]+)\)/im', $v, $matches);
                if($res > 0) {
                    $vv = explode('(', $v);
                    $vv = reset($vv);
                    if(isset($planeObject[$vv])) {
                        $vvv = $planeObject[$vv];
                        $params = $matches[1];
                        $params = explode('&', $params);
                        foreach($params as $param) {
                            $param = explode('=', $param);
                            $vvv = str_replace('('.$param[0].')', $param[1], $vvv);
                        }
                        return $vvv;
                    }
                }
                else {
                    if(isset($planeObject[$v]))
                        return $planeObject[$v];
                }
                return false;
                
            }, $string);
            return $string;
            
        }

        
    }
    
    class Encoding {
        
        const UTF8 = "utf-8";
        const CP1251 = "windows-1251";
        
        public static function Convert($string, $to, $from = false) {
            
            if(!$from)
                $from = Encoding::Detect($string);
            
            $to = strtolower($to);
            $from = strtolower($from);
            
            if($from != $to)
                return mb_convert_encoding($string, $to, $from);
                
            return $string;
        }
        
        public static function Check($string, $encoding) {
            return mb_check_encoding($string, strtolower($encoding));
        }
        
        public static function Detect($string) {
            return strtolower(mb_detect_encoding($string));
        }
            
        
        
    }
    

?>

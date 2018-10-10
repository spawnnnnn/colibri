<?php
    
    //  hashing class state and register storage object. Abstract base class only.
    class hashData {
        var $hash = null;
    }
     
    class hashMessage {
        //    retrieve the next chunk
        function nextChunk() {
            trigger_error('hashMessage::nextChunk() NOT IMPLEMENTED', E_USER_WARNING);
            return false;
        }
        
        function currentChunk() {
            trigger_error('hashMessage::currentChunk() NOT IMPLEMENTED', E_USER_WARNING);
            return false;
        }
    }
     
     
    class hashMessageFile extends hashMessage {
        function __construct( $filename ) {
            trigger_error('hashMessageFile::hashMessageFile() NOT IMPLEMENTED', E_USER_WARNING);
            return false;
        }
    }
     
    class hashMessageURL extends hashMessage {
        function __construct( $url ) {
            trigger_error('hashMessageURL::hashMessageURL() NOT IMPLEMENTED', E_USER_WARNING);
            return false;
        }
    }
     
     
    class hash {

        function __construct($str, $mode = 'hex') {
            trigger_error('hash::hash() NOT IMPLEMENTED', E_USER_WARNING);
            return false;
        }
     
        static function hashChunk($str, $length, $mode = 'hex') {
            trigger_error('hash::hashChunk() NOT IMPLEMENTED', E_USER_WARNING);
            return false;
        }
        
        static function hashFile($filename, $mode = 'hex') {
            trigger_error('hash::hashFile() NOT IMPLEMENTED', E_USER_WARNING);
            return false;
        }
     
        static function hashChunkFile($filename, $length, $mode = 'hex') {
            trigger_error('hash::hashChunkFile() NOT IMPLEMENTED', E_USER_WARNING);
            return false;
        }
        
        static function hashURL($url, $timeout = null, $mode = 'hex') {
            trigger_error('hash::hashURL() NOT IMPLEMENTED', E_USER_WARNING);
            return false;
        }
     
        static function hashChunkURL($url, $length, $timeout = null, $mode = 'hex') {
            trigger_error('hash::hashChunkURL() NOT IMPLEMENTED', E_USER_WARNING);
            return false;
        }
    }
     
    class SHA256 {

        static function hash($str, $mode = 'hex') {
            return SHA256::_hash( '', $str, $mode );
        }
        
        static function hashFile($filename, $mode = 'hex') {
            return SHA256::_hash( 'File', $filename, $mode );
        }
        
        static function hashURL($url, $mode = 'hex') {
            return SHA256::_hash( 'URL', $url, $mode );
        }
     
        static function _hash( $type, $str, $mode ) {
            $modes = array( 'hex', 'bin', 'bit' );
            $ret = false;
            if(!in_array(strtolower($mode), $modes)) {
                trigger_error('mode specified is unrecognized: ' . $mode, E_USER_WARNING);
            }
            else {
                $data = new SHA256Data( $type, $str );
     
                SHA256::compute($data);
     
                $func = array('SHA256', 'hash' . $mode);
                if(is_callable($func)) {
                    $func = 'hash' . $mode;
                    $ret = SHA256::$func($data);
                    //$ret = call_user_func($func, $data);
                    
                    if( $mode === 'HEX' )
                    {
                        $ret = strtoupper( $ret );
                    }
                }
                else {
                    trigger_error('SHA256::hash' . $mode . '() NOT IMPLEMENTED.', E_USER_WARNING);
                }
            }
            return $ret;
        }
            
        
        //    32-bit summation
        static function sum() {
            $T = 0;
            for($x = 0, $y = func_num_args(); $x < $y; $x++) {
                $a = func_get_arg($x);
                $c = 0;
                for($i = 0; $i < 32; $i++) {
                    $j = (($T >> $i) & 1) + (($a >> $i) & 1) + $c;
                    $c = ($j >> 1) & 1;
                    $j &= 1;
                    $T &= ~(1 << $i);
                    $T |= $j << $i;
                }
            }
            return $T;
        }
        
        static function compute(&$hashData) {
            static $vars = 'abcdefgh';
            static $K = null;
            if($K === null) {
                $K = array (
                     1116352408,     1899447441,    -1245643825,     -373957723,
                      961987163,     1508970993,    -1841331548,    -1424204075,
                     -670586216,      310598401,      607225278,     1426881987,
                     1925078388,    -2132889090,    -1680079193,    -1046744716,
                     -459576895,     -272742522,      264347078,      604807628,
                      770255983,     1249150122,     1555081692,     1996064986,
                    -1740746414,    -1473132947,    -1341970488,    -1084653625,
                     -958395405,     -710438585,      113926993,      338241895,
                      666307205,      773529912,     1294757372,     1396182291,
                     1695183700,     1986661051,    -2117940946,    -1838011259,
                    -1564481375,    -1474664885,    -1035236496,     -949202525,
                     -778901479,     -694614492,     -200395387,      275423344,
                      430227734,      506948616,      659060556,      883997877,
                      958139571,     1322822218,     1537002063,     1747873779,
                     1955562222,     2024104815,    -2067236844,    -1933114872,
                    -1866530822,    -1538233109,    -1090935817,     -965641998,
                    );
            }
            $W = array();
            while(($chunk = $hashData->message->nextChunk()) !== false) {

                for($j = 0; $j < 8; $j++)
                    ${$vars{$j}} = $hashData->hash[$j];
                
                for($j = 0; $j < 64; $j++) {
                    if($j < 16) {
                        $T1  = ord($chunk{$j*4  }) & 0xFF; $T1 <<= 8;
                        $T1 |= ord($chunk{$j*4+1}) & 0xFF; $T1 <<= 8;
                        $T1 |= ord($chunk{$j*4+2}) & 0xFF; $T1 <<= 8;
                        $T1 |= ord($chunk{$j*4+3}) & 0xFF;
                        $W[$j] = $T1;
                    }
                    else {
                        $W[$j] = SHA256::sum(((($W[$j-2] >> 17) & 0x00007FFF) | ($W[$j-2] << 15)) ^ ((($W[$j-2] >> 19) & 0x00001FFF) | ($W[$j-2] << 13)) ^ (($W[$j-2] >> 10) & 0x003FFFFF), $W[$j-7], ((($W[$j-15] >> 7) & 0x01FFFFFF) | ($W[$j-15] << 25)) ^ ((($W[$j-15] >> 18) & 0x00003FFF) | ($W[$j-15] << 14)) ^ (($W[$j-15] >> 3) & 0x1FFFFFFF), $W[$j-16]);
                    }
     
                    $T1 = SHA256::sum($h, ((($e >> 6) & 0x03FFFFFF) | ($e << 26)) ^ ((($e >> 11) & 0x001FFFFF) | ($e << 21)) ^ ((($e >> 25) & 0x0000007F) | ($e << 7)), ($e & $f) ^ (~$e & $g), $K[$j], $W[$j]);
                    $T2 = SHA256::sum(((($a >> 2) & 0x3FFFFFFF) | ($a << 30)) ^ ((($a >> 13) & 0x0007FFFF) | ($a << 19)) ^ ((($a >> 22) & 0x000003FF) | ($a << 10)), ($a & $b) ^ ($a & $c) ^ ($b & $c));
                    $h = $g;
                    $g = $f;
                    $f = $e;
                    $e = SHA256::sum($d, $T1);
                    $d = $c;
                    $c = $b;
                    $b = $a;
                    $a = SHA256::sum($T1, $T2);
                }
                
                for($j = 0; $j < 8; $j++)
                    $hashData->hash[$j] = SHA256::sum(${$vars{$j}}, $hashData->hash[$j]);
                    
            }
        }
        
        static function hashHex(&$hashData) {
            $str = '';
            
            reset($hashData->hash);
            do {
                $str .= sprintf('%08x', current($hashData->hash));
            } while(next($hashData->hash));
            
            return $str;
        }
        
        static function hashBin(&$hashData) {
            $str = '';
            
            reset($hashData->hash);
            do {
                $str .= pack('N', current($hashData->hash));
            } while(next($hashData->hash));
            
            return $str;
        }
        
        static function hashBit(&$hashData) {
            $str = '';
            
            reset($hashData->hash);
            do {
                $t = current($hashData->hash);
                for($i = 31; $i >= 0; $i--) {
                    $str .= ($t & (1 << $i) ? '1' : '0');
                }
            } while(next($hashData->hash));
            
            return $str;
        }
    }
     
    class SHA256Data extends hashData {
        
        function __construct( $type, $str ) {
            $type = 'SHA256Message' . $type;
            $this->message = new $type( $str );
            
            //    H(0)
            $this->hash = array (
                 1779033703,    -1150833019,
                 1013904242,    -1521486534,
                 1359893119,    -1694144372,
                  528734635,     1541459225,
            );
        }
    }
     
    class SHA256Message extends hashMessage {
        function __construct( $str ) {
            $str .= $this->calculateFooter( strlen( $str ) );
            
            preg_match_all( '#.{64}#', $str, $this->chunk );
            $this->chunk = $this->chunk[0];
            
            $this->curChunk = -1;
        }
        
        function nextChunk() {
            if( is_array($this->chunk) && ($this->curChunk >= -1) && isset($this->chunk[$this->curChunk + 1]) ) {
                $this->curChunk++;
                $ret =& $this->chunk[$this->curChunk];
            }
            else {
                $this->chunk = null;
                $this->curChunk = -1;
                $ret = false;
            }
            
            return $ret;
        }
        
        function currentChunk() {
            if( is_array($this->chunk) ) {
                if( $this->curChunk == -1 ) {
                    $this->curChunk = 0;
                }
                if( ($this->curChunk >= 0) && isset($this->chunk[$this->curChunk]) ) {
                    $ret =& $this->chunk[$this->curChunk];
                }
            }
            else {
                $ret = false;
            }
            return $ret;
        }
        
        function calculateFooter( $numbytes ) {
            $M =& $numbytes;
            $L1 = ($M >> 28) & 0x0000000F;    //    top order bits
            $L2 = $M << 3;    //    number of bits
            $l = pack('N*', $L1, $L2);
            $k = $L2 + 64 + 1 + 511;
            $k -= $k % 512 + $L2 + 64 + 1;
            $k >>= 3;   
            $footer = chr(128) . str_repeat(chr(0), $k) . $l;
            assert('($M + strlen($footer)) % 64 == 0');
            return $footer;
        }
    }
     
     
    class SHA256MessageFile extends hashMessageFile {
        function __construct( $filename ) {
            $this->filename = $filename;
            $this->fp = false;
            $this->size = false;
            $this->append = '';
            $this->chunk = '';
            $this->more = true;
            
            $info = parse_url( $filename );
            if( isset( $info['scheme'] ) && !in_array(strtolower( $info['scheme'] ), array('php','file')) ) {
                trigger_error('SHA256MessageFile(' . var_export($filename,true) . ' does not handle the ' . var_export( $info['scheme'], true ) . ' protocol.', E_USER_ERROR);
                return;
            }
            
            $this->fp = (is_readable( $filename ) ? @fopen( $filename, 'rb' ) : false);
            
            if( $this->fp === false ) {
                trigger_error('SHA256MessageFile(' . var_export($filename,true) . '): unable to open file for SHA256 hashing.', E_USER_ERROR);
            }
     
            $stat = @fstat( $this->fp );
            
            if( $stat === false ) {
                trigger_error('SHA256MessageFile(' . var_export($filename,true) . '): unable to pull file status information.', E_USER_ERROR);
                return;
            }
            
            $this->append = SHA256Message::calculateFooter($this->size = intval($stat['size']));
        }
        
        //    load the next chunk from the file.
        function nextChunk() {
            $ret = false;
            
            if( $this->fp !== false ) {
                $ret = @fread( $this->fp, 64 );
                if( ($l = strlen($ret)) != 64 ) {
                    if(strlen($ret . $this->append) > 64) {
                        $ret = substr( $ret . $this->append, 0, 64 );
                        $this->append = substr( $this->append, 64 - $l );
                    }
                    else {
                        $ret .= $this->append;
                        $this->more = false;
                        
                        assert('strlen($ret) % 64 == 0');
                    }
                }
                
                if(!$this->more) {
                    @fclose( $this->fp );
                    $this->fp = false;
                    $this->size = false;
                    $this->append = '';
                }
            }
            
            $this->chunk = (string)$ret;
            
            return $ret;
        }
        
        //    return the current chunk that was previously loaded
        function currentChunk() {
            if( $this->chunk === '' && $this->fp !== false ) {
                return $this->nextChunk();
            }
            else {
                return ($this->chunk === '' ? false : $this->chunk);
            }
        }
    }
     
     
    class SHA256MessageURL extends hashMessageURL {
        //    timeout for a socket resource open request
        var $socket_timeout = 5;
        function __construct( $url ) {
            $this->fp = false;
            $this->more = true;
            $this->first = true;
            $this->headers = false;
            $this->append = '';
            $this->chunk = '';
            $this->curChunk = 0;
            $this->size = 0;
            
            if( ini_get( 'allow_url_fopen' ) == false ) {    //    allow_url_fopen is off, check if protocol is http or not
                $info = parse_url($url);
                if( !isset($info['scheme']) || (strcasecmp(trim($info['scheme']), 'http') == 0) ) {    //    http mode, use fsockopen
                    
                    if( !isset($info['scheme']) ) {
                        $url = 'http://' . $url;
                        $info = parse_url($url);
                    }
                    
                    if( function_exists( 'fsockopen' ) == false ) {
                        trigger_error('SHA256MessageURL(): allow_url_fopen is off, fsockopen is disabled or not available.', E_USER_WARNING);
                        return;
                    }
                    elseif(empty($info['host'])) {    //    fsockopen is on, but there's no known host in the url
                        trigger_error('SHA256MessageURL(' . var_export($url,true) .') does not appear to be a url.', E_USER_NOTICE);
                        return;
                    }
                    
                    //    protocol has been determined to be 'http', use fsockopen
                    $this->fp = @fsockopen( $info['host'], (isset($info['port']) ? $info['port'] : 80), $errno, $errstr, $this->socket_timeout );
                    
                    if(!$this->fp) {    //    fsockopen failed
                        trigger_error('SHA256MessageURL(): fsockopen failure ' . $errno . ' - ' . $errstr, E_USER_WARNING);
                        return;
                    }
                    
                    //    send the request for the page
                    @fwrite($this->fp, 'GET ' . (empty($info['path']) ? '/' : $info['path']) . " HTTP/1.0\r\nHost: " . strtolower($info['host']) . "\r\n\r\n");
                    $this->headers = true;
                }
                else {
                    trigger_error('SHA256MessageURL(' . var_export($url,true) . ') is using an unsupported protocol: ' . var_export($info['scheme']), E_USER_WARNING);
                }
            }
            else {    //    allow_url_fopen is enabled, lets see if we can open the url
                $info = parse_url( $url );
                
                if( !isset($info['scheme']) ) {
                    $url = 'http://' . $url;
                }
                
                $this->fp = fopen( $url, 'rb' );
                
                if( $this->fp === false ) {    //    we cannot open the url
                    trigger_error('SHA256MessageURL(' . var_export($url,true) . '): unable to open the url supplied.', E_USER_WARNING);
                }
            }
        }
        
        
        //    retrieve the next message chunk
        function nextChunk() {
            $this->tossHeader();
            
            $ret = false;
            
            if( is_array($this->chunk) ) {
                //    first pass?
                if( $this->first === true )
                    $this->first = false;
                else
                    $this->curChunk++;
                
                $ret = (isset($this->chunk[$this->curChunk]) ? $this->chunk[$this->curChunk] : false);
            }
            elseif( $this->fp !== false ) {
                if( $this->first == true ) {
                    if( ($l = strlen($this->append)) > 64 ) {
                        $ret = substr($this->append, 0, 64);
                        $this->append = substr($this->append, 64);
                    }
                    else {
                        $ret = $this->append . fread( $this->fp, 64 - $l );
                        $this->append = '';
                        $this->first = false;
                    }
                }
                else 
                    $ret = @fread( $this->fp, 64 );
                
                $l = strlen($ret);
                $this->size += $l;
     
                if( $l != 64 ) {
                    if(empty($this->append)) {
                        $this->append = SHA256Message::calculateFooter( $this->size );
                    }
                    
                    if(strlen($ret . $this->append) > 64) {
                        $ret = substr( $ret . $this->append, 0, 64 );
                        $this->append = substr( $this->append, 64 - $l );
                    }
                    else {
                        $ret .= $this->append;
                        $this->more = false;
                        assert('strlen($ret) % 64 == 0');
                    }
                }
                
                if(!$this->more) {
                    @fclose( $this->fp );
                    $this->fp = false;
                    $this->size = false;
                    $this->append = '';
                }
                
                $this->chunk = (string)$ret;
            }
            
            return $ret;
        }
        
        
        //    return the current chunk that was previously loaded
        function currentChunk() {
            if( $this->chunk === '' && $this->fp !== false ) {
                return $this->nextChunk();
            }
            else {
                return ($this->chunk === '' ? false : $this->chunk);
            }
        }
     
        function tossHeader() {
            if( $this->headers === true ) {
                $buf = '';
                while(!feof($this->fp)) {
                    $buf .= fread($this->fp, 4);
                    if( preg_match("#(\r\n|\n\r|\r|\n)\\1#s", $buf, $match, PREG_OFFSET_CAPTURE ) ) {
                        $this->append = substr( $buf, $match[0][1] + strlen($match[0][0]) );
                        $this->headers = false;
                        break;
                    }
                }
                
                if( $this->headers === true ) {    //    the header/content divide was not found, End of file reached.
                    trigger_error('SHA256MessageURL::tossHeader(): no headers were sent. Falling back to string hashing', E_USER_NOTICE);
                    //    prevent this from showing again.
                    $this->headers = false;
                    
                    // fall back to breaking the whole thing apart into an array of chunks, just like SHA256Message
                    $this->chunk = $buf . SHA256Message::calculateFooter( $this->size = strlen( $buf ) );
                    preg_match_all( '#.{64}#', $str, $this->chunk );
                    $this->chunk = $this->chunk[0];
                }
            }
        }
    }
     
?>

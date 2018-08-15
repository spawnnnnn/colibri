<?php
    
    class Encryption {
        
        static function Encrypt($key, $data) {
            $sha = sha256::hash($key);
            $data = rc4crypt::encrypt($sha, $data);
            $data = base64_encode($data);    
            return $data;
        }
        
        static function Decrypt($key, $data) {
            $sha = sha256::hash($key);
            $data = base64_decode($data);
            $data = rc4crypt::decrypt($sha, $data);
            return $data;
        }
        
    }

?>

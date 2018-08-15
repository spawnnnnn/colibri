<?php
    
    class Nginx {
        
        public static function PurgeAll() {
            
        }
        
        public static function Purge($url) {
            
            // fastcgi_cache_key "$server_addr:$server_port$request_uri|$cookie_phpsessid|$cookie_contact|$cookie_xhprof";
            // rm /var/nginx/cache/$1
            
            $cache_path = '/var/nginx/cache/';
            $url = parse_url($url);
            if(!$url) {
                return $ret;
            }
            $scheme = $url['scheme'];
            $host = $url['host'];
            $requesturi = $url['path'];
            $requestmethod = 'GET';
            $server_addr = $_SERVER['SERVER_ADDR'];
            $server_port = $_SERVER['SERVER_PORT'];
            
            // out('v3'.$server_addr.$server_port.$requesturi.$requestmethod.urlencode($_COOKIE['CMP_HASH']));
            $hash = md5('v3'.$server_addr.$server_port.$requesturi.$requestmethod); // .urlencode($_COOKIE['CMP_HASH'])
            
            // out($cache_path . substr($hash, -1) . '/' . substr($hash,-3,2) . '/' . $hash);
            return shell_exec('sudo /srv/www/v3/htdocs/current/_/__purge '. substr($hash, -1) . '/' . substr($hash,-3,2) . '/' . $hash);
        }

        
    }
    
?>

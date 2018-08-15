<?php
    
    class ServiceCache {
        
        const BASE_RESULTS = 5000;
        
        const BASE_DIR = '/requests/';
        
        protected static function getPaths($service_type, $cid) {
            $paths = [];
            if (is_numeric($cid) && $cid <= static::BASE_RESULTS) {
                $paths = [
                    _PATH.'res/base_requests/'.$service_type.'/'.$cid.'.request',
                    _PATH.'res/base_requests/'.$service_type.'/'.$cid.'.request.results'
                ];
            }
            else {
                $folder1 = Strings::Substring($cid, -1, 1);
                $folder2 = Strings::Substring($cid, -3, 2);
                $paths = [
                    self::BASE_DIR.$service_type.'/'.$folder1.'/'.$folder2.'/'.$cid.'.request',
                    self::BASE_DIR.$service_type.'/'.$folder1.'/'.$folder2.'/'.$cid.'.request.results'
                ];
                
                $file = $paths[0]; 
                
                if ( ! file_exists(dirname($file))) {
                    mkdir(dirname($file), 0777, true);
                    chmod(dirname($file), 0777);
                    chmod(dirname(dirname($file)), 0777);
                    chmod(dirname(dirname(dirname($file))), 0777);
                }
            }
            
            return $paths;
        }
        
        protected static function getAdditional($request, $results, $service_type) {
            return array();
        }
        
        protected static function Store($request, $results, $additional, $service_type) {
            if (is_array($request) && isset($request['cid'])) {
                $cid = intval($request['cid']);
            }
            else {
                $cid = ServiceApi::NewCID();
            }
            
            $key = static::Key($request);

            list($file, $fileresults) = static::getPaths($service_type, $cid);

            file_put_contents($file, serialize(array(
                'cid' => $cid,
                'key' => $key,
                'request' => $request,
                'additional' => $additional,
            )));
            
            file_put_contents($fileresults, gzcompress(serialize($results)));
            
            try {
                list($symfile, $symfileresults) = static::getPaths($service_type, $key);
                $basePath = dirname(dirname(dirname($file)));
                
                if (is_link($symfile)) {
                    unlink($symfile);
                }
                if (is_link($symfileresults)) {
                    unlink($symfileresults);
                }
                
                symlink(str_replace($basePath, '../../', $file), $symfile);
                symlink(str_replace($basePath, '../../', $fileresults), $symfileresults);
            }
            catch(Exception $e) {
                //out($e->getMessage());
            }
            
            return $cid;
        }
        
        protected static function Load($cidOrKey, $service_type) {

            if (is_array($cidOrKey) || is_object($cidOrKey)) {
                $cidOrKey = static::Key($cidOrKey);
            }                         

            list($file, $fileresults) = static::getPaths($service_type, $cidOrKey);
            
            $cache = unserialize(file_get_contents($file));
            if(!$cache)
                throw new Exception('cache not exists');
            
            if(file_exists($fileresults)) {
                $cache['results'] = unserialize(gzuncompress(file_get_contents($fileresults)));
            }
            else
                $cache['results'] = false;
            
            return $cache;
        }
        
        public static function Exists($request, $service_type) {
            
            $cidOrKey = $request;
            if (is_array($request) || is_object($request))
                $cidOrKey = static::Key($request);

            list($file, $fileresults) = static::getPaths($service_type, $cidOrKey);
            
            return file_exists($file) && file_exists($fileresults);
        }
        
        public static function Key($request) {
            $request = is_object($request) ? (array) $request : $request;
            if (is_array($request)) {
                unset($request['requesttime']);
                unset($request['token']);
                unset($request['remote_addr']);
                unset($request['allowed_banks']);
                unset($request['cid']);
            }
            return md5(json_encode($request));
        }
        
        public static function Write($request, $results, $service_type, $additionalFields = []) {
            
            $additional = static::getAdditional($request, $results, $service_type);
            if ($additionalFields) {
                $additional = array_merge($additional, $additionalFields);
            }
            
            $cid = static::Store($request, $results, $additional, $service_type);
            
            $res = new stdClass();
            $res->cid = $cid;
            $res->key = static::Key($request);
            $res->additional = (object) $additional;
            $res->request = $request;
            $res->results = $results;
            
            return $res;
        }
        
        public static function Read($request, $service_type) {

            if (!static::Exists($request, $service_type)) {
                if (is_numeric($request)) {
                    $statsapi = new StatsApi();
                    if ($request_data = $statsapi->GetRequestData($service_type, $request)) {
                        $res =  new stdClass();
                        $res->cid = $request;
                        $res->key = '';
                        $res->additional = new stdClass();
                        $res->request = isset($request_data->request) ? (array) $request_data->request : array();
                        $res->results = '';
                        
                        if ( ! isset($res->request['version'])) { $res->request['version'] = 1; }
                        $res->request['deleted'] = 1;
                        
                        return $res;
                    }
                }
                return false;
            }
            
            $data = static::Load($request, $service_type);
            
            $res = new stdClass();
            $res->cid = $data["cid"];
            $res->key = $data["key"];
            $res->additional = isset($data["additional"]) ? $data["additional"] : array();
            $res->additional = (object) $res->additional;
            $res->request = $data["request"];
            $res->results = $data["results"];
            
            if (is_array($res->request) && ! isset($res->request['version'])) { $res->request['version'] = 1; }
            
            return $res;
        }
        
    }
?>
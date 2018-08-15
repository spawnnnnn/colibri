<?
    

    
    class Downloader {

        private function _getUrl($url) {

            /* get url title and description */
            $request = new WebRequest($url);

            $request->timeout = 10;
            $res = $request->Request();

            if($res->status != 200) {
                return '';
            }
            
            $res->data = str_replace('win-1251', 'windows-1251', $res->data);
            return $res->data;
            
        }
        
        private function detect_encoding($string) {  
            foreach (array('utf-8', 'windows-1251') as $item) {
                try { $sample = mb_convert_encoding($string, $item, $item); } catch(Exception $e) { $sample = null; }
                if (md5($sample) == md5($string))
                    return $item;
            }
            return null;
        }        
        
        private function _loadHTML($data) {
                
            libxml_use_internal_errors(true);
            
            $realEncoding = 'utf-8';
            
            if(preg_match('/meta.*?charset=.*?/', $data) == 0) {
                $realEncoding = $this->detect_encoding($data);
                if($realEncoding != 'utf-8')
                    $data = mb_convert_encoding($data, 'utf-8', $realEncoding);
                $realEncoding = '';
            }
            
            $data = str_replace('<meta charset="utf-8">', '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />', $data);
            
            try {$data = gzuncompress($data);} catch(Exception $e) { };
            
            $data = preg_replace('@<\!--(.*?)-->@sim', '', $data);
            /*$data = preg_replace('@<link(.*?)>@sim', '', $data);*/
            $data = preg_replace('@<script(.*?)>(.*?)<\/script>@sim', '', $data);
            $data = preg_replace('@(\r\n)@sim', '', $data);
            $data = preg_replace('@(\s+)@sim', ' ', $data);
            $data = preg_replace('@<p>(\s*)<\/p>@sim', '', $data);
            $data = preg_replace('@<style(.*?)>(.*?)<\/style>@sim', '', $data);
            $data = preg_replace('@<noscript(.*?)>(.*?)<\/noscript>@sim', '', $data);
            $data = preg_replace('@<form(.*?)>(.*?)<\/form>@sim', '', $data);
            $data = preg_replace('@<input(.*?)>@sim', '', $data);
            $data = preg_replace('@<textarea(.*?)>(.*?)<\/textarea>@sim', '', $data);
            $data = preg_replace('@<select(.*?)>(.*?)<\/select>@sim', '', $data);
            $data = preg_replace('@<object(.*?)>(.*?)<\/object>@sim', '', $data);
            $data = preg_replace('@<noindex(.*?)>(.*?)<\/noindex>@sim', '', $data);
            $data = preg_replace('@<\/noindex>@sim', '', $data);
            $data = preg_replace('@\&nbsp;@sim', ' ', $data);
            $data = preg_replace('@\&mdash;@sim', '-', $data);
            $data = preg_replace('@\&quote;@sim', '', $data);
            
            /*out(mb_detect_encoding($content));
            if(mb_detect_encoding($content) != 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content));
            }*/
            
            /*preg_match('/@<html(.*?)>(.*?)<\/html>@sim/', $data, $matches);
            out($matches);*/
            
            $xml = null;
            if(strstr($data, '<!DOCTYPE html>') !== false || $realEncoding != 'utf-8')
                $data = '<'.'?xml version="1.0" encoding="utf-8"?'.'>'.$data;
            
            try{ $xml = XMLNode::LoadHTML($data, false); }catch(Exception $e) {}

            return $xml;
            
        }
        
        private function _getHeaders($url) {
            $headers = array();
            try { $headers = get_headers($url); } catch(Exception $e) {}
            
            $hdr = new Object();
            foreach($headers as $h) {
                $hh = explode(":", $h);
                if(count($hh) > 1) {
                    $name = strtolower(str_replace('-', '_', $hh[0]));
                    $hdr->$name = $hh[1];
                }
            }
            return $hdr;
        }

        private function _getOG($xml, &$titles, &$descriptions, &$images, &$video) {
            // get og headers
            $ogtitles = $xml->Query('//meta[@property=\'og:title\']');
            $ogdescriptions = $xml->Query('//meta[@property=\'og:description\']');
            $ogimages = $xml->Query('//meta[@property=\'og:image\']');
            $ogvideo = $xml->Query('//meta[@property=\'og:video\']');
            $ogvideowidth = $xml->Query('//meta[@property=\'og:video:width\']');
            $ogvideoheight = $xml->Query('//meta[@property=\'og:video:height\']');

            if($ogtitles->count > 0 && !Variable::IsEmpty($ogtitles->first->attributes->content->value))        
                $titles[] = preg_replace('@(\s+)@sim', ' ', trim($ogtitles->first->attributes->content->value));
            if($ogdescriptions->count > 0 && !Variable::IsEmpty($ogdescriptions->first->attributes->content->value))  
                $descriptions[] = preg_replace('@(\s+)@sim', ' ', trim($ogdescriptions->first->attributes->content->value));
            if($ogimages->count > 0 && !Variable::IsEmpty($ogimages->first->attributes->content->value))        
                $images[] = $ogimages->first->attributes->content->value;
            if($ogvideo->count > 0 && !Variable::IsEmpty($ogvideo->first->attributes->content->value))         
                $video = $ogvideo->first->attributes->content->value;
                
            if($ogvideowidth->count > 0 && $ogvideoheight->count > 0) {
                $video .= '&amp;irwidth='.$ogvideowidth->first->attributes->content->value.'&amp;irheight='.$ogvideoheight->first->attributes->content->value;
            }
        }
        
        private function _getLinksRel($xml, &$titles, &$descriptions, &$images, &$video) {
            /*
            <meta name="title" content="«Улучшайзер» на ivi.ru" /> 
            <link rel="image_src" href="http://img.ivi.ru/static/frames/7029/103606.2.jpg" /> 
            <link rel="video_src" href="http://www.ivi.ru/video/player?_isB2C=1&amp;videoId=7029&autoStart=1"/> 
            <meta name="description" content="Смотреть видео «Улучшайзер» онлайн бесплатно и без регистрации в разделе «Программы» – отличное качество на ivi.ru"/>
            */
            
            // get og headers
            if(count($titles) == 0) {
                $ogtitles = $xml->Query('//meta[@name=\'title\']');
                if($ogtitles->count > 0 && !Variable::IsEmpty($ogtitles->first->attributes->content->value))        
                    $titles[] = preg_replace('@(\s+)@sim', ' ', trim($ogtitles->first->attributes->content->value));
            }

            if(count($descriptions) == 0) {
                $ogdescriptions = $xml->Query('//meta[@name=\'description\']');
                if($ogdescriptions->count > 0 && !Variable::IsEmpty($ogdescriptions->first->attributes->content->value))  
                    $descriptions[] = preg_replace('@(\s+)@sim', ' ', trim($ogdescriptions->first->attributes->content->value));
            }

            $ogimages = $xml->Query('//link[@rel=\'image_src\']');
            $ogvideo = $xml->Query('//link[@rel=\'video_src\']');
            
            if($ogimages->count > 0 && !Variable::IsEmpty($ogimages->first->attributes->href->value))        
                $images[] = $ogimages->first->attributes->href->value;
            if($ogvideo->count > 0 && !Variable::IsEmpty($ogvideo->first->attributes->href->value))         
                $video = $ogvideo->first->attributes->href->value;
            
            
        }
        
        public function UrlInfo($url) {
            
            if(substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://') {
                $url = 'http://'.$url;
            }
            
            $adomain = explode("/", $url);
            $domain = $adomain[2];
            
            $data = $this->_getUrl($url);
            $xml = $this->_loadHTML($data);
            if(!$xml)
                return false;
                
            $titles = array();
            $descriptions = array();
            $images = array();
            $video = '';

            if($xml) {
                
                $this->_getOG($xml, $titles, $descriptions, $images, $video);
                $this->_getLinksRel($xml, $titles, $descriptions, $images, $video);
                
                if(count($titles) == 0) {
                    if($xml->head && $xml->head->title)     
                        $titles[] = Strings::PrepareAttribute(trim($xml->head->title->value));
                    
                    $h1s = $xml->Query('//h1');
                    if($h1s->count == 1)                    
                        $titles[] = Strings::PrepareAttribute(trim($h1s->first->value));
                    
                    $titles = array_unique($titles);
                    
                }
                
                //if(count($descriptions) == 0) {
                    
                    $descs = $xml->Query('//meta[contains(translate(@name, \'abcdefghijklmnopqrstuvwxyz\', \'ABCDEFGHIJKLMNOPQRSTUVWXYZ\'), \'DESCRIPTION\')]');
                    if($descs->count > 0)       
                        $descriptions[] = preg_replace('@(\s+)@sim', ' ', Strings::PrepareAttribute($descs->first->attributes->content->value));
                        
                        
                    $descs = $xml->Query('//*[contains(translate(@class, \'abcdefghijklmnopqrstuvwxyz\', \'ABCDEFGHIJKLMNOPQRSTUVWXYZ\'), \'CONTENT\')]');
                    if($descs->count > 0) 
                        foreach($descs as $ddesc) {
                            if(Strings::Length(trim(Strings::StripHTML($ddesc->xml), "\r\n\t ")) > 100)
                                $descriptions[] = preg_replace('@(\s+)@sim', ' ', Strings::StripHTML($ddesc->xml));
                        }
                    
                    $descs = $xml->Query('//*[contains(translate(@id, \'abcdefghijklmnopqrstuvwxyz\', \'ABCDEFGHIJKLMNOPQRSTUVWXYZ\'), \'ARTICLE\')]');
                    if($descs->count > 0) 
                        foreach($descs as $ddesc) {
                            if(Strings::Length(trim(Strings::StripHTML($ddesc->xml), "\r\n\t ")) > 100)
                                $descriptions[] = preg_replace('@(\s+)@sim', ' ', Strings::StripHTML($ddesc->xml));
                        }
                    
                    $descs = $xml->Query('//*[contains(translate(@class, \'abcdefghijklmnopqrstuvwxyz\', \'ABCDEFGHIJKLMNOPQRSTUVWXYZ\'), \'ARTICLE\')]');
                    if($descs->count > 0) 
                        foreach($descs as $ddesc) {
                            if(Strings::Length(trim(Strings::StripHTML($ddesc->xml), "\r\n\t ")) > 100)
                                $descriptions[] = preg_replace('@(\s+)@sim', ' ', Strings::StripHTML($ddesc->xml));
                        }
                    
                    $descs = $xml->Query('//*[contains(translate(@class, \'abcdefghijklmnopqrstuvwxyz\', \'ABCDEFGHIJKLMNOPQRSTUVWXYZ\'), \'TEXT\')]');
                    if($descs->count > 0)
                        foreach($descs as $ddesc) {
                            if(Strings::Length(trim(Strings::StripHTML($ddesc->xml), "\r\n\t ")) > 100)
                                $descriptions[] = preg_replace('@(\s+)@sim', ' ', Strings::StripHTML($ddesc->xml));
                        }
                    
                    $descs = $xml->Query('//*[contains(translate(@class, \'abcdefghijklmnopqrstuvwxyz\', \'ABCDEFGHIJKLMNOPQRSTUVWXYZ\'), \'BODY\')]');
                    if($descs->count > 0)
                        foreach($descs as $ddesc) {
                            if(Strings::Length(trim(Strings::StripHTML($ddesc->xml), "\r\n\t ")) > 100)
                                $descriptions[] = preg_replace('@(\s+)@sim', ' ', Strings::StripHTML($ddesc->xml));
                        }
                    
                    $body = $xml->Query('//body');
                    if($body->count > 0)
                        $descriptions[] = Strings::PrepareAttribute(Strings::Words(Strings::StripHTML($body->first->xml), 50));
                    
                    
                    $descriptions = array_unique($descriptions);
                    $d = $descriptions;
                    $descriptions = array();
                    foreach($d as $dd) 
                        if(trim($dd) != '') 
                            $descriptions[] = $dd;
                    
                        
                //}
                
                if(count($images) == 0) {
                    
                    $imgs = $xml->Query('//img');
                    if($imgs->count > 0) {
                        $i = 0;
                        foreach($imgs as $img) {
                            
                            $src = trim($img->attributes->src->value);
                            if(!Variable::IsEmpty($src)) {
                            
                                if(substr($src, 0, 4) != 'http') $src = 'http://'.$domain.'/'.$src;
                                
                                $headers = $this->_getHeaders($src);
                                
                                $size = (int)$headers->content_length;
                                $type = $headers->content_type;
                                
                                if($size > 1024 && strstr($type, 'image') != false) {
                                    $images[$size] = $src;
                                }                             
                            
                                if($i ++ > 20)
                                    break;
                            
                            }        
                        
                        }
                        
                        $images = array_unique($images);
                        krsort($images);
                        $images = array_values($images);
                    }
                }

            }
            
            // fix html
                
            $d = new Object();    
            $d->titles = $titles;
            $d->descriptions = $descriptions;
            $d->url = $url;
            $d->images = $images;
            $d->video = $video;
            
            return $d;
            
        }
        
    }

?>
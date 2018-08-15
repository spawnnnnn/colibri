<?php
    class ScoreReport {
        
        public static function GetLines($text) {
            $lines = explode("\r\n", $text);
            
            $head = array();
            $data = array();
    
            foreach ($lines as $line) {
                $cols = explode(';', $line);
                $cols = array_map('trim', $cols);
                
                if ( ! $head) {
                    $head = $cols;
                    continue;
                }
                
                $data[] = $cols;
            }
            
            return array($head, $data);
        }
        
        public static function GetApplication($find, $fields) {
            $dtp = new DataPoint('auth');
            
            $where = '';
            foreach ($find as $key => $value) {
                if ($key == 'clientid') {
                    $where .= ' AND '.'data'.' LIKE \'%"clientid":"'.$value.'"%\'';
                } elseif ($key == 'email') {
                    $where .= ' AND '.'data'.' LIKE \'%"email":"'.$value.'"%\'';
                } elseif ($key == 'date') {
                    $where .= ' AND '.'data'.' LIKE \'%"date":"'.$value.'"%\'';
                } elseif ($key == 'phone') {
                    $where .= ' AND '.'data'.' LIKE \'%"phone":"'.$value.'"%\'';
                } else {
                    $where .= ' AND '.$key.'=\''.$value.'\'';
                }
            }
            $where = substr($where, 5);

            $dtrs = $dtp->Query('SELECT * FROM applications WHERE '.$where);
            $dtr = $dtrs->Read();
            
            $append = array();
            if ($dtr) {
                $additional = json_decode($dtr->additional);
                $data = json_decode($dtr->data);
                foreach ($fields as $field) {
                    if (isset($dtr->$field)) {
                        $append[$field] = $dtr->$field;
                    } elseif (isset($data->$field)) {
                        $append[$field] = $data->$field;
                    } elseif (isset($additional->$field)) {
                        $append[$field] = $additional->$field;
                    } else {
                        $append[$field] = '';
                    }
                    
                    if ($field == 'univeral') {
                        $append['univeral'] = (isset($data->master_consumer_posted) || isset($data->master_hypotheccredits_posted) || (isset($additional->master) && $additional->master === 'true')) ? '1' : '0';
                    } elseif ($field == 'date') {
                        $append['date'] = strtotime($append['date']);
                        $append['date'] += Date::HOUR*3; // To Moscow time
                        $append['date'] = date('d.m.Y H:i:s', $append['date']);
                    } elseif (is_array($append[$field]) || is_object($append[$field])) {
                        $append[$field] = json_encode($append[$field]);
                    }
                }
            } else {
                $append = array_map(function() {
                    return '';
                }, $fields);
            }
            
            return $append;
        }
        
        public static function Output($head, $fields, $requestids, $append, $data) {
            $return = '';
            
            $count_requestids = count($requestids);
            $count_append = count($append);
            $count_data = count($data);
            if ($count_requestids != $count_append || $count_requestids != $count_data) {
                throw new BaseException('Different count of requestids, append and data');
            }
            
            $head = array_merge(array('requestid'), array_values($fields), $head);
            $return .= implode(';', $head)."\r\n";
            
            for ($i = 0; $i < $count_requestids; $i++) {
                $row = array_merge($requestids[$i], $append[$i], $data[$i]);
                $return .= implode(';', $row)."\r\n";
            }
            
            return $return;
        }
        
    }
?>

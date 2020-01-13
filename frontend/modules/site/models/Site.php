<?php


namespace frontend\modules\site\models;

use common\classes\Debug;
use common\models\Audit;
use common\models\Dns;
use common\models\ExternalLinks;
use common\models\Links;
use common\models\Theme;
use common\models\Url;
use DOMDocument;
use dosamigos\highcharts\HighCharts;
use GuzzleHttp;
use http\Env\Request;
use yii\helpers\ArrayHelper;

//use GuzzleHttp\Psr7\Request;

class Site extends \common\models\Site
{
    public function init()
    {
        parent::init();
    }

    public static function getChart($data, $key)
    {
        $result = array();
        if($data->urls) {
            foreach ($data->urls[0]->audits as $value) {
                if($value->created_at >= strtotime("-3 month")){
                    if($key == "created_at") {
                        array_push($result, self::getDate($value->$key));
                    } else {
                        array_push($result, (int)$value->$key);
                    }
                }
            }
        }
        return $result;
    }

    public static function deleteSite($site)
    {
        try {
            foreach($site->urls as $url) {
                foreach ($url->audits as $audit) {
                    foreach ($audit->externalLinks as $link) {
                        ExternalLinks::deleteAll(['id' => $link->id]);
                    }
                    Audit::deleteAll(['id' => $audit->id]);
                }
                Url::deleteAll(['id' => $url->id]);
            }
            foreach ($site->dns as $dns) {
                Dns::deleteAll(['id' => $dns->id]);
            }
            Site::deleteAll(['id' => $site->id]);
        } catch (\Exception $e) { }
    }

    public static function getDate($date, $fl=0)
    {
        if($date) {
            $day = idate('d', $date);
            $month = idate('m', $date);
            $year = idate('Y', $date);

            if(!$fl) {
                return $day.".".$month.".".$year;
            } else {
                return strtotime($year."-".$month."-".$day);
            }
        }
    }

    public static function getDaysLeft($date) {
        $now = time();
        $expiration_date = self::getDate($date, 1);
        return floor(($expiration_date-$now)/ (60 * 60 * 24));
    }

    public static function getRegistrar($data, $fl)
    {
        $arr =  explode(", ", $data->registrar);

        if($fl) {
            return $data->registrar;
        } else {
            return implode("\n", $arr);
        }
    }

    public static function getStates($data, $fl)
    {
        $arr =  explode(", ", $data->states);

        if($fl == 1) {
            return $data->states;
        } elseif($fl == 0) {
            return implode("\n", $arr);
        } elseif ($fl == 2) {
            if(count($arr) == 1 && $arr[0] == "")
                return 0;
            else
                return count($arr);
        }
    }

    public static function getIp($data, $fl)
    {
        $ip_array = array();
        foreach($data->dns as $value) {
            if($value->ip) {
                array_push($ip_array, $value->ip);
            }
        }

        if($fl == 1) {
            return implode("<br>", $ip_array);
        } elseif($fl == 0) {
            return implode("\n", $ip_array);
        } elseif ($fl == 2) {
            return $ip_array;
        }
    }

    public static function getDnsServer($data, $fl)
    {
        $dns_array = array();

        foreach($data->dns as $value) {
            if($value->target) {
                array_push($dns_array, $value->target);
            }
        }

        if($fl == 1) {
            return implode("<br>", $dns_array);
        } elseif($fl == 0) {
            return implode("\n", $dns_array);
        } elseif ($fl == 2) {
            return $dns_array;
        }
    }

    public static function getAudit($data, $key)
    {
        $result = 0;
        if($data->urls) {
            foreach ($data->urls[0]->audits as $value) {
                $result = $value->$key;
            }
        }
        return $result;
    }

    public static function getIndex($data, $key)
    {
        $result = 0;
        if($data->indexing) {
            foreach ($data->indexing as $value) {
                $result = $value->$key;
            }
        }
        return $result;
    }

    public static function getAuditID($model, $key)
    {
        $id = str_replace("=", "", stristr($model, '='));
        $site = Site::findOne(['id' => $id]);
        $audit_id = 0;

        if($site->urls) {
            foreach ($site->urls[0]->audits as $value) {
                $audit_id = $value->id;
            }
        }
        return $audit_id;
    }

    public static function getAcceptor($data, $fl)
    {
        $n = 1;
        $external_links_array = array();
        if($data->urls) {
            if($data->urls[0]->audits) {
                $n = count($data->urls[0]->audits);
                if($n <= 0) $n = 1;
            }
        }

        if($data)
            if($data->urls)
                if($data->urls[0]->audits)
                    foreach ($data->urls[0]->audits[$n-1]->externalLinks as $value)
                        array_push($external_links_array, $value->acceptor);

        if($fl == 1)
            return implode("<br>", $external_links_array);
        elseif($fl == 0)
            return implode("\n", $external_links_array);
        elseif ($fl == 2)
            return $external_links_array;
    }

    public static function getAnchor($data, $fl)
    {
        $n = 1;
        $external_links_array = array();
        if($data->urls) {
            if($data->urls[0]->audits) {
                $n = count($data->urls[0]->audits);
                if($n <= 0) $n = 1;
            }
        }

        if($data) {
            if ($data->urls) {
                if ($data->urls[0]->audits) {
                    foreach ($data->urls[0]->audits[$n-1]->externalLinks as $value) {
                        $val = trim(self::clearstr($value->anchor));
                        $val = trim(str_replace(array("\r\n", "\r", "\n", "<br>"), "", $val));

                        if (!empty($val)) {
                            array_push($external_links_array, $val);
                        } else {
                            array_push($external_links_array, ' - анкор не задан');
                        }
                    }
                }
            }
        }

        if($fl == 1) {
            return implode("<br>", $external_links_array);
        } elseif($fl == 0) {
            return implode("\n", $external_links_array);
        } elseif ($fl == 2) {
            return $external_links_array;
        }
    }

    /**
     * Функция была взята с php.net
     **/
    public static function utf8_str_split($str) {
        $split=1;
        $array = array();
        for ( $i=0; $i < strlen( $str ); ){
            $value = ord($str[$i]);
            if($value > 127){
                if($value >= 192 && $value <= 223)
                    $split=2;
                elseif($value >= 224 && $value <= 239)
                    $split=3;
                elseif($value >= 240 && $value <= 247)
                    $split=4;
            }else{
                $split=1;
            }
            $key = NULL;
            for ( $j = 0; $j < $split; $j++, $i++ ) {
                $key .= $str[$i];
            }
            array_push( $array, $key );
        }
        return $array;
    }
    /**
     * Функция вырезки
     * @param <string> $str
     * @return <string>
     */
    public static function clearstr($str){
        $sru = 'ёйцукенгшщзхъфывапролджэячсмитьбю';
        $s1 = array_merge(self::utf8_str_split($sru), self::utf8_str_split(strtoupper($sru)), range('A', 'Z'), range('a','z'), range('0', '9'), array('&',' ','#',';','%','?',':','(',')','-','_','=','+','[',']',',','.','/','\\'));
        $codes = array();
        for ($i=0; $i<count($s1); $i++){
            $codes[] = ord($s1[$i]);
        }
        $str_s = self::utf8_str_split($str);
        for ($i=0; $i<count($str_s); $i++){
            if (!in_array(ord($str_s[$i]), $codes)){
                $str = str_replace($str_s[$i], '', $str);
            }
        }
        return $str;
    }
}
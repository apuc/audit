<?php


namespace frontend\modules\site\models;

use common\classes\Debug;
use common\models\Audit;
use common\models\Dns;
use common\models\ExternalLinks;
use common\models\Theme;
use common\models\Url;
use DOMDocument;
use GuzzleHttp;
use http\Env\Request;

//use GuzzleHttp\Psr7\Request;

class Site extends \common\models\Site
{
    public function init()
    {
        parent::init();
    }

    public static function getUrlName($id)
    {
        $url = Url::find()->where(['site_id' => $id])->asArray()->all();
        if($url) {
            return $url[0]['url'];
        }
    }

    public static function getIcon($url)
    {
        try {
            $client = new GuzzleHttp\Client();
            $client->request('GET', 'https://' . $url . '/favicon.ico');
            return "<img src='https://" . $url . "/favicon.ico'>";
        } catch (\Exception $e) {
            return "<img src='http://www.google.com/s2/favicons?domain=www." . $url . "'";
        }
    }

    public static function getDate($id, $key, $fl=0)
    {
        $site = \common\models\Site::find()->where(['id' => $id])->asArray()->all();
        if($site) {
            $day = idate('d', $site[0][$key]);
            $month = idate('m', $site[0][$key]);
            $year = idate('Y', $site[0][$key]);

            if(!$fl) {
                return $day.".".$month.".".$year;
            } else {
                return strtotime($year."-".$month."-".$day);
            }
        }
    }

    public static function getDaysLeft($id) {
        $now = time();
        $expiration_date = self::getDate($id, 'expiration_date', 1);
        return floor(($expiration_date-$now)/ (60 * 60 * 24));
    }

    public static function getTarget($id)
    {
        $dns = Dns::find()->where(['site_id'=>$id])->all();
        $target_array = array();

        foreach($dns as $value) {
            if($value->target) {
                array_push($target_array, $value->target);
            }
        }

        return implode("<br>", $target_array);
    }

    public static function getRegistrar($id, $fl)
    {
        $site = \common\models\Site::findOne(['id' => $id]);
        $arr = array();

        if($site) {
            $arr =  explode(", ", $site->registrar);
        }
        if($fl) {
            return implode("<br>", $arr);
        } else {
            return implode("\n", $arr);
        }

    }

    public static function getStates($id, $fl)
    {
        $site = \common\models\Site::findOne(['id' => $id]);
        $arr = array();

        if($site) {
            $arr =  explode(", ", $site->states);
        }
        if($fl) {
            return implode("<br>", $arr);
        } else {
            return implode("\n", $arr);
        }

    }

    public static function getIp($id, $fl)
    {
        $ip = Dns::find()->where(['site_id'=>$id])->all();
        $ip_array = array();

        foreach($ip as $value) {
            if($value->ip) {
                array_push($ip_array, $value->ip);
            }
        }
        if($fl) {
            return implode("<br>", $ip_array);
        } else {
            return implode("\n", $ip_array);
        }
    }

    public static function getDnsServer($id, $fl)
    {
        $dns = Dns::find()->where(['site_id'=>$id])->all();
        $dns_array = array();

        foreach($dns as $value) {
            if($value->target) {
                array_push($dns_array, $value->target);
            }
        }
        if($fl) {
            return implode("<br>", $dns_array);
        } else {
            return implode("\n", $dns_array);
        }
    }

    public static function getAudit($id, $key)
    {
        $audit_response = 0;
        $url = Url::find()->where(['site_id' => $id])->asArray()->all();
        if($url) {
            $url_id =  $url[0]['id'];
        }

        $audit = Audit::find()->where(['url_id'=>$url_id])->orderBy('created_at desc')->limit(1)->asArray()->all();
        if($audit) {
            $audit_response = $audit[0][$key];
        }

        return $audit_response;
    }

    public static function getAuditID($model, $key)
    {
        $id = str_replace("=", "", stristr($model, '='));
        $audit_response = 0;
        $url = Url::find()->where(['site_id' => $id])->asArray()->all();
        if($url) {
            $url_id =  $url[0]['id'];
        }

        $audit = Audit::find()->where(['url_id'=>$url_id])->orderBy('created_at desc')->limit(1)->asArray()->all();
        if($audit) {
            $audit_response = $audit[0][$key];
        }

        return $audit_response;
    }

    public static function getExternalLinks($id)
    {
       $audit_id = self::getAudit($id, 'id');
       $external_links = ExternalLinks::find()->where(['audit_id' => $audit_id])->all();

       $external_links_array = array();

       foreach ($external_links as $value) {
           //$val = trim(str_replace(array("\r\n", "\r", "\n", "<br>"), "", $value->anchor));
           $val = trim(self::clearstr($value->anchor));

           if(!empty($val)) {
               array_push($external_links_array, '<b>'.$value->acceptor.'</b> - ' . $value->anchor);
           } else {
               array_push($external_links_array, '<b>'.$value->acceptor.'</b> - анкор не задан');
           }
       }

       return implode("<br>", $external_links_array);
    }

    public static function getAcceptor($id, $fl)
    {
        $audit_id = self::getAudit($id, 'id');
        $external_links = ExternalLinks::find()->where(['audit_id' => $audit_id])->all();
        $external_links_array = array();

        if($external_links) {
            foreach ($external_links as $value) {
                array_push($external_links_array, $value->acceptor);
            }
        }
        if($fl) {
            return implode("<br>", $external_links_array);
        } else {
            return implode("\n", $external_links_array);
        }
    }

    public static function getAnchor($id, $fl)
    {
        $audit_id = self::getAudit($id, 'id');
        $external_links = ExternalLinks::find()->where(['audit_id' => $audit_id])->all();

        $external_links_array = array();

        if($external_links) {
            foreach ($external_links as $value) {


                $val = trim(self::clearstr($value->anchor));
                $val = trim(str_replace(array("\r\n", "\r", "\n", "<br>"), "", $val));

                if(!empty($val)) {
                    array_push($external_links_array, $val);
                } else {
                    array_push($external_links_array, ' - анкор не задан');
                }
            }
        }

        if($fl) {
            return implode("<br>", $external_links_array);
        } else {
            return implode("\n", $external_links_array);
        }
    }

    public static function getThemeCustom($id)
    {
        $site = Site::find()->where(['id' => $id])->asArray()->all();
        if($site) {
            $theme_id = $site[0]['theme_id'];
        }
        $theme = Theme::find()->where(['id' => $theme_id])->asArray()->all();
        if($theme) {
            return $theme[0]['name'];
        }
    }

    public static function getComment($id)
    {
        $site = Site::findOne(['id' => $id]);
        return $site->comment;
    }

    /**
     * Функция была взята с php.net
     **/
    public static function utf8_str_split($str) {
        // place each character of the string into and array
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
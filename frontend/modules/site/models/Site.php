<?php


namespace frontend\modules\site\models;


use common\classes\Debug;
use common\models\Audit;
use common\models\Dns;
use common\models\ExternalLinks;
use common\models\Url;
use GuzzleHttp;

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
        return "<img src='https://" . $url . "/favicon.ico'>";
    }

    public static function getDate($id, $key)
    {
        $site = \common\models\Site::find()->where(['id' => $id])->asArray()->all();
        if($site) {
            $day = idate('d', $site[0][$key]);
            $month = idate('m', $site[0][$key]);
            $year = idate('Y', $site[0][$key]);
//            switch ($month) {
//                case 1:
//                    $month = 'января';
//                    break;
//                case 2:
//                    $month = 'февраля';
//                    break;
//                case 3:
//                    $month = 'марта';
//                    break;
//                case 4:
//                    $month = 'апреля';
//                    break;
//                case 5:
//                    $month = 'мая';
//                    break;
//                case 6:
//                    $month = 'июня';
//                    break;
//                case 7:
//                    $month = 'июля';
//                    break;
//                case 8:
//                    $month = 'августа';
//                    break;
//                case 9:
//                    $month = 'сентября';
//                    break;
//                case 10:
//                    $month = 'октября';
//                    break;
//                case 11:
//                    $month = 'ноября';
//                    break;
//                case 12:
//                    $month = 'декабря';
//                    break;
//            }
            return $day.".".$month.".".$year;
        }
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

    public static function getIp($id)
    {
        $ip = Dns::find()->where(['site_id'=>$id])->all();
        $ip_array = array();

        foreach($ip as $value) {
            if($value->ip) {
                array_push($ip_array, $value->ip);
            }
        }

        return implode("<br>", $ip_array);
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
           if(trim($value->anchor) != "") {
               array_push($external_links_array, $value->acceptor . " - " . trim($value->anchor));
           } else {
               array_push($external_links_array, $value->acceptor . " - анкор не задан");
           }
       }

       return implode("<br>", $external_links_array);
    }

    public function getTheme($id)
    {

    }

}
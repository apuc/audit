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

    public static function getDate($id, $key)
    {
        $site = \common\models\Site::find()->where(['id' => $id])->asArray()->all();
        if($site) {
            $day = idate('d', $site[0][$key]);
            $month = idate('m', $site[0][$key]);
            $year = idate('Y', $site[0][$key]);

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
               array_push($external_links_array, '<b>'.$value->acceptor.'</b> - ' . trim($value->anchor));
           } else {
               array_push($external_links_array, '<b>'.$value->acceptor.'</b> - анкор не задан');
           }
       }

       return implode("<br>", $external_links_array);
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

}
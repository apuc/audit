<?php


namespace frontend\modules\links\models;


use common\services\AuditService;

class Links  extends \common\models\Links
{
    public function init()
    {
        parent::init();
    }

    public static function addLinks($links)
    {
        $links = self::formattingLinks($links);
        foreach ($links as $value) {
            $link = new \common\models\Links();
            $link->name = self::cutDomain(self::cutUrl($value));
            $link->link = $value;
            $link->save();
        }
    }

    public static function formattingLinks($links)
    {
        $exploded_urls = explode(",", str_replace(array("\r\n", "\r", "\n"), ",", $links));
        $formatting_urls = array();
        foreach ($exploded_urls as $exploded_url) {
            $trim_url = trim($exploded_url);
            if ($trim_url)
                array_push($formatting_urls, $trim_url);
        }
        return $formatting_urls;
    }

    public static function cutDomain($domain)
    {
        return (strripos($domain, '/')) ? stristr($domain, '/', true) : $domain;
    }

    public static function cutUrl($url)
    {
        return str_replace(array("http://", "https://", "www."), "", $url);
    }
}
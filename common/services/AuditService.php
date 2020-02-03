<?php

namespace common\services;

use common\classes\CurlHelper;
use common\classes\Debug;
use common\models\Audit;
use common\models\AuditPending;
use common\models\Dns;
use common\models\ExternalLinks;
use common\models\Indexing;
use common\models\Site;
use frontend\modules\url\models\DataForm;
use frontend\modules\url\models\Url;
use Iodev\Whois\Whois;
use phpQuery;
use TrueBV\Punycode;
use Yii;
use DateTime;
use Exception;


class AuditService
{
    public static function addData($data_array, $report)
    {
        foreach ($data_array as $data) {
            if (!$data->isSiteExist() && !$data->isUrlExist())
                self::insertSiteAndUrl($data, $report);
            elseif ($data->isSiteExist() && !$data->isUrlExist())
                self::insertUrl($data, $report);
        }
        return true;
    }

    public static function insertSiteAndUrl($data, $report)
    {
        try {
            $site_id = self::addSite($data->getSite());
            self::createUrl($data->getSiteUrl(), $site_id);
            self::addIndexing($site_id);
            $report->newSite++;
            $report->newUrl++;
        } catch (Exception $e) {
            Debug::prn($e->getMessage());
            $report->errorsUrl++;
            array_push($report->errorUrlArray, $data->getSiteUrl());
        }
    }

    public static function insertUrl($data, $report)
    {
        $site_id = Site::find()->where(['name' => $data->getSite()])->asArray()->all()[0]['id'];
        self::createUrl($data->getSiteUrl(), $site_id);

        $report->newUrl++;
    }

    public static function addIndexing($site_id)
    {
        $indexing = new Indexing();
        $indexing->google_indexing = 0;
        $indexing->yandex_indexing = 0;
        $indexing->google_indexed_pages = 0;
        $indexing->iks = 0;
        $indexing->status_google = 0;
        $indexing->status_yandex = 0;
        $indexing->status_indexing_pages = 0;
        $indexing->status_iks = 0;
        $indexing->status_date_cache = 0;
        $indexing->site_id = $site_id;
        $indexing->save();
    }


    public static function addSite($domain)
    {
        $domain = self::cutDomain($domain);
        try {
            $whois = Whois::create();
            $info = $whois->loadDomainInfo($domain);
            if ($info) return self::createSite($info, $domain);
            else {
                $host_names = explode(".", $domain);
                $bottom_host_name = $host_names[count($host_names) - 2] . "." . $host_names[count($host_names) - 1];
                $domain = $bottom_host_name;
                try {
                    $info = $whois->loadDomainInfo($domain);
                    return self::createSite($info, $domain);
                } catch (Exception $e) {
                    Debug::prn($e->getMessage());
                    return null;
                }
            }
        } catch (Exception $e) {
            Debug::prn($e->getMessage());
            return self::createSite(null, $domain);
        }
    }

    public static function addAudit($domain, $url_id, $pending_id)
    {
        AuditPending::deleteAll(['id' => $pending_id]);
        $server_response_code = 0;
        $size = 0;
        $loading_time = 0;
        $count = 0;
        $expirationDate = null;

        try {
            $whois = Whois::create();
            $info = $whois->loadDomainInfo($domain);
            if ($info) $expirationDate = $info->getExpirationDate();
            else {
                $host_names = explode(".", $domain);
                $bottom_host_name = $host_names[count($host_names) - 2] . "." . $host_names[count($host_names) - 1];
                $domain = $bottom_host_name;
                try {
                    $info = $whois->loadDomainInfo($domain);
                    if ($info) $expirationDate = $info->getExpirationDate();
                } catch (Exception $e) { }
            }
        } catch (Exception $e) { }

        $curl = new CurlHelper($domain);
        while($server_response_code == 0 && $count <= 10) {
            $curl = new CurlHelper($domain);
            if(!$curl->getError()) {
                $server_response_code = $curl->getServerResponseCode();
                $size = $curl->getSize();
                $loading_time = $curl->getLoadingTime();
            } else echo $curl->getError() . "\n";
            $count++;
        }
        $site = Site::findOne(['name' => $domain]);
        $site->title = self::getTitle($domain);
        $site->redirect = $curl->getRedirect();
        $site->expiration_date = $expirationDate;
        $site->save();

        $screenshot = self::getScreen('http://' . $domain, 'screenshots',false);
        $icon = self::getIconPicture($domain);

        $audit = self::createAudit($url_id, $server_response_code, $loading_time, $size, $screenshot, $icon);
        self::createExternalLinks($domain, $audit->id);
    }

    public static function createSite($info, $domain)
    {
        $site = new Site();

        if($info) {
            $creationDate = $info->getCreationDate();
            $expirationDate = $info->getExpirationDate();
            $registrar = $info->getRegistrar();
            $states = $info->getStates();

            $site->name = $domain;
            $site->creation_date = $creationDate;
            $site->expiration_date = $expirationDate;
            $site->registrar = $registrar;
            $site->states = implode(", ", $states);
            $site->title = '';
            $site->user_id = Yii::$app->user->identity->id;
            $site->save();
        } else {
            $site->name = $domain;
            $site->save();
        }
        self::createDns($domain, $site->id);

        return $site->id;
    }

    public static function createUrl($domain, $site_id)
    {
        $ip = gethostbyname($domain);
        $url = new Url();
        $url->url = $domain;
        $url->site_id = $site_id;
        $url->ip = $ip;
        $url->save();

        return $url->id;
    }

    public static function createDns($domain, $site_id)
    {
        try {
            $records = dns_get_record($domain);
            foreach ($records as $record) {
                $dns = new Dns();
                $dns->class = $record['class'];
                $dns->ttl = $record['ttl'];
                $dns->type = $record['type'];
                if (self::isExist($record, 'target'))
                    $dns->target = $record['target'];
                if (self::isExist($record, 'ip'))
                    $dns->ip = $record['ip'];
                $dns->site_id = $site_id;
                $dns->save();
            }
        } catch (Exception $e) {
           echo $e->getMessage() . "\n";
        }
    }

    public static function createAudit($url_id, $server_response_code, $loading_time = 0, $size = 0,
                                       $screenshot = '', $icon = '') {
        $audit = new Audit();
        $audit->size = $size;
        $audit->loading_time = round($loading_time * 1000);
        $audit->server_response_code = (string)$server_response_code;
        $audit->screenshot = $screenshot;
        $audit->icon = $icon;
        $audit->url_id = $url_id;
        $audit->save();
        print_r($audit->errors);
        return $audit;
    }

    public static function createExternalLinks($domain, $audit_id)
    {
       $result_array = self::getExternalLinks($domain);
       if($result_array)
           for ($i = 0; $i < count($result_array[0]); $i++) {
               $ext_links = new ExternalLinks();
               $ext_links->acceptor = $result_array[0][$i];
               $ext_links->anchor = $result_array[1][$i];
               $ext_links->audit_id = $audit_id;
               //$ext_links->screenshot = $result_array[2][$i];
               $ext_links->save();
           }
    }

    public static function getTitle($domain)
    {
        try {
            $Punycode = new Punycode();
            $page_content = file_get_contents('http://' . $Punycode->encode($domain));
            preg_match_all( "|<title>(.*)</title>|sUSi", $page_content, $titles);
            if(count($titles[1]))
                return $titles[1][0];
            else return '';
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getExternalLinks($domain)
    {
        try {
            $Punycode = new Punycode();
            $html = file_get_contents('http://' . $Punycode->encode($domain));
            $document = phpQuery::newDocument($html);

            if($document) {
                try {
                    $links = $document->find('a')->get();
                    $host_path_array = array();
                    $anchor_array = array();
                    //$elscreen_array = array();
                    $result_array = array();
                    foreach ($links as $link) {
                        if (AuditService::isExist(parse_url($link->getAttribute('href')), 'host')) {
                            $clean_url = AuditService::cutUrl(parse_url($link->getAttribute('href'))['host']);
                            $cut_domain = AuditService::cutDomain($domain);
                            if($clean_url != $cut_domain) {
                                if (AuditService::isExist(parse_url($link->getAttribute('href')), 'path')) {
                                    $host_path = $clean_url . parse_url($link->getAttribute('href'))['path'];
                                    if (!in_array($host_path, $host_path_array)) {
                                        array_push($host_path_array, $host_path);
                                        array_push($anchor_array, $link->nodeValue);
                                        //array_push($elscreen_array, self::getScreen('https://www.google.com/search?q=' . $link->nodeValue, 'elscreen',false));
                                    }
                                } else {
                                    if (!in_array($clean_url, $host_path_array)) {
                                        array_push($host_path_array, $clean_url);
                                        array_push($anchor_array, $link->nodeValue);
                                        //array_push($elscreen_array, self::getScreen('https://www.google.com/search?q=' . $link->nodeValue, 'elscreen',false));
                                    }
                                }
                            }
                        }
                    }
                    array_push($result_array, $host_path_array);
                    array_push($result_array, $anchor_array);
                    //array_push($result_array, $elscreen_array);
                    return $result_array;
                } catch (Exception $e) {
                    echo $e->getMessage() . "\n";
                    return null;
                }
            } else {
                echo 'external links error' . "\n";
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getScreen($url, $folder, $mobile = true)
    {
        try {
            $date = new DateTime();
            $file_name = $date->getTimestamp() . '.jpg';
            $path = Yii::getAlias('@frontend/web/' . $folder . '/') . $file_name;

            $query = http_build_query(array_filter([
                'strategy' => $mobile ? 'mobile' : null,
                'screenshot' => 'true',
                'url' => $url
            ]));
            $api_url = "https://www.googleapis.com/pagespeedonline/v2/runPagespeed?{$query}";

            $result = json_decode(file_get_contents($api_url), true);

            $screen_data = str_replace(
                ['_', '-', ' ', 'data:image/jpeg;base64,'],
                ['/', '+', '+', ''],
                $result['screenshot']['data']
            );

            $img = file_put_contents($path, base64_decode($screen_data));
            return $img ? $file_name : 'error.jpg';
        } catch (Exception $e) {
            Debug::prn($e->getMessage());
            return 'error.jpg';
        }
    }

    public static function getIconPicture($domain)
    {
        try {
            $date = new DateTime();
            $file_name = $date->getTimestamp() . '.jpg';
            $icon_path = Yii::getAlias('@frontend/web/i/') . $file_name;
            $img = copy('http://www.google.com/s2/favicons?domain=www.' . $domain, $icon_path);

            return $img ? $file_name : 'error.jpg';
        } catch (Exception $e) {
            Debug::prn($e->getMessage());
            return 'error.jpg';
        }
    }


    public static function formData($urls)
    {
        $formatting_urls = self::formattingUrl($urls);

        $data_array = array();
        $all_url_array = AuditService::allUrlArray();
        $all_site_array = AuditService::allSiteArray();

        foreach ($formatting_urls as $value) {
            $data = new DataForm();
            $data->setSite($value);
            $data->setSiteUrl($value);
            foreach ($all_site_array as $all_site_value)
                if ($all_site_value == $data->getSite())
                    $data->setSiteExist(1);

            foreach ($all_url_array as $all_url_value)
                if ($all_url_value == $data->getSiteUrl())
                    $data->setUrlExist(1);

            array_push($data_array, $data);
        }
        return $data_array;
    }

    public static function allUrlArray()
    {
        $all_url = Url::find()->where(['site.user_id' => Yii::$app->user->identity->id])->innerJoin('site', 'site.id = url.site_id')->all();
        $all_url_array = array();
        foreach ($all_url as $value)
            array_push($all_url_array, $value->url);

        return $all_url_array;
    }

    public static function allSiteArray()
    {
        $all_site = Site::find()->where(['user_id' => Yii::$app->user->identity->id])->all();
        $all_site_array = array();
        foreach ($all_site as $value)
            array_push($all_site_array, $value->name);

        return $all_site_array;
    }

    public static function formattingUrl($urls)
    {
        $separated_urls = str_replace(array("\r\n", "\r", "\n"), ",", $urls);
        $clean_urls = self::cutUrl($separated_urls);
        $exploded_urls = explode(",", $clean_urls);

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

    public static function isExist($array, $key)
    {
        $isExist = 0;
        while (current($array)) {
            if (key($array) == $key) {
                $isExist = 1;
                break;
            }
            next($array);
        }
        return $isExist;
    }
}
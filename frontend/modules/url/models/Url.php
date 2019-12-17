<?php

namespace frontend\modules\url\models;

use common\classes\Debug;
use common\classes\UserAgentArray;
use common\models\Audit;
use common\models\Dns;
use common\models\ExternalLinks;
use common\models\Search;
use common\models\Site;
use Yii;
use GuzzleHttp;
use Iodev\Whois\Whois;
use Exception;
use yii\db\Expression;
use DateTime;

class Url extends \common\models\Url
{
    public function init()
    {
        parent::init();
    }

    public static function formData($urls)
    {
        $data_array = array();
        $all_url_array = Url::allUrlArray();
        $all_site_array = Url::allSiteArray();

        foreach ($urls as $value) {
            $data = new DataForm();
            $data->setSite($value);
            $data->setSiteUrl($value);
            foreach ($all_site_array as $all_site_value) {
                if ($all_site_value == $data->getSite()) {
                    $data->setSiteExist(1);
                }
            }
            foreach ($all_url_array as $all_url_value) {
                if ($all_url_value == $data->getSiteUrl()) {
                    $data->setUrlExist(1);
                }
            }
            array_push($data_array, $data);
        }
        return $data_array;
    }

    public static function addData($data_array, $report)
    {
        foreach ($data_array as $data) {
            if ($data->isSiteExist() && $data->isUrlExist()) {
                self::updateData($data, $report);
            }
            elseif($data->isSiteExist() && !$data->isUrlExist()) {
                self::insertAndUpdateData($data, $report);
            } else {
                self::insertData($data, $report);
            }
        }
        return true;
    }

    public static function insertData($data, $report)
    {
        $server_response = 0;
        $server_response_code = 0;

        try {
            $site_id = self::addSite($data->getSite());

            $url_id = self::addUrl($data->getSiteUrl(), $site_id);
            self::addDns($data->getSite(), $site_id);

            $audit_id = self::addAudit($data->getSiteUrl(), $url_id);
            if($audit_id) {
                $server_response = Audit::find()->where(['id' => $audit_id])->asArray()->all();
            }
            if($server_response) {
                $server_response_code = $server_response[0]['server_response_code'];
            }

            if ($server_response_code == 200) {
                self::addExternalLinks($data->getSiteUrl(), $audit_id);
            }

            $report->newSite++;
            $report->newUrl++;
            $report->newAudit++;
        } catch (Exception $e) {
            //Debug::dd($e->getMessage());
            $report->errorsUrl++;
            array_push($report->errorUrlArray, $data->getSiteUrl());
        }
    }

    public static function insertAndUpdateData($data, $report)
    {
        $server_response = 0;
        $server_response_code = 0;

        $site_id = Site::find()->where(['name' => $data->getSite()])->asArray()->all()[0]['id'];
        $url_id = self::addUrl($data->getSiteUrl(), $site_id);

        $audit_id = self::addAudit($data->getSiteUrl(), $url_id);
        if($audit_id) {
            $server_response = Audit::find()->where(['id' => $audit_id])->asArray()->all();
        }
        if($server_response) {
            $server_response_code = $server_response[0]['server_response_code'];
        }

        if ($server_response_code == 200) {
            self::addExternalLinks($data->getSiteUrl(), $audit_id);
        }

        $report->newUrl++;
        $report->newAudit++;
    }

    public static function updateData($data, $report)
    {
        $server_response = 0;
        $server_response_code = 0;
        $site_id = Site::find()->where(['name' => $data->getSite()])->asArray()->all()[0]['id'];
        $url_id = Url::find()->where(['url' => $data->getSiteUrl()])->asArray()->all()[0]['id'];

        $audit_id = self::addAudit($data->getSiteUrl(), $url_id);
        if($audit_id) {
            $server_response = Audit::find()->where(['id' => $audit_id])->asArray()->all();
        }
        if($server_response) {
            $server_response_code = $server_response[0]['server_response_code'];
        }

        if ($server_response_code == 200) {
            self::addExternalLinks($data->getSiteUrl(), $audit_id);
        }

        $report->newAudit++;
    }

    public static function createSite($info, $domain)
    {
        $creationDate = $info->getCreationDate();
        $expirationDate = $info->getExpirationDate();
        $registrar = $info->getRegistrar();
        $states = $info->getStates();

        $site = new Site();
        $site->name = $domain;
        $site->creation_date = $creationDate;
        $site->expiration_date = $expirationDate;
        $site->registrar = $registrar;
        $site->states = implode(", ", $states);
        $site->save();

        return $site->id;
    }

    public static function addSite($domain)
    {
        $whois = Whois::create();
        $info = $whois->loadDomainInfo($domain);

        if ($info) {
           return self::createSite($info, $domain);
        } else {
            $host_names = explode(".", $domain);
            $bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
            $domain  = $bottom_host_name;
            try {
                $info = $whois->loadDomainInfo($domain);
                return self::createSite($info, $domain);
            } catch (Exception $e) {
                return null;
            }

        }
    }

    public static function addUrl($domain, $site_id)
    {
        $ip = gethostbyname($domain);
        $url = new Url();
        $url->url = $domain;
        $url->site_id = $site_id;
        $url->ip = $ip;
        $url->save();

        return $url->id;
    }

    public static function addDns($domain, $site_id)
    {
        $records = dns_get_record($domain);

        foreach ($records as $record) {
            $dns = new Dns();
            $dns->class = $record['class'];
            $dns->ttl = $record['ttl'];
            $dns->type = $record['type'];
            if (self::isExist($record, 'target')) {
                $dns->target = $record['target'];
            }
            if (self::isExist($record, 'ip')) {
                $dns->ip = $record['ip'];
            }
            $dns->site_id = $site_id;
            $dns->save();
        }
    }

    public static function addAudit($domain, $url_id)
    {
        try {
            $date = new DateTime();
            $file_name = $date->getTimestamp() . '.jpg';
            $path = Yii::getAlias('@frontend/web/screenshots/') . $file_name;
            $icon_path = Yii::getAlias('@frontend/web/icons/') . $file_name;
            self::makeScreen('https://' . $domain, $path, false);
            copy('http://www.google.com/s2/favicons?domain=www.' . $domain, $icon_path);

            $startTime = microtime(1);
            $client = new GuzzleHttp\Client(['User-Agent' => UserAgentArray::getRandom()]);
            $res = $client->request('GET', $domain);
            $endTime = microtime(1);

            $audit = new Audit();
            $audit->server_response_code = (string)$res->getStatusCode();
            $audit->size = strlen($res->getBody());
            $audit->loading_time = round(($endTime - $startTime) * 1000);
            $audit->check_search = 0;
            $audit->screenshot = $file_name;
            $audit->icon = $file_name;
            $audit->url_id = $url_id;
            $audit->save();
           //Debug::dd($audit->errors);
        } catch (Exception $e) {
           // Debug::dd($e->getMessage());
            $audit = new Audit();
            $audit->server_response_code = (string)$e->getCode();
            $audit->url_id = $url_id;
            $audit->save();
        }
        return $audit->id;
    }

    public static function addExternalLinks($domain, $audit_id)
    {
        $client = new GuzzleHttp\Client(['User-Agent' => UserAgentArray::getRandom()]);
        $res = $client->request('GET', $domain);
        $body = $res->getBody()->getContents();
        $document = \phpQuery::newDocumentHTML($body);
        $links = $document->find('a')->get();

        $site_array = Site::find()->all();

        $site = array();
        foreach ($site_array as $value) {
            array_push($site, $value->name);
        }

        $host_path_array = array();
        $anchor_array = array();
        foreach ($links as $link) {
            if (self::isExist(parse_url($link->getAttribute('href')), 'host')) {
                $clean_url = str_replace(array("http://", "https://", "www."), "",
                    parse_url($link->getAttribute('href'))['host']);

                if(strripos($domain, '/')) {
                    $cut_domain = stristr($domain, '/', true);
                } else {
                    $cut_domain = $domain;
                }

                if($clean_url != $cut_domain) {
                    if (self::isExist(parse_url($link->getAttribute('href')), 'path')) {
                        $host_path = $clean_url . parse_url($link->getAttribute('href'))['path'];

                        if (!in_array($host_path, $host_path_array)) {
                            array_push($host_path_array, $host_path);
                            array_push($anchor_array, $link->nodeValue);
                        }
                    } else {
                        if (!in_array($clean_url, $host_path_array)) {
                            array_push($host_path_array, $clean_url);
                            array_push($anchor_array, $link->nodeValue);
                        }
                    }
                }
            }
        }

        for ($i = 0; $i < count($host_path_array); $i++) {
            $ext_links = new ExternalLinks();
            $ext_links->acceptor = $host_path_array[$i];
            $ext_links->anchor = $anchor_array[$i];
            $ext_links->audit_id = $audit_id;
            $ext_links->save();
        }
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

    public static function formattingUrl($urls)
    {
        $separated_urls = str_replace(array("\r\n", "\r", "\n"), ",", $urls);
        $clean_urls = str_replace(array("http://", "https://", "www."), "", $separated_urls);
        $exploded_urls = explode(",", $clean_urls);

        $formatting_urls = array();
        foreach ($exploded_urls as $exploded_url) {
            $trim_url = trim($exploded_url);
            if ($trim_url) {
                array_push($formatting_urls, $trim_url);
            }
        }
        return $formatting_urls;
    }

    public static function allUrlArray()
    {
        $all_url = Url::find()->all();
        $all_url_array = array();
        foreach ($all_url as $value) {
            array_push($all_url_array, $value->url);
        }
        return $all_url_array;
    }

    public static function allSiteArray()
    {
        $all_site = Site::find()->all();
        $all_site_array = array();
        foreach ($all_site as $value) {
            array_push($all_site_array, $value->name);
        }
        return $all_site_array;
    }

    public static function makeScreen($url, $save_to, $mobile = true)
    {
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

        return file_put_contents($save_to, base64_decode($screen_data));
    }
}

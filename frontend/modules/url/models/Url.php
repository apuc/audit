<?php

namespace frontend\modules\url\models;

use common\classes\Debug;
use common\models\Audit;
use common\models\Dns;
use common\models\ExternalLinks;
use common\models\Search;
use common\models\Site;
use GuzzleHttp;
use Iodev\Whois\Whois;

class Url extends \common\models\Url
{
    public function init()
    {
        parent::init();
    }

    public static function insertData($urls)
    {
        foreach ($urls as $value) {
            //$content = self::makeScreen($value, __DIR__ . '/screen.jpg', false);
            $site_id = self::addSite($value);
            $url_id = self::addUrl($value, $site_id);
            self::addDns($value, $site_id);
            $audit_id = self::addAudit($value, $url_id);
            self::addExternalLinks($value, $value, $audit_id);
        }
    }

    public static function updateAudit($urls)
    {
        foreach ($urls as $value) {
            //$content = self::makeScreen($value, __DIR__ . '/screen.jpg', false);
            $url = \common\models\Url::find()->where(['url' => $value])->all();
            $audit_id = self::addAudit($value, $url[0]['id']);
            self::addExternalLinks($value, $url[0]['url'], $audit_id);
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

    public static function addSite($domain)
    {
        $cutedDomain = self::cutDomain($domain);
        $whois = Whois::create();
        $info = $whois->loadDomainInfo($cutedDomain);

        $creationDate = $info->getCreationDate();
        $expirationDate = $info->getExpirationDate();
        $registrar = $info->getRegistrar();
        $states = $info->getStates();

        $site = new Site();
        $site->name = $cutedDomain;
        $site->creation_date = $creationDate;
        $site->expiration_date = $expirationDate;
        $site->registrar = $registrar;
        $site->states = implode(", ", $states);
        $site->save();

        return $site->id;
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
        $cutedDomain = self::cutDomain($domain);
        $records = dns_get_record($cutedDomain);

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
        $startTime = microtime(1);
        $client = new GuzzleHttp\Client();

        $res = $client->request('GET', $domain);
        $endTime = microtime(1);

        $google = Search::check($domain, 'google');
        $yandex = Search::check($domain, 'ya');

        $audit = new Audit();
        $audit->server_response_code = (string)$res->getStatusCode();
        $audit->size = strlen($res->getBody());
        $audit->loading_time = round(($endTime - $startTime) * 1000);
        $audit->google_indexing = $google ? 1 : null;
        $audit->yandex_indexing = $yandex ? 1 : null;
        $audit->url_id = $url_id;
        $audit->save();

        return $audit->id;
    }

    public static function addExternalLinks($domain, $url, $audit_id)
    {
        $client = new GuzzleHttp\Client();
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
                if ($clean_url != $url) {
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

        for ($i=0; $i<count($host_path_array); $i++) {
            $ext_links = new ExternalLinks();
            $ext_links->acceptor = $host_path_array[$i];
            $ext_links->anchor = $anchor_array[$i];
            $ext_links->audit_id = $audit_id;
            $ext_links->save();
        }
    }

    public static function cutDomain($domain)
    {
        $cutedDomain = explode('/', $domain);
        $cutedDomain = $cutedDomain[0];

        return $cutedDomain;
    }

    public static function makeScreen($url, $save_to, $mobile = true) {
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

    public static function formattingUrl($urls){
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
}

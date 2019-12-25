<?php

namespace common\services;

use common\classes\Debug;
use common\classes\UserAgentArray;
use common\models\Audit;
use common\models\Dns;
use common\models\ExternalLinks;
use common\models\Site;
use frontend\modules\url\models\DataForm;
use frontend\modules\url\models\Url;
use Iodev\Whois\Whois;
use Yii;
use DateTime;
use Exception;
use GuzzleHttp;


class AuditService
{
    public static function addData($data_array, $report)
    {
        foreach ($data_array as $data) {
            if (!$data->isSiteExist() && !$data->isUrlExist()) {
                self::insertSiteAndUrl($data, $report);
            } elseif ($data->isSiteExist() && !$data->isUrlExist()) {
                self::insertUrl($data, $report);
            }
        }
        return true;
    }

    public static function insertSiteAndUrl($data, $report)
    {
        try {
            $site_id = self::addSite($data->getSite());
            self::createUrl($data->getSiteUrl(), $site_id);

            $report->newSite++;
            $report->newUrl++;

        } catch (Exception $e) {
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

    public static function addSite($domain)
    {
        try {
            $whois = Whois::create();
            $info = $whois->loadDomainInfo($domain);

            if ($info) {
                return self::createSite($info, $domain);
            } else {
                $host_names = explode(".", $domain);
                $bottom_host_name = $host_names[count($host_names) - 2] . "." . $host_names[count($host_names) - 1];
                $domain = $bottom_host_name;
                try {
                    $info = $whois->loadDomainInfo($domain);
                    return self::createSite($info, $domain);
                } catch (Exception $e) {
                    return null;
                }
            }
        } catch (Exception $e) {
            return self::createSite(null, $domain);
        }
    }

    public static function addAudit($domain, $url_id)
    {
        try {
            $audit = self::createAudit($domain, $url_id,'ok');
        } catch (Exception $e) {
            self::createAudit($domain, $url_id, (string)$e->getCode());
        }

        try {
            $site = Site::findOne(['name' => $domain]);
            $site->title = AuditService::getTitle($domain);
            echo '<br>Тайтл: ' . $site->title . '<br>';
            $site->redirect = AuditService::getRedirect($domain);
            echo 'Редирект: ' . $site->redirect . '<br>';
            $site->save();
        } catch (Exception $e) { }

        try {
            AuditService::createExternalLinks($domain, $audit->id);
        } catch (Exception $e) { }
    }

    public static function createAudit($domain, $url_id, $e)
    {
        echo 'Домен: ' . $domain . '<br>';
        $audit = new Audit();
        if($e == 'ok') {
            $startTime = microtime(1);
            $client = new GuzzleHttp\Client();
            $response = $client->get($domain, [
                'headers' => [
                    'User-Agent' => UserAgentArray::getRandom(),
                ],
                'verify' => false
            ]);
            $endTime = microtime(1);
            $loading_time = round(($endTime - $startTime) * 1000);

            $audit->server_response_code = (string)$response->getStatusCode();
            echo 'Код ответа сервера: ' . $audit->server_response_code . '<br>';
            $audit->size = strlen($response->getBody());
            echo 'Размер: ' . $audit->size . '<br>';
            $audit->loading_time = $loading_time;
            echo 'Время загрузки: ' . $audit->loading_time . '<br>';
            $audit->check_search = 0;
            echo 'Флаг индексации: ' . $audit->check_search . '<br>';
            try {
                $audit->screenshot = AuditService::makeScreen('https://' . $domain, false);
            } catch (Exception $e) {
                $audit->screenshot = 'error.jpg';
            }
            echo 'Скриншот: ' . $audit->screenshot . '<br>';
            try {
                $audit->icon = AuditService::makeIconPicture($domain);
            } catch (Exception $e) {
                $audit->icon = 'error.jpg';
            }
            echo 'Иконка: ' . $audit->icon . '<br>';
        } else {
            $audit->server_response_code = $e;
            echo 'Код ответа сервера: ' . $audit->server_response_code . '<br>';
            $audit->screenshot = 'error.jpg';
            echo 'Скриншот: ' . $audit->screenshot . '<br>';
        }

        $audit->url_id = $url_id;
        echo 'url_id: ' . $audit->url_id . '<br>';
        $audit->save();
        return $audit;
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
            $site->save();
        } else {
            $site->name = $domain;
            $site->save();
        }

        self::createDns($domain, $site->id);

        return $site->id;
    }

    public static function createDns($domain, $site_id)
    {
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

    public static function createExternalLinks($domain, $audit_id)
    {
        $client = new GuzzleHttp\Client([
            'User-Agent' => UserAgentArray::getRandom(),
        ]);
        $response = $client->get($domain);
        $body = $response->getBody()->getContents();
        $document = \phpQuery::newDocumentHTML($body);
        $links = $document->find('a')->get();

        $host_path_array = array();
        $anchor_array = array();
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
        var_dump($host_path_array);
        for ($i = 0; $i < count($host_path_array); $i++) {
            $ext_links = new ExternalLinks();
            $ext_links->acceptor = $host_path_array[$i];
            $ext_links->anchor = $anchor_array[$i];
            $ext_links->audit_id = $audit_id;
            $ext_links->save();
        }
    }

    public static function getTitle($domain)
    {
        $client = new GuzzleHttp\Client([
            'User-Agent' => UserAgentArray::getRandom(),
        ]);
        $response = $client->get($domain);
        $body = $response->getBody()->getContents();

        $document = \phpQuery::newDocumentHTML($body);
        $links = $document->find('title')->get();

        return $links ? $links[0]->nodeValue : null;

    }

    public static function getRedirect($domain)
    {
        $client = new GuzzleHttp\Client([
            'User-Agent' => UserAgentArray::getRandom(),
            'allow_redirects' => ['track_redirects' => true]
        ]);
        $response = $client->get($domain);
        $redirect = $response->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER)[0];
        $cut_redirect = self::cutUrl($redirect);
        $cut_again = self::cutDomain($cut_redirect);
        try {
            return ($cut_again == $domain) ? '' : $cut_again;
        }
        catch (Exception $e) {
            return null;
        }
    }

    public static function makeScreen($url, $mobile = true)
    {
        $date = new DateTime();
        $file_name = $date->getTimestamp() . '.jpg';
        $path = Yii::getAlias('@frontend/web/screenshots/') . $file_name;

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
    }

    public static function makeIconPicture($domain)
    {
        $date = new DateTime();
        $file_name = $date->getTimestamp() . '.jpg';
        $icon_path = Yii::getAlias('@frontend/web/i/') . $file_name;
        copy('http://www.google.com/s2/favicons?domain=www.' . $domain, $icon_path);

        return $file_name;
    }

    // проверяет наличие ключа в массиве
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

    // форматирование доменов и url
    public static function formattingUrl($urls)
    {
        $separated_urls = str_replace(array("\r\n", "\r", "\n"), ",", $urls);
        $clean_urls = self::cutUrl($separated_urls);
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

    public static function cutDomain($domain)
    {
        return (strripos($domain, '/')) ? stristr($domain, '/', true) : $domain;
    }

    public static function cutUrl($url)
    {
        return str_replace(array("http://", "https://", "www."), "", $url);
    }

    // формирование данных для добавления
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

    // возвращает массив url
    public static function allUrlArray()
    {
        $all_url = Url::find()->all();
        $all_url_array = array();
        foreach ($all_url as $value) {
            array_push($all_url_array, $value->url);
        }
        return $all_url_array;
    }

    // возвращает массив доменов
    public static function allSiteArray()
    {
        $all_site = Site::find()->all();
        $all_site_array = array();
        foreach ($all_site as $value) {
            array_push($all_site_array, $value->name);
        }
        return $all_site_array;
    }
}
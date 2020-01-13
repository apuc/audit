<?php

namespace common\services;

use common\classes\Debug;
use common\classes\ProxyListArray;
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
    const IS_PROXY = 1;

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
        $domain = self::cutDomain($domain);
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
        $domain = self::cutDomain($domain);
        if(self::IS_PROXY) {
            $fl = 0;
            $count = 0;
            while($fl == 0 && $count <= 10) {
                try {
                    $proxy = ProxyListArray::getRandom();
                    echo $proxy . "\n";
                    $startTime = microtime(1);
                    $client = new GuzzleHttp\Client([
                        'headers' => ['User-Agent' => UserAgentArray::getStatic()],
                        'verify' => true,
                        'curl' => [
                            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                            CURLOPT_PROXYTYPE => CURLPROXY_SOCKS4,
                            CURLOPT_PROXY => $proxy,
                            CURLOPT_CONNECTTIMEOUT => 30,
                        ],
                        'allow_redirects' => ['track_redirects' => true]
                    ]);
                    $response = $client->get($domain);
                    $endTime = microtime(1);
                    $loading_time = round(($endTime - $startTime) * 1000);

                    $body = $response->getBody()->getContents();
                    $document = \phpQuery::newDocumentHTML($body);

                    $server_response_code = self::getServerResponseCode($response);
                    $size = self::getSize($response);
                    $fl = 1;
                } catch (Exception $e) {
                    $fl = 0;
                    $response = null;
                    $document = null;
                    $loading_time = 0;
                    $size = 0;
                    $server_response_code = $e->getCode();
                    echo $e->getMessage() . "\n";
                }
                $count++;
            }
        } else {
            try {
                $startTime = microtime(1);
                $client = new GuzzleHttp\Client([
                    'headers' => ['User-Agent' => UserAgentArray::getStatic()],
                    'verify' => true,
                    'allow_redirects' => ['track_redirects' => true]
                ]);
                $response = $client->get($domain);
                $endTime = microtime(1);
                $loading_time = round(($endTime - $startTime) * 1000);

                $body = $response->getBody()->getContents();
                $document = \phpQuery::newDocumentHTML($body);

                $server_response_code = self::getServerResponseCode($response);
                $size = self::getSize($response);

            } catch (Exception $e) {
                $response = null;
                $document = null;
                $loading_time = 0;
                $size = 0;
                $server_response_code = $e->getCode();
                echo $e->getMessage() . "\n";
            }
        }

        echo 'server_response_code: ' . $server_response_code . "\n";
        echo 'size: ' . $size . "\n";
        echo 'loading_time: ' . $loading_time . "\n";

        $screenshot = self::getScreen('https://' . $domain, false);
        $icon = self::getIconPicture($domain);

        $site = Site::findOne(['name' => $domain]);
        $site->title = self::getTitle($document);
        echo $site->title . "\n";
        $site->redirect = self::getRedirect($domain, $response);
        $site->save();

        $audit = self::createAudit($url_id, $server_response_code, $loading_time, $size, $screenshot, $icon);

        self::createExternalLinks($domain, $audit->id, $document);
        echo "\n";
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

    public static function createAudit($url_id, $server_response_code, $loading_time = 0, $size = 0,
                                       $screenshot = '', $icon = '') {
        $audit = new Audit();
        $audit->size = $size;
        $audit->loading_time = $loading_time;
        $audit->server_response_code = $server_response_code;
        $audit->screenshot = $screenshot;
        $audit->icon = $icon;
        $audit->url_id = $url_id;
        $audit->save();
        return $audit;
    }

    public static function createExternalLinks($domain, $audit_id, $document)
    {
       $result_array = self::getExternalLinks($document, $domain);
       if($result_array) {
           echo 'external_links exist' . "\n";
           for ($i = 0; $i < count($result_array[0]); $i++) {
               $ext_links = new ExternalLinks();
               $ext_links->acceptor = $result_array[0][$i];
               $ext_links->anchor = $result_array[1][$i];
               $ext_links->audit_id = $audit_id;
               $ext_links->save();
           }
       }
    }


    public static function getTitle($document)
    {
        if($document) {
            try {
                $links = $document->find('title')->get();
                return $links ? $links[0]->nodeValue : null;
            } catch (Exception $e) {
                Debug::prn($e->getMessage());
                return null;
            }
        } else {
            Debug::prn('title error');
            return null;
        }

    }

    public static function getRedirect($domain, $response)
    {
        if($response) {
            try {
                $redirect = $response->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER)[0];
                $cut_redirect = self::cutDomain(self::cutUrl($redirect));
                return ($cut_redirect == $domain) ? '' : $cut_redirect;
            }
            catch (Exception $e) {
                Debug::prn($e->getMessage());
                return null;
            }
        } else {
            Debug::prn('redirect error');
            return null;
        }
    }

    public static function getServerResponseCode($response)
    {
        if($response) {
            try {
                return (string)$response->getStatusCode();
            } catch (Exception $e) {
                Debug::prn($e->getMessage());
                return 0;
            }
        } else {
            Debug::prn('server response code error');
            return 0;
        }
    }

    public static function getSize($response)
    {
        if($response) {
            try {
                return strlen($response->getBody());
            } catch (Exception $e) {
                Debug::prn($e->getMessage());
                return 0;
            }
        } else {
            Debug::prn('size error');
            return 0;
        }
    }

    public static function getExternalLinks($document, $domain)
    {
        if($document) {
            try {
                $links = $document->find('a')->get();
                $host_path_array = array();
                $anchor_array = array();
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
                array_push($result_array, $host_path_array);
                array_push($result_array, $anchor_array);
                return $result_array;
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
                return null;
            }
        } else {
            echo 'external links error' . "\n";
            return null;
        }
    }

    public static function getScreen($url, $mobile = true)
    {
        try {
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
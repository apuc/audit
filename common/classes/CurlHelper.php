<?php


namespace common\classes;


use common\services\AuditService;

class CurlHelper
{
    private $html;
    private $info;
    private $error;

    private $url;
    private $server_response_code;
    private $size;
    private $loading_time;
    private $redirect;

    public function __construct($domain)
    {
        $ch = curl_init($domain);

        curl_setopt_array($ch, array(
            CURLOPT_USERAGENT => UserAgentArray::getStatic(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_PROXYTYPE => CURLPROXY_SOCKS4,
            CURLOPT_PROXY => ProxyListArray::getRandom(),
            CURLOPT_FOLLOWLOCATION => true,
            //CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // предпочтительный ip
        ));

        $html = curl_exec($ch);
        $this->html = $html;
        $this->error = curl_error($ch);

        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            $this->info = $info;
            $this->url = $info['url'];;
            $this->server_response_code = $info['http_code'];
            $this->size = $info['size_download'];
            $this->loading_time = $info['total_time'];
            self::setRedirect($info, $domain);
        }
        curl_close($ch);
    }

    public function setRedirect($info, $domain)
    {
        $redirect = AuditService::cutDomain(AuditService::cutUrl($info['url']));
        if($redirect != $domain) $this->redirect = $redirect;
        else $this->redirect = '';
    }

    public function getRedirect()
    {
        return $this->redirect;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getServerResponseCode()
    {
        return $this->server_response_code;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getLoadingTime()
    {
        return $this->loading_time;
    }
}
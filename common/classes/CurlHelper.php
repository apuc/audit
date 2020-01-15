<?php


namespace common\classes;


class CurlHelper
{
    private $html;
    private $info;
    private $error;

    private $server_response_code;
    private $size;
    private $loading_time;

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
        //file_put_contents('test.txt', $html);

        $this->html = $html;
        $this->error = curl_error($ch);

        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            $this->info = $info;
            $this->server_response_code = $info['http_code'];
            $this->size = $info['size_download'];
            $this->loading_time = $info['total_time'];
        }

        curl_close($ch);
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
<?php

namespace common\classes;

use GuzzleHttp\Client;
use yii\base\Exception;

/**
 * Class MegaIndex
 * @package common\classes
 */
class MegaIndex
{

    const BASE_API_NAME = 'backlinks/counters';

    private $client;

    private $apiKey;
    private $apiName;
    private $domain;
    private $options = [];

    private $result;

    /**
     * MegaIndex constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setData($data);
        $this->client = new Client(['base_uri' => 'http://api.megaindex.com/']);
        $this->makeRequest();
    }

    /**
     * @param $data
     * @throws Exception
     */
    private function setData($data)
    {
        $this->setDomain($data);
        $this->setApiKey($data);
        $this->setApiName($data);
        $this->setOptions($data);
    }

    /**
     * @param $data
     * @throws Exception
     */
    private function setDomain($data)
    {
        if (isset($data['domain'])) {
            $this->domain = $data['domain'];
        } else {
            throw new Exception('domain is required property');
        }
    }

    /**
     * @param $data
     */
    private function setApiKey($data)
    {
        $this->apiKey = isset($data['apiKey']) ? $data['apiKey'] : \Yii::$app->params['apiKey'];
    }

    /**
     * @param $data
     */
    private function setApiName($data)
    {
        $this->apiName = isset($data['method']) ? trim($data['method'], '/') : self::BASE_API_NAME;
    }

    /**
     * @param $data
     */
    private function setOptions($data)
    {
        $this->options = isset($data['options']) ? $data['options'] : [];
        $this->options['key'] = $this->apiKey;
        $this->options['domain'] = $this->domain;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function makeRequest()
    {
        $result = $this->client->request('GET', $this->apiName, [
            'query' => $this->options
        ]);
        $this->result = $result->getBody()->getContents();
    }

    /**
     * @return mixed
     */
    public function json()
    {
        return $this->result;
    }

    /**
     * @return mixed
     */
    public function array()
    {
        return json_decode($this->result, true);
    }

    /**
     * @return mixed
     */
    public function object()
    {
        return json_decode($this->result);
    }

    /**
     * @param array $data
     * @return MegaIndex
     */
    public static function run(array $data)
    {
        return (new self($data));
    }
    
}
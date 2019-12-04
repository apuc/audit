<?php


namespace frontend\modules\url\models;


use yii\base\Model;

/**
 *
 * @property string $currentUrl
 * @property string $site
 * @property string $siteUrl
 * @property boolean $isSiteExist
 * @property boolean $isUrlExist
 *
 */

class DataForm
{
    private $site;
    private $siteUrl;
    private $isSiteExist = 0;
    private $isUrlExist = 0;

    public function setSite($data)
    {
        if($data) {
            $this->site = self::cutDomain($data);
        }
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setSiteUrl($data)
    {
        $this->siteUrl = $data;
    }

    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    public function setSiteExist($data)
    {
        $this->isSiteExist = $data;
    }

    public function setUrlExist($data)
    {
        $this->isUrlExist = $data;
    }

    public function isSiteExist()
    {
        if($this->isSiteExist) {
            return true;
        }
        else {
            return false;
        }
    }

    public function isUrlExist()
    {
        if($this->isUrlExist) {
            return true;
        }
        else {
            return false;
        }
    }

    public function cutDomain($currentUrl)
    {
        $cutedDomain = explode('/', $currentUrl);
        $cutedDomain = $cutedDomain[0];

        return $cutedDomain;
    }
}
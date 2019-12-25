<?php
/**
 * Created by PhpStorm.
 * User: apuc0
 * Date: 09.03.2018
 * Time: 23:40
 */

namespace common\models;

use common\classes\Debug;
use common\classes\UserAgentArray;
use Yii;

class Search
{

    public static function check($link, $searchSystem = null)
    {

        $sc = new self();
        if ($searchSystem === 'ya') {
            return $sc->checkYa($link);
        }
        if ($searchSystem === 'google') {
            return $sc->checkGoogle($link);
        }
        if ($searchSystem === null) {
            return [
                'ya' => $sc->checkYa($link),
                'google' => $sc->checkGoogle($link),
            ];
        }
        return false;

    }

    public function checkYa($link)
    {
        $res = $this->parse('https://yandex.ru/search/', [
            'text' => 'url:' . $link,
        ]);

        $document = \phpQuery::newDocument($res);
        $count = $document->find('.serp-adv__found')->count();
        return $count === 1;
    }

    public function checkGoogle($link)
    {
        $res = $this->parse('https://www.google.ru/search', [
            'q' => 'site:' . $link,
        ]);

        $document = \phpQuery::newDocument($res);
        $count = $document->find('#resultStats')->count();
        $text = $document->find('#resultStats')->text();
        return $count === 1 && $text !== '';
    }

    public static function getCount($link)
    {
        $sc = new self();
        return $sc->getCountIndexedPagesGoogle($link);
    }

    public function getCountIndexedPagesGoogle($link)
    {
        $res = $this->parse('https://www.google.ru/search', ['q' => 'site:' . $link,]);

        $document = \phpQuery::newDocument($res);
        $text = $document->find('#resultStats')->text();
        return str_replace(array(":", " "), "", stristr(stristr($text, ':'), '(', true));
    }

    public function parse($link, array $data = array())
    {
        $url = $link;
        if (!empty($data)) {
            $url .= '?';
            $dataStr = '';
            foreach ($data as $key => $datum) {
                $dataStr .= $key . '=' . $datum . '&';
            }
            $url .= substr($dataStr, 0, -1);
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, UserAgentArray::getRandom());
        curl_setopt($curl, CURLOPT_COOKIEJAR, Yii::getAlias('@frontend/web/cookie.txt'));
        curl_setopt($curl, CURLOPT_COOKIEFILE, Yii::getAlias('@frontend/web/cookie.txt'));

        return curl_exec($curl);
    }

}
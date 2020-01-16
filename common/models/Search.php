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
use GuzzleHttp;
use Yii;

class Search
{

    public static function cache($link, $key)
    {
        $sc = new self();
        if ($key === 'date') {
            return $sc->getDateCache($link);
        }
        return '';
    }

    public function getDateCache($link)
    {
        try {
            $client = new GuzzleHttp\Client(['User-Agent' => UserAgentArray::getRandom(),]);
            $response = $client->get('http://webcache.googleusercontent.com/search?q=cache:'.$link);
            $body = $response->getBody()->getContents();
            $document = \phpQuery::newDocumentHTML($body);
            $links = $document->find('span')->get();

            if($links) {
                $pattern = "/^[^0-9]*/";
                $date = $links[1]->nodeValue;
                $date = preg_replace($pattern, "", $date);
                $date = stristr($date, ':', true);
                $date = substr($date, 0, strlen($date)-3);
                echo "Дата кэша: " . $date . "\n";
                return $date;
            } else return '';
        } catch (\Exception $e) {
            echo "Ошибка кэша: " . $e->getMessage() . "\n";
            return '';
        }
    }

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
        $cuted = stristr(stristr($text, ':'), '(', true);
        $replaced = str_replace(array(":", " ", "%20", "&#160;", "&#8194;", "&#8195;", "&#8201;"), "", $cuted);
        $replaced = preg_replace('/[^\p{L}0-9 ]/iu','', $replaced);
        echo "Количество проиндексированных страниц: " . $replaced . "\n";
        return $replaced;
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
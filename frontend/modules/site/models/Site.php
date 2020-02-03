<?php


namespace frontend\modules\site\models;

use common\classes\Debug;
use common\models\Audit;
use common\models\AuditPending;
use common\models\Comments;
use common\models\Dns;
use common\models\ExternalLinks;
use common\models\Indexing;
use common\models\IndexingPending;
use common\models\Theme;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class Site extends \common\models\Site
{
    public $theme;

    public function init()
    {
        parent::init();
    }

    public static function getIconOutput($data)
    {
        if(Site::getAudit($data, 'icon') != 'error.jpg')
            return Html::tag('img', null, ['src' => Url::to('@web/i/'
                . Site::getAudit($data, 'icon')), 'width' => '16px', 'onclick' => "copyToClipboard('domain-".$data->name."')"]);
        else return '';
    }

    public static function getScreenshotOutput($data)
    {
        if(Site::getAudit($data, 'screenshot') != 'error.jpg')
            return Html::tag('img', null, [
                'src' => Url::to('@web/screenshots/' . Site::getAudit($data, 'screenshot')),
                'width' => '32px', 'class' => 'scale']);
        else return '';
    }

    public static function getChartOutput($data)
    {
        return "<span class='glyphicon glyphicon-signal target ".$data->id."' aria-hidden='true'></span>
                            <div class='graphic_size'><div id='size'></div></div>
                            <div class='graphic_loading_time'><div id='loading_time'></div></div>
                            <div class='graphic_server_response_code'><div id='server_response_code'></div></div>
                            <div id='close-chart'></div>";
    }

    public static function getItemOutput($title, $value, $count = null)
    {
        if($count) return '<div class="count">' . count($count)
            . '</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
            . $title . '" class="states">' . $value . '</div';
        else return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
            . $title . '" class="states">' . $value . '</div';
    }

    public static function getSitesTheme($data)
    {
        return '<a type="button" data-toggle="modal" data-target="#modalTheme" data-id="' . $data->id
            . '" class="theme" title="Темы"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>' . "<br>"
            . self::getItemOutput(self::getSiteTheme($data), self::getSiteTheme($data));
    }

    public static function getComment($data)
    {
        return '<a type="button" data-toggle="modal" data-target="#exampleModal" data-id="' . $data->id
            . '" class="comment" title="Добавить комментарий"><span class="glyphicon glyphicon-pencil" 
                            aria-hidden="true"></span></a>' . "<br>" .
            Html::a("<span class=\"glyphicon glyphicon-eye-open\" aria-hidden=\"true\"></span>",
                ['/comments/comments/?CommentsSearch[site_id]=' . $data->id],
                ['title' => 'Посмотреть комментарии к сайту']);
    }

    public static function getGoogleLinks($data)
    {
        return Html::a(Site::getIndex($data, 'google_indexed_pages'),
            'https://www.google.com/search?q=site:' . $data->name, ['target' => '_blank'],
            ['title' => 'Количество проиндексированных страниц']
        );
    }

    public static function formatAcceptor($data)
    {
        $arr = Site::getAcceptor($data, 2);
        $links = array();
        for($i = 0; $i < count($arr); $i++)
            array_push($links, Html::a($arr[$i], 'http://' . $arr[$i], ['target' => '_blank']));
        $str = implode("<br>", $links);
        return
            '<a type="button" data-toggle="modal" data-target="#linksModal" data-id="' . $data->id
            . '" class="links"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span><span class="count">' . count(Site::getAcceptor($data, 2)) . '</span></a>
                <div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="' . Site::getAcceptor($data, 0)
            . '" class="states">' . $str . '</div>';
    }

    public static function formatAnchor($data)
    {
        $arr = self::getAnchor($data, 2);
        $links = array();
        for($i = 0; $i < count($arr); $i++)
            array_push($links, Html::a($arr[$i], 'https://www.google.com/search?q=' . $arr[$i], ['target' => '_blank']));
        $str = implode("<br>", $links);
        return
            '<a type="button" data-toggle="modal" data-target="#linksModal" data-id="' . $data->id
            . '" class="links"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span><span class="count">' . count(Site::getAnchor($data, 2)) . '</span></a>
                <div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="' . Site::getAnchor($data, 0)
            . '" class="states">' . $str . '</div>';
    }

    public static function getHeader($value, $title = 0)
    {
        if(!$title)
            return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'.$value.'" class="states-header">'.$value.'</div>';
        else return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'.$title.'" class="states-header">'.$value.'</div>';
    }

    public static function getSiteTheme($data)
    {
        $result = '';
        if($data->siteThemes) {
            foreach ($data->siteThemes as $value) {
                $theme = Theme::findOne($value->theme_id);
                $result .= $theme->name . ', ';
            }
        }
        $result = substr($result, 0, strlen($result)-2);
        return $result;
    }

    public static function getChart($data, $key)
    {
        $result = array();
        if($data->urls) {
            foreach ($data->urls[0]->audits as $value) {
                if($value->created_at >= strtotime("-3 month")){
                    if($key == "created_at")
                        array_push($result, self::getDate($value->$key));
                    else array_push($result, (int)$value->$key);
                }
            }
        }
        return $result;
    }

    public static function deleteSite($site)
    {
        try {
            foreach($site->urls as $url) {
                foreach ($url->audits as $audit) {
                    foreach ($audit->externalLinks as $link) {
                        ExternalLinks::deleteAll(['id' => $link->id]);
                    }
                    $audit = Audit::findOne($audit->id);
                    unlink(Yii::getAlias('@frontend/web/i/') . $audit->icon);
                    unlink(Yii::getAlias('@frontend/web/screenshots/') . $audit->screenshot);
                    Audit::deleteAll(['id' => $audit->id]);
                }
                \common\models\Url::deleteAll(['id' => $url->id]);
            }
            foreach ($site->dns as $dns)
                Dns::deleteAll(['id' => $dns->id]);

            foreach ($site->comments as $comment)
                Comments::deleteAll(['id' => $comment->id]);

            foreach ($site->auditPending as $auditPending)
                AuditPending::deleteAll(['id' => $auditPending->id]);

            foreach ($site->indexingPending as $indexingPending)
                IndexingPending::deleteAll(['id' => $indexingPending->id]);

            foreach ($site->indexing as $indexing)
                Indexing::deleteAll(['id' => $indexing->id]);

            Site::deleteAll(['id' => $site->id]);
        } catch (\Exception $e) { Debug::dd($e->getMessage());}
    }

    public static function getDate($date, $fl=0)
    {
        if($date) {
            $day = idate('d', $date);
            $month = idate('m', $date);
            $year = idate('Y', $date);

            if(!$fl)
                return $day.".".$month.".".$year;
            else
                return strtotime($year."-".$month."-".$day);
        }
    }

    public static function getDaysLeft($date) {
        $now = time();
        $expiration_date = self::getDate($date, 1);
        return floor(($expiration_date-$now)/ (60 * 60 * 24));
    }

    public static function getDomainsAge($date) {
        $datetime1 = date_create(date('Y').'-'.date('m').'-'.date('d'));
        $datetime2 = date_create(idate('Y', $date).'-'.idate('m', $date).'-'.idate('d', $date));
        $diff =  date_diff($datetime1, $datetime2);
        return $diff->y;
    }

    public static function getRegistrar($data, $fl)
    {
        $arr =  explode(", ", $data->registrar);

        if($fl)
            return $data->registrar;
        else
            return implode("\n", $arr);
    }

    public static function getStates($data, $fl)
    {
        $arr =  explode(", ", $data->states);

        if($fl == 1) {
            return $data->states;
        } elseif($fl == 0) {
            return implode("\n", $arr);
        } elseif ($fl == 2) {
            if(count($arr) == 1 && $arr[0] == "")
                return 0;
            else return $arr;
        }
    }

    public static function getIp($data, $fl)
    {
        $ip_array = array();
        foreach($data->dns as $value) {
            if($value->ip) {
                array_push($ip_array, $value->ip);
            }
        }

        if($fl == 1) {
            return implode("<br>", $ip_array);
        } elseif($fl == 0) {
            return implode("\n", $ip_array);
        } elseif ($fl == 2) {
            return $ip_array;
        }
    }

    public static function getDnsServer($data, $fl)
    {
        $dns_array = array();

        foreach($data->dns as $value) {
            if($value->target) {
                array_push($dns_array, $value->target);
            }
        }

        if($fl == 1) {
            return implode("<br>", $dns_array);
        } elseif($fl == 0) {
            return implode("\n", $dns_array);
        } elseif ($fl == 2) {
            return $dns_array;
        }
    }

    public static function getAudit($data, $key)
    {
        $result = 0;
        if($data->urls) {
            foreach ($data->urls[0]->audits as $value)
                $result = $value->$key;
        }
        return $result;
    }

    public static function formatDate($data, $key)
    {
        $result = 0;
        if($data->indexing) {
            foreach ($data->indexing as $value)
                $result = $value->$key;
        }
        $month = trim(preg_replace('/\d/', '', $result));
        if($month == 'січ.' || $month == 'янв.')
            $result = str_replace(' '.$month.' ', '.01.', $result);
        elseif($month == 'лют.' || $month == 'фев.')
            $result = str_replace(' '.$month.' ', '.02.', $result);
        elseif($month == 'бер.' || $month == 'мар.')
            $result = str_replace(' '.$month.' ', '.03.', $result);
        elseif($month == 'квiт.' || $month == 'апр.')
            $result = str_replace(' '.$month.' ', '.04.', $result);
        elseif($month == 'трав.' || $month == 'май')
            $result = str_replace(' '.$month.' ', '.05.', $result);
        elseif($month == 'черв.' || $month == 'июн.')
            $result = str_replace(' '.$month.' ', '.06.', $result);
        elseif($month == 'лип.' || $month == 'июл.')
            $result = str_replace(' '.$month.' ', '.07.', $result);
        elseif($month == 'серп.' || $month == 'авг.')
            $result = str_replace(' '.$month.' ', '.08.', $result);
        elseif($month == 'вер.' || $month == 'сен.')
            $result = str_replace(' '.$month.' ', '.09.', $result);
        elseif($month == 'жовт.' || $month == 'окт.')
            $result = str_replace(' '.$month.' ', '.10.', $result);
        elseif($month == 'лист.' || $month == 'ноя.')
            $result = str_replace(' '.$month.' ', '.11.', $result);
        elseif($month == 'груд.' || $month == 'дек.')
            $result = str_replace(' '.$month.' ', '.12.', $result);

        return $result;
    }

    public static function getIndex($data, $key)
    {
        $result = 0;
        if($data->indexing) {
            foreach ($data->indexing as $value)
                $result = $value->$key;
        }
        return $result;
    }

    public static function getAuditID($model, $key)
    {
        $id = str_replace("=", "", stristr($model, '='));
        $site = Site::findOne(['id' => $id]);
        $audit_id = 0;

        if($site->urls)
            foreach ($site->urls[0]->audits as $value)
                $audit_id = $value->id;

        return $audit_id;
    }

    public static function getAcceptor($data, $fl)
    {
        $n = 1;
        $external_links_array = array();
        if($data->urls)
            if($data->urls[0]->audits) {
                $n = count($data->urls[0]->audits);
                if($n <= 0) $n = 1;
            }
        if($data)
            if($data->urls)
                if($data->urls[0]->audits)
                    foreach ($data->urls[0]->audits[$n-1]->externalLinks as $value)
                        array_push($external_links_array, $value->acceptor);
        if($fl == 1)
            return implode("<br>", $external_links_array);
        elseif($fl == 0)
            return implode("\n", $external_links_array);
        elseif ($fl == 2)
            return $external_links_array;
    }

    public static function getAnchor($data, $fl)
    {
        $n = 1;
        $external_links_array = array();
        if($data->urls)
            if($data->urls[0]->audits) {
                $n = count($data->urls[0]->audits);
                if($n <= 0) $n = 1;
            }
        if($data)
            if ($data->urls)
                if ($data->urls[0]->audits)
                    foreach ($data->urls[0]->audits[$n-1]->externalLinks as $value) {
                        $val = trim(self::clearstr($value->anchor));
                        $val = trim(str_replace(array("\r\n", "\r", "\n", "<br>"), "", $val));
                        if (!empty($val))
                            array_push($external_links_array, $val);
                        else array_push($external_links_array, ' - анкор не задан');
                    }
        if($fl == 1)
            return implode("<br>", $external_links_array);
        elseif($fl == 0)
            return implode("\n", $external_links_array);
        elseif ($fl == 2)
            return $external_links_array;
    }

    /**
     * Функция была взята с php.net
     **/
    public static function utf8_str_split($str) {
        $split = 1;
        $array = array();
        for ($i = 0; $i < strlen($str); ) {
            $value = ord($str[$i]);
            if($value > 127) {
                if($value >= 192 && $value <= 223)
                    $split = 2;
                elseif($value >= 224 && $value <= 239)
                    $split = 3;
                elseif($value >= 240 && $value <= 247)
                    $split = 4;
            } else $split = 1;

            $key = NULL;
            for ( $j = 0; $j < $split; $j++, $i++ )
                $key .= $str[$i];
            array_push( $array, $key );
        }
        return $array;
    }
    /**
     * Функция вырезки
     * @param <string> $str
     * @return <string>
     */
    public static function clearstr($str){
        $sru = 'ёйцукенгшщзхъфывапролджэячсмитьбю';
        $s1 = array_merge(self::utf8_str_split($sru), self::utf8_str_split(strtoupper($sru)), range('A', 'Z'), range('a','z'), range('0', '9'), array('&',' ','#',';','%','?',':','(',')','-','_','=','+','[',']',',','.','/','\\'));
        $codes = array();
        for ($i = 0; $i < count($s1); $i++){
            $codes[] = ord($s1[$i]);
        }
        $str_s = self::utf8_str_split($str);
        for ($i = 0; $i < count($str_s); $i++){
            if (!in_array(ord($str_s[$i]), $codes)){
                $str = str_replace($str_s[$i], '', $str);
            }
        }
        return $str;
    }
}
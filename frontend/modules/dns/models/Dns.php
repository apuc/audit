<?php


namespace frontend\modules\dns\models;

use common\models\Url;
use frontend\modules\site\models\Site;

class Dns extends \common\models\Dns
{
    public function init()
    {
        parent::init();
    }

    public static function getUrlID($id)
    {
        $site_id = Dns::find()->select(['site_id'])->where(['id' => $id])->asArray()->all();
        if ($site_id) {
            $url_id = Url::find()->select(['id'])->where(['site_id' => $site_id[0]['site_id']])->asArray()->all();
            if ($url_id) {
                return $url_id[0]['id'];
            }
        }
    }

    public static function getUrlName($id)
    {
        $site_id = Dns::find()->where(['id' => $id])->asArray()->all();
        if($site_id) {
            $url_name = Url::find()->where(['site_id' => $site_id[0]['site_id']])->asArray()->all();
            if($url_name) {
                return $url_name[0]['url'];
            }
        }
    }
}
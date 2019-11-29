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
       $url_id = Url::find()->select(['id'])->where(['site_id' => $site_id[0]['site_id']])->asArray()->all();
       return $url_id[0]['id'];
    }

    public static function getSiteName($id)
    {
        $site_id = Dns::find()->select(['site_id'])->where(['id' => $id])->asArray()->all();
        $site_name = Site::find()->select(['name'])->where(['id' => $site_id[0]['site_id']])->asArray()->all();
        return $site_name[0]['name'];
    }
}
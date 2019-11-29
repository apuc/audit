<?php


namespace frontend\modules\site\models;


use common\classes\Debug;
use common\models\Url;

class Site extends \common\models\Site
{
    public function init()
    {
        parent::init();
    }

    public static function getUrlID($id)
    {
        $url_id = Url::find()->select(['id'])->where(['site_id' => $id])->asArray()->all();
        return $url_id[0]['id'];
    }
}
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

    public static function getUrlName($id)
    {
        $url = Url::find()->where(['site_id' => $id])->asArray()->all()[0]['url'];
        return $url;
    }
}
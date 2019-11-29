<?php


namespace frontend\modules\audit\models;


use common\classes\Debug;
use common\models\Url;

class Audit extends \common\models\Audit
{
    public function init()
    {
        parent::init();
    }

    public static function getUrlID($id)
    {
        $url_id = Audit::find()->where(['id' => $id])->asArray()->all()[0]['url_id'];
        return $url_id;
    }

    public static function getUrlName($id)
    {
        $url_id = Audit::find()->where(['id' => $id])->asArray()->all()[0]['url_id'];
        $url = Url::find()->where(['id' => $url_id])->asArray()->all()[0]['url'];
        return $url;
    }
}
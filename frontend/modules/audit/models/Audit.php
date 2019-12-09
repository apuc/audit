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
        $url_id = Audit::find()->where(['id' => $id])->asArray()->all();
        if($url_id) {
            return $url_id[0]['url_id'];
        }
    }

    public static function getUrlName($id)
    {
        $url_id = Audit::find()->where(['id' => $id])->asArray()->all();
        if($url_id) {
            $url = Url::find()->where(['id' => $url_id[0]['url_id']])->asArray()->all();
            if($url) {
                return $url[0]['url'];
            }
        }
    }
}
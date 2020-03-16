<?php


namespace frontend\modules\settings\models;


class Settings extends \common\models\Settings
{
    public function init()
    {
        parent::init();
    }

    public static function getMode($settings, $key)
    {
        return $settings->$key;
    }
}
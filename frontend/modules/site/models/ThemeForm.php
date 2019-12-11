<?php


namespace frontend\modules\site\models;


use yii\base\Model;

class ThemeForm extends  Model
{
    public $theme;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['theme', 'safe'],
        ];
    }
}
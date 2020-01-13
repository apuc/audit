<?php


namespace frontend\modules\url\models;


use yii\base\Model;

class UrlForm extends Model
{
    public $urls;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['urls', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'urls' => '',
        ];
    }
}
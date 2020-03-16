<?php


namespace frontend\modules\links\models;


use yii\base\Model;

class LinksForm extends Model
{
    public $links;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['links', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'links' => '',
        ];
    }
}
<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "site".
 *
 * @property int $id
 * @property string $name
 * @property int|null $creation_date
 * @property int|null $expiration_date
 * @property string|null $registrar
 * @property string|null $states
 * @property int|null $theme_id
 * @property string|null $comment
 *
 * @property Dns[] $dns
 * @property Url[] $urls
 */
class Site extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'site';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['creation_date', 'expiration_date', 'theme_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['registrar', 'states', 'comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Домен',
            'registrar' => 'Регистратор',
            'states' => 'Состояния',
            'creation_date' => 'Дата создания',
            'expiration_date' => 'Дата истечения срока',
            'theme_id' => 'Тема',
            'comment' => 'Комментарий',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDns()
    {
        return $this->hasMany(Dns::className(), ['site_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUrls()
    {
        return $this->hasMany(Url::className(), ['site_id' => 'id']);
    }
}

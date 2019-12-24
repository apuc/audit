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
 * @property string|null $title
 * @property string|null $redirect
 *
 * @property Comments[] $comments
 * @property Dns[] $dns
 * @property Theme $theme
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
            [['registrar', 'states', 'title', 'redirect'], 'string', 'max' => 255],
            [['theme_id'], 'exist', 'skipOnError' => true, 'targetClass' => Theme::className(), 'targetAttribute' => ['theme_id' => 'id']],
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
            'creation_date' => 'Дата создания',
            'expiration_date' => 'Дата истечения срока',
            'registrar' => 'Регистратор',
            'states' => 'Состояния',
            'theme_id' => 'Theme ID',
            'title' => 'Тайтл',
            'redirect' => 'Редирект',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comments::className(), ['site_id' => 'id']);
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
    public function getTheme()
    {
        return $this->hasOne(Theme::className(), ['id' => 'theme_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUrls()
    {
        return $this->hasMany(Url::className(), ['site_id' => 'id']);
    }
}

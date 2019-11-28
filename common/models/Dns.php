<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dns".
 *
 * @property int $id
 * @property string|null $class
 * @property int|null $ttl
 * @property string|null $type
 * @property string|null $target
 * @property int $site_id
 * @property string|null $ip
 *
 * @property Site $site
 */
class Dns extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dns';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ttl', 'site_id'], 'integer'],
            [['site_id'], 'required'],
            [['class', 'type'], 'string', 'max' => 10],
            [['target', 'ip'], 'string', 'max' => 255],
            [['site_id'], 'exist', 'skipOnError' => true, 'targetClass' => Site::className(), 'targetAttribute' => ['site_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'class' => 'Класс',
            'ttl' => 'Ttl',
            'type' => 'Тип',
            'target' => 'Target',
            'site_id' => 'Site ID',
            'ip' => 'Ip',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }
}

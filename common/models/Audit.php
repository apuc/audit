<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "audit".
 *
 * @property int $id
 * @property string|null $server_response_code
 * @property int|null $size
 * @property int|null $loading_time
 * @property int $created_at
 * @property int $url_id
 * @property int $google_indexing
 * @property int $yandex_indexing
 *
 * @property Url $url
 */
class Audit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audit';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['created_at'],
                ],
                'createdAtAttribute' => 'created_at',
                'value' => new Expression("strftime('%s', 'now')"),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['size', 'loading_time', 'created_at', 'url_id'], 'integer'],
            [['url_id'], 'required'],
            [['server_response_code'], 'string', 'max' => 100],
            [
                ['url_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Url::className(),
                'targetAttribute' => ['url_id' => 'id']
            ],
            [['google_indexing', 'yandex_indexing'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'server_response_code' => 'Код ответа сервера',
            'size' => 'Размер',
            'loading_time' => 'Время загрузки',
            'created_at' => 'Дата мониторинга',
            'url_id' => 'Url ID',
            'google_indexing' => 'Индексация Google',
            'yandex_indexing' => 'Индексация Яндекс'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUrl()
    {
        return $this->hasOne(Url::className(), ['id' => 'url_id']);
    }
}

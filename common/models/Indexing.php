<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "indexing".
 *
 * @property int $id
 * @property bool|null $google_indexing
 * @property int|null $google_indexed_pages
 * @property string|null $date_cache
 * @property bool|null $yandex_indexing
 * @property int $site_id
 * @property int|null $iks
 * @property int|null $status_google
 * @property int|null $status_yandex
 * @property int|null $status_date_cache
 * @property int|null $status_indexing_pages
 * @property int|null $status_iks
 *
 * @property Site $site
 */
class Indexing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'indexing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['google_indexing', 'yandex_indexing'], 'boolean'],
            [['google_indexed_pages', 'site_id', 'iks', 'status_google', 'status_yandex', 'status_date_cache', 'status_indexing_pages', 'status_iks'], 'integer'],
            [['site_id'], 'required'],
            [['date_cache'], 'string', 'max' => 255],
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
            'google_indexing' => 'Google Indexing',
            'google_indexed_pages' => 'Google Indexed Pages',
            'date_cache' => 'Date Cache',
            'yandex_indexing' => 'Yandex Indexing',
            'site_id' => 'Site ID',
            'iks' => 'Iks',
            'status_google' => 'Status Google',
            'status_yandex' => 'Status Yandex',
            'status_date_cache' => 'Status Date Cache',
            'status_indexing_pages' => 'Status Indexing Pages',
            'status_iks' => 'Status Iks',
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

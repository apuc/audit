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
            [['google_indexed_pages', 'site_id'], 'integer'],
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

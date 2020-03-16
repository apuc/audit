<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "audit_pending".
 *
 * @property int $id
 * @property int $site_id
 *
 * @property Site $site
 */
class AuditPending extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audit_pending';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['site_id'], 'required'],
            [['site_id'], 'integer'],
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

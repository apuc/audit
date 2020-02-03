<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "external_links".
 *
 * @property int $id
 * @property string|null $acceptor
 * @property string|null $anchor
 * @property int $audit_id
 * @property string|null $screenshot
 *
 * @property Audit $audit
 */
class ExternalLinks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'external_links';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['audit_id'], 'required'],
            [['audit_id'], 'integer'],
            [['acceptor'], 'string', 'max' => 100],
            [['anchor', 'screenshot'], 'string', 'max' => 255],
            [['audit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Audit::className(), 'targetAttribute' => ['audit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'acceptor' => 'Акцептор',
            'anchor' => 'Анкор',
            'audit_id' => 'Аудит ID',
            'screenshot' => 'Скриншот'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAudit()
    {
        return $this->hasOne(Audit::className(), ['id' => 'audit_id']);
    }
}

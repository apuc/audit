<?php

namespace common\models;

use common\classes\Debug;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "theme".
 *
 * @property int $id
 * @property string $name
 */
class Theme extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'theme';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Тема',
        ];
    }

    public static function getList()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }

    public static function getSource()
    {
        $model = self::find()->all();
        $res = [];
        foreach ($model as $item){
            $res[] = ['id' => $item->id, 'text' => $item->name];
        }
        return $res;
    }
}

<?php


namespace frontend\modules\site\models;


use yii\base\Model;

class CommentForm extends Model
{
    public $comment;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['comment', 'safe'],
        ];
    }
}
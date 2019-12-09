<?php


namespace frontend\modules\site\models;


class CommentForm
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
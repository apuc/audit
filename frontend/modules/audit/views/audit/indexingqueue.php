<?php

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

$this->title = 'Сайты в очереди на проверку индексации';

$dataProvider = new ActiveDataProvider(['query' => \common\models\IndexingPending::find()
    ->where(['site.user_id' => Yii::$app->user->identity->id])
    ->innerJoin('site', 'indexing_pending.site_id=site.id')]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($data) {
                    return Html::a("<span class='glyphicon glyphicon-trash' aria-hidden='true'></span>",
                        ['/indexingpending/indexingpending/customdelete', 'id' => $data]);
                },
            ],
        ],
        'site.name'
    ],
]);

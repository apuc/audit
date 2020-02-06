<?php

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

$this->title = 'Сайты в очереди на аудит данных для графика';

$dataProvider = new ActiveDataProvider(['query' => \common\models\ChartAuditQueue::find()
    ->where(['site.user_id' => Yii::$app->user->identity->id])
    ->innerJoin('site', 'chart_audit_queue.site_id=site.id')]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($data) {
                    return Html::a("<span class='glyphicon glyphicon-trash' aria-hidden='true'></span>",
                        ['/chartaudit/chartaudit/customdelete', 'id' => $data]);
                },
            ],
        ],
        'site.name'
    ],
]);
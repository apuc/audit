<?php

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

echo "<h3>Сайты в очереди на аудит</h3><br>";
$dataProvider = new ActiveDataProvider(['query' => \common\models\AuditPending::find()]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($data) {
                    return Html::a("<span class='glyphicon glyphicon-trash' aria-hidden='true'></span>",
                        ['/auditpending/auditpending/customdelete', 'id' => $data]);
                },
            ],
        ],
        'site.name'
    ],
]);

echo "<br><br><h3>Сайты в очереди на индексацию</h3><br>";
$dataProvider = new ActiveDataProvider(['query' => \common\models\IndexingPending::find()]);
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

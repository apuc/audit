<?php

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

$this->title = 'Сайты в очереди на аудит данных для графика';

echo '<div class="sticky">';
echo Html::button('Удалить выделенное', ['class' => 'btn btn-primary chart_data_delete']) . '&nbsp';
echo '</div>';

$dataProvider = new ActiveDataProvider(['query' => \common\models\ChartAuditQueue::find()
    ->where(['site.user_id' => Yii::$app->user->identity->id])
    ->innerJoin('site', 'chart_audit_queue.site_id=site.id')]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'grid_chart',
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
        ['class' => 'yii\grid\CheckboxColumn'],
        'site.name'
    ],
]);
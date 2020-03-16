<?php

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

$this->title = 'Сайты в очереди на проверку индексации';

echo '<div class="sticky">';
echo Html::button('Удалить выделенное', ['class' => 'btn btn-primary indexing_delete']) . '&nbsp';
echo '</div>';

$dataProvider = new ActiveDataProvider(['query' => \common\models\IndexingPending::find()
    ->where(['site.user_id' => Yii::$app->user->identity->id])
    ->innerJoin('site', 'indexing_pending.site_id=site.id')]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'grid_indexing',
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
        ['class' => 'yii\grid\CheckboxColumn'],
        'site.name'
    ],
]);

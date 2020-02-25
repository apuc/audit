<?php

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

$this->title = 'Сайты в очереди на аудит';

echo '<div class="sticky">';
echo Html::button('Удалить выделенное', ['class' => 'btn btn-primary audit_delete']) . '&nbsp';
echo '</div>';

$dataProvider = new ActiveDataProvider(['query' => \common\models\AuditPending::find()
    ->where(['site.user_id' => Yii::$app->user->identity->id])
    ->innerJoin('site', 'audit_pending.site_id=site.id')]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'grid_audit',
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
        ['class' => 'yii\grid\CheckboxColumn'],
        'site.name'
    ],
]);
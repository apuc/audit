<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use frontend\modules\audit\models\Audit;

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\audit\models\AuditSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Аудит';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audit-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'url.url',
                'label' => 'Url',
                'value' => function ($data) {
                    return Html::a(
                        Audit::getUrlName($data->id),
                        Url::to(['/audit/audit?AuditSearch[url]=' . Audit::getUrlName($data->id)])
                    );
                },
                'format' => 'raw',
                'filter' => Html::activeTextInput(
                    $searchModel,
                    'url',
                    ['class' => 'form-control']
                ),
            ],
            'server_response_code',
            'size',
            'loading_time',
            'created_at:datetime',
            'google_indexing:boolean',
            'yandex_indexing:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

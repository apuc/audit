<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\dns\models\DnsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Dns';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dns-index">

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'site.name',
                'value' => function ($data) {
                    return Html::a(
                        \frontend\modules\dns\models\Dns::getUrlName($data->id),
                        Url::to(['/audit/audit?AuditSearch[url]=' . \frontend\modules\dns\models\Dns::getUrlName($data->id)])
                    );
                },
                'format' => 'raw',
            ],
            'class',
            'ttl',
            'type',
            'target',
            'ip',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

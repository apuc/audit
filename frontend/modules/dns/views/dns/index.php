<?php

use yii\helpers\Html;
use yii\grid\GridView;

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

            'class',
            'ttl',
            'type',
            'target',
            'ip',
            'site.name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\externallinks\models\ExternallinksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Внешние ссылки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-links-index">

    <p>
<!--        --><?//= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'acceptor:url',
            'anchor',
            'audit_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

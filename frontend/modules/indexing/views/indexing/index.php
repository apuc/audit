<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\indexing\models\IndexingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Indexings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="indexing-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Indexing', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'google_indexing:boolean',
            'google_indexed_pages',
            'date_cache',
            'yandex_indexing:boolean',
            //'site_id',
            //'iks',
            //'status_google',
            //'status_yandex',
            //'status_date_cache',
            //'status_indexing_pages',
            //'status_iks',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

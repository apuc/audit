<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Indexing */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Indexings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="indexing-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'google_indexing:boolean',
            'google_indexed_pages',
            'date_cache',
            'yandex_indexing:boolean',
            'site_id',
            'iks',
            'status_google',
            'status_yandex',
            'status_date_cache',
            'status_indexing_pages',
            'status_iks',
        ],
    ]) ?>

</div>

<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Audit */
/* @var $externalLinks yii\data\ActiveDataProvider */
/* @var $dns yii\data\ActiveDataProvider */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Аудит', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="audit-view">

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверенны, что хотите удалить эту запись?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <h2>Аудит</h2>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'url.url',
            'server_response_code',
            'size',
            'loading_time',
            'created_at:datetime',
            'google_indexing:boolean',
            'yandex_indexing:boolean',
        ],
    ]) ?>

    <h2>DNS</h2>
    <?= GridView::widget([
        'dataProvider' => $dns,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'site.name',
            'class',
            'ttl',
            'type',
            'target',
            'ip',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <h2>Внешние ссылки</h2>
    <?= GridView::widget([
        'dataProvider' => $externalLinks,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'acceptor',
            'anchor',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>

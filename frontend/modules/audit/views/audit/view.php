<?php

use frontend\modules\externallinks\models\Externallinks;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use frontend\modules\site\models\Site;

/* @var $this yii\web\View */
/* @var $model common\models\Audit */
/* @var $externalLinks yii\data\ActiveDataProvider */
/* @var $dns yii\data\ActiveDataProvider */
/* @var $site yii\data\ActiveDataProvider */
/* @var $audit yii\data\ActiveDataProvider */
/* @var $searchModel frontend\modules\externallinks\models\ExternallinksSearch */

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

    <h2>Данные домена</h2>
    <?= GridView::widget([
        'dataProvider' => $site,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'registrar',
            'states',
            'creation_date:date',
            'expiration_date:date',
            [
                'attribute' => ' Тема',
                'value' => function ($data) {
                    if($data->theme) {
                        return $data->theme->name;
                    } else {
                        return null;
                    }

                },
                'format' => 'raw',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <h2>Аудит</h2>
    <?= GridView::widget([
        'dataProvider' => $audit,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'url.url',
            'server_response_code',
            'size',
            'loading_time',
            'created_at:date',
            'google_indexing:boolean',
            'yandex_indexing:boolean',
            [
                'attribute' => 'Скриншот',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::tag('img', null, ['src' => Url::to('@web/screenshots/' . $model->screenshot), 'width' => '300px']);
                }
            ],
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

    <h2>DNS</h2>
    <?= GridView::widget([
        'dataProvider' => $dns,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'class',
            'ttl',
            'type',
            'target',
            'ip',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>

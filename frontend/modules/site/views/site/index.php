<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use \frontend\modules\site\models\Site;

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\site\models\SiteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сайты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-index">

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
                'attribute' => 'Иконка',
                'value' => function ($data) {
                    return Site::getIcon($data->name);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'name',
                'value' => function ($data) {
                    return Html::a($data->name, 'http://' .  $data->name, ['target' => '_blank',]);
                },
                'format' => 'raw',
            ],
            'registrar',
            'states',
            [
                'attribute' => 'Дата создания и истечения срока',
                'value' => function ($data) {
                    return Site::getDate($data->id, 'creation_date') . " - " . Site::getDate($data->id, 'expiration_date');
                },
            ],

            [
                'attribute' => 'Target',
                'value' => function ($data) {
                    return Site::getTarget($data->id);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'IP',
                'value' => function ($data) {
                    return Site::getIp($data->id);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'Код ответа сервера',
                'value' => function ($data) {
                    return Site::getAudit($data->id, 'server_response_code');
                },
            ],
            [
                'attribute' => 'Размер',
                'value' => function ($data) {
                    return Site::getAudit($data->id, 'size');
                },
            ],
            [
                'attribute' => 'Время загрузки',
                'value' => function ($data) {
                    return Site::getAudit($data->id, 'loading_time');
                },
            ],
            [
                'attribute' => 'Индексация Google',
                'value' => function ($data) {
                    return Site::getAudit($data->id, 'google_indexing');
                },
            ],
            [
                'attribute' => 'Индексация Яндекс',
                'value' => function ($data) {
                    return Site::getAudit($data->id, 'yandex_indexing');
                },
            ],
//            [
//                'class' => 'yii\grid\ActionColumn',
//                'template' => '{comment}',
//                'buttons' => [
//                    'comment' => function ($data) {
//                        return Html::a(
//                            "Комментарий",
//                            ['/audit/audit/view', 'id' => Site::getAuditID($data, 'id')]
//                        );
//                    },
//                ],
//            ],
            [
                'attribute' => 'Внешние ссылки',
                'value' => function ($data) {
                    return Site::getExternalLinks($data->id);
                },
                'format' => 'raw',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{show} {update}',
                'buttons' => [
                    'show' => function ($data) {
                        return Html::a(
                            "Подробнее",
                            ['/audit/audit/view', 'id' => Site::getAuditID($data, 'id')]
                        );
                    },
                ],
            ],
        ],
    ]); ?>


</div>

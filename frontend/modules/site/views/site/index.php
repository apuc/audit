<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use \frontend\modules\site\models\Site;
use \common\models\Theme;

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
                'class' => 'yii\grid\ActionColumn',
                'template' => '{show} {update}',
                'buttons' => [
                    'show' => function ($data) {
                        return Html::a(
                            "<span class=\"glyphicon glyphicon-eye-open\" aria-hidden=\"true\"></span>",
                            ['/audit/audit/view', 'id' => Site::getAuditID($data, 'id')]
                        );
                    },
                ],
            ],
            [
                'attribute' => '',
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
                    return Site::getDate($data->id, 'creation_date') . "<br>" . Site::getDate($data->id, 'expiration_date');
                },
                'format' => 'raw',
            ],
//            [
//                'attribute' => ' Тема',
//                'value' => function ($data) {
//                    return Site::getTheme($data->id);
//                },
//                'format' => 'raw',
//            ],
            [
                'attribute' => ' Тема',
                'value' => function ($data) {
                        return Html::a(
                            Site::getTheme($data->id),
                            ['/domain/site/theme', 'id' => $data->id]
                        );
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
                'attribute' => 'Размер (байт)',
                'value' => function ($data) {
                    return Site::getAudit($data->id, 'size');
                },
            ],
            [
                'attribute' => 'Время загрузки (мс)',
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
            [
                'attribute' => ' Комментарий',
                'value' => function ($data) {
                        return Html::a(
                            Site::getComment($data->id),
                            ['/domain/site/comment', 'id' => $data->id]
                        );
                    },
                'format' => 'raw',

            ],
            [
                'attribute' => 'Внешние ссылки',
                'value' => function ($data) {
                    return Site::getExternalLinks($data->id);
                },
                'format' => 'raw',
            ],
        ],
//        'tableOptions' =>['style' => 'width: 100%;'],
    ]); ?>

    <?php
//    $js = <<<JS
//    $('#comment').on('click', function(){
//        alert('Работает!');
//        return false;
//    });
//JS;
//
//    $this->registerJs($js);
    ?>

</div>

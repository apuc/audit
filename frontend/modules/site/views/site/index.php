<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use \frontend\modules\site\models\Site;
use \common\models\Theme;
use yii\bootstrap\Modal;
use dosamigos\editable\Editable;
use yii\widgets\ActiveForm;

/* @var $form yii\bootstrap\ActiveForm */
/* @var $theme */

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
            [
                'attribute' => 'theme.name',
                'value' => function ($data) {
                    if(Site::getThemeCustom($data->id) == "")
                        $value = '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span><br>';
                    else
                        $value = Site::getThemeCustom($data->id);
                       return Editable::widget( [
                           'name' => 'theme',
                           'value' => $value,
                           'url' => '/api/api/theme',
                           'type' => 'select2',
                           'mode' => 'pop',
                           'clientOptions' => [
                               'placement' => 'right',
                               'pk' => $data->id,
                               'select2' => [
                                   'width' => '124px'
                               ],
                               'source' => Theme::getSource(),
                           ]
                       ]);
                    },
                'filter' => Html::activeTextInput(
                    $searchModel,
                    'theme',
                    ['class' => 'form-control']
                ),
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
                'attribute' => 'Комментарий',
                'value' => function ($data) {
                    if(Site::getComment($data->id) == "")
                        $value = '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span><br>';
                    else
                        $value = Site::getComment($data->id);
                    return Editable::widget( [
                        'name' => 'comment',
                        'value' => $value,
                        'url' => '/api/api/comment',
                        'type' => 'textarea',
                        'mode' => 'pop',
                        'clientOptions' => [
                            'placement' => 'right',
                            'pk' => $data->id,
                            'textarea' => [
                                'width' => '124px'
                            ],
//                            'class' => 'custom-row',
                        ]
                    ]);
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
    $js = <<<JS
    $('.comment').on('click', function(){
        alert('Работает!');
        return false;
    });
JS;

    $this->registerJs($js);
    ?>

</div>

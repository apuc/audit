<?php

use common\models\Links;
//use dosamigos\highcharts\HighCharts;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use common\classes\SizerGridView;
use common\services\AuditService;
use \frontend\modules\site\models\Site;
use \common\models\Theme;
use \common\models\Comments;
use dosamigos\editable\Editable;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use \common\classes\Debug;


/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\site\models\SiteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model common\models\Comments */

$this->title = 'Сайты';
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="site-index">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= Html::button('Проверить индексацию', ['class' => 'btn btn-primary indexing']) ?>
        <?= Html::button('Провести аудит', ['class' => 'btn btn-primary audit']) ?>

        <?php
        Pjax::begin(['id' => 'sitePjax']);
        echo SizerGridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'id' => 'grid',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{show} {update} {delete}',
                    'buttons' => [
                        'show' => function ($data) {
                            return Html::a(
                                "<span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span>",
                                ['/audit/audit/view', 'id' => Site::getAuditID($data, 'id')]);
                        },
                        'delete' => function ($data) {
                            return Html::a("<span class='glyphicon glyphicon-trash' aria-hidden='true'></span>",
                                ['/domain/site/customdelete', 'id' => $data]);
                        },
                    ],
                ],
                ['class' => 'yii\grid\CheckboxColumn'],
                [
                    'attribute' => '',
                    'format' => 'raw',
                    'value' => function ($data) {
                        if(Site::getAudit($data, 'icon') != 'error.jpg')
                            return Html::tag('img', null, ['src' => Url::to('@web/i/'
                                . Site::getAudit($data, 'icon')), 'width' => '16px']);
                        else return '';
                    }
                ],
                [
                    'attribute' => '',
                    'format' => 'raw',
                    'value' => function ($data) {
                        if(Site::getAudit($data, 'screenshot') != 'error.jpg')
                            return Html::tag('img', null, [
                                'src' => Url::to('@web/screenshots/' . Site::getAudit($data, 'screenshot')),
                                'width' => '32px', 'class' => 'my-img']);
                        else return '';
                    }
                ],
//                [
//                    'attribute' => '',
//                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="График" class="states"><span class="glyphicon glyphicon-signal" aria-hidden="true"></span></div>',
//                    'format' => 'raw',
//                    'value' => function ($data) {
//
//                        return "<span class='glyphicon glyphicon-signal target ".$data->id."' aria-hidden='true'></span><div class='graphic'><div id='container'></div></div>";
                           // ."<div class='graphic'>"
//                            . HighCharts::widget([
//                                'clientOptions' => [
//                                    'chart' => ['type' => 'spline', 'width' => 250, 'height' => 250],
//                                    'title' => ['text' => ''],
//                                    'xAxis' => ['categories' => Site::getChart($data, 'created_at')],
//                                    'yAxis' => ['title' => ['text' => '']],
//                                    'series' => [['name' => 'Размер', 'data' => Site::getChart($data, 'size')]]
//                                ]
//                            ]) . HighCharts::widget([
//                                'clientOptions' => [
//                                    'chart' => ['type' => 'spline', 'width' => 250, 'height' => 250],
//                                    'title' => ['text' => ''],
//                                    'xAxis' => ['categories' => Site::getChart($data, 'created_at')],
//                                    'yAxis' => ['title' => ['text' => '']],
//                                    'series' => [['name' => 'Время загрузки', 'data' => Site::getChart($data, 'loading_time')]]
//                                ]
//                            ]) . HighCharts::widget([
//                                'clientOptions' => [
//                                    'chart' => ['type' => 'spline', 'width' => 250, 'height' => 250],
//                                    'title' => ['text' => ''],
//                                    'xAxis' => ['categories' => Site::getChart($data, 'created_at')],
//                                    'yAxis' => ['title' => ['text' => '']],
//                                    'series' => [['name' => 'Код ответа сервера', 'data' => Site::getChart($data, 'server_response_code')]]
//                                ]
//                            ])
                          //  . "</div>";
//                    },
//                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Домен" class="states">Домен</div>',
                    'value' => function ($data) {
                        return Html::a($data->name, 'http://' . $data->name,
                            ['target' => '_blank', 'id' => 'domain']);
                    },
                    'filter' => Html::activeTextInput($searchModel, 'name', ['class' => 'form-control']),
                    'format' => 'raw',
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Редирект" class="states">Редирект</div>',
                    'value' => function ($data) {
                        return Html::a($data->redirect, 'http://' . $data->redirect, ['target' => '_blank']);
                    },
                    'filter' => Html::activeTextInput($searchModel, 'redirect', ['class' => 'form-control']),
                    'format' => 'raw',
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Тайтл" class="states">Тайтл</div>',
                    'value' => function ($data) {
                        return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . $data->title . '" class="states">' . $data->title . '</div';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                                    title="Ссылки" class="states"><span class="glyphicon glyphicon-link"
                        aria-hidden="true"></span></div>',
                    'format' => 'raw',
                    'value' => function ($data) {
                        $array = ArrayHelper::map(Links::find()->all(), 'name', 'name');
                        $array[$data->name] = $data->name;
                        return Html::activeDropDownList($data, 'id', $array, [
                                'onchange' => 'jsFunction(this, this.value);',
                                'prompt' => '...',
                                'class' => 'custom-ddl',
                                'data-domain-name' => $data->name
                        ]);
                    },
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Тема" class="states">Тема</div>',
                    'value' => function ($data) {
                        if (!$data->theme)
                            $value = '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span><br>';
                        else $value = $data->theme->name;

                        return Editable::widget([
                            'name' => 'theme',
                            'value' => $value,
                            'url' => '/api/api/theme',
                            'type' => 'select2',
                            'mode' => 'pop',
                            'clientOptions' => [
                                'placement' => 'right',
                                'pk' => $data->id,
                                'select2' => ['width' => '124px'],
                                'source' => Theme::getSource(),
                            ]
                        ]);
                    },
                    'filter' => Html::activeTextInput($searchModel, 'theme', ['class' => 'form-control']),
                    'format' => 'raw',
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Комментарий" class="states"><span class="glyphicon glyphicon-comment "
                        aria-hidden="true"></span></div>',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return '<a type="button" data-toggle="modal" data-target="#exampleModal" data-id="' . $data->id
                            . '" class="comment" title="Добавить комментарий"><span class="glyphicon glyphicon-pencil" 
                            aria-hidden="true"></span></a>' . "<br>" .
                            Html::a("<span class=\"glyphicon glyphicon-eye-open\" aria-hidden=\"true\"></span>",
                                ['/comments/comments/?CommentsSearch[site_id]=' . $data->id],
                                ['title' => 'Посмотреть комментарии к сайту']
                            );
                    },
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Код ответа сервера" class="states">Код</div>',
                    'value' => function ($data) {
                        return Site::getAudit($data, 'server_response_code');
                    },
                    'filter' => Html::activeTextInput($searchModel, 'server_response_code', ['class' => 'form-control']),
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Размер (байт)" class="states">Байт</div>',
                    'value' => function ($data) {
                        return Site::getAudit($data, 'size');
                    },
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Время загрузки (мс)" class="states">Мс</div>',
                    'value' => function ($data) {
                        return Site::getAudit($data, 'loading_time');
                    },
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Регистратор" class="states">Регистратор</div>',
                    'value' => function ($data) {
                        return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . Site::getRegistrar($data, 0) . '" class="states">'
                            . Site::getRegistrar($data, 1) . '</div';
                    },
                    'format' => 'raw',
                    'filter' => Html::activeTextInput($searchModel, 'registrar', ['class' => 'form-control']),
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Состояния" class="states">Состояния</div>',
                    'value' => function ($data) {
                        return ' <div class="count">' . Site::getStates($data, 2)
                            . '</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . Site::getStates($data, 0) . '" class="states">'
                            . Site::getStates($data, 1) . '</div';
                    },
                    'format' => 'raw',
                    'contentOptions' => function ($data) {
                        return ['class' => (stristr(Site::getStates($data, 1), 'UNVERIFIED') ? 'danger' : '')];
                    },
                    'filter' => Html::activeTextInput($searchModel, 'states', ['class' => 'form-control']),
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Дата создания" class="states">Создан</div>',
                    'value' => function ($data) {
                        return Site::getDate($data->creation_date);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Дней до окончания регистрации" class="states">Дни</div>',
                    'value' => function ($data) {
                        return Site::getDaysLeft($data->expiration_date);
                    },
                    'format' => 'raw',
                    'contentOptions' => function ($data) {
                        return ['class' => (Site::getDaysLeft($data->expiration_date) < 30 ? 'danger' : '')];
                    },
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Индексация главной страницы в Google" class="states">'.
                        Html::tag('img', null, ['src' => Url::to('@web/img/google.jpg'), 'width' => '16px']).'</div>',
                    'value' => function ($data) {
                        return Site::getIndex($data, 'google_indexing');
                    },
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Количество проиндексированных страниц" class="states">N</div>',
                    'value' => function ($data) {
                        return Site::getIndex($data, 'google_indexed_pages');
                    },
                ],
//                [
//                    'attribute' => '',
//                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
//                        title="Кэш Google" class="states">CG</div>',
//                    'value' => function ($data) {
//                        return Html::a($data->name, 'http://webcache.googleusercontent.com/search?q=cache:' . $data->name, ['target' => '_blank']);
//                    },
//                    'format' => 'raw',
//                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Дата кэша" class="states">Дата кэша</div>',
                    'value' => function ($data) {
                        return Site::getIndex($data, 'date_cache');
                    },
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Индексация главной страницы в Yandex" class="states">'
                        . Html::tag('img', null, ['src' =>
                            Url::to('@web/img/yandex.jpg'), 'width' => '16px']).'</div>',
                    'value' => function ($data) {
                        return Site::getIndex($data, 'yandex_indexing');
                    },
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="IP" class="states">IP</div>',
                    'value' => function ($data) {
                        return '<div class="count">' . count(Site::getIp($data, 2))
                            . '</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . Site::getIp($data, 0) . '" class="states">'
                            . Site::getIp($data, 1) . '</div';
                    },
                    'format' => 'raw',
                    'contentOptions' => function ($data) {
                        return ['class' => (count(Site::getIp($data, 2)) > 2 ? 'warning' : '')];
                    },
                    'filter' => Html::activeTextInput($searchModel, 'ip', ['class' => 'form-control']),
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="DNS" class="states">DNS</div>',
                    'value' => function ($data) {
                        return '<div class="count">' . count(Site::getDnsServer($data, 2))
                            . '</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . Site::getDnsServer($data, 0) . '" class="states">'
                            . Site::getDnsServer($data, 1) . '</div';
                    },
                    'format' => 'raw',
                    'contentOptions' => function ($data) {
                        return ['class' => (count(Site::getDnsServer($data, 2)) > 2 ? 'warning' : '')];
                    },
                    'filter' => Html::activeTextInput($searchModel, 'dns', ['class' => 'form-control']),
                ],
                [
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Акцептор" class="states">Акцептор</div>',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return '<div class="count">' . count(Site::getAcceptor($data, 2))
                            . '</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . Site::getAcceptor($data, 0) . '" class="states">'
                            . Site::getAcceptor($data, 1) . '</div>';
                    },
                    'filter' => Html::activeTextInput($searchModel,'external_links', ['class' => 'form-control']),
                    'contentOptions' => function ($data) {
                        return ['class' => (count(Site::getAcceptor($data, 2)) > 2 ? 'warning' : '')];
                    },
                ],
                [
                    'attribute' => 'Анкор',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Анкор" class="states">Анкор</div>',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return '<div class="count">' . count(Site::getAnchor($data, 2))
                            . '</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . Site::getAnchor($data, 0) . '" class="states">'
                            . Site::getAnchor($data, 1) . '</div>';
                    },
                    'contentOptions' => function ($data) {
                        return ['class' => (count(Site::getAcceptor($data, 2)) > 2 ? 'warning' : '')];
                    },
                    'filter' => Html::activeTextInput($searchModel, 'anchor', ['class' => 'form-control']),
                ],
            ],
        ]);
        Pjax::end();
        ?>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true" data-site-id="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Комментарий</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php $form = ActiveForm::begin();
                    $model = new Comments(); ?>

                    <?= $form->field($model, 'destination_id')->dropDownList(
                        \yii\helpers\ArrayHelper::map(common\models\User::find()->all(), 'id', 'username'),
                        ['prompt' => '...']
                    ) ?>

                    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

                    <div class="form-group">
                        <?= Html::button('Сохранить', ['class' => 'btn btn-success', 'id' => 'commentAjax',
                            'data-dismiss' => "modal"]) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
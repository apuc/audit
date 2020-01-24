<?php

use common\models\Links;
use frontend\modules\settings\models\Settings;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use common\classes\SizerGridView;
use \frontend\modules\site\models\Site;
use \common\models\Theme;
use \common\models\Comments;
use dosamigos\editable\Editable;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;


/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\site\models\SiteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model common\models\Comments */
/* @var $settings common\models\Settings */

$this->title = 'Сайты';
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="site-index">

        <?= Html::button('Проверить индексацию', ['class' => 'btn btn-primary indexing']) ?>
        <?= Html::button('Провести аудит', ['class' => 'btn btn-primary audit']) ?>
        <?php
        $links = new \common\models\Links();
        $array = ArrayHelper::map(Links::find()->all(), 'name', 'name');
        $array['cache'] = 'Кэш Google';
        echo Html::activeDropDownList($links, 'id', $array, [
            'onchange' => 'redirect(this, this.value);',
            'prompt' => 'Выберите ссылку',
            'class' => 'btn btn-primary'
        ]);
        ?>

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
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'delete' => function ($data) {
                            return Html::a("<span class='glyphicon glyphicon-trash' aria-hidden='true'></span>",
                                ['/domain/site/customdelete', 'id' => $data]);
                        },
                    ],
                ],
                ['class' => 'yii\grid\CheckboxColumn'],
                [
                    'visible' => Settings::getMode($settings, 'icon'),
                    'attribute' => '',
                    'format' => 'raw',
                    'value' => function ($data) {
                        if(Site::getAudit($data, 'icon') != 'error.jpg')
                            return Html::tag('img', null, ['src' => Url::to('@web/i/'
                                . Site::getAudit($data, 'icon')), 'width' => '16px', 'onclick' => "copyToClipboard('domain-".$data->name."')"]);
                        else return '';
                    }
                ],
                [
                    'visible' => Settings::getMode($settings,'screenshot'),
                    'attribute' => '',
                    'format' => 'raw',
                    'value' => function ($data) {
                        if(Site::getAudit($data, 'screenshot') != 'error.jpg')
                            return Html::tag('img', null, [
                                'src' => Url::to('@web/screenshots/' . Site::getAudit($data, 'screenshot')),
                                'width' => '32px', 'class' => 'scale']);
                        else return '';
                    }
                ],
                [
                    'visible' => Settings::getMode($settings,'chart'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="График" class="states-header">
                        <span class="glyphicon glyphicon-signal" aria-hidden="true"></span></div>',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return "<span class='glyphicon glyphicon-signal target ".$data->id."' aria-hidden='true'></span>
                            <div class='graphic_size'><div id='size'></div></div>
                            <div class='graphic_loading_time'><div id='loading_time'></div></div>
                            <div class='graphic_server_response_code'><div id='server_response_code'></div></div>";
                    },
                ],
                [
                    'visible' => Settings::getMode($settings,'domain'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Домен" class="states-header">Домен</div>',
                    'value' => function ($data) {
                        return Html::a('<div id="domain-'.$data->name.'">' . $data->name . '</div>', 'http://' . $data->name,
                            ['target' => '_blank', 'id' => 'domain']);
                    },
                    'filter' => Html::activeTextInput($searchModel, 'name', ['class' => 'form-control']),
                    'format' => 'raw',
                ],
                [
                    'visible' => Settings::getMode($settings,'redirect'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Редирект" class="states-header">Редирект</div>',
                    'value' => function ($data) {
                        return Html::a($data->redirect, 'http://' . $data->redirect, ['target' => '_blank']);
                    },
                    'filter' => Html::activeTextInput($searchModel, 'redirect', ['class' => 'form-control']),
                    'format' => 'raw',
                ],
                [
                    'visible' => Settings::getMode($settings,'title'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Тайтл" class="states-header">Тайтл</div>',
                    'value' => function ($data) {
                        return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . $data->title . '" class="states">' . $data->title . '</div';
                    },
                    'format' => 'raw',
                ],
                [
                    'visible' => Settings::getMode($settings,'theme'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Тема" class="states-header">Тема</div>',
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
                    'visible' => Settings::getMode($settings,'comment'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Комментарий" class="states-header"><span class="glyphicon glyphicon-comment"
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
                    'visible' => Settings::getMode($settings,'server_response_code'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Код ответа сервера" class="states-header">Код</div>',
                    'value' => function ($data) {
                        return Site::getAudit($data, 'server_response_code');
                    },
                    'filter' => Html::activeTextInput($searchModel, 'server_response_code', ['class' => 'form-control']),
                ],
                [
                    'visible' => Settings::getMode($settings,'size'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Размер (байт)" class="states-header">Байт</div>',
                    'value' => function ($data) {
                        return Site::getAudit($data, 'size');
                    },
                ],
                [
                    'visible' => Settings::getMode($settings,'loading_time'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Время загрузки (мс)" class="states-header">Мс</div>',
                    'value' => function ($data) {
                        return Site::getAudit($data, 'loading_time');
                    },
                ],
                [
                    'visible' => Settings::getMode($settings,'registrar'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Регистратор" class="states-header">Регистратор</div>',
                    'value' => function ($data) {
                        return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . Site::getRegistrar($data, 0) . '" class="states">'
                            . Site::getRegistrar($data, 1) . '</div';
                    },
                    'format' => 'raw',
                    'filter' => Html::activeTextInput($searchModel, 'registrar', ['class' => 'form-control']),
                ],
                [
                    'visible' => Settings::getMode($settings,'states'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Состояния" class="states-header">Состояния</div>',
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
                    'visible' => Settings::getMode($settings,'created_at'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Дата создания" class="states-header">Создан</div>',
                    'value' => function ($data) {
                        return Site::getDate($data->creation_date);
                    },
                    'format' => 'raw',
                ],
                [
                    'visible' => Settings::getMode($settings,'days_left'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true"
                        title="Дней до окончания регистрации" class="states-header">Дни</div>',
                    'value' => function ($data) {
                        return Site::getDaysLeft($data->expiration_date);
                    },
                    'format' => 'raw',
                    'contentOptions' => function ($data) {
                        return ['class' => (Site::getDaysLeft($data->expiration_date) < 30 ? 'danger' : '')];
                    },
                ],
                [
                    'visible' => Settings::getMode($settings,'google_indexing'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Индексация главной страницы в Google" class="states-header">'.
                        Html::tag('img', null, ['src' => Url::to('@web/img/google.jpg'), 'width' => '16px']).'</div>',
                    'value' => function ($data) {
                        return Site::getIndex($data, 'google_indexing');
                    },
                    'contentOptions' => function ($data) {
                        return ['class' => (Site::getIndex($data, 'status_google') == 1 ? 'danger' : '')];
                    },
                ],
                [
                    'visible' => Settings::getMode($settings,'google_pages'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Количество проиндексированных страниц" class="states-header">N</div>',
                    'value' => function ($data) {
                        return Site::getIndex($data, 'google_indexed_pages');
                    },
                    'contentOptions' => function ($data) {
                        return ['class' => (Site::getIndex($data, 'status_indexing_pages') == 1 ? 'danger' : '')];
                    },
                ],
                [
                    'visible' => Settings::getMode($settings,'google_date_cache'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Дата кэша" class="states-header">Дата&nbspкэша</div>',
                    'value' => function ($data) {
                        return Site::getIndex($data, 'date_cache');
                    },
                    'contentOptions' => function ($data) {
                        return ['class' => (Site::getIndex($data, 'status_date_cache') == 1 ? 'danger' : '')];
                    },
                ],
                [
                    'visible' => Settings::getMode($settings,'yandex_indexing'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Индексация главной страницы в Yandex" class="states-header">'
                        . Html::tag('img', null, ['src' =>
                            Url::to('@web/img/yandex.jpg'), 'width' => '16px']).'</div>',
                    'value' => function ($data) {
                        return Site::getIndex($data, 'yandex_indexing');
                    },
                    'contentOptions' => function ($data) {
                        return ['class' => (Site::getIndex($data, 'status_yandex') == 1 ? 'danger' : '')];
                    },
                ],
                [
                    'visible' => Settings::getMode($settings,'iks'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="ИКС" class="states-header">ИКС</div>',
                    'value' => function ($data) {
                        return Site::getIndex($data, 'iks');
                    },
                    'contentOptions' => function ($data) {
                        return ['class' => (Site::getIndex($data, 'status_iks') == 1 ? 'danger' : '')];
                    },
                ],
                [
                    'visible' => Settings::getMode($settings,'ip'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="IP" class="states-header">IP</div>',
                    'value' => function ($data) {
                        return '<div class="count">' . count(Site::getIp($data, 2))
                            . '</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . Site::getIp($data, 0) . '" class="states">'
                            . Site::getIp($data, 1) . '</div';
                    },
                    'format' => 'raw',
                    'contentOptions' => function ($data) {
                        return ['class' => (count(Site::getIp($data, 2)) > 1 ? 'warning' : '')];
                    },
                    'filter' => Html::activeTextInput($searchModel, 'ip', ['class' => 'form-control']),
                ],
                [
                    'visible' => Settings::getMode($settings,'dns'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="DNS" class="states-header">DNS</div>',
                    'value' => function ($data) {
                        return '<div class="count">' . count(Site::getDnsServer($data, 2))
                            . '</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . Site::getDnsServer($data, 0) . '" class="states">'
                            . Site::getDnsServer($data, 1) . '</div';
                    },
                    'format' => 'raw',
                    'contentOptions' => function ($data) {
                        return ['class' => (count(Site::getDnsServer($data, 2)) > 1 ? 'warning' : '')];
                    },
                    'filter' => Html::activeTextInput($searchModel, 'dns', ['class' => 'form-control']),
                ],
                [
                    'visible' => Settings::getMode($settings,'acceptor'),
                    'attribute' => '',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Акцептор" class="states-header">Акцептор</div>',
                    'format' => 'raw',
                    'value' => function ($data) {
                        $arr = Site::getAcceptor($data, 2);
                        $links = array();
                        for($i = 0; $i < count($arr); $i++)
                            array_push($links, Html::a($arr[$i], 'http://' . $arr[$i], ['target' => '_blank']));
                        $str = implode("<br>", $links);
                        return
                            '<div class="count">'
                                . count(Site::getAcceptor($data, 2))
                            . '</div>'
                            .'<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . Site::getAcceptor($data, 0)
                            . '" class="states">'
                                . $str
                            . '</div>';
                    },
                    'filter' => Html::activeTextInput($searchModel,'external_links', ['class' => 'form-control']),
                    'contentOptions' => function ($data) {
                        return ['class' => (count(Site::getAcceptor($data, 2)) > 1 ? 'warning' : '')];
                    },
                ],
                [
                    'visible' => Settings::getMode($settings,'anchor'),
                    'attribute' => 'Анкор',
                    'header' => '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" 
                        title="Анкор" class="states-header">Анкор</div>',
                    'format' => 'raw',
                    'value' => function ($data) {
                        $arr = Site::getAnchor($data, 2);
                        $links = array();
                        for($i = 0; $i < count($arr); $i++)
                            array_push($links, Html::a($arr[$i], 'https://www.google.ru/search?q=' . $arr[$i], ['target' => '_blank']));
                        $str = implode("<br>", $links);
                        return
                            '<div class="count">'
                            . count(Site::getAnchor($data, 2))
                            . '</div>'
                            .'<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                            . Site::getAnchor($data, 0)
                            . '" class="states">'
                            . $str
                            . '</div>';
                    },
                    'contentOptions' => function ($data) {
                        return ['class' => (count(Site::getAcceptor($data, 2)) > 1 ? 'warning' : '')];
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

<script src="https://code.highcharts.com/highcharts.js"></script>
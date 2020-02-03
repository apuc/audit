<?php

use bluezed\floatThead\FloatThead;
use common\classes\SizerGridView;
use common\models\Links;
use frontend\modules\settings\models\Settings;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use \frontend\modules\site\models\Site;
use \common\models\Comments;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use kartik\select2\Select2;

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
        <?php
        echo '<div class="sticky">';
        echo Html::button('Проверить индексацию', ['class' => 'btn btn-primary indexing']) . '&nbsp';
        echo Html::button('Провести аудит', ['class' => 'btn btn-primary audit']) . '&nbsp';
        $links = new \common\models\Links();
        $array = ArrayHelper::map(Links::find()->all(), 'name', 'name');
        $array['cache'] = 'Кэш Google';
        echo Html::activeDropDownList($links, 'id', $array, ['onchange' => 'redirect(this, this.value);', 'prompt' => 'Выберите ссылку', 'class' => 'btn btn-primary']);

        FloatThead::widget([
            'tableId' => 'mainTable',
            'options' => [
                'top' => 33,
                'zIndex' => 1,
                'position' => 'absolute'
            ]
        ]);
        echo '</div>';

        Pjax::begin(['id' => 'reload']);
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => [
                'id' => 'mainTable',
                'class' => 'table table-striped table-bordered',
            ],
            'id' => 'grid',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'delete' => function ($data) {
                            return Html::a("<span class='glyphicon glyphicon-trash' aria-hidden='true'></span>", ['/domain/site/customdelete', 'id' => $data]);},
                    ],
                ],
                ['class' => 'yii\grid\CheckboxColumn'],
                [
                    'visible' => Settings::getMode($settings, 'icon'),
                    'format' => 'raw',
                    'value' => function ($data) { return Site::getIconOutput($data); },
                ],
                [
                    'visible' => Settings::getMode($settings,'screenshot'),
                    'format' => 'raw',
                    'value' => function ($data) { return Site::getScreenshotOutput($data); },
                ],
                [
                    'visible' => Settings::getMode($settings,'chart'),
                    'format' => 'raw',
                    'header' => Site::getHeader('<span class="glyphicon glyphicon-signal" aria-hidden="true"></span>', 'График'),
                    'value' => function ($data) { return Site::getChartOutput($data); },
                ],
                [
                    'visible' => Settings::getMode($settings,'domain'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Домен'),
                    'filter' => Html::activeTextInput($searchModel, 'name', ['class' => 'form-control']),
                    'value' => function ($data) {
                        return Html::a('<div id="domain-'.$data->name.'">' . $data->name . '</div>', 'http://' . $data->name, ['target' => '_blank', 'id' => 'domain']); },
                ],
                [
                    'visible' => Settings::getMode($settings,'redirect'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Редирект'),
                    'filter' => Html::activeTextInput($searchModel, 'redirect', ['class' => 'form-control']),
                    'value' => function ($data) { return Html::a($data->redirect, 'http://' . $data->redirect, ['target' => '_blank']); },
                ],
                [
                    'visible' => Settings::getMode($settings,'title'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Тайтл'),
                    'filter' => Html::activeTextInput($searchModel, 'title', ['class' => 'form-control']),
                    'value' => function ($data) {
                        return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="' . $data->title . '" class="states">' . $data->title . '</div'; },
                ],
                [
                    'visible' => Settings::getMode($settings,'theme'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Тема'),
                    'filter' => Html::activeTextInput($searchModel, 'theme', ['class' => 'form-control']),
                    'value' => function ($data) { return Site::getSitesTheme($data); },
                ],
                [
                    'visible' => Settings::getMode($settings,'comment'),
                    'format' => 'raw',
                    'header' => Site::getHeader('<span class="glyphicon glyphicon-comment" aria-hidden="true"></span>', 'Комментарий'),
                    'filter' => Html::activeTextInput($searchModel, 'comment', ['class' => 'form-control']),
                    'value' => function ($data) { return Site::getComment($data); },
                ],
                [
                    'visible' => Settings::getMode($settings,'server_response_code'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Код', 'Код ответа сервера'),
                    'filter' => Html::activeTextInput($searchModel, 'server_response_code', ['class' => 'form-control']),
                    'value' => function ($data) { return Site::getAudit($data, 'server_response_code'); },
                ],
                [
                    'visible' => Settings::getMode($settings,'size'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Байт', 'Размер (байт)'),
                    'filter' => Html::activeTextInput($searchModel, 'size', ['class' => 'form-control']),
                    'value' => function ($data) { return Site::getAudit($data, 'size'); },
                ],
                [
                    'visible' => Settings::getMode($settings,'loading_time'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Мс', 'Время загрузки (мс)'),
                    'filter' => Html::activeTextInput($searchModel, 'loading_time', ['class' => 'form-control']),
                    'value' => function ($data) { return Site::getAudit($data, 'loading_time'); },
                ],
                [
                    'visible' => Settings::getMode($settings,'registrar'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Регистратор'),
                    'filter' => Html::activeTextInput($searchModel, 'registrar', ['class' => 'form-control']),
                    'value' => function ($data) { return Site::getItemOutput(Site::getRegistrar($data,0), Site::getRegistrar($data,1)); },
                ],
                [
                    'visible' => Settings::getMode($settings,'states'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Состояния'),
                    'filter' => Html::activeTextInput($searchModel, 'states', ['class' => 'form-control']),
                    'contentOptions' => function ($data) { return ['class' => (stristr(Site::getStates($data, 1), 'UNVERIFIED') ? 'danger' : '')]; },
                    'value' => function ($data) { return Site::getItemOutput(Site::getStates($data,0), Site::getStates($data,1), Site::getStates($data,2)); },
                ],
                [
                    'visible' => Settings::getMode($settings,'created_at'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Создан', 'Дата создания'),
                    'value' => function ($data) { return Site::getDate($data->creation_date); },
                ],
                [
                    'visible' => Settings::getMode($settings,'domain_age'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Лет', 'Возраст домена (лет)'),
                    'value' => function ($data) { return Site::getDomainsAge($data->creation_date); },
                ],
                [
                    'visible' => Settings::getMode($settings,'days_left'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Дни', 'Дней до окончания регистрации'),
                    'contentOptions' => function ($data) { return ['class' => (Site::getDaysLeft($data->expiration_date) < 30 ? 'danger' : '')]; },
                    'value' => function ($data) { return Site::getDaysLeft($data->expiration_date); },
                ],
                [
                    'visible' => Settings::getMode($settings,'google_indexing'),
                    'format' => 'raw',
                    'header' => Site::getHeader(Html::tag('img', null, ['src' => Url::to('@web/img/google.jpg'), 'width' => '16px']), 'Индексация главной страницы в Google'),
                    'filter' => Html::activeTextInput($searchModel, 'google_indexing', ['class' => 'form-control']),
                    'contentOptions' => function ($data) { return ['class' => (Site::getIndex($data, 'status_google') == 1 ? 'danger' : '')]; },
                    'value' => function ($data) { return Site::getIndex($data, 'google_indexing'); },
                ],
                [
                    'visible' => Settings::getMode($settings,'google_pages'),
                    'format' => 'raw',
                    'header' => Site::getHeader('N', 'Количество проиндексированных страниц'),
                    'filter' => Html::activeTextInput($searchModel, 'google_indexed_pages', ['class' => 'form-control']),
                    'contentOptions' => function ($data) { return ['class' => (Site::getIndex($data, 'status_indexing_pages') == 1 ? 'danger' : '')]; },
                    'value' => function ($data) { return Site::getGoogleLinks($data); },
                ],
                [
                    'visible' => Settings::getMode($settings,'google_date_cache'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Дата&nbspкэша', 'Дата кэша'),
                    'contentOptions' => function ($data) { return ['class' => (Site::getIndex($data, 'status_date_cache') == 1 ? 'danger' : '')]; },
                    'value' => function ($data) { return Site::formatDate($data, 'date_cache'); },
                ],
                [
                    'visible' => Settings::getMode($settings,'yandex_indexing'),
                    'format' => 'raw',
                    'header' => Site::getHeader(Html::tag('img', null, ['src' => Url::to('@web/img/yandex.jpg'), 'width' => '16px']), 'Индексация главной страницы в Yandex'),
                    'filter' => Html::activeTextInput($searchModel, 'yandex_indexing', ['class' => 'form-control']),
                    'contentOptions' => function ($data) { return ['class' => (Site::getIndex($data, 'status_yandex') == 1 ? 'danger' : '')]; },
                    'value' => function ($data) { return Site::getIndex($data, 'yandex_indexing'); },
                ],
                [
                    'visible' => Settings::getMode($settings,'iks'),
                    'format' => 'raw',
                    'header' => Site::getHeader('ИКС'),
                    'filter' => Html::activeTextInput($searchModel, 'iks', ['class' => 'form-control']),
                    'contentOptions' => function ($data) { return ['class' => (Site::getIndex($data, 'status_iks') == 1 ? 'danger' : '')]; },
                    'value' => function ($data) { return Site::getIndex($data, 'iks'); },
                ],
                [
                    'visible' => Settings::getMode($settings,'ip'),
                    'format' => 'raw',
                    'header' => Site::getHeader('IP'),
                    'filter' => Html::activeTextInput($searchModel, 'ip', ['class' => 'form-control']),
                    'contentOptions' => function ($data) { return ['class' => (count(Site::getIp($data, 2)) > 1 ? 'warning' : '')]; },
                    'value' => function ($data) { return Site::getItemOutput(Site::getIp($data,0), Site::getIp($data,1), Site::getIp($data,2)); },
                ],
                [
                    'visible' => Settings::getMode($settings,'dns'),
                    'format' => 'raw',
                    'header' => Site::getHeader('DNS'),
                    'filter' => Html::activeTextInput($searchModel, 'dns', ['class' => 'form-control']),
                    'contentOptions' => function ($data) { return ['class' => (count(Site::getDnsServer($data, 2)) > 1 ? 'warning' : '')]; },
                    'value' => function ($data) { return Site::getItemOutput(Site::getDnsServer($data,0), Site::getDnsServer($data,1), Site::getDnsServer($data,2)); },
                ],
                [
                    'visible' => Settings::getMode($settings,'acceptor'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Акцептор'),
                    'filter' => Html::activeTextInput($searchModel,'external_links', ['class' => 'form-control']),
                    'contentOptions' => function ($data) { return ['class' => (count(Site::getAcceptor($data, 2)) > 1 ? 'warning' : '')]; },
                    'value' => function ($data) { return Site::formatAcceptor($data); },
                ],
                [
                    'visible' => Settings::getMode($settings,'anchor'),
                    'format' => 'raw',
                    'header' => Site::getHeader('Анкор'),
                    'filter' => Html::activeTextInput($searchModel, 'anchor', ['class' => 'form-control']),
                    'contentOptions' => function ($data) { return ['class' => (count(Site::getAnchor($data, 2)) > 1 ? 'warning' : '')]; },
                    'value' => function ($data) { return Site::formatAnchor($data); },
                ],
            ],
        ]);
        Pjax::end();
        ?>
    </div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-site-id="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Комментарий <span id="site-name-comment"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                    <?= Html::button('Сохранить', ['class' => 'btn btn-success', 'id' => 'commentAjaxButton', 'data-dismiss' => "modal"]) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTheme" tabindex="-1" role="dialog" aria-labelledby="modalThemeLabel" aria-hidden="true" data-site-id="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <?php $form = ActiveForm::begin();
                $model = new \common\models\SiteThemes(); ?>
                <?php
                echo $form->field($model, 'theme_id')->widget(Select2::class, [
                        'data' => \yii\helpers\ArrayHelper::map(\common\models\Theme::find()->all(), 'id', 'name'),
                        'options' => ['placeholder' => '...', 'class' => 'form-control', 'id' => 'theme_ids', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label('Темы'); ?>

                <div class="form-group">
                    <?= Html::button('Сохранить', ['class' => 'btn btn-success', 'id' => 'modalThemeButton', 'data-dismiss' => "modal"]) ?>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Отмена</button>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="linksModal" tabindex="-1" role="dialog" aria-labelledby="linksModalLabel" aria-hidden="true" data-site-id="">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="linksModalLabel">Внешние ссылки <span id="site-name"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="acceptorModal"></div>
                <div id="anchorModal"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
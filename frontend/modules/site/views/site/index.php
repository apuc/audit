<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \frontend\modules\site\models\Site;
use \common\models\Theme;
use \common\models\Comments;
use \common\models\User;
use dosamigos\editable\Editable;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $form yii\bootstrap\ActiveForm */
/* @var $theme */

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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
//         "id" => "grid",
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
                'class' => 'yii\grid\CheckboxColumn',
//                'checkboxOptions' => function ($model, $key, $index, $column) {
//                    return ['value' => $model->id];
//                }
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
            [
                'attribute' => 'Регистратор',
                'value' => function ($data) {
                    return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                        . Site::getRegistrar($data->id, 0)
                        . '" class="custom-grid-view">' . Site::getRegistrar($data->id, 1) . '</div';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'Состояния',
                'value' => function ($data) {
                    return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                        . Site::getStates($data->id, 0)
                        . '" class="custom-grid-view">' . Site::getStates($data->id, 1) . '</div';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'Дата создания',
                'value' => function ($data) {
                    return Site::getDate($data->id, 'creation_date');
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'Дней до окончания регистрации',
                'value' => function ($data) {
                    return Site::getDaysLeft($data->id);
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
                    return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                        . Site::getIp($data->id, 0)
                        . '" class="custom-grid-view">' . Site::getIp($data->id, 1) . '</div';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'DNS',
                'value' => function ($data) {
                    return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                        . Site::getDnsServer($data->id, 0)
                        . '" class="custom-grid-view">' . Site::getDnsServer($data->id, 1) . '</div';
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
                'format' => 'raw',
                'value' => function ($data) {
                    return  '<a type="button" data-toggle="modal" data-target="#exampleModal" data-id="'.
                        $data->id.'" class="comment" title="Добавить комментарий"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>'
                        . "<br>" .
                        Html::a("<span class=\"glyphicon glyphicon-eye-open\" aria-hidden=\"true\"></span>",
                            ['/comments/comments/?CommentsSearch[site_id]='.$data->id],
                            ['title' => 'Посмотреть комментарии к сайту']
                        );
                },
            ],
            [
                'attribute' => 'Акцептор',
                'format' => 'raw',
                'value' => function ($data) {
                    return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'.Site::getAcceptor($data->id, 0).'" class="custom-grid-view">' . Site::getAcceptor($data->id, 1) . '</div>';
                },
                'filter' => Html::activeTextInput(
                    $searchModel,
                    'external_links',
                    ['class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'Анкор',
                'format' => 'raw',
                'value' => function ($data) {
                    return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'.Site::getAnchor($data->id, 0).'" class="custom-grid-view">' . Site::getAnchor($data->id, 1) . '</div>';
                },
            ],
        ],
//        'tableOptions' =>['style' => 'width: 100%;'],
    ]); ?>

</div>
<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-site-id="">
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
                $model = new Comments();?>

                <?= $form->field($model, 'destination_id')->dropDownList(
                    \yii\helpers\ArrayHelper::map(common\models\User::find()->all(), 'id', 'username'),
                    ['prompt' => '...']
                ) ?>

                <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

                <div class="form-group">
                    <?= Html::button('Сохранить', ['class' => 'btn btn-success', 'id' => 'commentAjax', 'data-dismiss' => "modal"]) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$('.indexing').on('click', function(){
    let keys = $('#w0').yiiGridView('getSelectedRows');
        $.ajax({
            url: '/api/api/indexing',
            type: 'POST',
            data: {
                keys:keys
            },
            success: function(res){
                console.log(res);
            },
            error: function(){
                alert('Error!');
            }
        });
    });

 $('.comment').on('click', function(){
     let site_id = $(this).data("id");
     $("#exampleModal").attr("data-site-id", site_id);
 });

    $('#commentAjax').on('click', function(){
        let comment = document.getElementById('comments-comment').value;
        let destination_id = document.getElementById('comments-destination_id').value;
        let site_id = document.getElementById('exampleModal').getAttribute("data-site-id");
        $.ajax({
            url: '/api/api/comment',
            type: 'POST',
            data: {
                comment:comment,
                destination_id:destination_id,
                site_id:site_id
            },
            success: function(res){
                console.log(res);
            },
            error: function(){
                alert('Error!');
            }
        });
    });
JS;

$this->registerJs($js);
?>
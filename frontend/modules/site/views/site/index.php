<?php

use common\models\Links;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use \frontend\modules\site\models\Site;
use \common\models\Theme;
use \common\models\Comments;
use dosamigos\editable\Editable;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use \common\classes\Debug;

/* @var $form yii\bootstrap\ActiveForm */
/* @var $theme */

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\site\models\SiteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

/* @var $model common\models\Comments */

$this->title = 'Сайты';
$this->params['breadcrumbs'][] = $this->title;

$dataProvider->pagination->pageSize=15;

?>

<div class="site-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= Html::button('Проверить индексацию', ['class' => 'btn btn-primary indexing']) ?>
    <?= Html::button('Провести аудит', ['class' => 'btn btn-primary audit']) ?>

    <?php
    Pjax::begin(['id' => 'sitePjax']);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'grid',

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
            ['class' => 'yii\grid\CheckboxColumn'],
            [
                'attribute' => '',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::tag('img', null, ['src' => Url::to('/icons/' . Site::getAudit($data, 'icon')), 'width' => '16px']);
                }
            ],
            [
                'attribute' => '',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::tag('img', null, ['src' => Url::to('@web/screenshots/' . Site::getAudit($data, 'screenshot')), 'width' => '32px',
                        'class' => 'my-img',
                    ]);
                }
            ],
            [
                'attribute' => 'name',
                'value' => function ($data) {
                    return Html::a($data->name, 'http://' .  $data->name, ['target' => '_blank', 'id' => 'domain']);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'Тайтл',
                'value' => function ($data) {
                    return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                        . $data->title . '" class="states">'
                        . $data->title . '</div';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'Код ответа сервера',
                'value' => function ($data) {
                    return Site::getAudit($data, 'server_response_code');
                },
                'filter' => Html::activeTextInput(
                    $searchModel,
                    'server_response_code',
                    ['class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'Размер (байт)',
                'value' => function ($data) {
                    return Site::getAudit($data, 'size');
                },
            ],
            [
                'attribute' => 'Время загрузки (мс)',
                'value' => function ($data) {
                    return Site::getAudit($data, 'loading_time');
                },
            ],
            [
                'attribute' => 'Ссылки',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::activeDropDownList($data,'id', ArrayHelper::map(Links::find()->all(), 'name', 'name'),
                            ['class' => '.dropdownlist', 'onchange' => 'jsFunction(this.value);', 'prompt' => '...'])
                        . '<div class="innerHtml"></div>';
                },
//             'value' => function ($data) {
//                    return Html::activeDropDownList($data,'id', ArrayHelper::map(Site::getLink($data), 'id', 'name'),
//                            ['class' => 'btn btn-secondary dropdown-toggle']);
//                },
            ],
            [
                'attribute' => 'Регистратор',
                'value' => function ($data) {
                    return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                        . Site::getRegistrar($data, 0) . '" class="states">'
                        . Site::getRegistrar($data, 1) . '</div';
                },
                'format' => 'raw',
                'filter' => Html::activeTextInput(
                    $searchModel,
                    'registrar',
                    ['class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'Состояния',
                'value' => function ($data) {
                    return '<div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                        . Site::getStates($data, 0) . '" class="states">'
                        . Site::getStates($data, 1) . '</div';
                },
                'format' => 'raw',
                'contentOptions' => function ($data) {
                    return ['class' => (stristr(Site::getStates($data, 1), 'UNVERIFIED') ? 'danger' : '')];
                },
                'filter' => Html::activeTextInput(
                    $searchModel,
                    'states',
                    ['class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'Дата создания',
                'value' => function ($data) {
                    return Site::getDate($data->creation_date);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'Дней до окончания регистрации',
                'value' => function ($data) {
                    return Site::getDaysLeft($data->expiration_date);
                },
                'format' => 'raw',
                'contentOptions' => function ($data) {
                    return ['class' => (Site::getDaysLeft($data->expiration_date) < 30 ? 'danger' : '')];
                },
            ],
            [
                'attribute' => 'theme.name',
                'value' => function ($data) {

                    if(!$data->theme) {
                        $value = '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span><br>';
                    } else {
                        $value = $data->theme->name;
                    }

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
                    return '<div class="count">'.count(Site::getIp($data, 2)).'</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                        . Site::getIp($data, 0) . '" class="states">'
                        . Site::getIp($data, 1) . '</div';
                },
                'format' => 'raw',
                'contentOptions' => function ($data) {
                    return ['class' => (count(Site::getIp($data, 2)) > 2 ? 'warning' : '')];
                },
                'filter' => Html::activeTextInput(
                    $searchModel,
                    'ip',
                    ['class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'DNS',
                'value' => function ($data) {
                    return '<div class="count">'.count(Site::getDnsServer($data, 2)).'</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                        . Site::getDnsServer($data, 0) . '" class="states">'
                        . Site::getDnsServer($data, 1) . '</div';
                },
                'format' => 'raw',
                'contentOptions' => function ($data) {
                    return ['class' => (count(Site::getDnsServer($data, 2)) > 2 ? 'warning' : '')];
                },
                'filter' => Html::activeTextInput(
                    $searchModel,
                    'dns',
                    ['class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'Индексация Google',
                'value' => function ($data) {
                    return Site::getAudit($data, 'google_indexing');
                },
            ],
            [
                'attribute' => 'Индексация Яндекс',
                'value' => function ($data) {
                    return Site::getAudit($data, 'yandex_indexing');
                },
            ],
            [
                'attribute' => 'Комментарий',
                'format' => 'raw',
                'value' => function ($data) {
                    return  '<a type="button" data-toggle="modal" data-target="#exampleModal" data-id="'. $data->id
                        .'" class="comment" title="Добавить комментарий"><span class="glyphicon glyphicon-pencil" 
                            aria-hidden="true"></span></a>' . "<br>" .
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
                    return '<div class="count">'.count(Site::getAcceptor($data, 2)).'</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                        . Site::getAcceptor($data, 0)  . '" class="states">'
                        . Site::getAcceptor($data, 1) . '</div>';
                },
                'filter' => Html::activeTextInput(
                    $searchModel,
                    'external_links',
                    ['class' => 'form-control']
                ),
                'contentOptions' => function ($data) {
                    return ['class' => (count(Site::getAcceptor($data, 2)) > 2 ? 'warning' : '')];
                },
            ],
            [
                'attribute' => 'Анкор',
                'format' => 'raw',
                'value' => function ($data) {
                    return '<div class="count">'.count(Site::getAnchor($data, 2)).'</div><div type="button" data-toggle="tooltip" data-placement="top" data-html="true" title="'
                        . Site::getAnchor($data, 0) . '" class="states">'
                        . Site::getAnchor($data, 1) . '</div>';
                },
                'contentOptions' => function ($data) {
                    return ['class' => (count(Site::getAcceptor($data, 2)) > 2 ? 'warning' : '')];
                },
                'filter' => Html::activeTextInput(
                    $searchModel,
                    'anchor',
                    ['class' => 'form-control']
                ),
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
                $model = new Comments();?>

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

<?php
$js = <<<JS
$(document).ready(function() {
	$(".my-img").click(function() {
	  	let img = $(this);
		let src = img.attr('src');
		$("body").append("<div class='popup'>"+
						 "<div class='popup_bg'></div>"+
						 "<img src='"+src+"' class='popup_img' />"+
						 "</div>"); 
		$(".popup").fadeIn(100);
		$(".popup_bg").click(function() {
			$(".popup").fadeOut(100);
			setTimeout(function() {
			  $(".popup").remove();
			}, 100);
		});
	});
	
});

$('.indexing').on('click', function(){
    let keys = $('#grid').yiiGridView('getSelectedRows');
        $.ajax({
            url: '/api/api/indexing',
            type: 'POST',
            data: {
                keys:keys
            },
            success: function(res){
                $.pjax.reload({container:"#sitePjax"});
            },
            error: function(){
                alert('Error!');
            }
        });
    

$('.audit').on('click', function(){
    let keys = $('#grid').yiiGridView('getSelectedRows');
        $.ajax({
            url: '/api/api/audit',
            type: 'POST',
            data: {
                keys:keys
            },
            success: function(res){
                $.pjax.reload({container:"#sitePjax"});
                console.log(res);
                alert('Аудит проведен');
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
});
    
JS;
$this->registerJs($js);
?>
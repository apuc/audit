<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= Html::button('Выдать доступ', ['class' => 'btn btn-primary access']) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'grid',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['class' => 'yii\grid\CheckboxColumn'],

            'username',
            'email:email',
            'status',
            'created_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>

<?php
$js = <<<JS
$('.access').on('click', function(){
    let keys = $('#grid').yiiGridView('getSelectedRows');
        $.ajax({
            url: '/api/api/access',
            type: 'POST',
            data: {
                keys:keys
            },
            success: function(res) {
                alert('Перезагрузите страницу, чтобы увидеть изменения.');
            },
            error: function() {
                alert('Error!');
            }
        });
    });
JS;
$this->registerJs($js);
?>
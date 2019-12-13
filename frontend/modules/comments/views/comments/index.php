<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\comments\models\CommentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Комментарии';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comments-index">

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Сайты', ['/domain/site/'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'site.name',
            [
                'attribute' => 'Кто оставил комментарий',
                'value' => 'owner.username',
            ],
            [
                'attribute' => 'Для кого комментарий',
                'value' => 'destination.username',
            ],
            'comment',
            'created_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

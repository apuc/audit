<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\url\models\UrlSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Urls';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="url-index">

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
                'attribute' => 'url',
                'value' => function ($data) {
                    return Html::a(
                        $data->url,
                        Url::to(['/audit/audit?AuditSearch[url]=' . $data->url])
                    );
                },
                'format' => 'raw',
            ],
            'site.name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

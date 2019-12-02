<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use \frontend\modules\site\models\Site;

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
                'attribute' => 'name',
                'value' => function ($data) {
                    return Html::a(
                        $data->name,
                        Url::to(['/audit/audit?AuditSearch[url]=' .  Site::getUrlName($data->id)])
                    );
                },
                'format' => 'raw',
            ],
            'registrar',
            'states',
            'creation_date:datetime',
            'expiration_date:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Site */

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Сайты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

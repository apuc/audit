<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ExternalLinks */

$this->title = 'Изменить';
$this->params['breadcrumbs'][] = ['label' => 'Внешние ссылки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="external-links-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

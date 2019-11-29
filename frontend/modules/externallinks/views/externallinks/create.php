<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ExternalLinks */

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Внешние ссылки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-links-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

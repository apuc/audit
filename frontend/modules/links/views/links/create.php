<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Links */

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Ссылки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="links-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

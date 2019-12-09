<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Theme */

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Темы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="theme-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

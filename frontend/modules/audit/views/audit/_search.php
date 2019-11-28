<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\audit\models\AuditSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="audit-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'server_response_code') ?>

    <?= $form->field($model, 'size') ?>

    <?= $form->field($model, 'loading_time') ?>

    <?= $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'url_id') ?>

    <?php // echo $form->field($model, 'google_indexing')->checkbox() ?>

    <?php // echo $form->field($model, 'yandex_indexing')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сброс', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Indexing */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="indexing-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'google_indexing')->checkbox() ?>

    <?= $form->field($model, 'google_indexed_pages')->textInput() ?>

    <?= $form->field($model, 'date_cache')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'yandex_indexing')->checkbox() ?>

    <?= $form->field($model, 'site_id')->textInput() ?>

    <?= $form->field($model, 'iks')->textInput() ?>

    <?= $form->field($model, 'status_google')->textInput() ?>

    <?= $form->field($model, 'status_yandex')->textInput() ?>

    <?= $form->field($model, 'status_date_cache')->textInput() ?>

    <?= $form->field($model, 'status_indexing_pages')->textInput() ?>

    <?= $form->field($model, 'status_iks')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

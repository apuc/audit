<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ExternalLinks */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="external-links-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'acceptor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'anchor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'audit_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

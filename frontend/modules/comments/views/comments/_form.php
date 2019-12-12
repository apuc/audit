<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Comments */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comments-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'site_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(common\models\Site::find()->all(), 'id', 'name'),
        ['prompt' => '...']
    ) ?>

    <?= $form->field($model, 'owner_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(common\models\User::find()->all(), 'id', 'username'),
        ['prompt' => '...']
    ) ?>

    <?= $form->field($model, 'destination_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(common\models\User::find()->all(), 'id', 'username'),
        ['prompt' => '...']
    ) ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

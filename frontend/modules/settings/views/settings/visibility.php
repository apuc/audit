<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Settings */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="settings-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $items = [
        '0' => 'Выкл',
        '1' => 'Вкл',
    ];

    echo $form->field($model, 'icon')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'screenshot')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'chart')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'domain')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'redirect')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'title')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'theme')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'comment')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'server_response_code')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'size')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'loading_time')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'registrar')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'states')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'created_at')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'days_left')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'google_indexing')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'google_pages')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'google_date_cache')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'yandex_indexing')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'iks')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'ip')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'dns')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'acceptor')->dropDownList($items, ['class' => 'custom-drop-down']);
    echo $form->field($model, 'anchor')->dropDownList($items, ['class' => 'custom-drop-down']);
    ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

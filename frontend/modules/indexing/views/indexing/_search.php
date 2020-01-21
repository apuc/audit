<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\indexing\models\IndexingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="indexing-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'google_indexing')->checkbox() ?>

    <?= $form->field($model, 'google_indexed_pages') ?>

    <?= $form->field($model, 'date_cache') ?>

    <?= $form->field($model, 'yandex_indexing')->checkbox() ?>

    <?php // echo $form->field($model, 'site_id') ?>

    <?php // echo $form->field($model, 'iks') ?>

    <?php // echo $form->field($model, 'status_google') ?>

    <?php // echo $form->field($model, 'status_yandex') ?>

    <?php // echo $form->field($model, 'status_date_cache') ?>

    <?php // echo $form->field($model, 'status_indexing_pages') ?>

    <?php // echo $form->field($model, 'status_iks') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

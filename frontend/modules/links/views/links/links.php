<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\modules\links\models\LinksForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Добавить ссылки';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-contact">

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'urls-form']); ?>

            <p>Введите ссылки. Каждая ссылка с новой строки или через запятую.</p>
            <?= $form->field($model, 'links')->textarea(['rows' => 20]) ?>

            <div class="form-group">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'urls-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

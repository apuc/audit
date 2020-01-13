<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\modules\url\models\UrlForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Добавить сайты';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-contact">

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'urls-form']); ?>

            <p>Введите URL сайтов. Каждый URL с новой строки или через запятую.</p>
            <?= $form->field($model, 'urls')->textarea(['rows' => 20]) ?>

            <div class="form-group">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'urls-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

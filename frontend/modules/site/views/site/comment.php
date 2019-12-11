<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\modules\site\models\CommentForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\bootstrap\Modal;

$this->title = 'Коментарий';
$this->params['breadcrumbs'][] = $this->title;

Modal::begin([
    'header' => '<h2>Hello world</h2>',
    'toggleButton' => ['label' => 'click me'],
    'footer' => 'Низ окна',
]);

echo 'Say hello...';

Modal::end();
?>
<div class="site-contact">

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'comment-form']); ?>

            <?= $form->field($model, 'comment')->textarea(['rows' => 20]) ?>

            <div class="form-group">
                <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'comment-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
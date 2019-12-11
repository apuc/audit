<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\modules\site\models\ThemeForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Тема';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'theme-form']); ?>

            <?= $form->field($model, 'theme_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(common\models\Theme::find()->all(), 'id', 'name'),
                ['prompt' => '...']
            ) ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'theme-button']) ?>
                <?= Html::a('Добавить новую тему', ['/theme/theme/create'], ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\url\models\Url */

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Urls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="url-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

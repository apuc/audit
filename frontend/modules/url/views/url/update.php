<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\url\models\Url */

$this->title = 'Изменить';
$this->params['breadcrumbs'][] = ['label' => 'Urls', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="url-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

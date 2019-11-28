<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Dns */

$this->title = 'Изменить';
$this->params['breadcrumbs'][] = ['label' => 'Dns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="dns-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Audit */

$this->title = 'Изменить';
$this->params['breadcrumbs'][] = ['label' => 'Аудит', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="audit-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

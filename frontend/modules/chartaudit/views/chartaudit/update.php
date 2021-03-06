<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ChartAuditQueue */

$this->title = 'Update Chart Audit Queue: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Chart Audit Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="chart-audit-queue-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

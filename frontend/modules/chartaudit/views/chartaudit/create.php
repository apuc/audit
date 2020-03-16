<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ChartAuditQueue */

$this->title = 'Create Chart Audit Queue';
$this->params['breadcrumbs'][] = ['label' => 'Chart Audit Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chart-audit-queue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

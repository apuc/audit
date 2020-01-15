<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AuditPending */

$this->title = 'Create Audit Pending';
$this->params['breadcrumbs'][] = ['label' => 'Audit Pendings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audit-pending-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

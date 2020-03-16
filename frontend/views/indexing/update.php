<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexingPending */

$this->title = 'Update Indexing Pending: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Indexing Pendings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="indexing-pending-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

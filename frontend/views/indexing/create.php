<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexingPending */

$this->title = 'Create Indexing Pending';
$this->params['breadcrumbs'][] = ['label' => 'Indexing Pendings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="indexing-pending-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

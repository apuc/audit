<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Indexing */

$this->title = 'Create Indexing';
$this->params['breadcrumbs'][] = ['label' => 'Indexings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="indexing-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

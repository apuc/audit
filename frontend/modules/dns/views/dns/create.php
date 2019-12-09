<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Dns */

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Dns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dns-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

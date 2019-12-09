<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Audit */

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Аудит', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audit-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

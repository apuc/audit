<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Settings */

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="settings-view">
    <p>
        <?= Html::a('Изменить количество', ['update2', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <div class="settings-view">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
               'sizer'
            ]
        ]) ?>

    <p>
        <?= Html::a('Изменить задержку', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <div class="settings-view">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                    'audit_delay',
                    'indexing_delay',
                    'chart_audit_delay'
            ]
        ]) ?>

        <p>
            <?= Html::a('Изменить отображение', ['update1', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'icon',
                'screenshot',
                'chart',
                'domain',
                'redirect',
                'title',
                'theme',
                'comment',
                'server_response_code',
                'size',
                'loading_time',
                'registrar',
                'states',
                'created_at',
                'domain_age',
                'days_left',
                'google_indexing',
                'yandex_indexing',
                'google_pages',
                'google_date_cache',
                'iks',
                'ip',
                'dns',
                'acceptor',
                'anchor',
            ]
        ]) ?>
    </div>
</div>

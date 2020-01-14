<?php

use yii\grid\GridView;
use yii\data\ActiveDataProvider;

echo "<h3>Сайты в очереди на аудит</h3><br>";
$dataProvider = new ActiveDataProvider(['query' => \common\models\AuditPending::find()]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
]);

echo "<br><br><h3>Сайты в очереди на индексацию</h3><br>";
$dataProvider = new ActiveDataProvider(['query' => \common\models\IndexingPending::find()]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
]);

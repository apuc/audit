<?php


namespace console\controllers;


use common\models\ChartAuditQueue;
use common\models\Settings;
use common\models\Site;
use common\models\Url;
use common\services\AuditService;
use DateTime;
use yii\console\Controller;

class ChartauditController extends Controller
{
    public function actionRun()
    {
        $settings = Settings::findOne(1);
        $date = new DateTime();
        $date = $date->getTimestamp();

        if($date > $settings->chart_audit_time_available) {
            $queue = ChartAuditQueue::find()->limit(1)->all();
            if ($queue) {
                $date = new DateTime();
                date_add($date, date_interval_create_from_date_string($settings->audit_delay . ' minutes'));
                $date = $date->getTimestamp();
                $settings->chart_audit_time_available = $date;
                $settings->save();

                foreach ($queue as $value) {
                    $site = Site::findOne($value->site_id);
                    $url = Url::findOne(['site_id' => $value->site_id]);
                    AuditService::addChartAudit($site, $url->id, $value->id);
                }
            }
            return 0;
        }
    }
}
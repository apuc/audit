<?php


namespace console\controllers;

use common\models\ChartAuditQueue;
use common\models\Settings;
use common\models\Site;
use DateTime;
use yii\console\Controller;

class ChartdataController extends Controller
{
    public function actionRun()
    {
        $settings = Settings::findOne(1);
        $date = new DateTime();
        $date = $date->getTimestamp();

        if($date > $settings->available_audit_time_all) {
            $date = new DateTime();
            date_add($date, date_interval_create_from_date_string($settings->chart_audit_delay . ' minutes'));
            $date = $date->getTimestamp();
            $settings->available_audit_time_all = $date;
            $settings->save();

            $audit_pending = Site::find()->all();
            foreach ($audit_pending as $value) {
                $queue = new ChartAuditQueue();
                $queue->site_id = $value->id;
                $queue->save();
            }
        }
    }
}
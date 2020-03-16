<?php


namespace console\controllers;

use common\models\AuditPending;
use common\models\Settings;
use common\models\Site;
use common\models\Url;
use common\services\AuditService;
use DateTime;
use yii\console\Controller;

class AuditController extends Controller
{
    public function actionRun()
    {
        $settings = Settings::findOne(1);
        $date = new DateTime();
        $date = $date->getTimestamp();

        if($date > $settings->available_audit_time) {
            $audit_pending = AuditPending::find()->limit(1)->all();
            if ($audit_pending) {
                $date = new DateTime();
                date_add($date, date_interval_create_from_date_string($settings->audit_delay . ' minutes'));
                $date = $date->getTimestamp();
                $settings->available_audit_time = $date;
                $settings->save();

                foreach ($audit_pending as $value) {
                    $site = Site::findOne($value->site_id);
                    $url = Url::findOne(['site_id' => $value->site_id]);
                    AuditService::addAudit($site->name, $url->id, $value->id);
                }
            }
            return 0;
        }
    }

    public function actionDelete()
    {
        $queue = AuditPending::find()->all();
        foreach ($queue as $value)
            AuditPending::deleteAll(['id' => $value->id]);
    }
}
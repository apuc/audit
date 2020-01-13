<?php


namespace console\controllers;


use common\models\AuditPending;
use common\models\Site;
use common\models\Url;
use common\services\AuditService;
use yii\console\Controller;

class AuditController extends Controller
{
    public function actionRun()
    {
        $audit_pending = AuditPending::find()->limit(1)->all();
        if($audit_pending)
            foreach ($audit_pending as $value) {
                $site = Site::findOne($value->site_id);
                $url = Url::findOne(['site_id' => $value->site_id]);
                AuditService::addAudit($site->name, $url->id);
                AuditPending::deleteAll(['id' => $value->id]);
            }
        return 0;
    }
}
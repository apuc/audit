<?php


namespace console\controllers;


use common\classes\Debug;
use common\models\Indexing;
use common\models\IndexingPending;
use common\models\Search;
use common\models\Settings;
use common\models\Site;
use DateTime;
use yii\console\Controller;
use \common\classes\YandexIks;

class IndexingController extends Controller
{
    public function actionRun()
    {
        $settings = Settings::findOne(1);
        $date = new DateTime();
        $date = $date->getTimestamp();

        if($date > $settings->available_indexing_time) {
            $audit_pending = IndexingPending::find()->limit(1)->all();
            if($audit_pending) {
                $date = new DateTime();
                date_add($date, date_interval_create_from_date_string($settings->indexing_delay . ' minutes'));
                $date = $date->getTimestamp();
                $settings->available_indexing_time = $date;
                $settings->save();

                foreach ($audit_pending as $value) {
                    $site = Site::findOne($value->site_id);
                    $indexing = new Indexing();
                    $result = Search::check($site->name);
                    $old_indexing = Indexing::find()->where(['site_id' => $site->id])->orderBy('id desc')->limit(1)->all();
                    self::setData($result['ya'], $indexing, $old_indexing, 'yandex_indexing', 'status_yandex');
                    self::setData($result['google'], $indexing, $old_indexing, 'google_indexing', 'status_google');
                    self::setData(Search::getCount($site->name), $indexing, $old_indexing, 'google_indexed_pages', 'status_indexing_pages');
                    self::setData(Search::cache($site->name, 'date'), $indexing, $old_indexing, 'date_cache', 'status_date_cache');
                    self::setData(YandexIks::getValueFromImage($site->name), $indexing, $old_indexing, 'iks', 'status_iks');
                    $indexing->site_id = $site->id;
                    $indexing->save();
                    IndexingPending::deleteAll(['id' => $value->id]);
                }
            }
        }
    }

    public function setData($value, $indexing, $old_indexing, $key_field, $key_status)
    {
        if($value) {
            $indexing->$key_field = $value;
            $indexing->$key_status = 0;
        } else
            foreach ($old_indexing as $old)
                if($old->$key_field) {
                    $indexing->$key_field = $old->$key_field;
                    $indexing->$key_status = 1;
                    echo "Set old " . $key_field . " value\n";
                } else {
                    $indexing->$key_field = 0;
                    $indexing->$key_status = 0;
                }
    }
}
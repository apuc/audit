<?php


namespace console\controllers;


use common\classes\Debug;
use common\models\Indexing;
use common\models\IndexingPending;
use common\models\Search;
use common\models\Site;
use yii\console\Controller;
use \common\classes\YandexIks;

class IndexingController extends Controller
{
    public function actionRun()
    {
        $audit_pending = IndexingPending::find()->limit(1)->all();
        echo var_dump($audit_pending);
        if($audit_pending)
            foreach ($audit_pending as $value) {
                $site = Site::findOne($value->site_id);
                $indexing = new Indexing();
                $old_indexing = Indexing::find()->where(['site_id' => $site->id])->limit(1)->orderBy('id desc');
                $result = Search::check($site->name);

               try {
                    self::setData($result['ya'], $indexing, $old_indexing, 'yandex_indexing', 'status_yandex');
                    self::setData($result['google'], $indexing, $old_indexing, 'google_indexing', 'status_google');
                    self::setData(Search::getCount($site->name), $indexing, $old_indexing, 'google_indexed_pages', 'status_indexing_pages');
                    self::setData(Search::cache($site->name, 'date'), $indexing, $old_indexing, 'date_cache', 'status_date_cache');
                    self::setData(YandexIks::getValueFromImage($site->name), $indexing, $old_indexing, 'iks', 'status_iks');
                } catch (\Exception $e) {
                    $result['ya'] ? $indexing->yandex_indexing = 1 : false;
                    $result['google'] ? $indexing->google_indexing = 1 : false;
                    $indexing->google_indexed_pages = Search::getCount($site->name);
                    $indexing->date_cache = Search::cache($site->name, 'date');
                    $indexing->iks = YandexIks::getValueFromImage($site->name);
                }
                $indexing->site_id = $site->id;
                $indexing->save();
                IndexingPending::deleteAll(['id' => $value->id]);
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
                    echo "Set old" . $key_field . "value\n";
                } else {
                    $indexing->$key_field = false;
                    $indexing->$key_status = 0;
                }
    }
}
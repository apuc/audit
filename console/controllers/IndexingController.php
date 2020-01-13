<?php


namespace console\controllers;


use common\models\Indexing;
use common\models\IndexingPending;
use common\models\Search;
use common\models\Site;
use yii\console\Controller;

class IndexingController extends Controller
{
    public function actionRun()
    {
        $audit_pending = IndexingPending::find()->limit(1)->all();
        if($audit_pending)
            foreach ($audit_pending as $value) {
                $site = Site::findOne($value->site_id);
                $indexing = new Indexing();
                $result = Search::check($site->name);
                $result['ya'] ? $indexing->yandex_indexing = 1 : false;
                $result['google'] ? $indexing->google_indexing = 1 : false;
                $indexing->google_indexed_pages = Search::getCount($site->name);
                $indexing->date_cache = Search::cache($site->name, 'date');
                $indexing->site_id = $site->id;
                $indexing->save();

                IndexingPending::deleteAll(['id' => $value->id]);
            }
    }
}
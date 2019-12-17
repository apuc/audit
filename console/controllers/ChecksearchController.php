<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\Search;
use common\models\Audit;
use common\models\Url;

class ChecksearchController extends Controller
{
//    public $message;
//
//    public function options($actionID)
//    {
//        return ['message'];
//    }
//
//    public function optionAliases()
//    {
//        return ['m' => 'message'];
//    }

    public function actionCheck(){
        $audit = Audit::getNotCheckedAudit();
        if($audit){
            $url = Url::findOne($audit->url_id);

            $result = Search::check($url->url);
            $result['ya'] ? $audit->yandex_indexing = 1 : false;
            $result['google'] ? $audit->google_indexing = 1 : false;
            $audit->check_search = 1;
            $audit->save();

            $consoleYandexResult = $result['ya'] ? 'TRUE' : 'FALSE';
            $consoleGoogleResult = $result['google'] ? 'TRUE' : 'FALSE';
            $consoleResult = "Found url: {$url->url}\n-Yandex-indexing: {$consoleYandexResult}\n-Google-indexing: {$consoleGoogleResult}\n";
        }else{
            $consoleResult = "All urls audited.";
        }
        $this->stdout($consoleResult);
    }

    public function actionIndex()
    {
        echo "djfdjfdsjf";
    }
}
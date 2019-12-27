<?php


namespace frontend\modules\api\controllers;

use common\classes\Debug;
use common\models\Audit;
use common\models\Comments;
use common\models\Indexing;
use common\models\Search;
use common\models\Site;
use common\models\User;
use common\models\Url;
use common\services\AuditService;
use Yii;
use yii\web\Controller;


class ApiController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTheme()
    {
        if(Yii::$app->request->isAjax) {
            $data = str_replace('theme', "", Yii::$app->request->post());
            $pattern = "/^[^A-z]*/";

            $site_id_encoded = implode(preg_replace($pattern, "", $data));
            $theme_id = implode(str_replace($site_id_encoded, "", $data));
            $site_id_decoded = base64_decode($site_id_encoded);
            $site_id = str_replace(array("i:", ";"), "", $site_id_decoded);

            if (($site = Site::findOne($site_id)) !== null) {
                $site->theme_id = $theme_id;
                $site->save();
            }
        }
    }

    public function actionComment()
    {
        if(Yii::$app->request->isAjax) {
            $add_comment = new Comments();
            $add_comment->site_id = $_POST['site_id'];
            $add_comment->owner_id = Yii::$app->user->id;
            $add_comment->destination_id = $_POST['destination_id'];
            $add_comment->comment = $_POST['comment'];
            $add_comment->save();
        }
    }

    public function actionIndexing()
    {
        if(Yii::$app->request->isAjax) {
            $keys = Yii::$app->request->post();

            if($keys)
                foreach ($keys['keys'] as $key) {
                    $site = Site::findOne($key);
                    $indexing = new Indexing();
                    $result = Search::check($site->name);
                    $result['ya'] ? $indexing->yandex_indexing = 1 : false;
                    $result['google'] ? $indexing->google_indexing = 1 : false;
                    $indexing->google_indexed_pages = Search::getCount($site->name);
                    $indexing->date_cache = Search::cache($site->name, 'date');
                    $indexing->site_id = $site->id;
                    $indexing->save();
                }
        }
    }

    public function actionAudit()
    {
        Yii::info('start', 'audit');
        if(Yii::$app->request->isAjax) {
            $keys = Yii::$app->request->post();

            if($keys)
                foreach ($keys['keys'] as $key) {
                    $url = Url::findOne(['site_id' => $key]);
                    AuditService::addAudit($url->url, $url->id);
                }
        }
    }

    public function actionAccess()
    {
        if(Yii::$app->request->isAjax) {
            $keys = Yii::$app->request->post();

            if($keys)
                foreach ($keys['keys'] as $key) {
                    $user = User::findOne(['id' => $key]);
                    $user->status = 10;
                    $user->save();
                }
        }
    }

    public function actionRedirect()
    {
        if(Yii::$app->request->isAjax) {
            $link = $_POST['value'];
            $domain  =$_POST['domain'];
            return \frontend\modules\site\models\Site::getLink($link, $domain);
        }
    }
}
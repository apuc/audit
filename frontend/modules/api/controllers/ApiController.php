<?php


namespace frontend\modules\api\controllers;

use common\classes\ChartData;
use common\classes\Debug;
use common\models\Audit;
use common\models\AuditPending;
use common\models\Comments;
use common\models\Indexing;
use common\models\IndexingPending;
use common\models\Links;
use common\models\Search;
use common\models\Site;
use common\models\User;
use common\models\Url;
use common\services\AuditService;
use Yii;
use yii\web\Controller;
use yii\helpers\Html;


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
            $keys = $_POST['keys'];
            if($keys)
                foreach ($keys as $key) {
                    $indexing = new IndexingPending();
                    $indexing->site_id = $key;
                    $indexing->save();
                }
        }
    }

    public function actionAudit()
    {
        Yii::info('start', 'audit');
        if(Yii::$app->request->isAjax) {
            $keys = $_POST['keys'];
            if($keys)
                foreach ($keys as $key) {
                    $audit = new AuditPending();
                    $audit->site_id = $key;
                    $audit->save();
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
            try {
                $links_array = array();
                $link = $_POST['value'];
                $links = Links::findOne(['name' => $link]);
                $keys = $_POST['keys'];
                if ($keys)
                    foreach ($keys as $key) {
                        $site = Site::findOne($key);
                        if($link != 'cache')
                            array_push($links_array, $links->link . $site->name);
                        else array_push($links_array, 'http://webcache.googleusercontent.com/search?q=cache:' . $site->name);
                    }
                return json_encode($links_array);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function actionChart()
    {
        if(Yii::$app->request->isAjax) {
            $id = $_POST['id'];
            $site = Site::findOne($id);
            if($site) {
                $size = \frontend\modules\site\models\Site::getChart($site, 'size');
                $loading_time = \frontend\modules\site\models\Site::getChart($site, 'loading_time');
                $server_response_code = \frontend\modules\site\models\Site::getChart($site, 'server_response_code');
                $created_at = \frontend\modules\site\models\Site::getChart($site, 'created_at');
                $result = new ChartData($size, $loading_time, $server_response_code, $created_at);
                return json_encode($result);
            } else {
                return false;
            }
        }
    }
}
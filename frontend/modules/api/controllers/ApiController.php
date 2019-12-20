<?php


namespace frontend\modules\api\controllers;

use common\classes\Debug;
use common\classes\UserAgentArray;
use common\models\Audit;
use common\models\Comments;
use common\models\Search;
use common\models\Site;
use common\models\User;
use common\models\Url;
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

            if (($site = \common\models\Site::findOne($site_id)) !== null) {
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

            if($keys) {
                foreach ($keys['keys'] as $key) {
                    $url = Url::findOne(['site_id' => $key]);
                    if($url) {
                        $audit = Audit::find()
                            ->where(['url_id' => $url->id, 'check_search' => 0])
                            ->orderBy('created_at desc')
                            ->one();
                        if($audit) {
                            $result = Search::check($url->url);
                            $result['ya'] ? $audit->yandex_indexing = 1 : false;
                            $result['google'] ? $audit->google_indexing = 1 : false;
                            $audit->check_search = 1;
                            $audit->save();
                        }
                    }
                }
            }
        }
    }

    public function actionAudit()
    {
        if(Yii::$app->request->isAjax) {
            $keys = Yii::$app->request->post();

            if($keys) {
                foreach ($keys['keys'] as $key) {
                    $url = Url::findOne(['site_id' => $key]);
                    $audit_id = \frontend\modules\url\models\Url::addAudit($url->url, $url->id);
                    if($audit_id) {
                        $server_response = Audit::find()->where(['id' => $audit_id])->asArray()->all();
                    }
                    if($server_response) {
                        $server_response_code = $server_response[0]['server_response_code'];
                    }

                    if ($server_response_code == 200) {
                        \frontend\modules\url\models\Url::addExternalLinks($url->url, $audit_id);
                    }
                }
            }
        }
    }

    public function actionAccess()
    {
        if(Yii::$app->request->isAjax) {
            $keys = Yii::$app->request->post();

            if($keys) {
                foreach ($keys['keys'] as $key) {
                    $user = User::findOne(['id' => $key]);
                    $user->status = 10;
                    $user->save();
                }
            }
        }
    }
}
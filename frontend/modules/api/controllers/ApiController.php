<?php


namespace frontend\modules\api\controllers;

use common\models\Comments;
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
            echo Yii::$app->request->post();
        }
    }
}
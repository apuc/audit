<?php


namespace frontend\modules\api\controllers;

use frontend\modules\site\models\Site;
use Yii;
use common\classes\Debug;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


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
            $data = str_replace('comment', "", Yii::$app->request->post());

            $site_id_encoded = stristr(implode($data), 'aTo');
            $comment = implode(str_replace($site_id_encoded, "", $data));
            $site_id_decoded = base64_decode($site_id_encoded);
            $site_id = str_replace(array("i:", ";"), "", $site_id_decoded);

            if (($site = \common\models\Site::findOne($site_id)) !== null) {
                $site->comment = $comment;
                $site->save();
            }
        }
    }
}
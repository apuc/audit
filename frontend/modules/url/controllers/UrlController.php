<?php

namespace frontend\modules\url\controllers;

use common\classes\Debug;
use common\models\Audit;
use common\models\Dns;
use common\models\Site;
use frontend\modules\url\models\UrlForm;
use Yii;
use frontend\modules\url\models\Url;
use frontend\modules\url\models\UrlSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use GuzzleHttp;

/**
 * UrlController implements the CRUD actions for Url model.
 */
class UrlController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Url models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UrlSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionUrls()
    {
        $model = new UrlForm();

        if ($model->load(Yii::$app->request->post())) {

            $urls_arr = str_replace(array("\r\n", "\r", "\n"), ",", $model->urls);
            $clean_urls = str_replace(array("http://", "https://", "www."), "", $urls_arr);
            $explode_urls = explode(",", $clean_urls);

            $urls = array();
            foreach ($explode_urls as $explode_url) {
                $trim_url = trim($explode_url);
                if($trim_url) {
                    array_push($urls, $trim_url);
                }
            }

            $site_array = Site::find()->all();

            $site = array();
            foreach ($site_array as $value) {
                array_push($site, $value->name);
            }

            if ($site_array) {
                $urls_uintersect = array_uintersect($urls, $site, "strcasecmp");
                if($urls_uintersect) {
                    Url::updateAudit($urls_uintersect);
                } else {
                    $urls_diff = array_diff($urls, $site);
                    URL::insertData($urls_diff);
                }
            } else {
                URL::insertData($urls);
            }
            return $this->render('urls_view', [
                'urls' => $urls,
            ]);
        } else {
            return $this->render('urls', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays a single Url model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Url model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Url();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Url model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Url model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Url model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Url the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Url::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

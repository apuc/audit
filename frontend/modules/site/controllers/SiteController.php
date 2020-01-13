<?php

namespace frontend\modules\site\controllers;

use common\classes\Debug;
use common\classes\SizerGridView;
use common\classes\UserAgentArray;
use common\models\Indexing;
use common\models\Links;
use common\models\Search;
use common\models\Theme;
use common\services\AuditService;
use frontend\modules\url\models\Url;
use http\Exception;
use GuzzleHttp;
use Yii;
use common\models\Site;
use frontend\modules\site\models\SiteSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SiteController implements the CRUD actions for Site model.
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'update', 'view', 'create'],
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
     * Lists all Site models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SiteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if(Yii::$app->request->isAjax) {
            SizerGridView::setSize($_GET['value']);
        }

        $dataProvider->pagination->pageSize = SizerGridView::getSize();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Site model.
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
     * Creates a new Site model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Site();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Site model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Comment
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionComment($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('comment', [
            'model' => $model,
        ]);
    }

    /**
     * Theme
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionTheme($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('theme', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $site = Site::findOne(['id' => $id]);
        \frontend\modules\site\models\Site::deleteSite($site);
        return $this->redirect(['index']);
    }

    public function actionCustomdelete($id)
    {
        $clean_id = str_replace('=', "", stristr($id, '='));
        $site = Site::findOne($clean_id);
        \frontend\modules\site\models\Site::deleteSite($site);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Site model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Site the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Site::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAudit($domain)
    {
        $site = Site::findOne(['name' => $domain]);
        $url = \common\models\Url::findOne(['site_id' => $site->id]);
        AuditService::addAudit($domain, $url->id);
    }

    public function actionSearch($domain)
    {

    }

    public function actionTest($domain)
    {
        Debug::prn($domain);
        $links = Links::findOne(['name' => 	'ru.megaindex.com']);
        Debug::prn($links);
        $clean = str_replace(array("{PATH}", "{ANCHOR}"), "", $links->link);
        Debug::prn($clean);
        Debug::prn(str_replace(array("{SITE}"), $domain, $clean));
    }
}

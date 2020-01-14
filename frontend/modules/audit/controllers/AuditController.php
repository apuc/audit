<?php

namespace frontend\modules\audit\controllers;

use common\classes\Debug;
use common\models\Dns;
use common\models\ExternalLinks;
use common\models\Url;
use common\models\Site;
use Yii;
use common\models\Audit;
use frontend\modules\audit\models\AuditSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AuditController implements the CRUD actions for Audit model.
 */
class AuditController extends Controller
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
     * Lists all Audit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuditSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Audit models.
     * @return mixed
     */
    public function actionPending()
    {
        return $this->render('pending');
    }

    /**
     * Displays a single Audit model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $audit = Audit::find()->where(['id' => $id])->asArray()->all();
        if($audit) {
            $url = Url::find()->where(['id' => $audit[0]['url_id']])->asArray()->all();
            if($url) {
                $site_id = $url[0]['site_id'];

                $dns = new ActiveDataProvider([
                    'query' => Dns::find()->where(['site_id' => $site_id]),
                    'pagination' => [
                        'pageSize' => 20,
                    ],
                ]);

                $externalLinks = new ActiveDataProvider([
                    'query' => ExternalLinks::find()->where(['audit_id' => $id]),
                    'pagination' => [
                        'pageSize' => 20,
                    ],
                ]);

                $site = new ActiveDataProvider([
                    'query' => Site::find()->where(['id' => $site_id]),
                    'pagination' => [
                        'pageSize' => 20,
                    ],
                ]);

                $audit = new ActiveDataProvider([
                    'query' => Audit::find()->where(['id' => $id]),
                    'pagination' => [
                        'pageSize' => 20,
                    ],
                ]);

                return $this->render('view', [
                    'model' => $this->findModel($id),
                    'externalLinks' => $externalLinks,
                    'dns' => $dns,
                    'site' => $site,
                    'audit' => $audit
                ]);
            }
        }
        return $this->redirect(['/domain/site']);
    }

    /**
     * Creates a new Audit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Audit();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Audit model.
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
     * Deletes an existing Audit model.
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
     * Finds the Audit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Audit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Audit::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

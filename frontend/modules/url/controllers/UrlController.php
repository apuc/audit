<?php

namespace frontend\modules\url\controllers;

use common\classes\Debug;
use frontend\modules\url\models\ReportForm;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use frontend\modules\url\models\Url;
use frontend\modules\url\models\UrlForm;
use frontend\modules\url\models\UrlSearch;
use frontend\modules\url\models\DataForm;

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
            $formatting_urls = Url::formattingUrl($model->urls);
            $data_array = Url::formData($formatting_urls);
            $report = new ReportForm();
            Url::addData($data_array, $report);

            if($report->errorsUrl != 0) {
                Yii::$app->session->setFlash(
                    'success',
                    "<br> Добавлено новых доменов: " . $report->newSite . "<br>" .
                    "Добавлено новых url: " . $report->newUrl . "<br>" .
                    "Проведено аудитов: " . $report->newAudit . "<br>" .
                    "Ошибки: ". $report->errorsUrl . "<br>" .
                    "Мониторинг для этих сайтов не проведен: " . implode(", ", $report->errorUrlArray) . "<br>"
                );
            } else {
                Yii::$app->session->setFlash(
                    'success',
                    "<br> Добавлено новых доменов: " . $report->newSite . "<br>" .
                    "Добавлено новых url: " . $report->newUrl . "<br>" .
                    "Проведено аудитов: " . $report->newAudit . "<br>" .
                    "Ошибки: ". $report->errorsUrl . "<br>"
                );
            }

            return $this->redirect('domain/site');

        } else {
            return $this->render('urls', ['model' => $model]);

        }
    }

/**
 * Displays a single Url model.
 * @param integer $id
 * @return mixed
 * @throws NotFoundHttpException if the model cannot be found
 */
public
function actionView($id)
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
public
function actionCreate()
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
public
function actionUpdate($id)
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
public
function actionDelete($id)
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
protected
function findModel($id)
{
    if (($model = Url::findOne($id)) !== null) {
        return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
}
}

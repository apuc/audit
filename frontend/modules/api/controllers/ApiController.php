<?php


namespace frontend\modules\api\controllers;

use common\classes\ChartData;
use common\classes\Debug;
use common\models\Audit;
use common\models\AuditPending;
use common\models\ChartAuditQueue;
use common\models\Comments;
use common\models\ExternalLinks;
use common\models\Indexing;
use common\models\IndexingPending;
use common\models\Links;
use common\models\Search;
use common\models\Settings;
use common\models\Site;
use common\models\SiteThemes;
use common\models\Theme;
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
            $theme_ids = json_decode($_POST['theme_ids']);
            $selected_themes = SiteThemes::find()->where(['site_id' => $_POST['site_id']])->all();
            $new = array();
            $old = array();

            foreach ($theme_ids as $val)
                array_push($new, $val->id);

            foreach ($selected_themes as $selected_theme)
                array_push($old, $selected_theme->theme_id);

            $add = array_diff($new, $old);
            $del = array_diff($old, $new);

            if($add)
            foreach ($add as $item) {
                $site_theme  = new SiteThemes();
                $site_theme->site_id = $_POST['site_id'];
                $site_theme->theme_id = $item;
                $site_theme->save();
            }

            if($del)
            foreach ($del as $item) {
                SiteThemes::deleteAll(['site_id' => $_POST['site_id'], 'theme_id' => $item]);
            }
        }
    }

    public function actionSelected()
    {
        $themes = array();
        if(Yii::$app->request->isAjax) {
            $site = Site::findOne($_POST['id']);
            if (isset($site->siteThemes)) {
                foreach ($site->siteThemes as $val) {
                    array_push($themes, $val->theme_id);
                }
            }
        }
        return json_encode($themes);
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
                $result = new ChartData($site->name, $size, $loading_time, $server_response_code, $created_at);
                return json_encode($result);
            } else {
                return false;
            }
        }
    }

    public function actionLinks()
    {
        if(Yii::$app->request->isAjax) {
            $sql = 'SELECT site.name, external_links.acceptor, external_links.anchor, external_links.screenshot 
                    FROM site, url, audit, external_links 
                    WHERE site.id = ' . $_POST['site_id'] . '
                        AND url.site_id = site.id 
                        AND audit.url_id = url.id 
                        AND external_links.audit_id = audit.id 
                    GROUP BY external_links.acceptor 
                    ORDER BY audit.id DESC';
            $links = ExternalLinks::findBySql($sql)->asArray()->all();

            if(!$links) {
                $site = Site::findOne($_POST['site_id']);
                $links[0]['name'] = $site->name;
            }
            $links = json_encode($links);
            return $links;
        }
    }

    public function actionDeleteindexing()
    {
        if(Yii::$app->request->isAjax) {
            $keys = $_POST['keys'];
            if($keys)
                foreach ($keys as $key)
                    IndexingPending::deleteAll(['id' => $key]);
        }
    }

    public function actionDeleteaudit()
    {
        if(Yii::$app->request->isAjax) {
            $keys = $_POST['keys'];
            if($keys)
                foreach ($keys as $key)
                    AuditPending::deleteAll(['id' => $key]);
        }
    }

    public function actionDeletechartdata()
    {
        if(Yii::$app->request->isAjax) {
            $keys = $_POST['keys'];
            if($keys)
                foreach ($keys as $key)
                    ChartAuditQueue::deleteAll(['id' => $key]);
        }
    }

    public function actionDeletesites()
    {
        if(Yii::$app->request->isAjax) {
            $keys = $_POST['keys'];
            if($keys)
                foreach ($keys as $key)
                    Site::deleteAll(['id' => $key]);
        }
    }
}
<?php


namespace console\controllers;


use common\models\Audit;
use Yii;
use yii\console\Controller;

class ImagesController extends Controller
{
    public function actionDelete()
    {
        $icons_db = array();
        $screenshots_db = array();
        $audit = Audit::find()->all();
        foreach ($audit as $value) {
            array_push($icons_db, $value->icon);
            array_push($screenshots_db, $value->screenshot);
        }
        $icons_folder = scandir(Yii::getAlias('@frontend/web/i/'));
        unset($icons_folder[0]);
        unset($icons_folder[1]);
        $screenshots_folder = scandir(Yii::getAlias('@frontend/web/screenshots/'));
        unset($screenshots_folder[0]);
        unset($screenshots_folder[1]);

        $delete_icons = array_diff($icons_folder, $icons_db);
        $delete_screenshots = array_diff($screenshots_folder, $screenshots_db);

        foreach ($delete_icons as $value)
            unlink(Yii::getAlias('@frontend/web/i/') . $value);

        foreach ($delete_screenshots as $value)
            unlink(Yii::getAlias('@frontend/web/screenshots/') . $value);

        echo "script completed successfully\n";
    }
}
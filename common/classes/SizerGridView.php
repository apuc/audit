<?php

namespace common\classes;

use Yii;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\Cookie;

class SizerGridView extends GridView
{
    public $layout = "{sizer}\n{summary}\n{items}\n{pager}";

    public function renderSection($name)
    {
        $section = parent::renderSection($name);
        if(!$section){
            return $this->renderSizer();
        }
        return $section;
    }

    public function renderSizer()
    {
        $items=[
            20 => 20,
            100 => 100,
            300 => 300,
            500 => 500,
            1000 => 1000,
        ];

        return 'Количество записей на странице:<br>' . Html::dropDownList('size', SizerGridView::getSize(), $items,
                ['class' => 'btn btn-secondary dropdown-toggle custom-drop-down', 'onchange' => 'sizer(this.value);']);
    }

    public static function getSize()
    {
        $cookies = Yii::$app->request->cookies;
        if (($cookie = $cookies->get('size')) !== null) {
            return $cookie->value;
        }
        return 20;
    }

    public static function setSize($size)
    {
        $cookies = Yii::$app->request->cookies;

        $cookie = new Cookie([
            'name' => 'size',
            'value' => $size,
        ]);

        Yii::$app->getResponse()->getCookies()->add($cookie);
    }

}
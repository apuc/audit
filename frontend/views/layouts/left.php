<aside class="main-sidebar">

    <section class="sidebar">

        <?php
        use yii\helpers\Html;

        echo '<h4 class="white">&nbsp&nbsp&nbsp<img src="'.$directoryAsset.'/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>&nbsp' . Yii::$app->user->identity->username . '</h4>'
        ?>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => 'Добавить сайты',
                        'icon' => 'pencil',
                        'url' => ['/url/url/urls'],
                    ],
//                    [
//                        'label' => 'Добавить ссылки',
//                        'icon' => 'pencil',
//                        'url' => ['/links/links/links'],
//                    ],
                    [
                        'label' => 'Сайты',
                        'icon' => 'list-alt',
                        'url' => ['/domain/site'],
                        'active' => \Yii::$app->controller->id == 'site',
                    ],
                    [
                        'label' => 'Очередь',
                        'icon' => 'hourglass',
                        'url' => ['/audit/audit/pending'],
                        'active' => \Yii::$app->controller->id == 'audit',
                    ],
                    [
                        'label' => 'Ссылки',
                        'icon' => 'share-alt',
                        'url' => ['/links/links'],
                        'active' => \Yii::$app->controller->id == 'links',
                    ],
                    [
                        'label' => 'Темы сайтов',
                        'icon' => 'tags',
                        'url' => ['/theme/theme'],
                        'active' => \Yii::$app->controller->id == 'theme',
                    ],
                    [
                        'label' => 'Комментарии',
                        'icon' => 'comment',
                        'url' => ['/comments/comments'],
                        'active' => \Yii::$app->controller->id == 'comments',
                    ],
                    [
                        'label' => 'Пользователи',
                        'icon' => 'user',
                        'url' => ['/user/user'],
                        'active' => \Yii::$app->controller->id == 'user',
                    ],
                    [
                        'label' => 'Настройки',
                        'icon' => 'cog',
                        'url' => ['/settings/settings/view?id=1'],
                        'active' => \Yii::$app->controller->id == 'settings',
                    ],
                ],
            ]
        ) ?>

        <?php
        echo Html::beginForm(['/site/logout'], 'post');
        echo Html::submitButton('Выйти', ['class'=> 'btn btn-secondary btn-custom']);
        echo Html::endForm();
        ?>

    </section>

</aside>

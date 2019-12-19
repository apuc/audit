<aside class="main-sidebar">

    <section class="sidebar">

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => 'Проверить Url-ы',
                        'icon' => 'files-o',
                        'url' => ['/url/url/urls'],
                    ],
                    [
                        'label' => 'Сайты',
                        'icon' => 'files-o',
                        'url' => ['/domain/site'],
                        'active' => \Yii::$app->controller->id == 'site',
                    ],
                    [
                        'label' => 'Аудит',
                        'icon' => 'files-o',
                        'url' => ['/audit/audit'],
                        'active' => \Yii::$app->controller->id == 'audit',
                    ],
                    [
                        'label' => 'Комментарии',
                        'icon' => 'files-o',
                        'url' => ['/comments/comments'],
                        'active' => \Yii::$app->controller->id == 'comments',
                    ],
                    [
                        'label' => 'URL',
                        'icon' => 'files-o',
                        'url' => ['/url/url'],
                        'active' => \Yii::$app->controller->id == 'url',
                    ],
                    [
                        'label' => 'Темы сайтов',
                        'icon' => 'files-o',
                        'url' => ['/theme/theme'],
                        'active' => \Yii::$app->controller->id == 'theme',
                    ],
                    [
                        'label' => 'Пользователи',
                        'icon' => 'files-o',
                        'url' => ['/user/user'],
                        'active' => \Yii::$app->controller->id == 'user',
                    ],
                    [
                        'label' => 'Ссылки',
                        'icon' => 'files-o',
                        'url' => ['/links/links'],
                        'active' => \Yii::$app->controller->id == 'links',
                    ],
                    [
                        'label' => 'Инструкция',
                        'icon' => 'files-o',
                        'url' => ['/site/info'],
                        'active' => \Yii::$app->controller->id == 'site',
                    ],
                ],
            ]
        ) ?>

    </section>

</aside>

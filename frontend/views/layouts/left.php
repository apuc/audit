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
                        'label' => 'URL',
                        'icon' => 'files-o',
                        'url' => ['/url/url'],
                        'active' => \Yii::$app->controller->id == 'url',
                    ],
                    [
                        'label' => 'DNS',
                        'icon' => 'files-o',
                        'url' => ['/dns/dns'],
                        'active' => \Yii::$app->controller->id == 'dns',
                    ],
                    [
                        'label' => 'Аудит',
                        'icon' => 'files-o',
                        'url' => ['/audit/audit'],
                        'active' => \Yii::$app->controller->id == 'audit',
                    ],
                    [
                        'label' => 'Пользователи',
                        'icon' => 'files-o',
                        'url' => ['/user/user'],
                        'active' => \Yii::$app->controller->id == 'user',
                    ],
                    [
                        'label' => 'Инструменты',
                        'icon' => 'gears',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
                            ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
                        ],
                    ],
                ],
            ]
        ) ?>

    </section>

</aside>

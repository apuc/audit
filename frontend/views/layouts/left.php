<aside class="main-sidebar">

    <section class="sidebar">

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
                    [
                        'label' => 'Добавить ссылки',
                        'icon' => 'pencil',
                        'url' => ['/links/links/links'],
                    ],
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
                        'icon' => 'th-list',
                        'url' => ['/links/links'],
                        'active' => \Yii::$app->controller->id == 'links',
                    ],
                    [
                        'label' => 'Темы сайтов',
                        'icon' => 'th-list',
                        'url' => ['/theme/theme'],
                        'active' => \Yii::$app->controller->id == 'theme',
                    ],
                    [
                        'label' => 'Список URL',
                        'icon' => 'th-list',
                        'url' => ['/url/url'],
                        'active' => \Yii::$app->controller->id == 'url',
                    ],
                    [
                        'label' => 'История аудитов',
                        'icon' => 'th-list',
                        'url' => ['/audit/audit'],
                        'active' => \Yii::$app->controller->id == 'audit',
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
//                    [
//                        'label' => 'Инструкция',
//                        'icon' => 'info',
//                        'url' => ['/site/info'],
//                        'active' => \Yii::$app->controller->id == 'site',
//                    ],
                ],
            ]
        ) ?>

    </section>

</aside>

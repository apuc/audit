<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'name' => 'Monitoring',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['audit'],
                    'logFile' => '@frontend/runtime/logs/audit.log',
                ],

            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => '/domain/site',
            ],
        ],
    ],
    'params' => $params,

    'modules' => [
        'user' => [
            'class' => 'frontend\modules\user\User',
        ],
        'domain' => [
            'class' => 'frontend\modules\site\Site',
        ],
        'url' => [
            'class' => 'frontend\modules\url\Url',
        ],
        'dns' => [
            'class' => 'frontend\modules\dns\Dns',
        ],
        'audit' => [
            'class' => 'frontend\modules\audit\Audit',
        ],
        'externallinks' => [
            'class' => 'frontend\modules\externallinks\Externallinks',
        ],
        'theme' => [
            'class' => 'frontend\modules\theme\Theme',
        ],
        'api' => [
            'class' => 'frontend\modules\api\Api',
        ],
        'comments' => [
            'class' => 'frontend\modules\comments\Comments',
        ],
        'links' => [
            'class' => 'frontend\modules\links\Links',
        ],
        'auditpending' => [
            'class' => 'frontend\modules\auditpending\Auditpending',
        ],
        'indexingpending' => [
            'class' => 'frontend\modules\indexingpending\Indexingpending',
        ],
    ],
];

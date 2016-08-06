<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-qsyk',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'qsyk\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
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
                ['class' => 'yii\rest\UrlRule', 'controller' => ['resource', 'v2/resource']],
                ['class' => 'yii\rest\UrlRule', 'controller' => ['user','v2/user']],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user-task'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'resource-tag'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'tag'],
                'GET,HEAD resources/<sid>' => 'resource/view',
                'GET,HEAD resource-tags/<sid>' => 'resource-tag/view',
                'GET,HEAD tags/<sid>' => 'tag/view',
            ],
        ],
    ],
    'params' => $params,
];

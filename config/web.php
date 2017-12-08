<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'es',
    'layout' => 'codecanyon',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'W03zHdRh7W-DgBhFN4tEFdQKWSfgCBFb',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Usuario',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                // Testing
                'class' => 'Swift_MailTransport',
                // Produccion
                /*'class' => 'Swift_SmtpTransport',
                'host' => $params['mail_host'],
                'username' => $params['mail_username'],
                'password' => $params['mail_password'],
                'port' => $params['mail_port'],
                'encryption' => $params['mail_encryption'],*/
            ],
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
        'db' => require(__DIR__ . '/db.php'),
        // Habilita el uso de URL Amigables
        // Se combina con el uso de .htaccess
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enableStrictParsing' => true,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                '<controller:\w+>/?' => '<controller>/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                'module/<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                'site/page/view/<view:\w+>' => 'site/page',
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [/*
                'google' => [
                    'class' => 'yii\authclient\clients\GoogleOpenId'
                ],*/
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => '1559052341089539',
                    'clientSecret' => '4d6e14d24e1fabea0b7fda13d90c4a3e',
                ],
                'twitter' => [
                    'class' => 'yii\authclient\clients\Twitter',
                    'consumerKey' => 'yULojgZ2TEmhk4q78BbbNEqom',
                    'consumerSecret' => 'y0PSypijYb0F9VyZ9Gbi9s8WxaD3aY2kE6lL7j6fz3ZNlYNwqw',
                ],
                'linkedin' => [
                    'class' => 'yii\authclient\clients\LinkedIn',
                    'clientId' => '78mqxva54ykotc',
                    'clientSecret' => 'bmJSjFROKJvOgGqW',
                ],
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    // Yii2 por defecto utiliza la v2.1.4 pero no es soportada por -IE8
                    'js' => ['//code.jquery.com/jquery-1.11.3.min.js'],
                ],
                'yii\jui\JuiAsset' => [
                    'css' => [
                        //'themes/dark-hive/jquery-ui.css',
                        'themes/ui-darkness/jquery-ui.css',
                    ]
                ]
            ],
        ],
        // Configuración regional
        'formatter' => [
            'dateFormat' => 'php:d/m/Y',
            'datetimeFormat' => 'php:d/m/Y h:i a',
            'timeFormat' => 'hh:i a',
            'locale' => 'es-MX',
            'defaultTimeZone' => 'America/Mexico_City',
            'timeZone' => 'America/Mexico_City',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
        'generators' => [
            'crud' => [
                'class' => 'app\generators\crud\Generator',
                'templates' => [
                    'p4scu41_crud' => '@app/generators/crud/default'
                ]
            ],
            'model' => [
                'class' => 'app\generators\model\Generator',
                'templates' => [
                    'p4scu41_model' => '@app/generators/model/default'
                ]
            ]
        ]
    ];
}

return $config;

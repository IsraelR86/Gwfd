<?php
// $config hereda de web todos los valores,
// se sobreescriben solo aquellos que sean necesarios
$config = require(__DIR__ . '/../../../config/web.php');

$config['basePath'] = dirname(__DIR__).'/..';
$config['runtimePath'] = dirname(__DIR__).'/runtime';
$config['components']['request']['parsers'] = ['application/json' => 'yii\web\JsonParser'];
$config['components']['urlManager'] = [
        'enablePrettyUrl' => true,
        'enableStrictParsing' => true,
        'showScriptName' => false,
        'rules' => [
            [
                'class' => 'yii\rest\UrlRule',
                'controller' => [
                    'v1/site',
                    'v1' => 'v1/site',
                    'v1/usuarios' => 'v1/usuario',
                    'v1/concursos' => 'v1/concurso',
                    'v1/aliados' => 'v1/aliado',
                    'v1/noticias' => 'v1/noticia',
                    'v1/proyectos' => 'v1/proyecto',
                ]
            ],
        ],
    ];
$config['components']['errorHandler'] = ['errorAction' => 'v1/site/error'];
$config['components']['mailer']['viewPath'] = '../../mail/';

$config['modules']['v1'] = ['class' => 'app\api\modules\v1\Api'];
$config['modules']['aliases'] = ['@app' => '..\\app\\'];

return $config;
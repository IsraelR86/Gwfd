<?php
// Se configura el timezone
ini_set('date.timezone', 'America/Mexico_City');
// Se configura para que el registro de errores php.log se guarde en el directorio runtime/logs
ini_set('error_log', __DIR__.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'php.log');
// Se configura para que registre en el log todos los errores
ini_set('log_errors', 1);

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/config/api.php');

(new yii\web\Application($config))->run();

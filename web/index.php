<?php
// Se configura el timezone para que sea el local
ini_set('date.timezone', 'America/Mexico_City');
// Se configura para que el php_errors.log se guarde en el directorio runtime
ini_set('error_log', __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'php_errors.log');
// Se configura para que registre en el log todos los errores
ini_set('log_errors', 1);
// Habilita el uso de las etiquetas cortas
ini_set('short_open_tag', 1);

// comment out the following two lines when deployed to production
//defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();

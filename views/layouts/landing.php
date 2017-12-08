<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\models\Usuario;
AppAsset::register($this);
use yii\bootstrap\Modal;

// Se registra la variable para referenciar al home de manera relativa
$this->registerJs('var homeUrl = "'.Url::home().'";', \yii\web\View::POS_HEAD);


?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode(Yii::$app->params['title'].(isset($this->title) ? ' - '.$this->title : '')) ?></title>
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/css/landing.css" type="text/css" />
    <?php $this->head() ?>
</head>
<body class="<?php 
    switch(Yii::$app->user->identity->tipo) {
        case Usuario::$EMPRENDEDOR:
            echo 'themeRed';
            break;
        
        case Usuario::$ADMINISTRADOR:
        case Usuario::$INSTITUCION:
            echo 'themeBlue';
            break;
    }
?>" id="landing">
<?php $this->beginBody() ?>

    <div class="fade-black">
        <div class="header">
            <div class="relative">
                <div class="pull-left logo">
                    <img src="<?= Yii::$app->request->baseUrl ?>/img/FWD.png">
                </div>
                <div class="pull-right">
                    <a class="btnBorderRed" href="#">
                        CREA UN CONCURSO
                    </a>
                    <a class="btnBorderRed" href="#">
                        SIGN UP
                    </a>
                    <a class="btnBorderRed" href="#">
                        EXTRA
                    </a>
                </div>
            </div>
        </div>
        
        <div class="info">
            <div class="title">
                GO FOWARD
            </div>
            <div class="description">
                LA MAYOR PLATAFORMA DE EMPRENDIMIENTO EN AMÉRICA LATINA
            </div>
            <a class="btnBorderRed signup" href="#">
                SIGN UP
            </a>
        </div>
        <div class="nav-section">
            <center>
                <ul>
                    <li>FWD</li>
                    <li>TALENT</li>
                    <li>CHALLENGE</li>
                    <li>EVALUATION</li>
                    <li>BUSINESS</li>
                </ul>     
            </center>               
        </div>
            
    </div>
    
    <div class="landing-content">
        <?= $content; ?>
    </div>
    

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

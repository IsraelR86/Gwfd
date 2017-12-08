<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\models\Usuario;
use yii\bootstrap\Modal;
AppAsset::register($this);

// Se registra la variable para referenciar al home de manera relativa
$this->registerJs('var homeUrl = "'.Url::home().'";', \yii\web\View::POS_HEAD);

if (Yii::$app->session->hasFlash('welcome')) {
    $this->registerJs('var showWelcome = true;', \yii\web\View::POS_HEAD);
}
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode(Yii::$app->params['title'].(isset($this->title) ? ' - '.$this->title : '')) ?></title>
    <?php $this->head() ?>
</head>
<body class="<?php
    if (!Yii::$app->user->isGuest) {
        switch(Yii::$app->user->identity->tipo) {
            case Usuario::$EMPRENDEDOR:
                echo 'themeRed';
                break;

            case Usuario::$ADMINISTRADOR:
            case Usuario::$INSTITUCION:
                echo 'themeBlue';
                break;
        }
    }
?>">
<?php $this->beginBody() ?><script>
  window.fbAsyncInit = function() {
    FB.init({
      appId            : '183718785487466',
      autoLogAppEvents : true,
      xfbml            : true,
      version          : 'v2.9'
    });
    FB.AppEvents.logPageView();
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

    <header>
        <div class="container-fluid">
        <!-- INICIO Header -->
        <?= $this->renderFile('@app/views/layouts/header.php', ['view' => $this]) ?>
        <?= Yii::$app->user->identity != null ? $this->renderFile('@app/views/layouts/menu.php', ['view' => $this]) : ''; ?>
        <!-- FIN Header -->
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- INICIO Sidebar -->
            <?= yii::$app->controller->action->id !== 'error' && Yii::$app->user->identity != null ? $this->renderFile('@app/views/layouts/sidebar.php', ['view' => $this]) : ''; ?>
            <!-- FIN Sidebar -->

            <div class="col-md-12 col-xs-12" id="content">
                <!-- INICIO Contenido -->
                <?= $content ?>
                <!-- FIN Contenido -->
                
                
      
            </div>
        </div>
    </div>

<?php
Modal::begin([
    'id' => 'welcome',
    'headerOptions' => ['class' => 'no-border'],
]);
echo '<div class="title_container">
        <h3 class="title">¡Bienvenid@!</h3>
      </div>
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
            Jóvenes emprendedores, a través de este espacio les damos la bienvenida al 7º. Certamen
            Emprendedores 2017. Aquí podrán inscribir su proyecto y participar por uno de los tres
            lugares de la categoría única “Proyectos de Emprendimiento”, por lo que es importante
            cumplir con las bases, requisitos y procesos de registro que se establecen en la
            convocatoria.
        </div>
      </div>
      <div class="row" style="margin-top:20px;">
        <div class="col-md-10 col-md-offset-1">
            Fecha límite de registro: 14 de septiembre de 2017
        </div>
      </div>
      <div class="text-center" style="margin-top:20px;">
        <a class="btnBorderRed inline-block fontBlack" href="#modalCambiarPass" data-dismiss="modal">
            OK
        </a>
    </div>';
Modal::end();
?>

<?php $this->endBody() ?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-79730599-1', 'auto');
  ga('send', 'pageview');
</script>

<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
$.src="//v2.zopim.com/?3zIhtPjXgpXj1iZtfsRXkKUn2toAaHjV";z.t=+new Date;$.
type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>

</body>
</html>
<?php $this->endPage() ?>

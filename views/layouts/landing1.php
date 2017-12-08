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
$this->registerJs('var loginUrl = "'.Url::toRoute('site/loginajax').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var redirectloginUrl = "'.Url::toRoute('site/redirectlogin').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var origin = "http://gofwd.mx/";', \yii\web\View::POS_HEAD);
$this->registerJsFile(Url::to('web/js/landing/script.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('web/js/landing/registro.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('web/js/landing/main.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('web/js/landing/service_main.min.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('web/js/slick/slick.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);


$this->registerCssFile(Yii::$app->request->baseUrl."/css/landing/fonts.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/landing/headers.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/landing/login.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/landing/slider.min.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/landing/thumnail.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/landing/video.css");
//$this->registerCssFile(Yii::$app->request->baseUrl."/css/landing/styles.css");
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
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/css/landing/styles.css">
    
  <style type="text/css">
    html, body {
      margin: 0;
      padding: 0;
    }
    * {
      box-sizing: border-box;
    }
    .slider {
        width: 50%;
        margin: 100px auto;
    }
    .slick-slide {
      margin: 0px 20px;
    }
    .slick-slide img {
      width: 100%;
    }
    .slick-prev:before,
    .slick-next:before {
        color: black;
    }
.box-video{
  position: relative;
  max-width:590px;
  margin:0 auto 20px auto;
  cursor: pointer;
  overflow: hidden;
}

.box-video .bg-video{
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  z-index: 2;
}

.box-video .video-container{
  position: relative;
  margin: 0;
  z-index: 1;
}

.box-video .bt-play {
  position: absolute;
  top:50%;
  left:50%;
  margin:-30px 0 0 -30px;
  display: inline-block;
  width: 60px;
  height: 60px;
  background:#fff;
  border-radius: 50%;
  text-indent: -999em;
  cursor: pointer;
  z-index:2;
  -webkit-transition: all .3s ease-out;
  transition: all .3s ease-out;
}
.box-video .bt-play:after {
  content: '';
  position: absolute;
  left: 50%;
  top: 50%;
  height: 0;
  width: 0;
  margin: -12px 0 0 -6px;
  border: solid transparent;
  border-left-color: #000;
  border-width: 12px 20px;
  -webkit-transition: all .3s ease-out;
  transition: all .3s ease-out;
}
.box-video:hover .bt-play {
  transform: scale(1.1);
}

.box-video.open .bg-video{
  visibility: hidden;
  opacity: 1;

  -webkit-transition: all .6s .8s;
  transition: all .6s .8s;  
}
.box-video.open .video-container{
  opacity: 0;

  -webkit-transition: all .6s .8s;
  transition: all .6s .8s;
}    
  </style>
</head>
<body>
<?php $this->beginBody() ?>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '183718785487466',
      xfbml      : true,
      version    : 'v2.9'
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

<div class="center-content" style="max-width:100%">
  <div class="navigation1">
    <nav class="navbar navbar-default navbar-fixed-top center navB">
      <div class="center-content"> <!--Esta clase es para que este dentro del contenedor -->
      <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header navBar">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?= Url::home() ?>"><img class='imgnvBar' src="<?= Yii::$app->request->baseUrl."/img/landing/fwd1.png"?>" alt=""></a>
          <!-- <a class="navbar-brand" href="#"><img class='imgnvBar' src="img/fwd.png" alt=""></a> -->
        </div>
      
      <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav"></ul>
          <!-- <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
          </form> -->
          <ul class="nav navbar-nav navbar-right">
            <li class="btn3-navbar" style="display:none"><a href="#">CREA UN CONCURSO</a></li>
            <span>&nbsp</span>
            <li class="btn2-navbar"><a href=<?= Url::toRoute('usuario/singup')?>><img class='icon-unete-nav'   src="<?= Yii::$app->request->baseUrl."/img/landing/fwd.png" ?>" alt="" />&nbsp¡UNETE!</a></li>
            <li class="btn1-navbar" data-toggle="modal" data-target=".entrarModal"><a  href="#"><img class='icon-unete-nav2'  src="<?= Yii::$app->request->baseUrl."/img/landing/SignUp.png"?>" alt="" />&nbspENTRA</a></li>
            <li><a href="#">&nbsp</a></li>
            <!-- <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="itemet"></span></a>
            <ul class="dropdown-menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Separated link</a></li>
            </ul>
            </li> -->
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
  </div>

<?php
$route =  Yii::$app->controller->id."/".Yii::$app->controller->action->id; 
?>
  <div class="col-md-12 centered cover-center header-bk <?= $route=='site/index'? '':'hidden'?>" id='' style="background-image:url('/web/img/landing/Fondo-fese-landing.jpg') !important;height:700px; background-repeat:no-repeat;background-position:center;-webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover; padding:0">
    <video autoplay="" loop="" class="fillWidth fadeIn wow collapse in" data-wow-delay="0.5s"  id="video-background">
      <source src="video/oso.mp4" type="video/mp4">
    </video>
    <div style="padding:0 15px; height:100%; position:relative">
      <div class="img-responsivee">
        <img class='img-responsive centered' src="<?= Yii::$app->request->baseUrl."/img/landing/gofwd.png"?>" alt="" />
      </div>
      <h4 class="title-sections" style="color:white;position:inherit">LA MAYOR PLATAFORMA DE EMPRENDIMIENTO DE LATINOAMERICA</h4>
      <div class="btn-unete">
        <h3 class="title-sections" id="text-unete" style="color:white" >
          <a href="<?= Url::toRoute('usuario/singup') ?>" style="text-decoration:none; color:#fff;">
            <img class='icon-unete'  src="<?= Yii::$app->request->baseUrl."/img/landing/fwd.png"?>" alt="" />&nbsp¡UNETE!
          </a>
        </h3>
      </div>
    
      <div class="header-content2" style="width:100%; height:100px">
        <ul class="submenu">
          <li  class="active"><a id="h-submenu" href="#">FWD</a></li>
          <li><a id="h-submenu" href="#" color="white">TALENT</a></li>
          <li><a id="h-submenu" href="#" color="white">CHALLENGE</a></li>
          <li><a id="h-submenu" href="#">EVALUATION</a></li>
          <li><a id="h-submenu" href="#">BUSINESS</a></li>
        </ul>
      </div>
    </div>
  </div>

  <nav class='menu menuu <?= $route=='site/index'? '':'noindex'?>' style="z-index:1000;background-color:white;text-align:center;">
    <div id="navBar"  class="dropdown" style="display:none">
      <button class="dropbtn"><a href="index.html">FWD</a></button>
      <div class="dropdown-content">
        <a href="#">Business</a>
        <a href="#">Talent</a>
        <a href="#">Challenge</a>
        <a href="#">Evaluation</a>
      </div>
    </div>
    <div class="dropdown" >
      
      <button class="dropbtn"><a href="concursos.html">Concursos</a></button>
      <div class="dropdown-content">
        <a href="concursos.html" >Emprendimiento</a>
        <a href="#" >Universidades</a>
        <a href="#">Iniciativa Privada</a>
        <a href="#">...</a>
      </div>
    </div>
    <div class="dropdown" >
     <button class="dropbtn"><a href="fewnews.html">Noticias</a></button>
      <div class="dropdown-content">
        <a href="emprendimiento.html">Comunidad FWD</a>
        <a href="concursos.html">Concursos</a>
        <a href="#">Ganadores</a>
      </div>
    </div>
    <div class="dropdown" >
      <button class="dropbtn"><a href="#">Sobre FWD</a></button>
      <div class="dropdown-content">
        <a href="#" >Instituciones</a>
        <a href="#">¿Quien es FWD?</a>
        <a href="#">Contacto</a>
      </div>
    </div>
    <div class="dropdown" >
      <button class="dropbtn"><a href="">FAQ</a></button>
    </div>
      <!-- <div id="menuToggle" class="handle">Menu</div> -->
  </nav>
  
  <?= $content ?>
  
  <div class="row" id="footeer">
    <div class="col-md-4" id="foot-logo">
      <img src="<?= Yii::$app->request->baseUrl."/img/landing/FWDLOGO.png"?>">
    </div>
    <div class="col-md-2" id="footeer">
      <h3 id="title-foot">Contacto</h3>
      <h3 id="title-foot">Redes</h3>
      <ul class="redes">
        <li id="text-li">
          <a target="_blank" href="https://www.facebook.com/gofwdmx/">
            <i class="fa fa-facebook-square fa-2x" id="social-icon"><span id="text-foot">Facebook</span></i>
          </a>
        </li>
        <li id="text-li" style="display:none">
          <a  href="#">
            <i class="fa fa-twitter-square fa-2x" id="social-icon"><span id="text-foot">Twitter</span></i>
          </a>
        </li>
        <li id="text-li" style="display:none">
          <a href="#">
            <i class="fa fa-linkedin-square fa-2x" id="social-icon"><span id="text-foot">LinkedIn</span></i>
          </a>
        </li>
      </ul>
    </div>
    <div class="col-md-2" id="footeer">
      <h3 id="title-foot">Links</h3>
      <span id="text-foot"><a href=""> Sobre FWD</a></span><br>
      <span id="text-foot"><a href=""> Instituciones</a></span><br>
      <span id="text-foot"><a href=""> Sobre FWD</a></span><br>
      <span id="text-foot"><a href=""> ¿Quién es FWD?</a></span><br>
      <span id="text-foot"><a href=""> Prensa</a></span><br>
      <span id="text-foot"><a href=""> SITEMAP</a></span><br>
      <span id="text-foot"><a href=""> Políticas y Privacidad</a></span><br><br><br>
      <span id="text-foot"><a href=""> Términos y Condiciones</a></span><br>
      <span id="text-foot" style="color:white">&copy; 2016 FWD</span>
    </div>
    <div class="col-md-2" id="">
      <h3 id="title-foot">Ciudad 1</h3>
      <span id="text-foot"><a href=""> Linea de direccion 1</a></span><br>
      <span id="text-foot"><a href=""> Delegacion, CP</a></span><br>
      <span id="text-foot"><a href=""> Telefono</a></span><br>
    </div>
    <div class="col-md-2" id="">
      <h3 id="title-foot">Ciudad 2</h3>
      <span id="text-foot"><a href=""> Linea de direccion 1</a></span><br>
      <span id="text-foot"><a href=""> Delegacion, CP</a></span><br>
      <span id="text-foot"><a href=""> Telefono</a></span><br>
      <div class="power">
        <img class='img-responsive' id='powered' src="<?= Yii::$app->request->baseUrl."/img/landing/poweredbylogo.png"?>" alt="" />
      </div>
    </div>
  </div>

  <!-- Slick slider -->
  <!-- <script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script> -->
  <script src="<?= Yii::$app->request->baseUrl."/js/slick/slick.js"?>" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript">
  nc=jQuery.noConflict();
    $(document).on('ready', function() {
      $(".regular").slick({
        dots: true,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 3
      });
      $(".center").slick({
        dots: true,
        infinite: true,
        centerMode: true,
        slidesToShow: 3,
        slidesToScroll: 3
      });
      $(".variable").slick({
        dots: true,
        infinite: true,
        variableWidth: true
      });
    });
  </script>
  <!--end  Slick slider -->
</div>



<!-- Modales -->
<div class="modal fade entrarModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="ModalUnete">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <!-- Sign Secundario-->
      <div class="col-md-12 container-registro" style="margin: auto;" id="modal2">
        <div class="row">
          <div class="col-md-12" id="header-registro">
            <img src="<?= Yii::$app->request->baseUrl."/img/landing/GOFWDCTA.png"?>" class="img-responsive" id="header-img">
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center" id="une">
            <span><small>Únete con <a href="#" class="refSign">Facebook</a>, <a href="#" class="refSign">Google</a> o <a href="#" class="refSign">LinkedIn</a></small></span>
          </div>
        </div>
        <div class="row">
        <!-- Registro de FWD-->
          <form id="form-sigin">
          <div class="col-md-12 divInput">
            <div class="absoluto"></div>
            <input id="name" type="text" placeholder="Nombre(s)" class="form-inputs">
          </div>
          <div class="col-md-12 divInput">
            <div class="absoluto"></div>
            <input id="lastname" type="text" placeholder="Apellido(s)" class="form-inputs">
          </div>
          <div class="col-md-12 divInput">
            <div class="absoluto"></div>
            <input id="email" type="text" placeholder="Correo Electrónico" class="form-inputs">
          </div>
          <div class="col-md-12 divInput">
            <div class="absoluto"></div>
            <input id="password" type="password" placeholder="Password" class="form-inputs">
          </div>
          <div class="col-md-12 divPadding">
            <div class="row">
              <div class="col-md-1">
                <input id="check" type="checkbox" id="check">
              </div>
              <div class="col-md-10">
                <small class="small-text">Me gustaría enterarme de los concursos mas recientes y de noticias de emprendimiento.</small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 divPadding text-center">
              <small class="Xsmall-text">Al unirte, aceptas los <a href="#" class="refSign">Términos de Uso</a> y la <a href="#" class="refSign">Política de Privacidad</a>.</small>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 divPadding text-center">
              <button  id="button-une" type="submit" ><img src="FWDICON.png" height="30px">  ¡ÚNETE!</button>
            </div>
          </div>
        </form>
        </div>
        <div class="dividir"></div>
        <div class="row">
          <div class="col-md-12 divPadding" id="divEntrar">
            <div class="row">
              <div class="col-xs-8 col-sm-7 col-md-7">
                <span><small>¿Ya eres miembro?</small></span>
              </div>
              <div class="col-xs-4 colsm-5 col-md-5">
                <button class="button-entrar"><img src="SignUp.png">    ENTRA</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    
      <!-- Sign Principal-->
      <div class="col-md-12 container-registro" style="margin: auto;" id="modal1">
        <div class="row">
          <div class="col-md-12" id="header-registro">
            <img src="GOFWDCTA.png" class="img-responsive" id="header-img">
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">
            <button class="botonSignR borderFB"><div class="absolutoSign"></div>Con Facebook</button>
          </div>
        </div>
        <div class="dividir"></div>
        <div class="row">
          <div class="col-md-12 text-center">
            <button class="botonSignR borderG"><div class="absolutoSign"></div>Con Google</button>
          </div>
        </div>
        <div class="dividir"></div>
        <div class="row">
          <div class="col-md-12 text-center">
            <button class="botonSignR borderLK"><div class="absolutoSign"></div>Con LinkedIn</button>
          </div>
        </div>
        <div class="dividir"></div>
        <div class="row">
          <div class="col-md-12 text-center">
            <button class="botonSignR borderM" id="mailButton">Con tu e-mail</button>
          </div>
        </div>
      
        <div class="row">
          <div class="col-md-12 divPadding">
            <small class="Xsmall-text"> Al unirte, aceptas los <a href="#" class="refSign">Términos de Uso</a> y la <a href="#" class="refSign">Política de Privacidad</a>.</small>
          </div>
        </div>
        <div class="dividir"></div>
        <div class="row">
          <div class="col-md-12 divPadding divTopLine" id="divEntrar">
          <div class="row">
            <div class="col-xs-8 col-sm-7 col-md-7">
              <span><small>¿Ya eres miembro?</small></span>
            </div>
            <div class="col-xs-4 colsm-5 col-md-5">
              <button class="button-entrar"><img src="SignUp.png">    ENTRA</button>
            </div>
          </div>
          </div>
        </div>
      </div>
      
      <!-- Login -->
      <div class="col-md-12 container-registro" style="margin: auto;" id="modal3">
        <div class="row">
          <div class="col-md-12" id="header-registro">
            <img src="<?= Yii::$app->request->baseUrl."/img/landing/GOFWDCTA.png"?>" class="img-responsive" id="header-img">
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center" id="une">
            <span>Inicia sesión con tu correo electrónico</span><br>
            <span><small class="errorLabel1">Llena todos los campos para iniciar sesión</small></span>
            <span><small class="errorLabel2">Correo o contraseña incorrecta, por favor verifícalo.</small></span>
          </div>
        </div>
        <div class="row">
          <form id="form-loginn">
            <div id="mailInput" class="col-md-12 divInput">
              <div class="absoluto"></div>
              <input id="email" name="email" type="text" placeholder="Correo Electrónico" class="form-inputs">
            </div>
            <div id="passInput" class="col-md-12 divInput">
              <div class="absoluto"></div>
              <input id="password" name="pass" type="password" placeholder="Password" class="form-inputs">
            </div>
            <div class="col-md-12 divPadding">
              <div class="row">
                <div class="col-md-12 divPadding text-center">
                  <button  id="button-loginn" type="submit" ><img src="<?= Yii::$app->request->baseUrl."/img/landing/FWDICON.png"?>" height="30px">Entra</button>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="row">
          <div class="col-md-12 divPadding text-right">
            <a href="<?= Url::toRoute('site/resetpassword');?>">Olvidé mi contraseña</a>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 divPadding">
            <small class="Xsmall-text"> Al unirte, aceptas los <a href="#" class="refSign">Términos de Uso</a> y la <a href="#" class="refSign">Política de Privacidad</a>.</small>
          </div>
        </div>
        <div class="dividir"></div>
        <div class="row">
          <div class="col-md-12 divPadding divTopLine" id="divEntrar">
            <div class="row">
              <div class="col-xs-8 col-sm-7 col-md-7">
                <span><small>¿No eres miembro?</small></span>
              </div>
              <div class="col-xs-4 colsm-5 col-md-5">
                <a href="<?= Url::toRoute('usuario/singup') ?>" class="button-entrar"><img src="<?= Yii::$app->request->baseUrl."/img/landing/SignUp.png"?>">Únete</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--end Modales -->


<?php $this->endBody() ?>
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

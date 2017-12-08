<!DOCTYPE html>
<html>
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>FWD</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->


     <!-- Font Awesome  -->
    <script src="https://use.fontawesome.com/4faa018609.js"></script>

    <!-- Bootstrap CDN -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

    <!-- DateTimePicker Style -->
    <!-- <link href="jquery.datetimepicker.css" rel="stylesheet"> -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Mis estilos -->
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
    <link href="css/thumnail.css" rel="stylesheet" >
    <link href="css/video.css" rel="stylesheet" >
    <!-- <link href="css/headers.css" rel="stylesheet"> -->

    <!-- Fuentes -->
    <!-- <link rel="stylesheet" type="text/css" href="css/stylefonts.css"/> -->

    <!-- Touch Spinner -->

    <!-- <link rel="shortcut icon" href="favicon.ico">
    <link href="prettify.css" rel="stylesheet" type="text/css" media="all">
    <link href="src/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/sweetalert.css" rel="stylesheet">
    <link href="demo.css" rel="stylesheet" type="text/css" media="all"> -->

    <!-- jquery CDN  -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->

    <!-- <script src="prettify.js"></script>
    <script src="src/jquery.bootstrap-touchspin.js"></script> -->
    <!-- end Touch Spinner -->



    <!-- Fuentes -->
    <link rel="stylesheet" href="css/fonts.css" media="screen" title="no title" charset="utf-8">
    <!-- favicon -->
    <link rel="icon" href="img/fwd.png">
    <!-- My script -->
    <script src="js/navFixed.js" charset="utf-8"></script>
    <script src="js/registro.js" charset="utf-8"></script>
    

    <script src="js/service_main.min.js" charset="utf-8"></script>

    <!-- Slick Slider Resources-->
    <link rel="stylesheet" type="text/css" href="slick/slick.css">
  <link rel="stylesheet" type="text/css" href="slick/slick-theme.css">


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
  </style>
  <!-- end Slick Slider Resources-->

  </head>
  <body>
      <div class="center-content">
          <div class="">
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
                          <a class="navbar-brand" href="#"><img class='imgnvBar' src="img/fwd1.png" alt=""></a>
                          <!-- <a class="navbar-brand" href="#"><img class='imgnvBar' src="img/fwd.png" alt=""></a> -->
                      </div>

                      <!-- Collect the nav links, forms, and other content for toggling -->
                      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                          <ul class="nav navbar-nav">
                              <!-- <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
                              <li><a href="#">Link</a></li> -->
                              <!-- <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                              <ul class="dropdown-menu">
                              <li><a href="#">Action</a></li>
                              <li><a href="#">Another action</a></li>
                              <li><a href="#">Something else here</a></li>
                              <li role="separator" class="divider"></li>
                              <li><a href="#">Separated link</a></li>
                              <li role="separator" class="divider"></li>
                              <li><a href="#">One more separated link</a></li>
                          </ul>
                      </li> -->
                  </ul>
                  <!-- <form class="navbar-form navbar-left" role="search">
                  <div class="form-group">
                  <input type="text" class="form-control" placeholder="Search">
              </div>
              <button type="submit" class="btn btn-default">Submit</button>
          </form> -->
          <ul class="nav navbar-nav navbar-right">
              <li class="btn3-navbar"><a href="#">CREA UN CONCURSO</a></li>
              <span>&nbsp</span>
              <li class="btn2-navbar"><a href="#"><img class='icon-unete-nav'   src="img/fwd.png" alt="" />&nbsp¡UNETE!</a></li>
            <li><span>&nbsp</span></li>
              <li class="btn1-navbar" data-toggle="modal" data-target=".entrarModal"><a  href="#"><img class='icon-unete-nav2'  src="img/SignUp.png" alt="" />&nbspENTRA</a></li>
              <li><a href="#">&nbsp</a></li>


              <!-- <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
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

          <div class="col-md-12 centered cover-center" id='' style="display:none;background-image:url('img/background.jpg') !important;height:700px; background-repeat:no-repeat;background-position:center;-webkit-background-size: cover;
          -moz-background-size: cover;
          -o-background-size: cover;
          background-size: cover;">
          <video style="display:none;" autoplay="" loop="" class="fillWidth fadeIn wow collapse in" data-wow-delay="0.5s"  id="video-background">
              <source src="video/oso.mp4" type="video/mp4">


              </video>






                <div class="img-responsivee">
                <img class='img-responsive centered' src="img/gofwd.png" alt="" />
                </div>
                <h4 class="title-sections" style="color:white;position:inherit">LA MAYOR PLATAFORMA DE EMPRENDIMIENTO DE LATINOAMERICA</h4>
                <div class="btn-unete">
                    <h3 class="title-sections" id="text-unete" style="color:white" > <img class='icon-unete'  src="img/fwd.png" alt="" />&nbsp¡UNETE!</h3>

                </div>

                    <div class="header-content2">

                        <ul class="submenu">
                            <li  class="active"><a id="h-submenu" href="#">FWD</a></li>
                            <li><a id="h-submenu" href="#" color="white">TALENT</a></li>
                            <li><a id="h-submenu" href="#" color="white">CHALLENGE</a></li>
                            <li><a id="h-submenu" href="#">EVALUATION</a></li>
                            <li><a id="h-submenu" href="#">BUSSINES</a></li>
                        </ul>

                    </div>



            </div>


<nav class='menu menuu' style="z-index:1000;background-color:white;text-align:center;">

    <div id="navBar"  class="dropdown" >
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
        <a href="">Comunidad FWD</a>
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

      <div id="" style='background-color:white;' class="container-fluid">

          <h1 class="title-sections">&nbsp</h1>
          <h1 class="title-sections">&nbsp</h1>
          <h2 class="title-sections">Noticias FWD</h2>
          <h1 class="title-sections">&nbsp</h1>
          <br>

      <div class="content">
      <div class="row">
        <div class="box" id="newFWD"></div>
      </div>
    </div>


      </div>


<div style="height:50px;background-color: white;">
  
</div>




<div class="row" id="footeer">
      <div class="col-md-4" id="foot-logo">
        <img src="img/FWDLOGO.png">

      </div>
      <div class="col-md-2" id="footeer">
        <h3 id="title-foot">Contacto</h3>

        <h3 id="title-foot">Redes</h3>
        <ul class="redes">
          <li id="text-li">
            <a href="#"><i class="fa fa-facebook-square fa-2x" id="social-icon"><span id="text-foot">Facebook</span></i></a>
          </li>
          <li id="text-li">
            <a  href="#"><i class="fa fa-twitter-square fa-2x" id="social-icon"><span id="text-foot">Twitter</span></i></a>
          </li>

          <li id="text-li">
            <a href="#"><i class="fa fa-linkedin-square fa-2x" id="social-icon"><span id="text-foot">LinkedIn</span></i></a>
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
          <img class='img-responsive' id='powered' src="img/poweredbylogo.png" alt="" />
        </div>


      </div>

    </div>

</div>



<!-- Slick slider -->
<!-- <script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script> -->
<script src="slick/slick.js" type="text/javascript" charset="utf-8"></script>
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

<!-- Modales -->

                <div class="modal fade entrarModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="ModalUnete">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
    <!-- Sign Secundario-->
      <div class="col-md-12 container-registro" style="margin: auto;" id="modal2">
      <div class="row">
        <div class="col-md-12" id="header-registro">
          <img src="GOFWDCTA.png" class="img-responsive" id="header-img">
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
              <button id="button-entrar"><img src="SignUp.png">    ENTRA</button>
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
              <button id="button-entrar"><img src="SignUp.png">    ENTRA</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</div>
<!--end Modales -->





  </body>


  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
   <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> -->

   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
   <!-- <script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script> -->


   <!-- Include all compiled plugins (below), or include individual files as needed -->
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"> </script>
</html>

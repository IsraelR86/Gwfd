<?php 
use yii\helpers\Url;
$id = $_REQUEST['id'];
$data = file_get_contents("http://www.gofwd.mx/web/api/v1/noticias/".$id);
$data = json_decode($data, true);

$this->registerMetaTag(['name' => 'og:url', 'content' => Url::home(true) ]);
$this->registerMetaTag(['name' => 'og:type', 'content' => 'article' ]);
$this->registerMetaTag(['name' => 'og:title', 'content' => $data['titulo'] ]);
$this->registerMetaTag(['name' => 'og:description', 'content' => $data['resumen'] ]);
$this->registerMetaTag(['name' => 'og:image', 'content' => Url::home(true).'img/news/'.$id.'.jpg' ]);

 ?>

  <div class="container-fluid" style="background-image:url('<?php echo $data['portada']; ?>');height:450px; background-repeat:no-repeat;background-position:center;-webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
  ">
  
    <div class="row">
      <div class="col-sm-6 col-md-6"></div>
      <div class="col-sm-6 col-md-6" style="background-color:black; height:450px;opacity:0.7">
        <div class="subtitle-news">
          <img style="text-align:right;width:5%;opacity:100;visibility:hidden;"  src="<?= Yii::$app->request->baseUrl ?>/img/landing/face.png" >
          <img style="text-align:right;width:5%;opacity:100;visibility:hidden;"  src="<?= Yii::$app->request->baseUrl ?>/img/landing/twitter.png" >
          <img style="text-align:right;width:5%;opacity:100;visibility:hidden;"  src="<?= Yii::$app->request->baseUrl ?>/img/landing/Add.png" >
          <h3 style="text-align:right;color:white;opacity:100;">Tag Noticia</h3>
          <h2 style="text-align:center;color:white;"><?php echo $data['titulo']; ?></h2><br><br>
          <h4 style="text-align:center;color:white;opacity:100 !important"><?php echo $data['resumen']; ?></h4>
        </div>
      </div>
    </div>
  </div>

  <br><br><br>
  <div class="container-fluid">
  
    <div class="row">
      <div class="col-sm-3" ></div>
      <div class="col-sm-4">
      <?php echo $data['contenido']; ?>
      </div>
      <div class="col-sm-2" ></div>
      <div class="col-sm-3" >
        <p class="ntc-mod-title"><span>AS Recommends</span></p>
        <!-- <ul class="ntc-list">
        <li class="ntc">
        <article>
        <a href="/en/2016/05/28/football/1464389914_026904.html?omnil=resrelrecom">
        <h3 class="ntc-subtitle">Champions League final</h3>
        <h4 class="ntc-title">Real Madrid - Atlético Madrid Champions League final 2015/16 as it happened</h4>
        </a>
        </article>
        </li>
        <li class="ntc">
        <article>
        <a href="/en/2016/05/29/football/1464476601_365277.html?omnil=resrelrecom">
        <h3 class="ntc-subtitle">REAL MADRID 1 -ATLÉTICO 1 (5-3) | SIMEONE</h3>
        <h4 class="ntc-title">Simeone distraught at losing Champions League final to Real</h4>
        </a>
        </article>
        </li>
        <li class="ntc">
        <article>
        <a href="/en/2016/05/28/album/1464470021_142107.html?omnil=resrelrecom">
        <h3 class="ntc-subtitle">Real Madrid-Atlético</h3>
        <h4 class="ntc-title">Champions League final 2016, Real Madrid-Atlético in pictures</h4>
        </a>
        </article>
        </li>
        <li class="ntc">
        <article>
        <a href="/en/2016/05/29/football/1464474208_099277.html?omnil=resrelrecom">
        <h3 class="ntc-subtitle">REAL MADRID 1 - ATLÉTICO 1</h3>
        <h4 class="ntc-title">Ronaldo says he told Zidane he'd score Champions League winner</h4>
        </a>
        </article>
        </li>
        </ul>
        </div>
        
        </article> -->
      </div>
    </div>
    <div  class="container">
      <img src="<?php echo $data['portada']; ?>" class="img-responsive" alt="Cinque Terre" width="800" height="600">
    </div>
    
    <div  class="container-fluid">
      <div class="row">
        <div class="col-sm-3" ></div>
        <div class="col-sm-4"></div>
        <div class="col-sm-2" ></div>
        <div class="col-sm-3" >
          <p class="ntc-mod-title"><span>AS Recommends</span></p>
        </div>
      </div>
    </div>
    
    <div id="" style='background-color:white;height:60px;' class="container-fluid"></div>
  </div><!--  endContainerfluid -->

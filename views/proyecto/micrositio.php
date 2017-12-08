<?php
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->registerJsFile(Url::to('@web/js/proyecto/micrositio.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/proyecto/show_respuesta_geografica.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerCssFile(Url::to('@web/css/icons_micrositio.css'), ['depends' => [\app\assets\AppAsset::className()]]);

if ($proyecto == null) {
    echo '<h2 class="text-danger text-center">Proyecto no disponible</h2>';
} else {
?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-xs-6 bgImagen" style="background-image: url('<?= $proyecto->byteImagen ?>'); height: 700px">

                <div class="vertical-center text-center">
                    <div class="bgLogo img-circle">
                        <img src="<?= $proyecto->byteLogo ?>" class="img-circle">
                    </div>
                </div>

            </div>

            <div class="col-md-6 col-xs-6 noPadding">

                <div class="col-md-12">
                    <!--<a href="<?= $proyecto->emprendedorCreador->facebook; ?>"><img src="<?= Url::to('@web/img/facebook.png'); ?>"></img></a>-->
                    <!--<a href="<?= $proyecto->emprendedorCreador->twitter; ?>"><img src="<?= Url::to('@web/img/twitter.png'); ?>"></img></a>-->
                </div>

                <div class="col-md-12 noPadding">
                    <div class="title_container">
                        <h3 class="title"><?= $proyecto->nombre ?></h3>
                    </div>
                    <div class="descripcion nano center-block">
                        <p class="nano-content">
                            <?= $proyecto->descripcion ?>
                        </p>
                    </div>

                    <iframe width="100%" height="413" src="<?= $proyecto->url_video ?>" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>

        </div>


        <div class="row">
            <?php foreach($proyecto->ganadores as $ganador): ?>
            <div class="col-md-12">
                <div style="background-image: url('<?= $ganador->idConcurso->byteImagen ?>'); height: 200px;
                background-position: center center; background-size: 100%; position:relative;">
                    <div class="lugar bgBlack">
                        <p><b>1er</b></p>
                    </div>
                </div>
                <div class="bgGray">
                    <p><b><?= $ganador->idConcurso->nombre ?></b></p>
                    <p><?= $ganador->idConcurso->institucion->nombre ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="container bgGray">
        <div class="row menu_seccion bgBlack">
            <div class="col-md-4 col-xs-4">
                <div id="mosaico_secciones">
                    <div class="">
                        <div class="fila item_menu section1 tall-section tooltipster" data-section="1" data-id="1" title="Problema">
                            <span class="custom-icon-1"></span>
                        </div>
                        <div class="fila">
                            <div class="item_menu section1 tiny-section tooltipster" data-section="1" data-id="2" title="Segmento de clientes">
                                <span class="custom-icon-2"></span>
                            </div>
                            <div class="item_menu section1 tiny-section tooltipster" data-section="1" data-id="3" title="Propuesta de valor único">
                                <span class="custom-icon-3"></span>
                            </div>
                        </div>
                        <div class="fila item_menu section4 tall-section tooltipster" data-section="4" data-id="4" title="Solución">
                            <span class="custom-icon-4"></span>
                        </div>
                        <div class="fila">
                            <div class="item_menu section2 tiny-section tooltipster" data-section="2" data-id="5" title="Canales">
                                <span class="custom-icon-5"></span>
                            </div>
                            <div class="item_menu section2 tiny-section tooltipster" data-section="2" data-id="6" title="Fuentes de ingreso">
                                <span class="custom-icon-6"></span>
                            </div>
                        </div>
                        <div class="fila item_menu section2 tall-section tooltipster" data-section="2" data-id="7" title="Estructura de costos">
                            <span class="custom-icon-7"></span>
                        </div>
                    </div>
                    <div class="">
                        <div class="halfWidth item_menu section3 wide-section tooltipster" data-section="3" data-id="8" title="Indicadores de medición">
                            <span class="custom-icon-8"></span>
                        </div>
                        <div class="halfWidth item_menu section3 wide-section tooltipster" data-section="3" data-id="9" title="Ventaja competitiva">
                            <span class="custom-icon-9"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xs-4">
                <h3>LEAN CANVAS</h3>
                <p id="nombre_seccion"></p>
            </div>

        </div>

        <?php
        foreach ($secciones as $seccion) {
            echo '<div class="seccion" data-id="'.$seccion->id.'" data-nombre="'.$seccion->descripcion.'" style="display: none;">';
                $respuestas = $proyecto->getRespuestasBySeccion($seccion->id);
                $noPregunta = 1;

                if (count($respuestas) == 0) {
                    echo 'No hay respuestas para esta sección';
                } else {
                    foreach ($respuestas as $respuesta) {
                        echo '<div class="col-md-6 pregunta">'.$noPregunta.'. '.$respuesta->pregunta->descripcion.'.';

                        echo '<div class="descripcion_respuesta">';


                            foreach ($respuesta->getDescripcionRespuesta() as $desRespuesta) {
                                $extras = '';

                                if ($respuesta->pregunta->tipo_pregunta == 6 ||  // Punto Radial Geográfico
                                    $respuesta->pregunta->tipo_pregunta == 7 ||  // Polígono Geográfico
                                    $respuesta->pregunta->tipo_pregunta == 8 ) { // Punto Geográfico
                                    $desRespuesta = '<span class="glyphicon glyphicon-map-marker icon" aria-hidden="true"></span>'.$desRespuesta;
                                    $extras = ' class="respuesta_geografica cursor-pointer" '.
                                        'data-tipo="'.$respuesta->pregunta->tipo_pregunta.'" '.
                                        'data-puntos=\''.$respuesta->respuesta_geografica.'\' ';
                                }

                                echo '<div '.$extras.'><i class="fa fa-circle icon"></i> '.$desRespuesta.'</div>';
                            }

                        echo '</div></div>';

                        $noPregunta++;
                    }
                }
            echo '</div>';
        }
    echo '</div>';
}
?>
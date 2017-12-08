<?php
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->registerJsFile(Url::to('//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/jquery.easypiechart.min.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/concurso/ganadores.js'), ['depends' => [\app\assets\AppAsset::className()]]);

if ($concurso == null) {
    echo '<h2 class="text-danger text-center">Concurso no disponible</h2>';
} else if (empty($concurso->fecha_resultados) || $concurso->fecha_resultados =='0000-00-00') {
    echo '<h2 class="text-danger text-center">Todavía no se han publicado los resultados del concurso</h2>';
} else {
    $proyectos = $concurso->getResultadosEvaluacionProyectos();
    
    if (count($proyectos) == 0) {
        echo '<h2 class="text-danger text-center">No se encontrarón proyectos ganadores para este concurso</h2>';
    } else {
        ?>
        <div class="row">
            <div class="col-md-8 col-xs-8">&nbsp;</div>
            <div class="col-md-4 col-xs-4 text-center">
                <a href="#"><img src="<?= Url::to('@web/img/facebook.png'); ?>"></img></a>
                <a href="#"><img src="<?= Url::to('@web/img/twitter.png'); ?>"></img></a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-2 col-xs-2">&nbsp;</div>
            <div class="col-md-8 col-xs-8">
                <div class="title_container">
                    <h3 class="title2">GANADORES DEL CONCURSO <br><?= $concurso->nombre ?></h3>
                </div>
            </div>
            <div class="col-md-2 col-xs-2">&nbsp;</div>
        </div>
        
        <div class="row">
            
            <div class="col-md-4 col-xs-4 bgGray noPadding marginLeft">
                <div class="col-md-12 col-xs-12 bgImagen" style="background-image: url('<?= $proyectos[0]->byteImagen ?>');">
                    <div class="vertical-center text-center">
                        <div class="bgLogo img-circle">
                            <img src="<?= $proyectos[0]->byteLogo ?>" class="img-circle">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8 col-xs-8 bgGray marginRight">
                <div class="col-md-6 col-xs-6 noPadding bgGray">
                    <div class="col-md-12 col-xs-12">
                        <h3 class="title2"><?= $proyectos[0]->nombre ?></h3>
                        <div class="field-info">
                            <span class="container-label"><label>DESCRIPCIÓN</label></span>
                        </div>
                        <div class="descripcion nano center-block">
                            <p class="nano-content">
                                <?= $proyectos[0]->descripcion ?>
                            </p>
                        </div>
                        <div class="field-info">
                            <span class="container-label"><label>EQUIPO</label></span>
                        </div>
                        <?PHP 
                        $equipo = '';
                        
                        if (count($proyectos[0]->emprendedores)) {
                            foreach($proyectos[0]->emprendedores as $emprendedor)
                            {
                                $equipo .= $emprendedor->usuario->nombre_completo.' / ';
                            }
                            echo '<p>'.substr($equipo, 0, strlen($equipo)-3).'</p>';
                        }
                        ?>
                        <br>
                        <div class="field-info">
                            <span class="container-label"><label>SOCIAL</label></span>
                        </div>
                        <p>
                            <a href="<?= $proyectos[0]->emprendedorCreador->facebook; ?>"><img src="<?= Url::to('@web/img/facebook.png'); ?>"></img></a>
                            <a href="<?= $proyectos[0]->emprendedorCreador->twitter; ?>"><img src="<?= Url::to('@web/img/twitter.png'); ?>"></img></a>
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-xs-6 noPaddingLeft ">
                    <h3 class="text-center title2">
                        <img src="http://placehold.it/150x150" class="img-circle">
                    </h3>
                    
                    <div class="puntajesGanador">
                        <table>
                            <?php 
                                $concursoAplicado = $proyectos[0]->getConcursoAplicado($concurso->id);
                                $puntajeRubricas = $concursoAplicado->getPuntajeByRubrica();
                                
                                foreach ($puntajeRubricas as $puntaje) {
                                    echo '<tr><td class="col-md-8 col-xs-8">'.$puntaje['nombre'].'</td>
                                        <td class="col-md-4 col-xs-4">
                                            <div class="easy-pie-chart chartPuntaje" data-percent="'.round(($puntaje['puntaje']/$puntaje['calificacion_maxima'])*100).'" data-scale-color="#ffb400">
                                                <span class="percent"></span>
                                            </div>
                                        </td></tr>';
                                }
                            ?>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>

        <?php
        $noProyectosAMostrar = $concurso->no_ganadores-1;

        for ($i = 1; $i <= $noProyectosAMostrar; $i++) {
            if ($proyectos[$i] != null) {
                $concursoAplicado = $proyectos[$i]->getConcursoAplicado($concurso->id);
                $puntajeTotal = $concursoAplicado->getPuntajeTotal();
                ?>
                <div class="col-md-4 col-xs-4 itemGanadores">
                    <div class="col-md-12 col-xs-12 noPadding">
                        <div class="col-md-12 col-xs-12 bgImagen proyectosGanadores" style="background-image: url('<?= $proyectos[$i]->byteImagen ?>');">
                            <img src="http://placehold.it/80x80" class="img-circle pull-right">
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 bgGray">
                        <div class="col-md-8 col-xs-8">
                            <br>
                            <p><strong><?= $proyectos[$i]->nombre ?></strong></p>
                            <p>Por: <?= $proyectos[$i]->emprendedorCreador->usuario->nombre_completo; ?></p>
                            <p><?= $proyectos[$i]->emprendedorCreador->estado->descripcion; ?></p>
                        </div>
                        
                        <div class="col-md-4 col-xs-4">
                            <div class="easy-pie-chart chartPuntaje" data-percent="<?= round(($puntajeTotal['puntaje']/$puntajeTotal['calificacion_maxima'])*100) ?>" data-scale-color="#ffb400">
                                <span class="percent"></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
            }
        }
        ?>
    <?php 
    }
}
?>
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use app\helpers\MyHtml;
use app\helpers\Functions;
use app\models\RespuestaConcurso;
use app\models\ConcursoAplicado;

$this->registerJsFile(Url::to('@web/js/plugins/jquery.fileDownload.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/institucion/emprendedores.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJs('var urlDownloadRespuestaArchivo = "'.Url::toRoute('proyecto/downloadrespuestaarchivo').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSetStatusAplicacion = "'.Url::toRoute('proyecto/setstatusaplicacion').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlAsignarEvaluadores = "'.Url::toRoute('concurso/asignarevaluadores').'";', \yii\web\View::POS_HEAD);

?>
<div class="title_container">
    <h2 class="title">Emprendedores del Concurso <?= $concurso->nombre ?></h2>
</div>

<!--<div class="text-center" id="loadingMsg">
    <i class="fa fa-spinner fa-pulse fa-lg"></i>
</div>-->

<div>
    <div class="row">
        <div class="col-md-12">


            <?php
            if (Functions::compareDates($concurso->fecha_arranque, date('Y-m-d'))<0) {
                echo '<div class="text-center text-danger">Todavía no puede visualizar los proyectos, la fecha de arranque del concurso es '.Functions::transformDate($concurso->fecha_arranque, 'd-m-Y').'</div>';
            }
            else if (count($proyectos) == 0 && count($proyectosNoAprobados) == 0) {
                echo '<div class="text-center">No se encontraron proyectos aplicados a este concurso</div>';
            } else {

            ?>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center"><strong>Proyecto</strong></th>
                        <th class="text-center"><strong>Emprendedor</strong></th>
                        <?php
                        if (count($preguntas)) {
                            foreach($preguntas as $pregunta) {
                                echo '<th class="text-center"><strong>'.$pregunta->descripcion.'</strong></th>';
                            }
                        }
                        ?>
                        <th class="text-center"><strong>Fecha de Aplicación</strong></th>
                        <th class="text-center"><strong>Puntuación Cuestionario</strong></th>
                        <th class="text-center"><strong>Aprobado</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < count($proyectos); $i++) {
                        $concursoAplicado = ConcursoAplicado::getConcursoAplicado($proyectos[$i]->id, $concurso->id);

                        echo '<tr>'.
                            '<td>'.$proyectos[$i]->nombre.'</td>'.
                            '<td>'.$proyectos[$i]->emprendedorCreador->usuario->nombre_completo.'</td>';

                            if (count($preguntas)) {
                                foreach($preguntas as $pregunta) {
                                    echo '<td class="text-center">';
                                    switch($pregunta->id_tipo_pregunta_concurso) {
                                        case '1': // Texto
                                            $respuesta = RespuestaConcurso::find()
                                                ->where(['id_concurso' => $concurso->id,
                                                    'id_proyecto' => $proyectos[$i]->id,
                                                    'id_pregunta' => $pregunta->id,
                                                    'solo_concurso' => '1'
                                                ])->one();

                                            if (!empty($respuesta)) {
                                                echo $respuesta->respuesta_texto;
                                            } else {
                                                echo '<span class="no-set">Sin respuesta</span>';
                                            }

                                            break;
                                        case '2': // Archivo
                                            echo '<a href="#" class="icon btnDownloadArchivo"
                                                data-concurso="'.$concurso->id.'" data-proyecto="'.$proyectos[$i]->id.'" data-pregunta ="'.$pregunta->id.'"><i class="fa fa-arrow-circle-o-down fa-lg"></i></a>';
                                            break;
                                    }
                                    echo '</td>';
                                }
                            }

                        echo '<td class="text-center">'.Yii::$app->formatter->asDate($concursoAplicado->fecha_alta).'</td>'.
                            '<td class="text-center">'.$proyectos[$i]->sumRespuestasPonderacion.'</td>'.
                            '<td class="text-center">';
                        if (Functions::compareDates($concurso->fecha_cierre, date('Y-m-d'))>0 && empty($concurso->fecha_resultados)) {
                            echo '<input type="checkbox" class="statusProyecto" value="1"
                                data-concurso="'.$concurso->id.'" data-proyecto="'.$proyectos[$i]->id.'" '.($concursoAplicado->paso_filtros==1 ? 'checked' : '').' /></td>';
                        } else {
                            echo ($concursoAplicado->paso_filtros==1 ? 'Si' : 'No');
                        }

                        echo '</tr>';
                    }

                    for ($i = 0; $i < count($proyectosNoAprobados); $i++) {
                        $concursoAplicado = ConcursoAplicado::getConcursoAplicado($proyectosNoAprobados[$i]->id, $concurso->id);

                        echo '<tr>'.
                            '<td>'.$proyectosNoAprobados[$i]->nombre.'</td>'.
                            '<td>'.$proyectosNoAprobados[$i]->emprendedorCreador->usuario->nombre_completo.'</td>';

                            if (count($preguntas)) {
                                foreach($preguntas as $pregunta) {
                                    echo '<td class="text-center">';
                                    switch($pregunta->id_tipo_pregunta_concurso) {
                                        case '1': // Texto
                                            $respuesta = RespuestaConcurso::find()
                                                ->where(['id_concurso' => $concurso->id,
                                                    'id_proyecto' => $proyectosNoAprobados[$i]->id,
                                                    'id_pregunta' => $pregunta->id,
                                                    'solo_concurso' => '1'
                                                ])->one();

                                            if (!empty($respuesta)) {
                                                echo $respuesta->respuesta_texto;
                                            } else {
                                                echo '<span class="no-set">Sin respuesta</span>';
                                            }

                                            break;
                                        case '2': // Archivo
                                            echo '<a href="#" class="icon btnDownloadArchivo"
                                                data-concurso="'.$concurso->id.'" data-proyecto="'.$proyectosNoAprobados[$i]->id.'" data-pregunta ="'.$pregunta->id.'"><i class="fa fa-arrow-circle-o-down fa-lg"></i></a>';
                                            break;
                                    }
                                    echo '</td>';
                                }
                            }

                        $filtros_no_pasados = json_decode($concursoAplicado->filtros_no_pasados);

                        echo '<td class="text-center">'.Yii::$app->formatter->asDate($concursoAplicado->fecha_alta).'</td>'.
                            '<td class="text-center">'.$proyectosNoAprobados[$i]->sumRespuestasPonderacion.'</td>'.
                            '<td class="text-center">'.$filtros_no_pasados->descripcion.'</td>'.
                        '</tr>';
                    }
                    ?>
                </tbody>
            </table>
            <?php
            }
            ?>
        </div>
    </div>

    <?php
    if (Functions::compareDates($concurso->fecha_cierre, date('Y-m-d'))<=0) {
        echo '<div class="text-center text-danger">Todavía no puede evaluar los proyectos, la fecha de cierre del concurso es '.Functions::transformDate($concurso->fecha_cierre, 'd-m-Y').'</div>';
    }
    else if (Functions::compareDates($concurso->fecha_resultados, date('Y-m-d'))>0 && !empty($concurso->fecha_resultados)) {
        echo '<div class="text-center text-danger">Ya no puede evaluar los proyectos, la fecha de resultados del concurso fué '.Functions::transformDate($concurso->fecha_resultados, 'd-m-Y').'</div>';
    }
    else if (count($proyectos) != 0) {
        echo '<div class="text-center">
            <a class="btnBorderRed inline-block fontBlack" href="#" id="btnAsignarEvaluadores" data-id="'.$concurso->id.'" >
                Asignar Evaluadores
            </a>
        </div>';
    }
    ?>
    <p>&nbsp;</p>
</div>
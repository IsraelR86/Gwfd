<?php
use yii\helpers\Url;
use yii\bootstrap\Modal;
use app\models\Rubrica;
use app\models\Evaluaciones;
use app\models\Evaluador;

$this->registerJs('var urlGetPuntaje = "'.Url::toRoute('concurso/getpuntaje').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlMicrositio = "'.Url::toRoute('proyecto/micrositio').'";', \yii\web\View::POS_HEAD);
$this->registerJsFile(Url::to('@web/js/evaluador/misevaluaciones.js'), ['depends' => [\app\assets\AppAsset::className()]]);

echo $this->renderFile('@app/views/templates/modal-puntaje-proyecto-tpl.php');
?>

<div class="title_container">
    <h2 class="title">EVALUACIONES</h2>
</div>

    <div class="col-md-12">
       <table class="table table-striped tableProyectosEvaluador">
            <thead>
                <tr>
                    <th class="col-md-3"><strong>CONCURSO</strong></th>
                    <th class="col-md-3"><strong>PROYECTO</strong></th>
                    <th class="col-md-4"><strong>DESCRIPCIÓN</strong></th>
                    <th class="col-md-2"><strong>ESTADO</strong></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $concursos = Yii::$app->user->identity->evaluador->getConcursosActivos();
                    foreach ($concursos as $concurso) {
                        $proyectos = $concurso->getProyectosEvaluador(Yii::$app->user->id);
                        $primeraFila = true;
                        $tr = '';
                        
                        if (count($proyectos)) {
                            foreach ($proyectos as $proyecto) {
                                if ($primeraFila) {
                                    $tr .= '<tr>
                                        <td rowspan="'.count($proyectos).'" class="bTopBlue">'.$concurso->nombre.'</td>';
                                } else {
                                    $tr .= '<tr>';
                                }
                                
                                $tr .= '<td>'.$proyecto->nombre.'</td>
                                    <td>'.$proyecto->descripcion.'</td>
                                    <td><div class="col-md-12 col-xs-12 text-center noPadding">
                                            <a class="fontBlack ';
                                    
                                $totalRubricas = count($concurso->rubricas);
                                $totalEvaluadas = count(Evaluaciones::getRubricasEvaluadas($proyecto->id, $concurso->id, Yii::$app->user->id));
                                
                                if ($totalEvaluadas == 0) {
                                    $tr .= 'btnBorderRed" href="'.Url::toRoute('evaluacionproyecto').'?c='.$concurso->id.'&p='.$proyecto->id.'">EVALUAR';
                                } else if ($totalEvaluadas < $totalRubricas) {
                                    $tr .= 'btnBorderRed" href="'.Url::toRoute('evaluacionproyecto').'?c='.$concurso->id.'&p='.$proyecto->id.'">EN PROCESO';
                                } else {
                                    $evaluacionFinal = Evaluador::getEvaluacionesByProyectos($concurso->id, Yii::$app->user->id, $proyecto->id);
                                    $tr .= 'colorBlack no-link evaluacionFinalizada" href="#" data-c="'.$concurso->id.'" data-p="'.$proyecto->id.'">FINALIZADO <br>'.
                                        $evaluacionFinal[0]['puntaje'].'/'.$evaluacionFinal[0]['calificacion_maxima'].' Puntos';
                                }
                                        
                                $tr .= '</a>
                                        </div>
                                    </td>
                                </tr>';
                                
                                $primeraFila = false;
                            }
                        }
                        
                        echo $tr;
                    }
                ?>
            </tbody>
        </table>
    </div>

<?php 
Modal::begin([
    'id' => 'modalPuntajeProyecto',
    'size' => Modal::SIZE_LARGE,
    'headerOptions' => ['class' => 'no-border'],
]);

Modal::end();
?>
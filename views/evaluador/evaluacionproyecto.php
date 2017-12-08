<?php
use app\models\Pregunta;
use app\helpers\MyHtml;
use yii\helpers\Url;

$this->registerCssFile(Url::to('@web/js/plugins/gmap3/gmap3-menu.css', ['depends' => [\yii\bootstrap\BootstrapAsset::className()]]));
$this->registerJsFile(Url::to('//maps.google.com/maps/api/js?key=AIzaSyA7R0-dIqqboUBfhemO-b2Knqbb0OnR9Io&language=es'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/gmaps.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/gmap3/gmap3.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/gmap3/gmap3-menu.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/emprendedor/pregunta_geografica.js?rand='.rand()), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/proyecto/show_respuesta_geografica.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/evaluador/evaluacionproyecto.js'), ['depends' => [\app\assets\AppAsset::className()]]);

$this->registerJs('var urlGetRubricasEvaluar = "'.Url::toRoute('rubrica/getrubricasevaluar').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSetEvaluacionRubrica = "'.Url::toRoute('rubrica/setevaluacion').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var concurso = "'.Yii::$app->request->get('c').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var proyecto = "'.Yii::$app->request->get('p').'";', \yii\web\View::POS_HEAD);

echo $this->renderFile('@app/views/templates/evaluacion-proyecto-tpl.php');
?>
<div class="title_container">
    <div class="title">
        <h2>
            <?= $proyecto->nombre ?>
        </h2>
        <h5>
          <!--  Para evaluar el concurso, analiza la información del proyecto asignado-->
                Para evaluar el “Proyecto de Emprendimiento”, analice la información que se presenta
        </h5>
    </div>
</div>

<div class="field-info">
    <span class="container-label"><label>PROYECTO</label></span>
    <span class="container-text">
        <p class="text-center"><strong><?= $proyecto->nombre?></strong></p>
        <p><?= $proyecto->descripcion ?></p>
    </span>
</div>

<div id="evaluacion_rubrica">

    <p class="text-center"><i class="fa fa-pulse fa-spinner fa-2x"></i></p>

</div>

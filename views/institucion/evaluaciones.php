<?php
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */

$this->registerJsFile(Url::to('@web/js/plugins/waterfall/waterfall.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/institucion/evaluaciones.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);

// URLs utilizadas para cargas los datos de los concursos
$this->registerJs('var urlGetAllAvailables = "'.Url::toRoute('concurso/getbyinstitucion').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetEvaluaciones = "'.Url::toRoute('concurso/getevaluacionesbyevaluador').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGanadores = "'.Url::toRoute('concurso/ganadores').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetPuntaje = "'.Url::toRoute('concurso/getpuntaje').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlMicrositio = "'.Url::toRoute('proyecto/micrositio').'";', \yii\web\View::POS_HEAD);

// Cargamos los templates HTML utilizados por el motor de plantillas Handlebars
echo $this->renderFile('@app/views/templates/waterfall-evaluaciones-tpl.php');
echo $this->renderFile('@app/views/templates/modal-evaluaciones-tpl.php');
echo $this->renderFile('@app/views/templates/modal-puntaje-proyecto-tpl.php');

Modal::begin([
    'id' => 'modalEvaluaciones',
    'size' => Modal::SIZE_LARGE,
    'headerOptions' => ['class' => 'no-border'],
    'clientOptions' => ['backdrop' => 'static'],
]);

Modal::end();
?>

<div class="title_container">
    <h2 class="title">EVALUACIONES</h2>
</div>

<div class="waterfall" id="waterfall"></div>
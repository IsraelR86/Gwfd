<?php
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */

$this->registerJsFile(Url::to('@web/js/plugins/waterfall/waterfall.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/emprendedor/misconcursos.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);

// URLs utilizadas para cargas los datos de los concursos
$this->registerJs('var urlGetAll = "'.Url::toRoute('concurso/getbyemprendedor').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetAplicacion = "'.Url::toRoute('concurso/getaplicacion').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlAbandonarConcurso = "'.Url::toRoute('concurso/abandonar').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetPuntaje = "'.Url::toRoute('concurso/getpuntaje').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlMicrositio = "'.Url::toRoute('proyecto/micrositio').'";', \yii\web\View::POS_HEAD);

// Cargamos los templates HTML utilizados por el motor de plantillas Handlebars
echo $this->renderFile('@app/views/templates/waterfall-mi-concurso-tpl.php');
echo $this->renderFile('@app/views/templates/modal-aplicacion-tpl.php');
echo $this->renderFile('@app/views/templates/modal-puntaje-proyecto-tpl.php');

Modal::begin([
    'id' => 'modalInfoConcurso',
    'size' => Modal::SIZE_LARGE,
    'headerOptions' => ['class' => 'no-border'],
    'clientOptions' => ['backdrop' => 'static'],
]);

Modal::end();

Modal::begin([
    'id' => 'modalPuntajeProyecto',
    'size' => Modal::SIZE_LARGE,
    'headerOptions' => ['class' => 'no-border'],
    'clientOptions' => ['backdrop' => 'static'],
]);

Modal::end();
?>

<div class="title_container">
    <h2 class="title">MIS CONCURSOS</h2>
</div>

<div class="waterfall" id="waterfall"></div>
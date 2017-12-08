<?php
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */

$this->registerJsFile(Url::to('@web/js/plugins/waterfall/waterfall.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/institucion/concursos.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/jquery.fileDownload.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/jquery.easypiechart.min.js'), ['depends' => [\app\assets\AppAsset::className()]]);

// URLs utilizadas para cargas los datos de los concursos
$this->registerJs('var urlGetAllAvailables = "'.Url::toRoute('concurso/getbyinstitucion').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetById = "'.Url::toRoute('concurso/getbyid').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlCancelarConcurso = "'.Url::toRoute('concurso/cancelar').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetEvaluadores = "'.Url::toRoute('concurso/getevaluadores').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetRubricas = "'.Url::toRoute('concurso/getrubricas').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSendRubricas = "'.Url::toRoute('concurso/setrubricas').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlPublicar = "'.Url::toRoute('concurso/publicar').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlDownloadBases = "'.Url::toRoute('concurso/downloadbases').'";', \yii\web\View::POS_HEAD);
//$this->registerJs('var urlAsignarEvaluadores = "'.Url::toRoute('concurso/asignarevaluadores').'";', \yii\web\View::POS_HEAD);

// Cargamos los templates HTML utilizados por el motor de plantillas Handlebars
echo $this->renderFile('@app/views/templates/waterfall-concurso-tpl.php');
echo $this->renderFile('@app/views/templates/modal-mi-concurso-tpl.php');
//echo $this->renderFile('@app/views/templates/modal-preguntas-concurso-tpl.php');

Modal::begin([
    'id' => 'modalInfoConcurso',
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
<?php
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */

$this->registerJsFile(Url::to('@web/js/plugins/waterfall/waterfall.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/emprendedor/misproyectos.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/jquery.easypiechart.min.js'), ['depends' => [\app\assets\AppAsset::className()]]);

// URLs utilizadas para cargas los datos de los proyectos
$this->registerJs('var urlGetAll = "'.Url::toRoute('proyecto/getallbyemprendedor').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetById = "'.Url::toRoute('proyecto/getbyemprendedor').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlMicrositio = "'.Url::toRoute('proyecto/micrositio').'";', \yii\web\View::POS_HEAD);

// Cargamos los templates HTML utilizados por el motor de plantillas Handlebars
echo $this->renderFile('@app/views/templates/waterfall-proyecto-tpl.php');
echo $this->renderFile('@app/views/templates/modal-info-proyecto-tpl.php');

Modal::begin([
    'id' => 'modalInfoProyecto',
    'size' => Modal::SIZE_LARGE,
    'headerOptions' => ['class' => 'no-border'],
    //'clientOptions' => ['backdrop' => 'static'],
]);

Modal::end();
?>

<div class="title_container">
    <h2 class="title">¡MIS PROYECTOS!</h2>
</div>

<div class="waterfall" id="waterfall"></div>
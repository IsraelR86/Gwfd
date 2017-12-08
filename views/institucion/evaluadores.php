<?php
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */

$this->registerJsFile(Url::to('@web/js/plugins/waterfall/waterfall.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/institucion/evaluadores.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/jquery.easypiechart.min.js'), ['depends' => [\app\assets\AppAsset::className()]]);

// URLs utilizadas para cargas los datos de los evaluadores
$this->registerJs('var urlGetAll = "'.Url::toRoute('institucion/getevaluadores').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGet = "'.Url::toRoute('evaluador/get').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlDelete = "'.Url::toRoute('institucion/delevaluador').'";', \yii\web\View::POS_HEAD);

// Cargamos los templates HTML utilizados por el motor de plantillas Handlebars
echo $this->renderFile('@app/views/templates/waterfall-evaluadores-tpl.php');
echo $this->renderFile('@app/views/templates/modal-evaluador-tpl.php');

Modal::begin([
    'id' => 'modalEvaluador',
    'size' => Modal::SIZE_LARGE,
    'headerOptions' => ['class' => 'no-border'],
    'clientOptions' => ['backdrop' => 'static'],
]);

Modal::end();
?>

<div class="title_container">
    <h2 class="title">EVALUADORES</h2>
</div>

<div class="waterfall" id="waterfall"></div>
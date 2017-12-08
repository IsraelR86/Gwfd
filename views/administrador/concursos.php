<?php
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */

$this->registerJsFile(Url::to('@web/js/plugins/waterfall/waterfall.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/administrador/concursos.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);

// URLs utilizadas para cargas los datos de los concursos
$this->registerJs('var urlGetAll = "'.Url::toRoute('concurso/getall').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetById = "'.Url::toRoute('concurso/getbyid').'";', \yii\web\View::POS_HEAD);

// Cargamos los templates HTML utilizados por el motor de plantillas Handlebars
echo $this->renderFile('@app/views/templates/waterfall-admin-concurso-tpl.php');
echo $this->renderFile('@app/views/templates/modal-concurso-tpl.php');

Modal::begin([
    'id' => 'modalInfoConcurso',
    'size' => Modal::SIZE_LARGE,
    'headerOptions' => ['class' => 'no-border'],
    'clientOptions' => ['backdrop' => 'static'],
]);

Modal::end();
?>

<div class="title_container">
    <h2 class="title">CONCURSOS</h2>
</div>

<div class="waterfall" id="waterfall"></div>
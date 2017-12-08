<?php

use app\helpers\MyHtml;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->registerJsFile(Url::to('@web/js/administrador/filtros_preguntas.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);

// URLs utilizada para cargar las secciones y sus respectivas preguntas
$this->registerJs('var urlGetAll = "'.Url::toRoute('seccion/getall').'";', \yii\web\View::POS_HEAD);

// URLs utilizada para enviar los criterios de las preguntas
$this->registerJs('var urlSetFiltros = "'.Url::toRoute('filtroconcurso/setfiltros').'";', \yii\web\View::POS_HEAD);

// URLs utilizada para obtener los criterios de las preguntas
$this->registerJs('var urlGetFiltros = "'.Url::toRoute('filtroconcurso/getfiltros').'";', \yii\web\View::POS_HEAD);

// Cargamos el templates HTML utilizado por el motor de plantillas Handlebars
echo $this->renderFile('@app/views/templates/filtros-preguntas-tpl.php');

echo '<div class="title_container">
    <h2 class="title">Filtros por Pregunta</h2>
</div>';
?>

<div id="loadingFiltros" class="text-center"></div>
<div id="container_tbl_preguntas"></div>

<div class="text-center">
    <a id="btnGuardarCriterios" href="#" class="btnBorderRed fontBlack">
        <i class="fa fa-check"></i> Guardar Criterios
    </a>
</div>
<?php

use app\helpers\MyHtml;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->registerJsFile(Url::to('@web/js/administrador/preguntas_rubrica.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);

// URLs utilizada para cargar las secciones y sus respectivas preguntas
$this->registerJs('var urlGetAll = "'.Url::toRoute('seccion/getall').'";', \yii\web\View::POS_HEAD);

// URLs utilizada para cargar las rubricas del concurso
$this->registerJs('var urlGetByConcurso = "'.Url::toRoute('rubrica/getbyconcurso').'";', \yii\web\View::POS_HEAD);

// URLs utilizada para enviar las preguntas de la rúbrica
$this->registerJs('var urlSetPreguntas = "'.Url::toRoute('rubrica/setpreguntas').'";', \yii\web\View::POS_HEAD);

$this->registerJs('var urlConcursos = "'.Url::toRoute('administrador/concursos').'";', \yii\web\View::POS_HEAD);

// Cargamos el templates HTML utilizado por el motor de plantillas Handlebars
echo $this->renderFile('@app/views/templates/preguntas-rubrica-tpl.php');

echo '<div class="title_container">
    <h2 class="title">Preguntas por Rúbrica</h2>
</div>';
?>

<h3 id="title_rubrica" class="text-center"></h3>

<p id="desc_rubrica"></p>

<div id="container_tbl_preguntas_rubricas"></div>

<?= MyHtml::pager('wide pagerSeccion') ?>
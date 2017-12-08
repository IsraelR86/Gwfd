<?php
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */

$this->registerJsFile(Url::to('@web/js/plugins/waterfall/waterfall.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/emprendedor/aplica.js'), ['depends' => [\yii\web\JqueryAsset::className(), \app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/jquery.fileDownload.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerCssFile('//hayageek.github.io/jQuery-Upload-File/4.0.8/uploadfile.css');
$this->registerJsFile('//hayageek.github.io/jQuery-Upload-File/4.0.8/jquery.uploadfile.min.js', ['depends' => [\app\assets\AppAsset::className()]]);

// URLs utilizadas para cargas los datos de los concursos
$this->registerJs('var urlGetAllAvailables = "'.Url::toRoute('concurso/getallavailables').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetById = "'.Url::toRoute('concurso/getbyid').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetAllProyectos = "'.Url::toRoute('proyecto/getallbyemprendedor').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlAplicar = "'.Url::toRoute('concurso/aplicar').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlConfirmAplicar = "'.Url::toRoute('concurso/confirmaplicar').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSetPreguntasConcurso = "'.Url::toRoute('concurso/setpreguntas').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlDownloadBases = "'.Url::toRoute('concurso/downloadbases').'";', \yii\web\View::POS_HEAD);

// Cargamos los templates HTML utilizados por el motor de plantillas Handlebars
echo $this->renderFile('@app/views/templates/waterfall-concurso-tpl.php');
echo $this->renderFile('@app/views/templates/modal-concurso-tpl.php');
echo $this->renderFile('@app/views/templates/modal-preguntas-concurso-tpl.php');

if (Yii::$app->session->hasFlash('alert')) {
    $this->registerJs("$.jAlert({
		'title': 'Completa tu Perfil',
		'content': '<div class=\"text-justify\">".Yii::$app->session->getFlash('alert')."</div>',
		'theme': 'red'
	});");
}

Modal::begin([
    'id' => 'modalInfoConcurso',
    'size' => Modal::SIZE_LARGE,
    'headerOptions' => ['class' => 'no-border'],
    //'clientOptions' => ['backdrop' => 'static'],
]);

Modal::end();

Modal::begin([
    'id' => 'modalConfirmAplica',
    'headerOptions' => ['class' => 'no-border'],
]);

    echo '<div class="title_container">
            <h2 class="title">¡FELICIDADES!</h2>
        </div>
        <div clas="col-md-12 col-sm-12">
            <p class="text-center">La inscripción ha sido exitosa, te llegará un mail con los detalles de tu participación. <br><br><strong>¡Vamos adelante!</strong>   <i class="fa fa-thumbs-o-up fa-2x"></i></p>
            <p class="text-center"><a class="btn btn-danger" data-dismiss="modal">Aceptar <i class="fa fa-check"></i></a></p>
        </div>';

Modal::end();
?>

<div class="title_container">
    <h2 class="title">¡APLICA!</h2>
</div>

<div class="waterfall" id="waterfall"></div>
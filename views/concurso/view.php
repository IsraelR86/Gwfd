<?php

use yii\helpers\Url;

$this->registerJsFile(Url::to('@web/js/plugins/jquery.fileDownload.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile(Url::to('@web/js/concurso/view.js'), ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJs('var urlDownloadBases = "'.Url::toRoute('concurso/downloadbases').'";', \yii\web\View::POS_HEAD);

?>

<div class="row">
    
    <div class="col-md-4 col-xs-4 bgGray noPadding">
        <div class="col-md-12 col-xs-12 bgImagen" style="background-image: url('<?= $model->byteImagen ?>');" ?>
            <div class="vertical-center text-center">
                
            </div>
        </div>
    </div>
    
    <div class="col-md-8 col-xs-8 bgGray">
            <div class="col-md-12 col-xs-12">
                <h3><strong>
                    <?= $model->nombre; ?>
                    </strong>
                </h3>
                <h4>
                <?= $model->institucion->nombre ?>
                </h4>
                <p>&nbsp;</p>
            </div>

            <div class="col-md-12 col-xs-12">
                <div class="field-info">
                    <span class="container-label"><label>DESCRIPCIÓN</label></span>
                </div>
                <p>
                    <?= $model->descripcion; ?>,
                </p>
                <br>
            </div>
            
            <div class="col-md-12 col-xs-12">
                <div class="field-info">
                    <span class="container-label"><label>BASES</label></span>
                </div>
                <p>
                    Descarga las bases del concurso aquí. 
                    <span class="pull-right icon btnDownloadBases" data-id="<?= $model->id ?>"><i class="fa fa-arrow-circle-o-down fa-lg"></i> &nbsp; </span>
                </p>
                <br>
            </div>
            
            <div class="col-md-12 col-xs-12">
                <div class="field-info">
                    <span class="container-label"><label>LÍMITE(s)</label></span>
                </div>
                <p>
                    <?= $model->fechaArranque.' - '.$model->fechaCierre; ?>
                </p>
                <br>
            </div>

            <div class="col-md-12 col-xs-12">
                <div class="field-info">
                    <span class="container-label"><label>PREMIOS</label></span>
                </div>
                <p>
                    <?= $model->premios; ?>
                </p>
                <br>
            </div>

    </div>
    
</div>

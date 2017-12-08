<?php 
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="modal-info-concurso-eva-tpl">
<div class="container-fluid item">
    <div class="row">
        <div class="col-md-4 noPadding bgBodyInfo">
            <div class="header">
                <span class="title">{{nombre}}</span>
                <img src="{{byteImagen}}" height="300">
            </div>
            
            <div class="body">
                <div class="col-md-12 col-xs-12 text-center noPadding">
                    <a class="btnBorderRed fontBlack width95porc btnProyectosRegistrados" href="#" data-id="{{id}}">
                        {{proyectosRegistrados}} PROYECTOS REGISTRADOS
                    </a>
                </div>
                <div class="col-md-12 col-xs-12 text-center noPadding">
                    <a class="btnBorderRed fontBlack width95porc btnProyectosCompletados" href="<?= Url::toRoute(['concurso/view']); ?>/{{id}}" data-id="{{id}}">
                        VER MICROSITIO
                    </a>
                </div>
                
                <div class="col-md-12 col-xs-12 text-center noPadding">
                    <span class="date bgColorTurquoise width95porc btnEvaluasConcurso" href="#" data-id="{{id}}" style="display: none;">
                        YA EVALUAS ESTE CONCURSO
                    </span>
                </div>
                
            </div>
        </div>
        <div class="col-md-8 containerConcurso">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            
            <div class="title_container">
                <h2 class="title">{{nombre}}</h2>
            </div>
            
            <div class="body_mi_concurso">

                <div>
                    En esta ficha puedes ver la info general del concurso.
                </div>
                <p>&nbsp;</p>
                
                <div class="progess-bar bgBlack"></div>
                
                <div class="field-info">
                    <span class="container-label"><label>DESCRIPCIÓN</label></span>
                    <span class="container-text height-scroll">
                        <div class="text-center"><strong>{{institucion.nombre}}</strong></div> {{descripcion}}
                    </span>
                </div>
                
                <div class="field-info">
                    <span class="container-label"><label>BASES</label></span>
                    <span class="container-text">
                        Descarga las bases del concurso aquí. 
                        <span class="pull-right icon btnDownloadBases" data-id="{{id}}"><i class="fa fa-arrow-circle-o-down fa-lg"></i> &nbsp; </span>
                    </span>
                </div>
                
                <div class="field-info">
                    <span class="container-label"><label>LÍMITE(s)</label></span>
                    <span class="container-text">{{fechaArranque}} al {{fechaCierre}}</span>
                </div>
                
                <div class="field-info">
                    <span class="container-label"><label>PREMIOS</label></span>
                    <span class="container-text">{{premios}}</span>
                </div>
                
            </div>
        </div>
    </div>
    <div class="row footer">
    
        
            <div class="col-md-3 noPaddingLeft">
                <p class="tag">
                    {{#join_slash etiquetas}}{{descripcion}}{{/join_slash}}
                </p>
            </div>
            <div class="col-md-9 text-right menu">
                {{#ifCond status '==' 'EN PROCESO'}}
                    <a class="btnBorderRed btnAplica" href="#" data-id="{{id}}">
                        !APLICA¡
                    </a>
                {{/ifCond}}
                <a class="btnBorderRed btnRubricas" href="#" data-id="{{id}}">
                    RÚBRICAS
                </a>
            </div>
        
        
    </div>
</div>
</script>
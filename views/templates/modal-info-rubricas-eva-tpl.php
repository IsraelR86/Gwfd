<?php 
use yii\helpers\Url;
?>
<script type="text/x-handlebars-template" id="modal-info-rubricas-eva-tpl">
    <div class="container-fluid item">
    <div class="row">
        <div class="col-md-4 noPadding bgBodyInfo">
            <div class="header">
                <span class="title">{{concurso.nombre}}</span>
                <img src="{{concurso.byteImagen}}" height="300">
            </div>
            
            <div class="body">
                <div class="col-md-12 col-xs-12 text-center noPadding">
                    <a class="btnBorderRed fontBlack width95porc btnProyectosRegistrados" href="#" data-id="{{concurso.id}}">
                        PROYECTOS REGISTRADOS
                    </a>
                </div>
                <div class="col-md-12 col-xs-12 text-center noPadding">
                    <a class="btnBorderRed fontBlack width95porc btnProyectosCompletados" href="<?= Url::toRoute(['concurso/view']); ?>/{{concurso.id}}" data-id="{{concurso.id}}">
                        VER MICROSITIO
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-8 containerConcurso">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            
            <div class="title_container">
                
            </div>
            
            <div class="body_mi_concurso">

                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        
                        <div class='container-tablePuntajeProyecto'>
                            <table class="table table-striped table-hover tablePuntajeProyecto">
                                <thead>
                                    <tr>
                                        <th><strong>RÚBRICA</strong></th>
                                        <th><strong>DESCRIPCIÓN</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{#rubricas}}
                                        <tr>
                                            <td class="col-md-5">{{nombre}}</td>
                                            <td class="col-md-7">{{descripcion}}</td>
                                        </tr>
                                    {{/rubricas}}
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <div class="row footer">
        
        <div class="col-md-3 noPaddingLeft">
            <p class="tag">
                {{#join_slash concurso.etiquetas}}{{descripcion}}{{/join_slash}}
            </p>
        </div>
        <div class="col-md-9 text-right menu">
            <a class="btnBorderRed btnRegresarConcurso" href="#" data-concurso="{{concurso.id}}">
                VER CONCURSO
            </a>
        </div>
        
    </div>
</div>

</script>
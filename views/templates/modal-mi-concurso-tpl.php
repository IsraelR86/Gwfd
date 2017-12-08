<?php
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="modal-mi-concurso-tpl">
<div class="container-fluid item">
    <div class="row">
        <div class="col-md-4 noPadding bgBodyInfo">
            <div class="header">
                <span class="title">{{nombre}}</span>
                <img src="{{byteImagen}}" height="300">
            </div>

            <div class="body">
                <div class="col-md-6 col-xs-6 text-center noPadding">
                    <a class="btnBorderRed fontBlack width95porc btnProyectosRegistrados cursor-text" href="#" data-id="{{id}}">
                        PROYECTOS REGISTRADOS
                        <div class="easy-pie-chart small" id="chartProyectosRegistrados" data-percent="{{proyectosRegistrados}}" data-scale-color="#ffb400">
                            <span class="number"></span>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-xs-6 text-center noPadding">
                    <a class="btnBorderRed fontBlack width95porc btnProyectosCompletados cursor-text" href="#" data-id="{{id}}">
                        PROYE 100% COMPLETADOS
                        <div class="easy-pie-chart small" id="chartProyectosCompletados" data-percent="{{proyectosCompletados}}" data-scale-color="#ffb400">
                            <span class="number"></span>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-xs-6 text-center noPadding">
                    <a class="btnBorderRed fontBlack width95porc btnSuperanEvaluacionATM cursor-text" href="#" data-id="{{id}}">
                        SUPERAN EVALUACIÓN ATM<br>
                        <div class="easy-pie-chart small" id="chartSuperanEvaluacionATM" data-percent="{{superanEvaluacionATM}}" data-scale-color="#ffb400">
                            <span class="number"></span>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-xs-6 text-center noPadding">
                    <a class="btnBorderRed fontBlack width95porc btnPosiblesPlagios cursor-text" href="#" data-id="{{id}}">
                        POSIBLES PLAGIOS<br><br>
                        <div class="easy-pie-chart small" id="chartPosiblesPlagios" data-percent="{{posiblesPlagios}}" data-scale-color="#ffb400">
                            <span class="number"></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            <div class="title_container">
                <h2 class="title">MI CONCURSO</h2>
            </div>

            <div class="body_mi_concurso">

                <div>
                    En esta ficha puedes ver la info general de tu concurso.
                </div>
                <p>&nbsp;</p>

                <div class="progess-bar bgBlack"></div>

                <div class="field-info">
                    <span class="container-label"><label>CONVOCA</label></span>
                    <span class="container-text">
                        <div class="text-center"><strong>{{institucion.nombre}}</strong></div> {{descripcion}}
                    </span>
                </div>

                {{#ifCond status '==' 'FINALIZADO'}}
                    <div class="field-info">
                        <span class="container-label"><label>BASES</label></span>
                        <span class="container-text">
                            Descarga las bases del concurso aquí.
                            <span class="pull-right icon btnDownloadBases" data-id="{{id}}"><i class="fa fa-arrow-circle-o-down fa-lg"></i> &nbsp; </span>
                        </span>
                    </div>
                {{/ifCond}}

                {{#ifCond status '==' 'EN PROCESO'}}
                    <div class="field-info">
                        <span class="container-label"><label>EVALUADORES</label></span>
                        <span class="container-text">{{noEvaluadores}} Evaluadores Activos</span>
                    </div>
                {{/ifCond}}

                <div class="field-info">
                    <span class="container-label"><label>LÍMITE(s)</label></span>
                    <span class="container-text">{{fechaArranque}} al {{fechaCierre}}</span>
                </div>

                <div class="field-info">
                    <span class="container-label"><label>PREMIOS</label></span>
                    <span class="container-text">{{premios}}</span>
                </div>

                <div class="field-info">
                    <span class="blueBox text-center width100porc center-block">
                    {{#if cancelado}}
                        CANCELADO
                    {{else}}
                        {{status}}
                    {{/if}}
                    </span>
                </div>

            </div>
        </div>
    </div>
    <div class="row footer">

        {{#ifCond status '==' 'FINALIZADO'}}
            <div class="col-md-12 text-right menu">
                <!--<a class="btnBorderRed btnAsignarEvaluadores" href="#" data-id="{{id}}" title="Asignar Evaluadores a Proyectos">
                    ASIGNAR EVALUADORES
                </a>-->
                <!--<a class="btnBorderRed btnEvaluaciones" href="#" data-id="{{id}}">
                    EVALUACIONES
                </a>-->
                <a class="btnBorderRed btnGanadores" href="<?= Url::toRoute(['concurso/ganadores']) ?>?c={{id}}" data-id="{{id}}">
                    GANADORES
                </a>
                <a class="btnBorderRed" href="<?= Url::toRoute('institucion/emprendedores') ?>/{{id}}" data-id="{{id}}">
                    EMPRENDEDORES
                </a>
                <a class="btnBorderRed bgRed btnPublicar" href="#" data-id="{{id}}">
                    PUBLICAR
                </a>
                <a class="btnBorderRed small" href="#" data-id="{{id}}" data-dismiss="modal" title="Cerrar">
                    <i class="fa fa-close"></i>
                </a>
            </div>
        {{/ifCond}}

        {{#ifCond status '==' 'EN PROCESO'}}
            <div class="col-md-2 noPaddingLeft">
                <p class="tag">
                    {{#join_slash etiquetas}}{{descripcion}}{{/join_slash}}
                </p>
            </div>
            <div class="col-md-10 text-right menu">
                <a class="btnBorderRed btnEditar" href="#" data-id="{{id}}">
                    EDITAR
                </a>
                <a class="btnBorderRed btnEditRubricas" href="#" data-id="{{id}}">
                    RUBRICAS
                </a>
                <a class="btnBorderRed btnEditEvaluadores" href="#" data-id="{{id}}">
                    EVALUADORES
                </a>
                <a class="btnBorderRed" href="<?= Url::toRoute('institucion/emprendedores') ?>/{{id}}" data-id="{{id}}">
                    EMPRENDEDORES
                </a>
                <a class="btnBorderRed bgRed btnCancelarConcurso" href="#" data-id="{{id}}">
                    CANCELAR
                </a>
                <a class="btnBorderRed small" href="#" data-id="{{id}}" data-dismiss="modal" title="Cerrar">
                    <i class="fa fa-close"></i>
                </a>
            </div>
        {{/ifCond}}

    </div>
</div>
</script>
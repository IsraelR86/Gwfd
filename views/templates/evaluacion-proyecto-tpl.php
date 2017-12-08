<?php
use yii\helpers\Url;
use app\helpers\MyHtml;
$this->registerJsFile(Url::to('@web/js/plugins/jquery.fileDownload.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('var urlDownloadDocumento = "'.Url::toRoute('rubrica/downloaddocumento').'";', \yii\web\View::POS_HEAD);
?>

<script type="text/x-handlebars-template" id="evaluacion-proyecto-tpl">
  <!--  <div class="field-info">
        <span class="container-label"><label>RÚBRICA</label></span>
        <span class="container-text">
            <p class="text-center"><strong>{{nombre}}</strong></p>
            <p>{{descripcion}}</p>
        </span>
    </div>

    <hr />-->

    {{#each secciones}}
        <div class="field-info">
            <span class="container-label"><label>{{descripcion}}</label></span>
            <span class="container-text">
                {{#each preguntas}}
                <ul>
                    <li>{{descripcion}}</li>
                    <ul>
                        {{#ifCond respuesta_geografica '==' ''}}
                        {{#ifCond respuesta '==' 'Documento'}}
                            <span class="pull-right icon btnDownloadDocumento" data-concurso="{{../../../../concurso}}" data-proyecto="{{../../../../proyecto}}" data-id="{{id}}"><i class="fa fa-arrow-circle-o-down fa-lg"></i> &nbsp; </span>
                        {{/ifCond}}
                            <li>{{respuesta}}</li>
                        {{/ifCond}}


                        {{#ifCond respuesta_geografica '!==' ''}}
                            <li class="respuesta_geografica cursor-pointer" data-puntos='{{respuesta_geografica}}' data-tipo="{{tipo_pregunta}}">
                                <span class="glyphicon glyphicon-map-marker icon" aria-hidden="true"></span>{{respuesta}}
                            </li>
                        {{/ifCond}}
                    </ul>
                </ul>
                {{/each}}
            </span>
        </div>
    {{/each}}


    <hr />

    <div class="field-info">
        <!--<span class="container-label"><label>RÚBRICA</label></span>-->
        <span class="container-label"><label>CRITERIO DE EVALUACIÓN</label></span>
        <span class="container-text">
            <p class="text-center"><strong>{{nombre}}</strong></p>
            <p>{{descripcion}}</p>
        </span>
    </div>

    <hr />

    <div class="field-info">
        <span class="container-label"><label>CALIFICACIÓN</label></span>
        <span class="container-text">
            <p>
                <select name="calificacion" id="calificacion">
                    {{#opciones_calificacion}}
                        <option value="{{calificacion}}">{{calificacion}}</option>
                    {{/opciones_calificacion}}
                </select>
            </p>
            <p>
                <!--<textarea name="comentarios" id="comentarios" placeholder="Comentarios" class="form-control" maxlength="300">{{comentarios}}</textarea>-->
                <textarea name="comentarios" id="comentarios" placeholder="Establecer las recomendaciones generales para el mejoramiento del proyecto en la pregunta 12/12." class="form-control" maxlength="300">{{comentarios}}</textarea>
            </p>
        </span>
    </div>

    <p>&nbsp;</p>
    <div style="clear: both;">
        <div class="text-right">
            <span><i id="no_current_rubrica"></i>/<i id="total_rubricas"></i></span> &nbsp; &nbsp; &nbsp; &nbsp;
            <?= MyHtml::pager('small pagerEvaluacion pull-right', '', '') ?>
        </div>
    </div>
    <p>&nbsp;</p>
</script>

<?php
use app\helpers\MyHtml;
use yii\helpers\Url;
use app\models\TipoPreguntaConcurso;

$this->registerJs('var urlGetEtiquetas = "'.Url::toRoute('etiqueta/getall').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSendDatosGenerales = "'.Url::toRoute('concurso/set').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSendBases = "'.Url::toRoute('concurso/uploadbases').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSendPreguntas = "'.Url::toRoute('concurso/setpreguntasconcurso').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlFindEvaluador = "'.Url::toRoute('institucion/findevaluador').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSendEvaluadores = "'.Url::toRoute('concurso/setevaluadores').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var tiposPreguntaConcurso = '.json_encode(TipoPreguntaConcurso::find()->asArray()->all()).';', \yii\web\View::POS_HEAD);

$this->registerCssFile('//hayageek.github.io/jQuery-Upload-File/4.0.8/uploadfile.css');
$this->registerJsFile('//hayageek.github.io/jQuery-Upload-File/4.0.8/jquery.uploadfile.min.js', ['depends' => [\app\assets\AppAsset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/jquery.maskedinput.min.js'), ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<script type="text/x-handlebars-template" id="modal-frm-concurso-tpl">
<div class="container-fluid item">
    <div class="row">
        <div class="col-md-4 noPadding bgBodyInfo">
            <div class="header">
                <span class="title">{{nombre}}</span>
                <img src="{{byteImagen}}" height="300">
            </div>
            <p class="body">{{descripcion}}</p>
        </div>
        <div class="col-md-8">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            <div class="title_container">
                <h2 class="title">AGREGA TU CONCURSO</h2>
            </div>

            <p>Para agregar un concurso, llena las formas y agrega las rúbricas necesarias para hacer de tu concurso un éxito.</p>

            <div class="progess-bar tresSecciones">
                <span class="tooltipster" data-seccion="datos_generales" title="<span class='title'>Datos Generales</span>"></span>
                <span class="tooltipster" data-seccion="preguntas" title="<span class='title'>Preguntas</span>"></span>
                <span class="tooltipster" data-seccion="evaluadores" title="<span class='title'>Evaluadores</span>"></span>
            </div>

            <form name="frmProyecto" id="frmProyecto" class="slide_seccion" enctype="multipart/form-data">

                <div class="seccion" data-id="datos_generales">

                    <div class="pagina">

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <input type="text" name="nombre" class="tooltipster" title="Nombre" placeholder="Nombre" maxlength="100"/>
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <textarea name="descripcion" placeholder="Descripción" class="tooltipster" title="Descripción" maxlength="600"></textarea>
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <textarea name="premios" placeholder="Premios" class="tooltipster" title="Premios" maxlength="600"></textarea>
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <div class="container-input" id="container-fileUploaderBases">
                                <div class="colorGray">Solo archivos PDF menores de 2MB</div>
                                <div id="fileUploaderBases" class="files">Bases</div>
                            </div>
                        </div>

                    </div>

                    <div class="pagina">

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <input type="number" name="calificacion_minima_proyectos" class="tooltipster" title="Calificación mínima a evaluar" placeholder="Calificación mínima a evaluar" min="1" />
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <input type="number" name="no_ganadores" class="tooltipster" title="Número de Ganadores a visualizar" placeholder="Número de Ganadores a visualizar" min="1" />
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <input type="text" name="fecha_arranque" class="tooltipster" title="Fecha de Arranque" placeholder="Fecha de Arranque" maxlength="10"/>
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <input type="text" name="fecha_cierre" class="tooltipster" title="Fecha de Cierre" placeholder="Fecha de Cierre" maxlength="10"/>
                        </div>

                         <div class="field-input">
                            <span class="container-icon"></span>
                            <div class="container-input" id="container-fileuploaderImagen">
                                <div id="fileuploaderImagen" class="files">Imagen</div>
                            </div>
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <input type="number" name="evaluadores_x_proyecto" class="tooltipster" title="Define el número de evaluadores que evaluarán a cada proyecto" placeholder="Número de evaluadores por proyecto"/>
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <select name="etiquetas" class="form-control" multiple="multiple"></select>
                        </div>

                        <div class="field-input answered text-ceter" id="loadingDatosGenerales" style="display: none">
                            <i class="fa fa-spinner fa-pulse fa-lg"></i> Enviando datos generales... Espere un momento.
                        </div>

                    </div>

                </div>

                <div class="seccion" data-id="preguntas">
                    <div class="pagina">
                        Puede agregar preguntas especificas al concurso. De click al boton agregar pregunta.

                        <a class="inline-block btnBorderRed fontBlack" id="btnAddPregunta" href="#">
                            <i class="fa fa-question"></i> Agregar Pregunta
                        </a>
                        <p>&nbsp;</p>
                        <div id="seccion_preguntas">
                            {{#each preguntas}}
                                <div class="field-input pregunta" data-id="{{id}}" data-tipo="{{id_tipo_pregunta_concurso}}">
                                    <span class="container-icon"><div class="vertical-center text-center"><i class="fa fa-times icon delPregunta"></i></div></span>
                                    <select name="tipo">
                                        <option value="">Tipo</option>
                                        {{#each ../tiposPreguntaConcurso}}
                                            <option value="{{id}}">{{descripcion}}</option>
                                        {{/each}}
                                    </select>
                                    <textarea name="pregunta" placeholder="Descripción de la Pregunta" maxlength="300">{{descripcion}}</textarea>
                                </div>
                            {{/each}}
                        </div>
                    </div>
                </div>

                <div class="seccion" data-id="evaluadores">
                    <div class="pagina">

                        <div id="seccion_evaluadores" class="col-md-12 col-md-xs-12">

                        </div>

                    </div>
                </div>


            </form>
            <p>&nbsp;</p>
            <div style="clear: both;">
                <div class="text-right">
                    <span><i id="no_current_pagina"></i>/<i id="total_pagina_seccion"></i></span> &nbsp; &nbsp; &nbsp; &nbsp;
                    <?= MyHtml::pager('small pagerFrmConcurso pull-right', '', '') ?>
                </div>
            </div>
            <p>&nbsp;</p>

        </div>
    </div>
    <div class="row footer">
        <div class="col-md-4 noPaddingLeft">
            <p class="tag">
                {{#join_slash etiquetas}}{{descripcion}}{{/join_slash}}
            </p>
        </div>
        <div class="col-md-8 text-right menu">
            <a class="btnBorderRed inline-block btnFinalizar" href="#">
                Finalizar
            </a>
        </div>
    </div>
</div>
</script>

<script type="text/x-handlebars-template" id="pregunta-tpl">
    <div class="field-input pregunta" data-id="{{id}}" data-tipo="{{id_tipo_pregunta_concurso}}">
        <span class="container-icon"><div class="vertical-center text-center"><i class="fa fa-times icon delPregunta"></i></div></span>
        <select name="tipo">
            <option value="">Tipo</option>
            {{#tiposPreguntaConcurso}}
                <option value="{{id}}"
                {{#ifCond id '==' ../id_tipo_pregunta_concurso}}
                    selected
                {{/ifCond}}
                >{{descripcion}}</option>
            {{/tiposPreguntaConcurso}}
        </select>
        <textarea name="pregunta" placeholder="Descripción de la Pregunta" maxlength="300">{{descripcion}}</textarea>
    </div>
</script>

<script type="text/x-handlebars-template" id="evaluador-tpl">
    <div class="col-md-{{col}} col-xs-{{col}} itemEvaluador" data-id="{{id}}">
    <table>
        <tr>
            <td class="">
                <span class="imgEvaluador">
                    <img src="{{byteimagen}}" class="img-circle"/>
                </span>
            </td>
            <td class="col-md-8 col-xs-8">
                <p>{{text}}</p>
                <p class="expertise">{{#join etiquetas}}{{descripcion}}{{/join}}</p>
            </td>
            <td>
                <div class="">
                    <span class="fa-stack icon delEvaluador">
                        <i class="fa fa-circle-o fa-stack-2x"></i>
                        <i class="fa fa-close fa-stack-1x"></i>
                    </span>
                </div>
            </div>
        </tr>
    </table>
    </div>
</script>

<script type="text/x-handlebars-template" id="seccion-evaluadores-tpl">
    <div class="field-input">
        <span class="container-icon"></span>
        <select name="list_evaluadores" class="form-control" multiple="multiple"></select>
    </div>

    <div class="col-md-12 col-md-xs-12" style="visibility: hidden;">Lorem ipsum dolor sit amet, consectetur adipiscing elit,  </div>

    <div class="col-md-12 col-md-xs-12" id="seccion_list_evaluadores">
        {{#evaluadores}}
            <div class="col-md-6 col-xs-6 itemEvaluador" data-id="{{id}}">
            <table>
                <tr>
                    <td class="">
                        <span class="imgEvaluador">
                            <img src="{{byteimagen}}" class="img-circle"/>
                        </span>
                    </td>
                    <td class="col-md-8 col-xs-8">
                        <p>{{nombre_completo}}</p>
                        <p class="expertise">{{#etiquetas}}{{descripcion}}, {{/etiquetas}}</p>
                    </td>
                    <td>
                        <div class="">
                            <span class="fa-stack icon delEvaluador">
                                <i class="fa fa-circle-o fa-stack-2x"></i>
                                <i class="fa fa-close fa-stack-1x"></i>
                            </span>
                        </div>
                    </div>
                </tr>
            </table>
        </div>
        {{/evaluadores}}
    </div>
</script>

<script type="text/x-handlebars-template" id="seccion-rubricas-tpl">
    <div class="field-input">
        <span class="container-icon"></span>
        <input type="text" name="nombre" class="tooltipster" title="Nombre" placeholder="Nombre" maxlength="45"/>
        <textarea name="descripcion" placeholder="Descripción" class="tooltipster" title="Descripción" maxlength="500"></textarea>
        <input type="number" name="calificacion_minima" class="tooltipster" title="Calificación Mínima" placeholder="Calificación Mínima"/>
        <input type="number" name="calificacion_maxima" class="tooltipster" title="Calificación Máxima" placeholder="Calificación Máxima"/>
    </div>

    <div class="col-md-12 col-xs-12 text-center">
        <a class="inline-block btnBorderRed fontBlack" id="btnAddRubrica" href="#">
            <i class="fa fa-question"></i> Agregar Rubrica
        </a>
    </div>

    <div id="list_rubricas">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>RUBRICA</th>
                    <th>DESCRIPCIÓN</th>
                    <th>CALIF MÍNIMA</th>
                    <th>CALIF MÁXIMA</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {{#rubricas}}
                    <tr class="rubrica" data-id="{{id}}">
                        <td class="col-md-3 nombre">{{nombre}}</td>
                        <td class="col-md-4 descripcion">{{descripcion}}</td>
                        <td class="col-md-2 calificacion_minima text-center">{{calificacion_minima}}</td>
                        <td class="col-md-2 calificacion_maxima text-center">{{calificacion_maxima}}</td>
                        <td class="col-md-1">
                            <span class="fa-stack icon delRubrica">
                                <i class="fa fa-circle-o fa-stack-2x"></i>
                                <i class="fa fa-close fa-stack-1x"></i>
                            </span>
                        </td>
                    </tr>
                {{/rubricas}}
            </tbody>
        </table>
    </div>
</script>
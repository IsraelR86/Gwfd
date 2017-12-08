<?php
use app\helpers\MyHtml;
use yii\helpers\Url;

$this->registerJs('var urlGetSecciones = "'.Url::toRoute('seccion/getall').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlGetEtiquetas = "'.Url::toRoute('etiqueta/getall').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlFindByName = "'.Url::toRoute('emprendedor/findbyname').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSendDatosGenerales = "'.Url::toRoute('proyecto/set').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSendLogo = "'.Url::toRoute('proyecto/uploadlogo').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSendRespuestas = "'.Url::toRoute('proyecto/responderpreguntas').'";', \yii\web\View::POS_HEAD);
$this->registerJs('var urlSendRespuestaArchivo = "'.Url::toRoute('proyecto/responderpreguntasarchivo').'";', \yii\web\View::POS_HEAD);

$this->registerCssFile('//hayageek.github.io/jQuery-Upload-File/4.0.8/uploadfile.css');
$this->registerJsFile('//hayageek.github.io/jQuery-Upload-File/4.0.8/jquery.uploadfile.min.js', ['depends' => [\app\assets\AppAsset::className()]]);
?>

<script type="text/x-handlebars-template" id="modal-frm-proyecto-tpl">
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
                <h2 class="title">AGREGA TU PROYECTO</h2>
            </div>

            <p>Para agregar un proyecto, llena las formas y ¡empieza a emprender!</p>

            <div class="progess-bar">
                <span class="tooltipster" data-seccion="-1" title="<span class='title'>Datos Generales</span>"></span>
                {{#secciones}}
                    <span class="tooltipster" data-seccion="{{id}}" title="<span class='title'>{{descripcion}}</span>"></span>
                {{/secciones}}
            </div>

            <form name="frmProyecto" id="frmProyecto" class="slide_seccion" enctype="multipart/form-data">

                <div class="seccion" data-id="-1">

                    <div class="pagina">
                        <h2>Datos Generales</h2>
                        <div class="field-input">
                            <span class="container-icon"></span>
                            <input type="text" name="nombre" class="tooltipster" title="Nombre" placeholder="Nombre" maxlength="250"/>
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <textarea name="descripcion" placeholder="Descripción" class="tooltipster" title="Descripción" maxlength="300"></textarea>
                        </div>

                        <div style="display:none;" class="field-input">
                            <span class="container-icon"></span>
                            <input type="url" name="url_video" value="www.aaa.com" class="tooltipster" title="URL Video. Si la fuente es <strong>Youtube</strong>, la URL debe tener el formato de <strong>Insertar vínculo</strong>. Ejemplo https://www.youtube.com/embed/rsxUDjfhj5Y" placeholder="URL Video" maxlength="100"/>
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <div class="container-input" id="container-fileuploaderImagen" title="Sube la imagen de tu proyecto." >
                                <div id="fileuploaderImagen" class="files">Imagen</div>
                            </div>
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <div class="container-input" id="container-fileuploaderLogo" title="Sube el logotipo de tu proyecto.">
                                <div id="fileuploaderLogo" class="files">Logo</div>
                            </div>
                        </div>
                    </div>

                    <div class="pagina">
                        <h2>Datos Generales</h2>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <select name="etiquetas" class="form-control tooltipster" title="Etiquetas" multiple="multiple"></select>
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <input type="number" name="integrantes" class="tooltipster" title="No. Total de Integrantes" placeholder="No. Total de Integrantes" min="0" />
                        </div>

                        <div class="field-input">
                            <span class="container-icon"></span>
                            <select name="list_integrantes" class="form-control tooltipster" title="Escriba la primera letra del nombre del integrante" multiple="multiple"></select>
                        </div>

                        <div class="field-input answered text-ceter" id="loadingDatosGenerales" style="display: none">
                            <i class="fa fa-spinner fa-pulse fa-lg"></i> Enviando datos generales... Subiendo imagenes... Espere un momento.
                        </div>

                    </div>

                </div>

                {{#each secciones}}
                    <div class="seccion" data-id="{{id}}">
                        {{#each paginas}}
                            <div class="pagina">
                                <h3>{{../descripcion}}</h3>

                                {{#each preguntas}}
                                    <p>{{descripcion}}</p>
                                    <div class="field-input tooltipster"
                                        {{#ifCond ayuda '!=' ""}} title="{{ayuda}}" {{/ifCond}}
                                        data-tipopregunta="{{tipoPregunta.id}}" data-pregunta="{{id}}">
                                        <span class="container-icon"></span>
                                        {{!-- 1 Texto --}}
                                        {{#ifCond tipoPregunta.id '==' 1}}
                                            <input type="text" name="respuesta" class="">
                                        {{/ifCond}}

                                        {{!-- 2 Numérica --}}
                                        {{#ifCond tipoPregunta.id '==' 2}}
                                            <input type="number" name="respuesta" class="">
                                        {{/ifCond}}

                                        {{!-- 3 Opción Múltiple --}}
                                        {{#ifCond tipoPregunta.id '==' 3}}
                                            <div class="container-input">
                                                {{#opcionesMultiple}}
                                                    <div><label><input type="checkbox" name="respuesta" value="{{id}}"> {{descripcion}}</label></div>
                                                {{/opcionesMultiple}}
                                            </div>
                                        {{/ifCond}}

                                        {{!-- 4 Opción Única --}}
                                        {{#ifCond tipoPregunta.id '==' 4}}
                                            <select name="respuesta" class="">
                                                <option value="">Seleccione la respuesta</option>
                                                {{#opcionesMultiple}}
                                                    <option value="{{id}}">{{descripcion}}</option>
                                                {{/opcionesMultiple}}
                                            </select>
                                        {{/ifCond}}

                                        {{!-- 5 Hipervínculo --}}
                                        {{#ifCond tipoPregunta.id '==' 5}}
                                            <input type="text" name="respuesta" class="">
                                        {{/ifCond}}

                                        {{!-- 6 Punto Radial Geográfico --}}
                                        {{#ifCond tipoPregunta.id '==' 6}}
                                            <div class="container-input">
                                                <a href="#" class="respuesta_geografica radial colorBlack" data-pregunta="{{id}}">
                                                    <span class="glyphicon glyphicon-map-marker icon" aria-hidden="true"></span> Seleccione los puntos <span class="num_puntos"></span>
                                                </a>
                                            </div>
                                        {{/ifCond}}

                                        {{!-- 7 Polígono Geográfico --}}
                                        {{#ifCond tipoPregunta.id '==' 7}}
                                            <div class="container-input">
                                                <a href="#" class="respuesta_geografica poligono colorBlack" data-pregunta="{{id}}">
                                                    <span class="glyphicon glyphicon-map-marker icon" aria-hidden="true"></span> Seleccione los puntos <span class="num_puntos"></span>
                                                </a>
                                            </div>
                                        {{/ifCond}}

                                        {{!-- 8 Punto Geográfico --}}
                                        {{#ifCond tipoPregunta.id '==' 8}}
                                            <div class="container-input">
                                                <a href="#" class="respuesta_geografica punto colorBlack" data-pregunta="{{id}}">
                                                    <span class="glyphicon glyphicon-map-marker icon" aria-hidden="true"></span> Seleccione los puntos <span class="num_puntos"></span>
                                                </a>
                                            </div>
                                        {{/ifCond}}

                                    </div>
                                {{/each}}

                                <div class="col-md-12 col-md-xs-12" style="visibility: hidden;">Lorem ipsum dolor sit amet, consectetur adipiscing elit,  </div>
                            </div>
                        {{/each}}
                    </div>
                {{/each}}

            </form>
            <p>&nbsp;</p>
            <div style="clear: both;">
                <div class="text-right">
                    <span><i id="no_current_pagina"></i>/<i id="total_pagina_seccion"></i></span> &nbsp; &nbsp; &nbsp; &nbsp;
                    <?= MyHtml::pager('small pagerFrmProyecto pull-right', '', '') ?>
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
        <div class="col-md-8 text-right">
            <a class="btnBorderRed inline-block" href="#" id="btnFinalizarFrmProyecto">
                Finalizar
            </a>
        </div>
    </div>
</div>
</script>

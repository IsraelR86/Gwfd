<script type="text/x-handlebars-template" id="modal-preguntas-concurso-tpl">
<div class="container-fluid item">
    <div class="row">
        <div class="col-md-4 noPadding bgBodyInfo">
            <div class="header">
                <span class="title">{{nombre}}</span>
                <img src="{{byteImagen}}" height="300">
            </div>
            <span class="date">FECHA LIM: {{fechaCierre}}</span>
            <p class="body">{{descripcion}}</p>
        </div>
        <div class="col-md-8">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            
            <div class="title_container">
                <h2 class="title">INTEGRACIÓN DE ARCHIVOS</h2>
            </div>
            
            <div>
                Integre los archivos que se solicitan para completar su registro:
            </div>
            <p>&nbsp;</p>
            
            <div id="progress" class="progress">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            
            <div class="preguntasConcurso">
                {{#each preguntas}}
                    <p>{{descripcion}}</p>
                    <div class="field-input" data-tipo="{{id_tipo_pregunta_concurso}}">
                        <span class="container-icon"></span>
                        
                        {{!-- 1 Texto --}}
                        {{#ifCond id_tipo_pregunta_concurso '==' 1}}
                            <textarea name="respuesta" class="" data-pregunta="{{id}}"></textarea>
                        {{/ifCond}}
                        
                        {{!-- 2 Archivo --}}
                        {{#ifCond id_tipo_pregunta_concurso '==' 2}}
                            <div class="container-input">
                                <div class="files" data-pregunta="{{id}}" data-concurso="{{../../id}}" data-proyecto="{{../../proyecto}}">Archivo</div>
                            </div>
                        {{/ifCond}}
                    </div>
                {{/each}}
            </div>
            
            <div class="container-input">
                <div><label><input type="checkbox" name="acepto_concurso" id="acepto_concurso" value="1"> <strong> Acepto las bases de este concurso</strong></label></div>
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
            <a class="btnBorderRed" href="#modalInfoConcurso" data-dismiss="modal">
                CANCELAR
            </a>
            <a class="btnBorderRed btnSendPreguntasConcurso" href="#" data-concurso="{{id}}" data-proyecto="{{proyecto}}">
                APLICAR
            </a>
        </div>
    </div>
</div>
</script>
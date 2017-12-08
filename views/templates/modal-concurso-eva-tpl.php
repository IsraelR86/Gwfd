<script type="text/x-handlebars-template" id="modal-concurso-tpl">
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
                <h2 class="title">INFO GENERAL</h2>
            </div>
            
            <div>
                Conoce los lineamientos del concurso y elige el proyecto con el que quieres aplicar (no todos los proyectos aplican en todos los concursos)
            </div>
            <p>&nbsp;</p>
            
            <div class="field-info">
                <span class="container-label"><label>CONVOCA</label></span>
                <span class="container-text">
                    <a class="pull-right" href="{{institucion.pagina_web}}" target="_blank">
                        <span class="fa-stack icon">
                            <i class="fa fa-circle-o fa-stack-2x"></i>
                            <i class="fa fa-link fa-stack-1x"></i>
                        </span>
                    </a>
                    <div class="text-center">{{institucion.nombre}}</div> {{descripcion}}
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
                <span class="container-label"><label>PREMIOS</label></span>
                <span class="container-text">{{premios}}</span>
            </div>
            
            <div class="field-info">
                <span class="container-label"><label>APLICA</label></span>
                <span class="container-text">
                    <select id="proyecto_aplica">
                        <option value="">Aplica con uno de tus proyectos</option>
                        {{#each proyectos}}
                            <option value="{{id}}">{{nombre}}</option>
                        {{/each}}
                    </select>
                </span>
            </div>
        </div>
    </div>
    <div class="row footer">
        <div class="col-md-4 noPaddingLeft">
            <p class="tag">
                {{#join_slash etiquetas}}{{descripcion}}{{/join_slash}}
            </p>
        </div>
        <div class="col-md-8 text-right">
            <a class="btnBorderRed" href="#" data-dismiss="modal">
                CANCELA
            </a>
            <a class="btnBorderRed btnAplicarConcurso" href="#" data-id="{{id}}">
                APLICA
            </a>
        </div>
    </div>
</div>
</script>
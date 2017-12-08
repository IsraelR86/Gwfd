<script type="text/x-handlebars-template" id="modal-aplicacion-tpl">
<div class="container-fluid item">
    <div class="row">
        <div class="col-md-4 noPadding bgBodyInfo">
            <div class="header">
                <span class="title">{{concurso.nombre}}</span>
                <img src="{{concurso.byteImagen}}" height="300">
            </div>
            <span class="date">FECHA LIM: {{concurso.fechaCierre}}</span>
            <p class="body">{{concurso.descripcion}}</p>
        </div>
        <div class="col-md-8">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            <div class="title_container">
                <h2 class="title">MI APLICACIÓN</h2>
            </div>

            <div>
                Revisa los lineamientos del concurso al que aplicaste y completa las formas si aún no estan llenas.
            </div>
            <p>&nbsp;</p>

            <div class="field-info">
                <span class="container-label"><label>PROYECTO</label></span>
                <span class="container-text">
                    <div class="text-center"><strong>{{proyecto.nombre}}</strong></div> {{proyecto.descripcion}}
                </span>
            </div>

            <div class="field-info">
                <span class="container-label"><label>PREMIOS</label></span>
                <span class="container-text">{{concurso.premios}}</span>
            </div>

            {{#ifCond concurso.status '==' 'FINALIZADO'}}
                <div class="field-info">
                    <span class="container-label"><label>LUGAR</label></span>
                    <span class="container-text">

                    </span>
                </div>
            {{/ifCond}}

            <div class="field-info">
                <span class="redBox text-center width100porc center-block">{{concurso.status}}</span>
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
          <!--  {{#ifCond concurso.status '==' 'FINALIZADO'}}
                <a class="btnBorderRed btnPuntajeProyecto" href="#" data-concurso="{{concurso.id}}" data-proyecto="{{proyecto.id}}">
                    PUNTAJE
                </a>
            {{/ifCond}}-->

            <a class="btnBorderRed" href="#" data-dismiss="modal">
                OK
            </a>

            {{#ifCond concurso.status '==' 'EN PROCESO'}}
                <a class="btnBorderRed btnAbandonarConcurso" href="#" data-concurso="{{concurso.id}}" data-proyecto="{{proyecto.id}}">
                    ABANDONAR
                </a>
            {{/ifCond}}
        </div>
    </div>
</div>
</script>

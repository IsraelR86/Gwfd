<script type="text/x-handlebars-template" id="modal-info-proyecto-tpl">
<div class="container-fluid item">
    <div class="row">
        <div class="col-md-4 noPadding bgBodyInfo">
            <div class="header">
                <span class="title">{{nombre}}</span>
                <img src="{{byteimagen}}" height="300">
            </div>

            <p class="body">
                <div class="col-md-6">
                    <span class="redBox">COMPLETADO</span>
                    <div class="easy-pie-chart" id="chartCompletado" data-percent="{{porcentajeCompletado}}" data-scale-color="#ffb400">
                        <span class="percent"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <span class="redBox">PARTICIPACIÓN</span>
                    <div class="easy-pie-chart" id="chartParticipacion" data-percent="100" data-participacion="{{noParticipacion}}" data-scale-color="#ffb400">
                        <span class="number"></span>
                    </div>
                </div>
                <!--<div class="col-md-12">
                    <span class="redBox text-center width100porc center-block">BADGES</span>
                    <i class="fa fa-star fa-2x"></i> &nbsp;
                    <i class="fa fa-star-half-o fa-2x"></i> &nbsp;
                    <i class="fa fa-star-o fa-2x"></i> &nbsp;
                </div>-->
            </p>
        </div>
        <div class="col-md-8">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            <div class="title_container">
                <h2 class="title">MI PROYECTO</h2>
            </div>

            <div>
                En esta ficha puedes ver la info general de tu proyecto. Además, puedes decidir editar la info o ir al micrositio del mismo.
            </div>
            <p>&nbsp;</p>

            <div class="field-info">
                <span class="container-label"><label>NOMBRE</label></span>
                <span class="container-text">{{nombre}}</span>
            </div>

            <div class="field-info">
                <span class="container-label"><label>DESCRIPCIÓN</label></span>
                <span class="container-text">{{descripcion}}</span>
            </div>

            <div class="field-info">
                <span class="container-label"><label>EQUIPO</label></span>
                <span class="container-text">
                    {{#emprendedores}}{{nombre}}, {{/emprendedores}}
                </span>
            </div>

            <div class="field-info">
                <span class="container-label"><label>CONTENIDO</label></span>
                <span class="container-text">
                    <div><i class="fa fa-lg" id="iconHasVideo"></i> VIDEO PITCH</div>
                    <div><i class="fa fa-lg" id="iconHasLogo"></i> LOGO</div>
                    <div><i class="fa fa-lg" id="iconHasImagen"></i> IMAGEN DEL PROYECTO</div>
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
            <a class="btnBorderRed btnVerProyecto" href="{{url_micrositio}}?p={{id}}" data-id="{{id}}">
                VER
            </a>
            <a class="btnBorderRed btnEditarProyecto" href="#" data-id="{{id}}">
                EDITAR
            </a>
        </div>
    </div>
</div>
</script>
<?php 
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="modal-evaluador-tpl">
<div class="container-fluid item">
    <div class="row">
        <div class="col-md-4 noPadding bgBodyInfo">
            <div class="header">
                <img src="{{byteimagen}}" height="300">
            </div>
            
            <div class="body">
                <div class="col-md-6 col-xs-6 text-center noPadding">
                    <a class="btnBorderRed fontBlack width95porc" href="#">
                        PROMEDIO CALIFICACIONES
                        <div class="easy-pie-chart small" id="chartPromedioCalificaciones" data-percent="100" data-number="{{promedio_calificaciones}}" data-scale-color="#ffb400">
                            <span class="number"></span>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-xs-6 text-center noPadding">
                    <a class="btnBorderRed fontBlack width95porc" href="#">
                        PROYECTOS CALIFICADOS
                        <div class="easy-pie-chart small" id="chartProyectosCalificados" data-percent="100" data-number="{{proyectos_calificados}}" data-scale-color="#ffb400">
                            <span class="number"></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            
            <div class="title_container">
                <h2 class="title">{{nombre_completo}}</h2>
            </div>
            
            <div>
                
                <div class="progess-bar bgBlack"></div>
                
                <div class="field-info">
                    <span class="container-label"><label>SEMBLANZA</label></span>
                    <span class="container-text">{{evaluador.semblanza}}</span>
                </div>
                
                <div class="field-info">
                    <span class="container-label"><label>EXPERTISE</label></span>
                    <span class="container-text">{{#join etiquetas}}{{descripcion}}{{/join}}</span>
                </div>
                
                <div class="field-info">
                    <span class="container-label"><label>ACTIVOS</label></span>
                    <span class="container-text">
                        <ul class="list-unstyled">
                            {{#concursos_activos}}
                                <li>{{nombre}}</li>
                            {{/concursos_activos}}
                        </ul>
                    </span>
                </div>
                
                <div class="field-info">
                    <span class="container-label"><label>PASADOS</label></span>
                    <span class="container-text">
                        <ul class="list-unstyled">
                            {{#concursos_pasados}}
                                <li>{{nombre}}</li>
                            {{/concursos_pasados}}
                        </ul>
                    </span>
                </div>
                
            </div>
        </div>
    </div>
    <div class="row footer">
    
        <div class="col-md-12 text-right menu">
            <a class="btnBorderRed" href="#" data-dismiss="modal">
                CERRAR
            </a>
            <a class="btnBorderRed bgRed btnEliminarEvaluador" href="#" data-id="{{id}}">
                ELIMINAR
            </a>
        </div>

    </div>
</div>
</script>
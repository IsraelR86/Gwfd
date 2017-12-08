<script type="text/x-handlebars-template" id="modal-puntaje-proyecto-tpl">
<div class="container-fluid item">
    <div class="row footer">
        <div class="col-md-7 col-xs-7">
            <h3>{{nombre_proyecto}}</h3>
        </div>
        <div class="col-md-5 col-xs-5 text-right">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            {{{btnRegresar}}}
            <a class="btnBorderRed" href="{{url_micrositio}}?p={{id_proyecto}}">
                VER MICROSITIO
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 col-xs-12">
            
            <div class='container-tablePuntajeProyecto'>
                <table class="table table-striped table-hover tablePuntajeProyecto">
                    <thead>
                        <tr>
                            <th>RUBRICA</th>
                            <th>DESCRIPCIÓN</th>
                            <th>PUNTAJE</th>
                            <th>COMENTARIOS</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{#puntaje}}
                            <tr>
                                <td class="col-md-3">{{nombre}}</td>
                                <td class="col-md-4">{{descripcion}}</td>
                                <td class="col-md-2">{{puntaje}} / {{calificacion_maxima}}</td>
                                <td class="col-md-3">{{comentarios}}</td>
                            </tr>
                        {{/puntaje}}
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
    
</div>
</script>
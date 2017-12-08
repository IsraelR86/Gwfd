<script type="text/x-handlebars-template" id="modal-evaluaciones-tpl">
<div class="container-fluid item">
    <div class="row footer">
        <div class="col-md-8 col-xs-8">
            <h3 id="titleModal">{{title_header}}</h3>
        </div>
        <div class="col-md-4 col-xs-4 text-right">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <a class="btnBorderBlue" id="linkHeaderModal" href="{{url_link}}">
                {{texto_link}}
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 col-xs-12" id="bodyModal">
            
            <div class='container-tablePuntajeProyecto'>
                <table class="table table-striped table-hover tablePuntajeProyecto">
                    <thead>
                        <tr>
                            <th class="col-md-5">EVALUADORES</th>
                            <th class="col-md-8">
                                <span class="col-md-9 text-center">PROYECTOS</span>
                                <span class="col-md-3 text-center">PUNTAJE</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {{#evaluadores}}
                            <tr>
                                <td class="col-md-5">
                                    <table>
                                        <tr><td>{{nombre_completo}}</td></tr>
                                        <tr><td>{{#join etiquetas}}{{descripcion}}{{/join}}</td></tr>
                                        <tr><td class="colorGray">Proyectos evaluados: {{no_evaluados}}/{{total_evaluar}}</td></tr>
                                    </table>
                                </td>
                                <td class="col-md-8">
                                    <table class="table">
                                        {{#evaluaciones}}
                                            <tr class="filaEvaluacion">
                                                <td  class="col-md-9">
                                                    <span class="fa-stack icon btnViewEvaluacion" data-proyecto="{{id}}" data-concurso="{{../../concurso}}">
                                                        <i class="fa fa-circle-o fa-stack-2x"></i>
                                                        <i class="fa fa-eye fa-stack-1x"></i>
                                                    </span> {{nombre}}
                                                </td>
                                                <td  class="col-md-3 text-center">{{puntaje}}/{{calificacion_maxima}}</td>
                                            </tr>
                                        {{/evaluaciones}}
                                        <tr>
                                            <td  class="col-md-9 colorRed"><span class="fa-stack icon"></span> PROMEDIO</td>
                                            <td  class="col-md-3 text-center colorRed">{{promedioPuntajeProyectos}}/{{promedioPuntajeTotal}}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        {{/evaluadores}}
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
    
</div>
</script>

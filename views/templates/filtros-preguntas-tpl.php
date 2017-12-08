<?php 
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="filtros-preguntas-tpl">
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover table-slide" id="tbl_preguntas">
    <thead>
        <tr>
            <th class="text-center">Incluir</th>
            <th class="text-center">Pregunta</th>
            <th class="text-center">Tipo de Respuesta</th>
            <th class="text-center">Validar Copias</th>
            <th class="text-center">Criterio</th>
        </tr>
    </thead>
    <tbody>
    {{#secciones}}
        <tr>
        <td colspan="5">
        <div>
            <span class="icon icon_seccion" data-id="{{id}}">
                <img src="<?= Url::to('@web/img/Up.png') ?>">
            </span>
            <strong>{{descripcion}}</strong>
        </div>
        </td>
        {{#preguntas}}
            <tr class="tr_pregunta_seccion_{{../id}}">
                <td class="text-center">
                <div><label>
                    <input type="checkbox" name="pregunta" value="{{id}}" data-tipopregunta="{{tipoPregunta.id}}">
                    </label>
                </div>
                </td>
                <td><div>{{descripcion}}</div></td>
                <td><div>{{tipoPregunta.descripcion}}</div>
                </td>
                <td class="text-center">
                <div>
                    <div class="div_criterio" style="display: none">
                        <label><input type="checkbox" name="validar_copia" value="1"></label>
                    </div>
                </div>
                </td>
                <td class="width350">
                <div>
                    <div class="div_criterio" style="display: none">
                        {{#if tipoPregunta.tipoFiltros}}
                            <select name="criterio" id="criterio_{{../id}}" class="form-control">
                                <option value="">Seleccione un criterio</option>
                                {{#tipoPregunta.tipoFiltros}}
                                    <option value="{{id}}">{{descripcion}}</option>
                                {{/tipoPregunta.tipoFiltros}}
                            </select>
                            
                            {{!-- 1 Texto --}}
                            {{#ifCond tipoPregunta.id '==' 1}}
                                <input type="text" name="valor_x" id="valor_{{../id}}" placeholder="Valor X" class="form-control left">
                                <input type="text" name="valor_y" id="valor_{{../id}}" placeholder="Valor Y" class="form-control left">
                            {{/ifCond}}
                            
                            {{!-- 2 Numérica --}}
                            {{#ifCond tipoPregunta.id '==' 2}}
                                <input type="text" name="valor_x" id="valor_{{../id}}" placeholder="Valor X" class="form-control left">
                                <input type="text" name="valor_y" id="valor_{{../id}}" placeholder="Valor Y" class="form-control left">
                            {{/ifCond}}
                            
                            {{!-- 3 Opción Múltiple --}}
                            {{#ifCond tipoPregunta.id '==' 3}}
                                {{#opcionesMultiple}}
                                    <div><label><input type="checkbox" name="valores" id="valor_{{../id}}" value="{{id}}"> {{descripcion}}</label></div>
                                {{/opcionesMultiple}}
                            {{/ifCond}}
                            
                            {{!-- 4 Opción Única --}}
                            {{#ifCond tipoPregunta.id '==' 4}}
                                <select name="valor" id="valor_{{../id}}" class="form-control">
                                    <option value="">Seleccione la respuesta</option>
                                    {{#opcionesMultiple}}
                                        <option value="{{id}}">{{descripcion}}</option>
                                    {{/opcionesMultiple}}
                                </select>
                            {{/ifCond}}
                            
                        {{/if}}
                        
                        <textarea name="comentarios" class="form-control" placeholder="Comentarios"></textarea>
                        
                    </div>
                </div>
                </td>
            </tr>
        {{/preguntas}}
    {{/secciones}}
    </tbody>
    </table>
</div>
</script>
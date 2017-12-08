<?php 
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="filtros-participantes-tpl">
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover table-slide margin-auto width80porc" id="tbl_filtros_participantes">
    <thead>
        <tr>
            <th class="text-center">Incluir</th>
            <th class="text-center">Filtro</th>
            <th class="text-center">Criterio</th>
        </tr>
    </thead>
    <tbody>
    {{#filtros}}
        <tr>
            <td class="text-center">
            <div><label>
                <input type="checkbox" name="filtro" value="{{id}}">
                </label>
            </div>
            </td>
            <td><div>{{descripcion}}</div></td>
            <td class="width350">
            <div>
                <div class="div_filtro" style="display: none">
                    {{#if opciones}}
                        <select name="restricion" class="form-control" multiple="multiple">
                            {{#opciones}}
                                <option value="{{id}}">{{descripcion}}</option>
                            {{/opciones}}
                        </select>
                    {{else}}
                        <input type="text" name="restricion" class="form-control" value="">
                    {{/if}}
                </div>
            </div>
            </td>
        </tr>
    {{/filtros}}
    </tbody>
    </table>
</div>
</script>
<?php
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="preguntas-rubrica-tpl">
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover table-slide" id="tbl_preguntas_rubricas">
    <thead>
        <tr class="text-center">
            <th class="text-center">Incluir</th>
            <th class="text-center">Pregunta</th>
            <th class="text-center">Tipo de Respuesta</th>
        </tr>
    </thead>
    <tbody>
    {{#secciones}}
        <tr>
        <td colspan="3">
        <div>
            <span class="icon icon_seccion" data-id="{{id}}">
                <img src="<?= Url::to('@web/img/Up.png') ?>">
            </span>
            <label>
                <!--<input type="checkbox" name="seccion" value="{{id}}" class="chk_seccion">-->
            </label>
            <strong>{{descripcion}}</strong>
        </div>
        </td>
        {{#preguntas}}
            <tr class="tr_pregunta_seccion_{{../id}}">
                <td class="text-center">
                <div><label>
                  {{#if pagina}}
                      <input type="checkbox" name="pregunta" value="{{id}}" class="chk_pregunta_seccion_{{../id}}">
                  {{else}}
                      <input type="checkbox" name="pregunta_concurso" value="{{id}}" class="chk_pregunta_concurso_{{../id}}">
                  {{/if}}

                    </label>
                </div>
                </td>
                <td><div>{{descripcion}}</div></td>
                <td><div class="tooltipster"
                    {{#if opcionesMultiple}}
                        title="<ol class='PaddingLeft'>
                            {{#opcionesMultiple}}
                                <li>{{descripcion}}</li>
                            {{/opcionesMultiple}}
                        </ol>"
                    {{/if}}>
                    {{tipoPregunta.descripcion}}</div>
                </td>
            </tr>
        {{/preguntas}}
    {{/secciones}}
    </tbody>
    </table>
</div>
</script>

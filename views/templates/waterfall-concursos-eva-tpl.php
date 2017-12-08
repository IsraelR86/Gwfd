<?php
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="waterfall-concursos-eva-tpl">
{{#result}}
    <div class="item" data-concurso="{{id}}">
        <div class="header" data-concurso="{{id}}" style="cursor: pointer;">
            <span class="title">{{nombre}}</span>
            <img src="{{byteImagen}}">
        </div>
        <div class="body">
            <span class="date {{#ifCond status '==' 'FINALIZADO'}} bgColorTurquoise {{/ifCond}}">
                {{#if cancelado}}
                    CANCELADO
                {{else}}
                    {{#ifCond status '==' 'FINALIZADO'}}
                        FINALIZADO
                    {{/ifCond}}
                    {{#ifCond status '!=' 'FINALIZADO'}}
                        FECHA LIM: {{fechaCierre}}
                    {{/ifCond}}
                {{/if}}
            </span>
            <p>{{descripcion}}</p>
        </div>
        <div class="footer">
            {{#ifCond status '==' 'FINALIZADO'}}
                {{#ifCond cancelado '==' 0}}
                    <a class="btnBorderBlue width95porc" href="<?= Url::toRoute(['concurso/ganadores']) ?>?c={{id}}" data-id="{{id}}" data-nombre="{{nombre}}">
                        GANADORES
                    </a>
                {{/ifCond}}
            {{/ifCond}}
            <p class="tag">
                {{#join_slash etiquetas}}{{descripcion}}{{/join_slash}}
            </p>
            <span class="icon">
                <span class="btnGetRubricas btnShowConcurso" data-id="{{id}}" data-concurso="{{id}}">
                    <img src="<?= Url::to('@web/img/Edit.png'); ?>">
                </span>
            </span>
        </div>
    </div>
{{/result}}
</script>
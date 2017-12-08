<?php 
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="waterfall-concurso-tpl">
{{#result}}
    <div class="item" data-concurso="{{id}}">
        <div class="header">
            <span class="title">{{nombre}}</span>
            {{#ifCond status '==' 'FINALIZADO'}}
                <a href="<?= Url::toRoute(['concurso/ganadores']) ?>?c={{id}}" title="Ganadores" class="icon iconGanadores tooltipster"><i class="fa fa-bullseye fa-lg icon"></i></a>
            {{/ifCond}}
            <img src="{{byteImagen}}">
        </div>
        <div class="body">
            <span class="date">
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
            <p class="tag">
                {{#join_slash etiquetas}}{{descripcion}}{{/join_slash}}
            </p>
            <span class="icon">
                <span class="btnModalConcurso tooltipster" data-id="{{id}}" title="<strong><?= \Yii::$app->controller->action->id == 'aplica' ? 'Aplica' : 'Ver' ?></strong>">
                    <img src="<?= Url::to('@web/img/Edit.png'); ?>">
                </span>
            </span>
        </div>
    </div>
{{/result}}
</script>
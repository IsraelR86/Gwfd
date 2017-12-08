<?php 
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="waterfall-proyecto-tpl">
{{#result}}
    <div class="item">
        <div class="header">
            <span class="title">{{nombre}}</span>
            <img src="{{byteimagen}}">
        </div>
        <div class="body">
            <p>{{descripcion}}</p>
        </div>
        <div class="footer">
            <p class="tag">
                {{#join_slash etiquetas}}{{descripcion}}{{/join_slash}}
            </p>
            <span class="icon">
                <span class="btnModalProyecto tooltipster" data-id="{{id}}" title="<strong>Ver</strong>">
                    <img src="<?= Url::to('@web/img/Edit.png'); ?>">
                </span>
            </span>
        </div>
    </div>
{{/result}}
</script>
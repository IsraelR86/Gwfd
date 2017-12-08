<?php 
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="waterfall-evaluadores-tpl">
{{#result}}
    <div class="item" data-evaluador="{{id}}">
        <div class="header">
            <img src="{{byteimagen}}">
        </div>
        <div class="body">
            <span class="date">
                {{nombre_completo}}
            </span>
            <p>{{evaluador.semblanza}}</p>
        </div>
        <div class="footer">
            <p class="tag">
                {{#join_slash etiquetas}}{{descripcion}}{{/join_slash}}
            </p>
            <span class="icon">
                <span class="btnModalEvaluador" data-id="{{id}}">
                    <img src="<?= Url::to('@web/img/Edit.png'); ?>">
                </span>
            </span>
        </div>
    </div>
{{/result}}
</script>
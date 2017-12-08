<?php 
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="waterfall-evaluaciones-tpl">
{{#result}}
    <div class="item" data-concurso="{{id}}">
        <div class="header">
            <span class="title">{{nombre}}</span>
            <img src="{{byteImagen}}">
        </div>
        <div class="footer">
            <a class="btnBorderBlue width95porc" href="<?= Url::toRoute(['concurso/ganadores']) ?>?c={{id}}" data-id="{{id}}" data-nombre="{{nombre}}">
                GANADORES
            </a>
            <a class="btnBorderBlue width95porc btnEvaluaciones" href="#" data-id="{{id}}" data-nombre="{{nombre}}">
                EVALUACIONES
            </a>
        </div>
    </div>
{{/result}}
</script>
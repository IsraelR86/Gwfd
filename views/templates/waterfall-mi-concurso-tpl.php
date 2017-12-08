<?php 
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="waterfall-mi-concurso-tpl">
{{#result}}
    <div class="item" data-concurso="{{concurso.id}}" data-proyecto="{{proyecto.id}}">
        <div class="header">
            <span class="title">{{concurso.nombre}}</span>
            <img src="{{concurso.byteImagen}}">
        </div>
        <div class="body">
            <p>{{substr concurso.descripcion}}</p>
        </div>
        <div class="footer">
            <p class="tag">
                <strong>{{proyecto.nombre}}</strong>
            </p>
            <span class="icon">
                <span class="btnModalConcurso tooltipster" data-concurso="{{concurso.id}}" data-proyecto="{{proyecto.id}}" title="<strong>Detalles</strong>">
                    <img src="<?= Url::to('@web/img/Edit.png'); ?>">
                </span>
            </span>
        </div>
    </div>
{{/result}}
</script>
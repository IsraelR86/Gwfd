<?php 
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="waterfall-admin-concurso-tpl">
{{#result}}
    <div class="item">
        <div class="header">
            <span class="title">{{nombre}}</span>
            <img src="{{byteImagen}}">
        </div>
        <div class="body">
            <span class="date">FECHA LIM: {{fechaCierre}}</span>
            <p>{{descripcion}}</p>
        </div>
        <div class="footer text-right">
            <a class="icon tooltipster" href="<?= Url::toRoute('administrador/preguntasrubrica'); ?>?id={{id}}" title="Preguntas por Rúbrica">
                <img src="<?= Url::to('@web/img/Edit.png'); ?>">
            </a> &nbsp; 
            <a class="icon tooltipster" href="<?= Url::toRoute('administrador/filtrospreguntas'); ?>?id={{id}}" title="Filtros para las preguntas">
                <img src="<?= Url::to('@web/img/Edit.png'); ?>">
            </a> &nbsp; 
            <a class="icon tooltipster" href="<?= Url::toRoute('administrador/filtrosparticipantes'); ?>?id={{id}}" title="Filtros para los participantes">
                <img src="<?= Url::to('@web/img/Edit.png'); ?>">
            </a> &nbsp; 
            <a class="icon tooltipster btnModalConcurso" data-id="{{id}}" title="Visualizar concurso">
                <img src="<?= Url::to('@web/img/Edit.png'); ?>">
            </a>
        </div>
    </div>
{{/result}}
</script>
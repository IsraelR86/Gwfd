<?php 
use yii\helpers\Url;
?>

<script type="text/x-handlebars-template" id="waterfall-concursos-eva-tpl" >
{{#result}}
    <div class="item" data-concurso="{{id}}" style="widht:400px">
        <div class="header">
            <span class="title">{{nombre}}</span>
            <img style="widht:100%" src="{{byteImagen}}">
        </div>
    </div>
{{/result}}
</script>
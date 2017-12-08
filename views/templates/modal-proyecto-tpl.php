<script type="text/x-handlebars-template" id="modal-proyecto-tpl">
<div class="container-fluid item">
    <div class="row">
        <div class="col-md-4 noPadding bgBodyInfo">
            <div class="header">
                <span class="title">{{nombre}}</span>
                <img src="{{byteImagen}}" height="300">
            </div>
            <span class="date">FECHA LIM: {{fechaCierre}}</span>
            <p class="body">{{descripcion}}</p>
        </div>
        <div class="col-md-8">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            
            <div class="title_container">
                <h2 class="title">INFO GENERAL</h2>
            </div>
            
            <div class="field-info">
                <span class="container-label"><label>CONVOCA</label></span>
                <span class="container-text">{{institucion.nombre}}</span>
            </div>
            
            <div class="field-info">
                <span class="container-label"l><label>OBJETIVO</label></span>
                <span class="container-text">{{descripcion}}</span>
            </div>
            
            <div class="field-info">
                <span class="container-label"><label>BASES</label></span>
                <span class="container-text">{{bases}}</span>
            </div>
            
            <div class="field-info">
                <span class="container-label"><label>PREMIOS</label></span>
                <span class="container-text">{{premios}}</span>
            </div>
        </div>
    </div>
    <div class="row footer">
        <div class="col-md-4 noPaddingLeft">
            <p class="tag">
                {{#join_slash etiquetas}}{{descripcion}}{{/join_slash}}
            </p>
        </div>
        <div class="col-md-8 text-right">
            <a class="btnBorderRed inline-block" href="#" data-id="{{id}}" id="">
                APLICAR
            </a>
            <a class="btnBorderRed inline-block" href="#" data-dismiss="modal">
                CERRAR
            </a>
        </div>
    </div>
</div>
</script>
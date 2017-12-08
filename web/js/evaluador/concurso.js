'use strict';
// Utilizamos un motor de plantillas JS;
var templateConcurso = Handlebars.compile( $('#modal-info-concurso-eva-tpl').html() );
var templateRubricas = Handlebars.compile( $('#modal-info-rubricas-eva-tpl').html() );

helpers.builderWaterfall('#waterfall-concursos-eva-tpl', urlGetAll, function() {
    $(".header:not(.addedClick)").click(showInfoConcurso).addClass("addedClick");
    $(".btnShowConcurso:not(.addedClick)").click(showInfoConcurso).addClass("addedClick");
    //$(".btnGetRubricas:not(.addedClick)").click(showRubricas).addClass("addedClick");
    // Agregamos clase como bandera para evitar agregar mas de
    // una vez el evento click al mismo elemento
});

$('#modalInfoConcurso').on('click', '.btnRubricas', showRubricas);
$('#modalInfoConcurso').on('click', '.btnAplica', aplica);
$('#modalInfoConcurso').on('click', '.btnRegresarConcurso', showInfoConcurso);

function showInfoConcurso(event) {
    event.preventDefault();
    event.stopPropagation();

    var $this = $(this),
        modal = "#modalInfoConcurso",
        el = $this.html(); // Cacheamos el contenido actual del elemento

    var request = $.ajax({
        type: 'POST',
        url: urlGetAplicacion,
        data: 'id=' + $this.data("concurso"),
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            if ($this.find('.title').length) {
                $this.find('.title').append('<p class="colorRed">' + helpers.spinner + '<p>');
            } else {
                $this.html(helpers.spinner);
            }
        }
    });

    request.done(function(data, status, xhr) {
        $(modal + ' .modal-body').html(templateConcurso(data));
        $(modal).modal("show");

        if (data.evalua_concurso){
            $(modal + ' .btnEvaluasConcurso').show();
            $(modal + ' .btnAplica').hide();
        }
    });

    request.always(function(data, status, xhr) {
        // Restauramos el contenido del elemento por el cacheado
        if ($this.find('.title').length) {
            $this.find('.title').find('p').remove();
        } else {
            $this.html(el);
        }
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by helpers.builderWaterfall: '+error);
        }
    });

    return false;
}


function showRubricas(event) {
    event.preventDefault();
    event.stopPropagation();

    var $this = $(this),
        modal = "#modalInfoConcurso",
        el = $this.html(); // Cacheamos el contenido actual del elemento

    var request = $.ajax({
        type: 'POST',
        url: urlGetRubricasByConcurso,
        data: {
            id_concurso: $this.data("id"),
            extras: true,
        },
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $this.html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
            //$('.btnModalProyecto[data-id='+$this.data("id")+']').html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
        }
    });

    request.done(function(data, status, xhr) {
        $(modal + ' .modal-body').html(templateRubricas(data));
        $(modal).modal("show");
    });

    request.always(function(data, status, xhr) {
        // Restauramos el contenido del elemento por el cacheado
        $this.html(el);
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by helpers.builderWaterfall: '+error);
        }
    });

    return false;
}

$('#modalInfoConcurso').on('click', '.btnDownloadBases', function(){
    var $self = $(this);
    var el = $self.html();
    $self.html(helpers.spinner);

    $.fileDownload(urlDownloadBases, {
        httpMethod: "POST",
        data: {_csrf: yii.getCsrfToken(), concurso: $self.data('id')},
        //failMessageHtml: 'Error al descargar el archivo',
        successCallback: function (url) {
            $self.html(el);
        },
        failCallback: function (html, url) {
            $self.html(el);
            var error = html.replace('<pre style="word-wrap: break-word; white-space: pre-wrap;">', '').replace('</pre>','');
            toastr.error('Falló la descarga del archivo: '+error);
        }
    });
});

function aplica(event) {
    event.preventDefault();
    event.stopPropagation();

    var $this = $(this),
        el = $this.html(); // Cacheamos el contenido actual del elemento

    var request = $.ajax({
        type: 'POST',
        url: urlAplica,
        data: {
            id_concurso: $this.data("id"),
        },
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $this.html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
            //$('.btnModalProyecto[data-id='+$this.data("id")+']').html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
        }
    });

    request.done(function(data, status, xhr) {
        $('#modalInfoConcurso .btnEvaluasConcurso').show();
        $('#modalInfoConcurso .btnAplica').hide();
        toastr.success('Ha aplicado para evaluar este concurso.');
    });

    request.always(function(data, status, xhr) {
        // Restauramos el contenido del elemento por el cacheado
        $this.html(el);
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by helpers.builderWaterfall: '+error);
        }
    });

    return false;
}
'use strict';
// Utilizamos un motor de plantillas JS;
var templateEvaluacion= Handlebars.compile( $('#evaluacion-proyecto-tpl').html() );
var currentRubrica = 0;
var rubricas = null;

/*$('.evaluacionFinalizada').click(function(event) {
    event.preventDefault();
    event.stopPropagation();

    var $this = $(this),
        modal = "#modalPuntajeProyecto",
        el = $this.html(); // Cacheamos el contenido actual del elemento

    $this.html(helpers.spinner);

    $.ajax({
        type: 'POST',
        url: urlGetPuntaje,
        data: {
            concurso: $this.data("c"),
            proyecto: $this.data('p'),
            _csrf:  yii.getCsrfToken()
        },
        dataType: 'json'
    })
    .done(function(data, status, xhr) {
        if (data.error) {
            toastr.error('Error: '+data.message);
        } else {
            data.url_micrositio = urlMicrositio;
            $(modal + ' .modal-body').html(templatePuntajeProyecto(data));
            $(modal).modal("show");
        }

        $this.html(el);

    });
});*/

$(document).ready(function() {
    /* Para las preguntas geograficas */
    $( "#dialogMapaPuntoRadial" ).dialog({
        autoOpen: false,
        width: 550,
        height: 400,
        //position: {of: "#modalFrmProyecto .modal-body", within: "#modalFrmProyecto .modal-body"},
        open: function(event, ui) {
            $('#dialogMapaPuntoRadial').parent().css('position', 'fixed');
            $('#dialogMapaPuntoRadial').parent().css('top', '80px');

            if ($('#dialogMapaPuntoRadial').parent().css('left') == '0px') {
                var left = ($(document).outerWidth() - $('#dialogMapaPuntoRadial').parent().outerWidth()) / 2;
                $('#dialogMapaPuntoRadial').parent().css('left', left+'px');
            }
        }
    });

    // Al dialog se le aplica display none para evitar el parpadeo al iniciar la página
    // Por lo tanto despues de cargar la página, se elimina el display none
    $( "#dialogMapaPuntoRadial" ).show();

    $( "#dialogMapaPoligono" ).dialog({
        autoOpen: false,
        width: 550,
        height: 400,
        //position: {of: "#modalFrmProyecto .modal-body", within: "#modalFrmProyecto .modal-body"},
        open: function(event, ui) {
            $('#dialogMapaPoligono').parent().css('position', 'fixed');
            $('#dialogMapaPoligono').parent().css('top', '80px');

            if ($('#dialogMapaPoligono').parent().css('left') == '0px') {
                var left = ($(document).outerWidth() - $('#dialogMapaPoligono').parent().outerWidth()) / 2;
                $('#dialogMapaPoligono').parent().css('left', left+'px');
            }
        }
    });

    // Al dialog se le aplica display none para evitar el parpadeo al iniciar la página
    // Por lo tanto despues de cargar la página, se elimina el display none
    $( "#dialogMapaPoligono" ).show();

    $( "#dialogMapaPunto" ).dialog({
        autoOpen: false,
        width: 550,
        height: 400,
        //position: {of: "#modalFrmProyecto .modal-body", within: "#modalFrmProyecto .modal-body"},
        open: function(event, ui) {
            $('#dialogMapaPunto').parent().css('position', 'fixed');
            $('#dialogMapaPunto').parent().css('top', '80px');

            if ($('#dialogMapaPunto').parent().css('left') == '0px') {
                var left = ($(document).outerWidth() - $('#dialogMapaPunto').parent().outerWidth()) / 2;
                $('#dialogMapaPunto').parent().css('left', left+'px');
            }
        }
    });

    // Al dialog se le aplica display none para evitar el parpadeo al iniciar la página
    // Por lo tanto despues de cargar la página, se elimina el display none
    $( "#dialogMapaPunto" ).show();

    getRubricas();

    $('#evaluacion_rubrica').on('click', '.btnNext', sendEvaluacionRubrica);
    $('#evaluacion_rubrica').on('click', '.btnPrev', prevRubrica);

    $('#evaluacion_rubrica').on('click','.btnDownloadDocumento',function(event){
        var $self = $(this);
        var el = $self.html();
        $self.html(helpers.spinner);

        $.fileDownload(urlDownloadDocumento, {
            httpMethod: "POST",
            data: {_csrf: yii.getCsrfToken(), concurso: $self.data('concurso'),proyecto: $self.data('proyecto'),pregunta: $self.data('id')},
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
});

function sendEvaluacionRubrica(event) {
    event.stopPropagation();
    event.preventDefault();
    var icono = $('#evaluacion_rubrica .btnNext').html()

    $('#evaluacion_rubrica .btnNext').html(helpers.spinner);

    $.ajax({
        type: 'POST',
        url: urlSetEvaluacionRubrica,
        data: {
            concurso: concurso,
            proyecto: proyecto,
            rubrica: rubricas['rubricas'][currentRubrica].id,
            calificacion: $('#calificacion').val(),
            comentarios: $('#comentarios').val(),
            _csrf:  yii.getCsrfToken()
        },
        dataType: 'json'
    })
    .done(function(data, status, xhr) {
        $('#evaluacion_rubrica .btnNext').html(icono);

        nextRubrica(event);
    });
}

function getRubricas() {
    $.ajax({
        type: 'POST',
        url: urlGetRubricasEvaluar,
        data: {
            concurso: concurso,
            proyecto: proyecto,
            _csrf:  yii.getCsrfToken()
        },
        dataType: 'json'
    })
    .done(function(data, status, xhr) {
        if (data.rubricas.length == 0) {
            toastr.error('No se encontraron rúbricas para evaluar');
            $('#evaluacion_rubrica').html('');
            return false;
        }

        rubricas = data;
        rubricas.concurso = concurso;
        rubricas.proyecto = proyecto;
        buildRubrica();
        $('.respuesta_geografica').click(showDialogMapa);
    });
}

function buildRubrica() {
    rubricas['rubricas'][currentRubrica].concurso = concurso;
    rubricas['rubricas'][currentRubrica].proyecto = proyecto;
    $('#evaluacion_rubrica').html(templateEvaluacion(rubricas['rubricas'][currentRubrica]));
    $('#no_current_rubrica').html(currentRubrica+1);
    $('#total_rubricas').html(rubricas['rubricas'].length);
    $('#calificacion option[value='+rubricas['rubricas'][currentRubrica].calificacion+']').prop('selected', true);
}

function nextRubrica(event)
{
    event.stopPropagation();
    event.preventDefault();

    if (currentRubrica == (rubricas['rubricas'].length - 1)) {
      //  toastr.success('No existen más rúbricas que evaluar.');
        toastr.success('Evaluación finalizada.');
        return false;
    }

    currentRubrica++;

    buildRubrica();
}

function prevRubrica(event) {
    event.stopPropagation();
    event.preventDefault();

    if (currentRubrica == 0) {
        //toastr.error('No existen más rúbricas que evaluar.');
        toastr.success('Evaluación finalizada.');
        return false;
    }

    currentRubrica--;

    buildRubrica();
}

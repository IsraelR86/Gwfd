'use strict';
// Utilizamos un motor de plantillas JS;
var templateEvaluaciones = Handlebars.compile( $('#modal-evaluaciones-tpl').html() );
var templatePuntajeProyecto= Handlebars.compile( $('#modal-puntaje-proyecto-tpl').html() );

helpers.builderWaterfall('#waterfall-evaluaciones-tpl', urlGetAllAvailables+'?finalizados=true', function() {
    helpers.builderTooltipster('.iconGanadores', {position: 'left'});
    
    $(".btnEvaluaciones:not(.addedClick)").click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        
        mostrarEvaluaciones($(this), '#modalEvaluaciones');
        
        return false;
    }).addClass("addedClick");
    // Agregamos clase como bandera para evitar agregar mas de 
    // una vez el evento click al mismo elemento
});

$('#modalEvaluaciones').on('click', '.btnViewEvaluacion', function(event){
    $(this).html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
    $(this).addClass('visible');
    
    $.ajax({
        type: 'POST',
        url: urlGetPuntaje,
        data: {
            concurso: $(this).data("concurso"),
            proyecto: $(this).data('proyecto'),
            _csrf:  yii.getCsrfToken()
        },
        dataType: 'json'
    })
    .done(function(data, status, xhr) {
        if (data.error) {
            toastr.error('Error: '+data.message);
            $('#modalEvaluaciones').modal('hide');
        } else {
            data.url_micrositio = urlMicrositio;
            var tablaPuntaje = templatePuntajeProyecto(data);
            
            $('#modalEvaluaciones .modal-body').fadeOut('slow', function() {
                $('#modalEvaluaciones .modal-body').html(tablaPuntaje);
                $('#modalEvaluaciones .modal-body').fadeIn();
            });
        }
    });
});

function mostrarEvaluaciones($this, modal) {
    var request = $.ajax({
        type: 'POST',
        url: urlGetEvaluaciones,
        data: 'id_concurso=' + $this.data("id"),
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $this.html(helpers.spinner);
        }
    });

    request.done(function(data, status, xhr) {
        if (data.evaluadores.length == 0) {
            toastr.error('No se encontraron evaluadores para este concurso');
        } else {
            data.concurso = $this.data("id");
            data.title_header = $this.data('nombre');
            data.url_link = urlGanadores + '?c='+$this.data('id');
            data.texto_link = 'GANADORES';
            $(modal + ' .modal-body').html(templateEvaluaciones(data));
            $(modal).modal("show");
        }
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by helpers.builderWaterfall: '+error);
        }
    });
    
    request.always(function() {
        $this.html('EVALUACIONES');
    });
}


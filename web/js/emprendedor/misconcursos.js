'use strict';
// Utilizamos un motor de plantillas JS;
var templateConcurso = Handlebars.compile( $('#modal-aplicacion-tpl').html() );
var templatePuntajeProyecto= Handlebars.compile( $('#modal-puntaje-proyecto-tpl').html() );

helpers.builderWaterfall('#waterfall-mi-concurso-tpl', urlGetAll, function() {
    $(".btnModalConcurso:not(.addedClick)").click(showModalConcurso).addClass("addedClick");
    // Agregamos clase como bandera para evitar agregar mas de 
    // una vez el evento click al mismo elemento
    
    helpers.builderTooltipster('.tooltipster', {position: 'bottom'});
});

$('#modalPuntajeProyecto').on('click', '#btnRegresarAModalConcurso', showModalConcurso);

function showModalConcurso(event) {
    event.preventDefault();
    event.stopPropagation();
    
    var $this = $(this),
        modal = "#modalInfoConcurso",
        el = $this.html(); // Cacheamos el contenido actual del elemento
    
    var request = $.ajax({
        type: 'POST',
        url: urlGetAplicacion,
        data: 'proyecto=' + $this.data("proyecto")+'&concurso='+$this.data("concurso"),
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $this.html(helpers.spinner);
        }
    });

    request.done(function(data, status, xhr) {
        $(modal + ' .modal-body').html(templateConcurso(data));

        if ($this.prop('id') == 'btnRegresarAModalConcurso') {
            $('#modalPuntajeProyecto').data("mostrarconcurso", true);
            $('#modalPuntajeProyecto').modal("hide");
        } else {
            $(modal).modal("show");
        }
        
        $('.tooltipster').tooltipster('hide');
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

$('#modalInfoConcurso').on('click', '.btnAbandonarConcurso', function () {
    var $this = $(this);
    
    $.jAlert({'type': 'confirm', 'confirmBtnText': 'Si', 'content': '¿Quieres abandonar y dejar de participar en este concurso?', 
    'onConfirm': function() {
        $.ajax({
            type: 'POST',
            url: urlAbandonarConcurso,
            data: {
                concurso: $this.data("concurso"),
                proyecto: $this.data('proyecto'),
                _csrf:  yii.getCsrfToken()
            },
            dataType: 'json',
            beforeSend: function(xhr, settings) {
                // Reemplazamos el contenido actual del elemento por un spinner
                $this.html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
            }
        })
        .done(function(data, status, xhr) {
            $this.html('ABANDONAR');
            
            if (data.error) {
                toastr.error('Error: '+data.message);
            } else {
                toastr.success(data.message);
                $('#waterfall').waterfall('removeItems', $('.item[data-concurso='+$this.data("concurso")+'][data-proyecto='+$this.data('proyecto')+']'));
            }
            
            $('#modalInfoConcurso').modal('hide');
        });
    }});
    
});

$('#modalInfoConcurso').on('hidden.bs.modal', function (e) {
    $('.btnPuntajeConcurso').html('PUNTAJE');
    
    if ($(this).data('mostrarpuntaje')) {
        $('#modalPuntajeProyecto').modal('show');
    }
    
    $(this).data('mostrarpuntaje', false);
});

$('#modalPuntajeProyecto').on('hidden.bs.modal', function (e) {
    if ($(this).data('mostrarconcurso')) {
        $('#modalInfoConcurso').modal('show');
    }
    $(this).data('mostrarconcurso', false);
});

$('#modalInfoConcurso').on('click', '.btnPuntajeProyecto', function () {
    var $this = $(this);
    $this.html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
    
    $.ajax({
        type: 'POST',
        url: urlGetPuntaje,
        data: {
            concurso: $this.data("concurso"),
            proyecto: $this.data('proyecto'),
            _csrf:  yii.getCsrfToken()
        },
        dataType: 'json'
    })
    .done(function(data, status, xhr) {
        if (data.error) {
            toastr.error('Error: '+data.message);
        } else {
            data.url_micrositio = urlMicrositio;
            data.id_concurso = $this.data("concurso");
            data.btnRegresar = '<a class="btnBorderRed" id="btnRegresarAModalConcurso" href="#" data-proyecto="'+data.id_proyecto+'" data-concurso="'+data.id_concurso+'">REGRESAR</a>'
            // Variable bandera de ayuda para mostrar el puntaje cuando el modal de mi aplicación se cierre
            $('#modalInfoConcurso').data('mostrarpuntaje', true);
            $('#modalPuntajeProyecto .modal-body').html(templatePuntajeProyecto(data));
        }
        
        $('#modalInfoConcurso').modal('hide');
    });
});


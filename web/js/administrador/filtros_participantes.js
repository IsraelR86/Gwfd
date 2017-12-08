'use strict';
// Utilizamos un motor de plantillas JS;
var template = Handlebars.compile( $('#filtros-participantes-tpl').html() );

$(document).ready(function() {
    loadFiltros().done(function() {
        getFiltros().done(function() {
            $("select").select2({
                placeholder: "Seleccione una o mas opciones",
            });
        });
    });
    
    $('#btnGuardarFiltros').click(function(event) {
        event.preventDefault();
        sendFiltros();
    });
});

function setCheckboxesClick () {
    // Estilizamos los checkboxes
    helpers.builderiCheck();
    
    // Se utiliza el evento ifClicked porque es el que lanza el plugin iCheck
    $('#tbl_filtros_participantes').on('ifChanged', 'input[type="checkbox"][name="filtro"]', function () {
        var $tr = $(this).closest('tr');
        
        if ($(this).prop('checked')) {
            $tr.find('.div_filtro').slideDown();
        } else {
            $tr.find('.div_filtro').slideUp();
        }
    });
}

function loadFiltros() {
    var $container = $('#container_tbl_filtros');
    
    var request = $.ajax({
        type: 'GET',
        url: urlGetAll,
        data: '',
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $container.html(helpers.loadingMsg);
        }
    });

    request.done(function(data, status, xhr) {
        $container.html(template(data));
        
        setCheckboxesClick();
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by loadPreguntas: '+error);
        }
    });
    
    return request;
}

function sendFiltros() {
    var filtros = gatherFiltros();
    
    if (filtros.length == 0) {
        toastr.error('Debe seleccionar los filtros');
        return false;
    }
    
    if (!validarFiltros(filtros)) {
        return false;
    }
    
    var request = $.ajax({
        type: 'POST',
        url: urlSetFiltros,
        data: {
            id_concurso: helpers.getQuerystring('id'),
            filtros: filtros,
            _csrf: yii.getCsrfToken(),
        },
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            $('#btnGuardarCriterios i').removeClass('fa-check');
            $('#btnGuardarCriterios i').addClass('fa-spinner fa-pulse');
        }
    });

    request.done(function(data, status, xhr) {
        if (data.error) {
            toastr.error('Error al procesar los datos: ' + data.message);
        } else {
            toastr.success(data.message);
        }
    });
    
    request.always(function(data, status, xhr) {
        $('#btnGuardarCriterios i').removeClass('fa-spinner fa-pulse');
        $('#btnGuardarCriterios i').addClass('fa-check');
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by loadRubrica: '+error);
        }
    });
    
    return request;
}

function gatherFiltros() {
    var filtros = [];
    
    $('input[type="checkbox"][name="filtro"]:checked').each(function(){
        var $fila = $(this).closest('tr');
        
        var filtro = {
            id_tipo_filtro_participante: $(this).val(),
            restricion: (function (filtro) {
                // 1 Edad
                if ($(filtro).val() == 1) {
                    return $fila.find('[name=restricion]').val()
                    //return $fila.find('[name=valores]:checked').length ? 
                      //  '['+$fila.find('[name=valores]:checked').map(function() { return this.value; }).get().join(', ')+']' : '';
                } 
                // 4 Opción Única
                else {
                    return $fila.find('[name=restricion] option:selected').length ? 
                        '['+$fila.find('[name=restricion] option:selected').map(function() { return this.value; }).get().join(', ')+']' : '';
                } 
            })(this)
        };
        
        filtros.push(filtro);
    });
    
    return filtros;
}

function validarFiltros(listfiltros) {
    $('.alert-danger').removeClass('alert-danger');
    
    for(var c in listfiltros) {
        var $filtro = $('input[type="checkbox"][name="filtro"][value="'+listfiltros[c].id_tipo_filtro_participante+'"]');
        
        if (listfiltros[c].restricion == '' || listfiltros[c].restricion == '[]') {
            // 1 Edad
            if (listfiltros[c].id_tipo_filtro_participante == 1) {
                $filtro.closest('tr').find('[name=restricion]').addClass('alert-danger');
            } else {
                $filtro.closest('tr').find('.select2-selection').addClass('alert-danger');
            }
            $.scrollTo($filtro, 800, {offset:-50});
            toastr.error('Debe asignar un valor a evaluar');
            return false;
        }
    }
    
    return true;
}

function getFiltros() {
    var request = $.ajax({
        type: 'POST',
        url: urlGetFiltros,
        data: {
            concurso: helpers.getQuerystring('id')
        },
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            $('#loadingFiltros').html(helpers.loadingMsg)
        }
    });

    request.done(function(data, status, xhr) {
        for(var item in data) {
            var $filtro = $('input[type="checkbox"][name="filtro"][value="'+data[item].id_tipo_filtro_participante+'"]');
            var $tr = $filtro.closest('tr');
            
            $filtro.iCheck('check');
            
            // 1 Edad
            if (data[item].id_tipo_filtro_participante == 1) {
                $tr.find('[name=restricion]').val(data[item].minimo);
            } else {
                var opciones = $.parseJSON(data[item].restricion);
                for (var i in opciones) {
                    $tr.find('[name=restricion] option[value='+opciones[i]+']').prop('selected', true);
                }
            }
        }
    });
    
    request.always(function(data, status, xhr) {
        $('#loadingFiltros').html('');
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by getFiltros: '+error);
        }
    });
    
    return request;
}
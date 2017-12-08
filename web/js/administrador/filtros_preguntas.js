'use strict';
// Utilizamos un motor de plantillas JS;
var template = Handlebars.compile( $('#filtros-preguntas-tpl').html() );
var current_rubrica = 0;
var cache_rubricas = [];

$(document).ready(function() {
    loadPreguntas().done(function () {
        getCriterios();
    });
    
    $('#btnGuardarCriterios').click(function(event) {
        event.preventDefault();
        sendCriterios();
    });
});

function setCheckboxesClick () {
    // Estilizamos los checkboxes
    helpers.builderiCheck();
    
    // Se utiliza el evento ifClicked porque es el que lanza el plugin iCheck
    $('#tbl_preguntas').on('ifChanged', 'input[type="checkbox"][name="pregunta"]', function () {
        var $tr = $(this).closest('tr');
        
        if ($(this).prop('checked')) {
            $tr.find('.div_criterio').slideDown();
        } else {
            $tr.find('.div_criterio').slideUp();
        }
    });
    
    $('.icon_seccion').click(function() {
        helpers.slideRowTable(this, '.tr_pregunta_seccion_'+$(this).data('id'), 'inactive');
    });
}

function loadPreguntas() {
    var $container = $('#container_tbl_preguntas');
    
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
        
        helpers.builderTooltipster('#container_tbl_preguntas .tooltipster', {position: 'left', maxWidth: 350});
        
        setCheckboxesClick();
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by loadPreguntas: '+error);
        }
    });
    
    return request;
}

function sendCriterios() {
    var criterios = gatherCriterios();
    
    if (criterios.length == 0) {
        toastr.error('Debe seleccionar los criterios');
        return false;
    }
    
    if (!validarCriterios(criterios)) {
        return false;
    }
    
    var request = $.ajax({
        type: 'POST',
        url: urlSetFiltros,
        data: {
            id_concurso: helpers.getQuerystring('id'),
            filtros: criterios,
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

function gatherCriterios() {
    var criterios = [];
    
    $('input[type="checkbox"][name="pregunta"]:checked').each(function(){
        var $fila = $(this).closest('tr');
        
        var criterio = {
            id_pregunta: $(this).val(),
            tipo_filtro: $fila.find('[name=criterio]').val(),
            minimo: $fila.find('[name=valor_x]').length ? $fila.find('[name=valor_x]').val() : '',
            maximo: $fila.find('[name=valor_y]').length ? $fila.find('[name=valor_y]').val() : '',
            arreglo_opcion: (function (pregunta) {
                // 3 Opción Múltiple
                if ($(pregunta).data('tipopregunta') == 3) {
                    return $fila.find('[name=valores]:checked').length ? 
                        '['+$fila.find('[name=valores]:checked').map(function() { return this.value; }).get().join(', ')+']' : '';
                } 
                // 4 Opción Única
                else if ($(pregunta).data('tipopregunta') == 4) {
                    return $fila.find('[name=valor] option:selected').length ? $fila.find('[name=valor] option:selected').val() : ''
                } else {
                    return '';
                }
            })(this),
            validar_copia: $fila.find('[name=validar_copia]:checked').length ? $fila.find('[name=validar_copia]').val() : '',
            comentarios: $fila.find('[name=comentarios]').val()
        };
        
        criterios.push(criterio);
    });
    
    return criterios;
}

function validarCriterios(listcriterios) {
    $('.alert-danger').removeClass('alert-danger');
    
    for(var c in listcriterios) {
        var $pregunta = $('input[type="checkbox"][name="pregunta"][value="'+listcriterios[c].id_pregunta+'"]');
        
        if (listcriterios[c].tipo_filtro == '') {
            $pregunta.closest('tr').find('[name=criterio]').addClass('alert-danger');
            $.scrollTo($pregunta, 800, {offset:-50});
            toastr.error('Debe seleccionar un criterio');
            return false;
        }
        
        switch (parseInt($pregunta.data('tipopregunta'))) {
            case 1: // 1 Texto
                switch (parseInt(listcriterios[c].tipo_filtro)) {
                    case 1: // 1 Más de X caracteres
                        if (listcriterios[c].minimo == '') {
                            $pregunta.closest('tr').find('[name=valor_x]').addClass('alert-danger');
                            $.scrollTo($pregunta, 800, {offset:-50});
                            toastr.error('Debe especificar un valor X a evaluar');
                            return false;
                        }
                        break;
                        
                    case 2: // 2 Menos de Y caracteres
                        if (listcriterios[c].maximo == "") {
                            $pregunta.closest('tr').find('[name=valor_y]').addClass('alert-danger');
                            $.scrollTo($pregunta, 800, {offset:-50});
                            toastr.error('Debe especificar un valor Y a evaluar');
                            return false;
                        }
                        break;
                        
                    case 3: // Entre X y Y caracteres
                        if (listcriterios[c].minimo == '') {
                            $pregunta.closest('tr').find('[name=valor_x]').addClass('alert-danger');
                            $.scrollTo($pregunta, 800, {offset:-50});
                            toastr.error('Debe especificar un valor X a evaluar');
                            return false;
                        }
                        if (listcriterios[c].maximo == "") {
                            $pregunta.closest('tr').find('[name=valor_y]').addClass('alert-danger');
                            $.scrollTo($pregunta, 800, {offset:-50});
                            toastr.error('Debe especificar un valor Y a evaluar');
                            return false;
                        }
                        break;
                }
                break;
                
            case 2: // 2 Numérica
                switch (parseInt(listcriterios[c].tipo_filtro)) {
                    case 4: // Mayor o igual a X
                    case 7: // Igual a X
                    case 8: // Distinto a X
                        if (listcriterios[c].minimo == '') {
                            $pregunta.closest('tr').find('[name=valor_x]').addClass('alert-danger');
                            $.scrollTo($pregunta, 800, {offset:-50});
                            toastr.error('Debe especificar un valor X a evaluar');
                            return false;
                        }
                        break;
                    
                    case 5: // Menor o igual a Y
                        if (listcriterios[c].maximo == "") {
                            $pregunta.closest('tr').find('[name=valor_y]').addClass('alert-danger');
                            $.scrollTo($pregunta, 800, {offset:-50});
                            toastr.error('Debe especificar un valor Y a evaluar');
                            return false;
                        }
                        break;
                        
                    case 6: // Entre X y Y
                        if (listcriterios[c].minimo == '' && listcriterios[c].maximo == "") {
                            $pregunta.closest('tr').find('[name=valor_x]').addClass('alert-danger');
                            $pregunta.closest('tr').find('[name=valor_y]').addClass('alert-danger');
                            $.scrollTo($pregunta, 800, {offset:-50});
                            toastr.error('Debe especificar un valor X y Y a evaluar');
                            return false;
                        }
                }
                break;
                
            case 3: // 3 Opción Múltiple
                if (listcriterios[c].arreglo_opcion == "" || listcriterios[c].arreglo_opcion == "[]") {
                    $pregunta.find('.div_criterio').addClass('alert-danger');
                    $.scrollTo($pregunta, 800, {offset:-50});
                    toastr.error('Debe especificar por lo menos un valor a evaluar');
                    return false;
                }
                break;
                
            case 4: // 4 Opción Única
                if (listcriterios[c].arreglo_opcion == "" || listcriterios[c].arreglo_opcion == "[]") {
                    $pregunta.closest('tr').find('[name=valor]').addClass('alert-danger');
                     $.scrollTo($pregunta, 800, {offset:-50});
                    toastr.error('Debe especificar por lo menos un valor a evaluar');
                    return false;
                }
        }
    }
    
    return true;
}

function getCriterios() {
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
            var $pregunta = $('input[type="checkbox"][name="pregunta"][value="'+data[item].id_pregunta+'"]');
            var $tr = $pregunta.closest('tr');
            
            $pregunta.iCheck('check');
            $tr.find('[name=validar_copia]').iCheck(data[item].validar_copia==1 ? 'check' : 'uncheck');
            $tr.find('[name=criterio] option[value='+data[item].tipo_filtro+']').prop('selected', true);
            $tr.find('[name=comentarios]').val(data[item].comentarios);
            
            // 1 Texto, 2 Numérica
            if ($pregunta.data('tipopregunta') == 1 || $pregunta.data('tipopregunta') == 2) {
                $tr.find('[name=valor_x]').val(data[item].minimo);
                $tr.find('[name=valor_y]').val(data[item].maximo);
            }
            
            // 3 Opción Múltiple
            if ($pregunta.data('tipopregunta') == 3) {
                var opciones = $.parseJSON(data[item].arreglo_opcion);
                for (var i in opciones) {
                    $tr.find('[name=valores][value='+opciones[i]+']').iCheck('check');
                }
            }
            
            // 4 Opción Única
            if ($pregunta.data('tipopregunta') == 4) {
                $tr.find('[name=valor] option[value='+data[item].arreglo_opcion+']').prop('selected', true);
            }
        }
    });
    
    request.always(function(data, status, xhr) {
        $('#loadingFiltros').html('');
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by getCriterios: '+error);
        }
    });
    
    return request;
}
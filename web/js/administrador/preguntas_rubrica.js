'use strict';
// Utilizamos un motor de plantillas JS;
var template = Handlebars.compile( $('#preguntas-rubrica-tpl').html() );
var current_rubrica = 0;
var cache_rubricas = [];

$(document).ready(function() {
    loadPreguntas().done(function() {
        loadRubrica();
    });

    $('.pagerSeccion').on('click', '.btnPrev', function() {
        event.preventDefault();

        if (current_rubrica > 0) {
            var $icon = $(this).find('.icon');
            var $cache_icon = $icon.html();
            $icon.html(helpers.spinner);

            sendPreguntasRubrica().done(function() {
                $icon.html($cache_icon);
                $('#container_tbl_preguntas_rubricas').slideUp("slow").slideDown("slow");
                current_rubrica--;
                setRubrica();
            });
        }
    });

    $('.pagerSeccion').on('click', '.btnNext', function(event) {
        // En la ultima ejecución dejamos que redireccione a la url de href
        if (current_rubrica == cache_rubricas.length) {
            return true;
        }

        if (current_rubrica == cache_rubricas.length-1) {
            // En la última rúbrica cambiamos el botón por finalizar y el href a la url de concursos
            $(this).attr('href', urlConcursos);
            $(this).html('Finalizar <span class="icon"><img src="'+homeUrl+'img/Next.png"></span>');
        }

        event.preventDefault();

        if (current_rubrica < cache_rubricas.length) {
            var $icon = $(this).find('.icon');
            var $cache_icon = $icon.html();
            $icon.html(helpers.spinner);

            sendPreguntasRubrica().done(function() {
                $icon.html($cache_icon);
                $('#container_tbl_preguntas_rubricas').slideUp("slow").slideDown("slow");
                current_rubrica++;
                setRubrica();
            });
        }
    });
});

function loadPreguntas() {
    var $container = $('#container_tbl_preguntas_rubricas');

    var request = $.ajax({
        type: 'GET',
        url: urlGetAll,
        data: {include_preguntas_concurso:true,id_concurso:helpers.getQuerystring('id')},
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $container.html(helpers.loadingMsg);
        }
    });

    request.done(function(data, status, xhr) {
        $container.html(template(data));

        helpers.builderTooltipster('#container_tbl_preguntas_rubricas .tooltipster', {position: 'left', maxWidth: 350});
        setCheckboxesClick();
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by loadPreguntas: '+error);
        }
    });

    return request;
}

function loadRubrica() {
    var request = $.ajax({
        type: 'POST',
        url: urlGetByConcurso,
        data: 'id='+helpers.getQuerystring('id')+'&_csrf='+yii.getCsrfToken(),
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $('#title_rubrica').html(helpers.loadingMsg);
        }
    });

    request.done(function(data, status, xhr) {
        if (data.length == 0) {
            $('#title_rubrica').html('No se encontraron rúbricas para este concurso');
            $('#title_rubrica').addClass('alert-danger');
            $('.pagerSeccion').hide();
        }

        cache_rubricas = data;
        setRubrica();
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by loadRubrica: '+error);
        }
    });

    return request;
}

function setRubrica() {
    if (cache_rubricas[current_rubrica]) {
        $('#title_rubrica').html(cache_rubricas[current_rubrica].nombre);
        $('#desc_rubrica').html(cache_rubricas[current_rubrica].descripcion);

        setPreguntasRubrica();
    }
}

function setPreguntasRubrica() {
    $('input').iCheck('uncheck');

    $.each(cache_rubricas[current_rubrica].preguntas, function(key, value) {
        $('input[type="checkbox"][name="pregunta"][value="'+value.id+'"]').iCheck('check');
    });

    $.each(cache_rubricas[current_rubrica].preguntasConcurso, function(key, value) {
        $('input[type="checkbox"][name="pregunta_concurso"][value="'+value.id+'"]').iCheck('check');
    });
}

function gatherPreguntasRubrica() {
    var preguntas = [];
    var preguntasConcurso = [];

    $('input[type="checkbox"][name="pregunta"]:checked').each(function (index) {
        preguntas.push({id: $(this).val(), concurso:0});
    });
    cache_rubricas[current_rubrica].preguntas = preguntas;


    $('input[type="checkbox"][name="pregunta_concurso"]:checked').each(function (index) {
        preguntasConcurso.push({id: $(this).val(), concurso:1});
    });

    cache_rubricas[current_rubrica].preguntasConcurso = preguntasConcurso;
}

function sendPreguntasRubrica() {
    gatherPreguntasRubrica();

    var request = $.ajax({
        type: 'POST',
        url: urlSetPreguntas,
        data: {
            id: cache_rubricas[current_rubrica].id,
            preguntas: cache_rubricas[current_rubrica].preguntas,
            preguntasConcurso: cache_rubricas[current_rubrica].preguntasConcurso,
            _csrf: yii.getCsrfToken(),
        },
        dataType: 'json'
    });

    request.done(function(data, status, xhr) {
        if (data.error) {
            toastr.error('Error al procesar los datos: ' + data.message);
        }
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by sendPreguntasRubrica: '+error);
        }
    });

    return request;
}

function setCheckboxesClick () {
    // Estilizamos los checkboxes
    helpers.builderiCheck();

    // Se utiliza el evento ifClicked porque es el que lanza el plugin iCheck
    $('#tbl_preguntas_rubricas').on('ifClicked', '.chk_seccion', function () {
        var $this = $(this);

        if (!$this.prop('checked')) { // Aplique la logica inversa porque ifClicked no detecta correctamente el checked
            $('.chk_pregunta_seccion_'+$this.val()).iCheck('check');
        } else {
            $('.chk_pregunta_seccion_'+$this.val()).iCheck('uncheck');
        }

        if (!$this.prop('checked')) {
            $('.chk_pregunta_concurso_'+$this.val()).iCheck('check');
        } else {
            $('.chk_pregunta_concurso_'+$this.val()).iCheck('uncheck');
        }
    });

    // Revisa si todos los elementos de una seccion esta seleccionadas, selecciona el header de la seccion
    $('#tbl_preguntas_rubricas').on('ifChanged', 'input[type="checkbox"][name="pregunta"]', function () {
        var clase = $(this).attr('class').split('_');
        var seccion = clase[clase.length-1];

        if ($('input[name="pregunta"][class=chk_pregunta_seccion_'+seccion+']:not(:checked)').length == 0) {
            $('input[name="seccion"][value='+seccion+']').iCheck('check');
            $('input[name="seccion"][value='+seccion+']').prop('checked', true);
        } else {
            $('input[name="seccion"][value='+seccion+']').iCheck('uncheck');
            $('input[name="seccion"][value='+seccion+']').prop('checked', false);
        }
    });

   $('#tbl_preguntas_rubricas').on('ifChanged', 'input[type="checkbox"][name="pregunta_concurso"]', function () {
        var clase = $(this).attr('class').split('_');
        var seccion = clase[clase.length-1];

        if ($('input[name="pregunta_concurso"][class=chk_pregunta_concurso_'+seccion+']:not(:checked)').length == 0) {
            $('input[name="seccion"][value='+seccion+']').iCheck('check');
            $('input[name="seccion"][value='+seccion+']').prop('checked', true);
        } else {
            $('input[name="seccion"][value='+seccion+']').iCheck('uncheck');
            $('input[name="seccion"][value='+seccion+']').prop('checked', false);
        }
    });

    $('.icon_seccion').click(function() {
        helpers.slideRowTable(this, '.tr_pregunta_seccion_'+$(this).data('id'), 'inactive');
    });
}

'use strict';
// Utilizamos un motor de plantillas JS;
var templateInfoProyecto = Handlebars.compile( $('#modal-info-proyecto-tpl').html() );

helpers.builderWaterfall('#waterfall-proyecto-tpl', urlGetAll, function() {
    $(".btnModalProyecto:not(.addedClick)").click(showInfoProyecto).addClass("addedClick");
    // Agregamos clase como bandera para evitar agregar mas de
    // una vez el evento click al mismo elemento

    helpers.builderTooltipster('.tooltipster', {position: 'bottom'});
});

$('#modalInfoProyecto').on('click', '.btnEditarProyecto', showEditProyecto);

function showInfoProyecto(event) {
    event.preventDefault();
    event.stopPropagation();

    var $this = $(this),
        modal = "#modalInfoProyecto",
        el = $this.html(); // Cacheamos el contenido actual del elemento

    $.ajax({
        type: 'POST',
        url: urlGetById,
        data: {
            id: $this.data("id"),
            includeExtras: true,
            _csrf:  yii.getCsrfToken()
        },
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $this.html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
        }
    })
    .done(function(data, status, xhr) {
        $this.html(el);
        data.url_micrositio = urlMicrositio;
        $(modal + ' .modal-body').html(templateInfoProyecto(data));

        evalClass(data.url_video, '#iconHasVideo');
        evalClass(data.bytelogo, '#iconHasLogo');
        evalClass(data.byteimagen, '#iconHasImagen');

        $(modal).modal('show');

        $(modal).on('shown.bs.modal', function(){
            $('#chartCompletado').easyPieChart({
                easing: 'easeOutBounce',
                lineWidth: 5,
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent').text(Math.round(percent));
                }
            });

            $('#chartParticipacion').easyPieChart({
                easing: 'easeOutBounce',
                lineWidth: 5,
                onStep: function(from, to, percent) {
                    $(this.el).find('.number').text( $(this.el).data('participacion')+'v' );
                }
            });
        })

        $('.tooltipster').tooltipster('hide');
    });
}

function evalClass(val, selector) {
    if (val != '') {
        $(selector).addClass('fa-check-circle-o icon');
    } else {
        $(selector).addClass('fa-circle-o colorGray');
    }
}

function showEditProyecto(event) {
    event.preventDefault();
    event.stopPropagation();

    var $this = $(this),
        modal = "#modalFrmProyecto",
        el = $this.html(); // Cacheamos el contenido actual del elemento

    var request = $.ajax({
        type: 'POST',
        url: urlGetById,
        data: {
            id: $this.data("id"),
            includeExtras: true,
            _csrf:  yii.getCsrfToken()
        },
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $this.html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
            $('.btnModalProyecto[data-id='+$this.data("id")+']').html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
        }
    });

    request.done(function(data, status, xhr) {
        $("#modalInfoProyecto").modal('hide');

        loadFrmProyecto($this).done(function() {
            $('.btnModalProyecto[data-id='+$this.data("id")+']').html('<img src="'+homeUrl+'img/Edit.png">');
            $(modal + ' .title').html('EDITA TU PROYECTO');
            // Asigna el id del proyecto que actualmente se esta ejecutando
            proyecto = $this.data("id");

            $this.html(el);

            $(modal + ' .modal-body .header .title').html(data.nombre);
            $(modal + ' .modal-body .header img').attr('src', data.byteimagen);
            $(modal + ' .modal-body .bgBodyInfo .body').html(data.descripcion);


            $(modal + ' input[name=nombre]').val(data.nombre).closest('.field-input').addClass('answered');
            $(modal + ' textarea[name=descripcion]').val(data.descripcion).closest('.field-input').addClass('answered');
            $(modal + ' input[name=url_video]').val(data.url_video).closest('.field-input').addClass('answered');
            $(modal + ' input[name=integrantes]').val(data.integrantes).closest('.field-input').addClass('answered');

            // Mostrar las imagenes subieron al registrar el proyecto, en caso de existir
            if (data.byteimagen != '') {
                $('#container-fileuploaderImagen .ajax-file-upload-container').html(
                    '<div class="ajax-file-upload-statusbar current" style="width: 400px;">\n\
                        <img class="ajax-file-upload-preview" src="'+data.byteimagen+'" style="width: 100px; height: 100px;">\n\
                        <div class="ajax-file-upload-filename"></div>\n\
                        <div class="ajax-file-upload-red ajax-file-upload-cancel" style="">Cancelar</div>\n\
                    </div>').closest('.field-input').addClass('answered');
                $('#container-fileuploaderImagen .ajax-file-upload-container .ajax-file-upload-cancel').click(function() {
                    $(this).parent().remove();
                });
            }

            if (data.bytelogo != '') {
                $('#container-fileuploaderLogo .ajax-file-upload-container').html(
                    '<div class="ajax-file-upload-statusbar current" style="width: 400px;">\n\
                        <img class="ajax-file-upload-preview" src="'+data.bytelogo+'" style="width: 100px; height: 100px;">\n\
                        <div class="ajax-file-upload-filename"></div>\n\
                        <div class="ajax-file-upload-red ajax-file-upload-cancel" style="">Cancelar</div>\n\
                    </div>').closest('.field-input').addClass('answered');
                $('#container-fileuploaderLogo .ajax-file-upload-container .ajax-file-upload-cancel').click(function() {
                    $(this).parent().remove();
                });
            }

            // Llenar los selects con las opciones guardadas
            var arrayEtiquetas = [];

            for(var etiqueta in data.etiquetas) {
                arrayEtiquetas.push(data.etiquetas[etiqueta].id);
            }

            select2Etiquetas.val(arrayEtiquetas).trigger("change").closest('.field-input').addClass('answered');

            var arrayIntegrantes = [];

            for(var emprendedor in data.emprendedores) {
                var option = '<option value="'+data.emprendedores[emprendedor].id+'" selected>'+data.emprendedores[emprendedor].nombre+'</option>';
                select2Integrantes.append(option);
            }

            select2Integrantes.trigger("change").closest('.field-input').addClass('answered');
            var puntos = null;

            // Asignar las respuestas a las preguntas de las secciones
            if (data.respuestas) {
                for (var iRespuesta in data.respuestas) {
                    var fieldPregunta = $('.field-input[data-pregunta='+data.respuestas[iRespuesta].id_pregunta+']');
                    fieldPregunta.addClass('answered');

                    switch (parseInt(fieldPregunta.data('tipopregunta'))) {
                        case 1: // 1 Texto
                        case 5: // 5 Hipervínculo
                            fieldPregunta.find('[name=respuesta]').val(data.respuestas[iRespuesta].respuesta_texto);
                            break;
                        case 2: // 2 Numérica
                            fieldPregunta.find('[name=respuesta]').val(data.respuestas[iRespuesta].respuesta_numerica);
                            break;

                        case 3: // 3 Opción Múltiple
                            var valores = jQuery.parseJSON(data.respuestas[iRespuesta].respuesta_opcion);

                            for (var val in valores) {
                                fieldPregunta.find('[name=respuesta][value='+valores[val]+']').iCheck('check');
                            }
                            break;

                        case 4: // 4 Opción Única
                            fieldPregunta.find('[name=respuesta] option[value='+data.respuestas[iRespuesta].respuesta_opcion+']').prop('selected', true);
                            break;

                        case 6: // 6 Punto Radial Geográfico
                        case 7: // 7 Polígono Geográfico
                        case 8: // 8 Punto Geográfico
                            if (data.respuestas[iRespuesta].respuesta_geografica != '') {
                                puntos = jQuery.parseJSON(data.respuestas[iRespuesta].respuesta_geografica);
                                fieldPregunta.data('respuesta', puntos);
                                fieldPregunta.find('.num_puntos').html(' ('+puntos.length+')');
                            }
                            break;
                    }
                }
            }

            $('.progess-bar span').each(function(){
                if ($('.seccion[data-id='+$(this).data('seccion')+'] .field-input:not(.answered)').length == 0) {
                    $(this).addClass('complete');
                }
            });

            //modalFrmProyecto
        });
    });

    request.fail(function(xhr, status, error) {
        $this.html(el);

        if (config.isDebugging()) {
            console.log('Error '+status+' by helpers.builderWaterfall: '+error);
        }
    });

    return false;
}
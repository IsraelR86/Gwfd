'use strict';
// Utilizamos un motor de plantillas JS;
var templateFrmConcurso = Handlebars.compile( $('#modal-frm-concurso-tpl').html() );
var templateSeccionEvaluadores = Handlebars.compile( $('#seccion-evaluadores-tpl').html() );
var templateItemEvaluador = Handlebars.compile( $('#evaluador-tpl').html() );
var templateSeccionRubricas = Handlebars.compile( $('#seccion-rubricas-tpl').html() );
var templatePregunta = Handlebars.compile( $('#pregunta-tpl').html() );
var fileUploaderImagen, fileUploaderBases, concurso = null;
var myDeferredDatosGenerales = $.Deferred();
var select2Etiquetas = null, select2Evaluadores;
var configUploadFile = {
    multiple: false,
    dragDrop: false,
    maxFileCount: 1,
    allowedTypes: 'jpg,jpeg,png,gif',
    acceptFiles: "image/*",
    maxFileSize: 2*1024*1024,
    cancelStr: "Cancelar",
    extErrorStr: "no es aceptado. Extensiones permitidas: ",
    sizeErrorStr: "excede el tamaño límite. Tamaño límite permitido: ",
    maxFileCountErrorStr: "no esta permitido. El número máximo de archivos es: ",
    showPreview: true,
    previewHeight: "100px",
    previewWidth: "100px",
    autoSubmit: false,
    sequential: true,
    sequentialCount: 1,
    onError: function(files, status, errMsg, pd) {
        toastr.error(errMsg);
        myDeferredDatosGenerales.reject();
    },
};

$(".btnConcursoNuevo").click(function(event) {
    event.preventDefault();
    event.stopPropagation();
    loadFrmConcurso($(this));
    return false;
});

$(document).ready(function () {
    $( "#dialogTipoPreguntaConcurso" ).dialog({
        autoOpen: false,
        width: 550,
        height: 400,
        position: {of: "#modalFrmConcurso .modal-body", within: "#modalFrmConcurso .modal-body"},
        open: function(event, ui) {
            $('#dialogTipoPreguntaConcurso').parent().css('position', 'fixed');
            $('#dialogTipoPreguntaConcurso').parent().css('top', '80px');
        }
    });

    $('#modalFrmConcurso').on('click', '.btnFinalizar', function(event) {
        var $currentSeccion = $('.seccion .pagina.current').parent();

        if (!$currentSeccion.hasClass('complete')) {
            // Si no se ha enviado la seccion actual, se envia de forma automatica
            $currentSeccion.find('.pagina:last-child').addClass('current');
            $('.pagerFrmConcurso .btnNext').trigger('click');
        }

        $('#modalFrmConcurso').modal('hide');
        $('#modalConfirmConcursoNuevo').modal('show');
    });

    $('#modalFrmConcurso').on('click', '#btnAddPregunta', function(event) {

        $('#seccion_preguntas').append( templatePregunta({tiposPreguntaConcurso: tiposPreguntaConcurso}) );
    });

    $('#modalFrmConcurso').on('click', '.delPregunta', function(event) {
        $(this).closest('.field-input.pregunta').remove();
    });

    $('.modal-body').on('click', '.delEvaluador', function(event) {
        $(this).closest('.itemEvaluador').fadeOut('slow',function(){ $(this).remove(); });
    });

    $('#modalFrmConcurso').on('click', '.pagerFrmConcurso .btnPrev', function(event) {
        event.preventDefault();

        var $currentSeccion = $('.seccion .pagina.current').parent();
        var $ante = $currentSeccion.find('.pagina.current').prev();

        // Si no hay mas páginas que mostrar
        if ($ante.length == 0) {
            // Retrocedemos a la anterior página
            $ante = $currentSeccion.prev().find('.pagina:last-child');
        }

        if ($ante.length != 0) {
            $currentSeccion.find('.pagina.current').removeClass('current').hide();
            $ante.addClass('current').show('slide', {direction: 'left'}, 'slow')
        }

        builNumbersPaginador();
    });

    $('#modalFrmConcurso').on('click', '.pagerFrmConcurso .btnNext', function(event) {
        event.preventDefault();

        // Revisa si el proceso anterior todavía no ha terminado
        if ($(this).find('.fa-spinner').length != 0) {
            // Si todavia continua en ejecución salimos
            return false;
        }

        var $currentSeccion = $('.seccion .pagina.current').parent();
        var $sig = $currentSeccion.find('.pagina.current').next();
        var promiseDatosGenerales = null;
        console.log("dsd1");
        // Si no hay mas páginas que mostrar
        // Quiere decir que se completo la sección actual
        if ($sig.length == 0) {
            // Avanzamos a la siguiente página
            $sig = $currentSeccion.next().find('.pagina:first-child');

            // Si la sección que se completo es la de datos generales
            switch ($currentSeccion.data('id')) {
                case 'datos_generales':
                    myDeferredDatosGenerales = $.Deferred();
                    promiseDatosGenerales = sendDatosGenerales();

                    if (promiseDatosGenerales != false) {
                        promiseDatosGenerales.done(function(data) {
                            $('.pagerFrmConcurso .btnNext .icon').html( $('.pagerFrmConcurso .btnNext .icon').data('cacheIco') );
                            if (!data.error) {
                                if ($currentSeccion.data('id') != '') {
                                    $('#modalFrmConcurso .progess-bar span[data-seccion='+$currentSeccion.data('id')+']').addClass('complete');
                                }

                                siguientePagina($currentSeccion, $sig);
                            }
                        });
                    }
                    break;

                case 'preguntas':
                    if (gatherPreguntas().length) {
                        sendPreguntas().done(function(data){
                            if (!data.error) {
                                if ($currentSeccion.data('id') != '') {
                                    $('#modalFrmConcurso .progess-bar span[data-seccion='+$currentSeccion.data('id')+']').addClass('complete');
                                }

                                siguientePagina($currentSeccion, $sig);
                            }
                        });
                    } else {
                        $('#modalFrmConcurso .progess-bar span[data-seccion='+$currentSeccion.data('id')+']').addClass('complete');
                        siguientePagina($currentSeccion, $sig);
                    }
                    break;

                case 'evaluadores':
                    sendEvaluadores().done(function(data){
                        if (!data.error) {
                            toastr.success('Ha completado exitosamente el registro del concurso');
                            $('#modalFrmConcurso').modal('hide');
                            $('#modalConfirmConcursoNuevo').modal('show');
                            location.reload();
                            console.log("no hay error");
                        }
                            console.log("error");
                    });
                    break;
            }
        } else {
            siguientePagina($currentSeccion, $sig);
            console.log("1");
        }
            console.log("2");
    });

    $('#modalFrmConcurso').on('click', '.progess-bar span', function() {
        $('.seccion .pagina.current').removeClass('current').hide();
        $('.seccion[data-id="'+$(this).data('seccion')+'"] .pagina:first-child').addClass('current').show('slide', {direction: 'right'}, 'slow');

        builNumbersPaginador();
    });
});

function siguientePagina($currentSeccion, $sig) {
    if ($sig.length != 0) {
        $currentSeccion.find('.pagina.current').removeClass('current').hide();
        $sig.addClass('current').show('slide', {direction: 'right'}, 'slow');
    } else {
        $("#modalFrmConcurso").modal("hide");
        $("#modalConfirmProyectoNuevo").modal("show");
        //toastr.success('Felicidades, concurso registrado exitosamente');
    }

    builNumbersPaginador();
}

function builNumbersPaginador() {
    var current_seccion = $('.seccion .pagina.current').closest('.seccion');

    $('#no_current_pagina').html((current_seccion.find('.pagina').index($('.seccion .pagina.current'))+1));
    $('#total_pagina_seccion').html(current_seccion.find('.pagina').length);
}

function loadFrmConcurso($self) {
    var modal = "#modalFrmConcurso";

    var request = $.ajax({
        type: 'GET',
        url: urlGetEtiquetas,
        data: '',
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            $self.find('.fa-plus').removeClass('fa-plus').addClass('fa-spinner fa-pulse');
        }
    });

    request.done(function(data, status, xhr) {
        data.tiposPreguntaConcurso = tiposPreguntaConcurso;
        $(modal + ' .modal-body').html(templateFrmConcurso(data));
        $('.pagerFrmConcurso .btnNext .icon').data('cacheIco', $('.pagerFrmConcurso .btnNext .icon').html() );
        $(modal + ' .modal-body #seccion_evaluadores').html(templateSeccionEvaluadores(data));

        helpers.fillSelect(modal + " .modal-body [name='etiquetas']", data, true, 'id', 'descripcion');

        select2Etiquetas = $(modal + " .modal-body [name='etiquetas']").select2({
            placeholder: "Seleccione una o más etiquetas",
            language: "es",
        });

        buildSelect2Evaluadores();

        helpers.builderTooltipster('.modal-body .tooltipster', {position: 'top'});
        // Estilizamos los checkboxes
        helpers.builderiCheck('input[name=respuesta]');
        $('[name="fecha_arranque"]').mask('99-99-9999', {placeholder:"dd-mm-yyyy"});
        $('[name="fecha_cierre"]').mask('99-99-9999', {placeholder:"dd-mm-yyyy"});

        concurso = null;

        fileUploaderImagen = $("#fileuploaderImagen").uploadFile($.extend(configUploadFile, {
            allowedTypes: 'jpg,jpeg,png,gif',
            acceptFiles: "image/*",
            url: urlSendDatosGenerales,
            fileName: 'imagen',
            uploadStr: "Imagen",
            dynamicFormData: infoDatosGenerales,
            onSuccess: function(files, data, xhr, pd) {
                concurso = data.id;

                if (!data.error) {
                    $(modal + ' .modal-body .header .title').html($('input[name=nombre]').val());
                    $(modal + ' .modal-body .header img').attr('src', data.byteImagen);
                    $(modal + ' .modal-body .bgBodyInfo .body').html($('textarea[name=descripcion]').val());
                }

                if (data.error) {
                    var message = '', errors = jQuery.parseJSON(data.message);
                    for(var i in errors) {
                        $('[name='+i+']').addClass('ng-invalid');
                        message += errors[i]+' ';
                    }
                    fileUploaderImagen.reset(false);
                    toastr.error(message);
                    // Regresa a la primera página para mostrar el error
                    $('.pagerFrmConcurso .btnPrev').trigger('click');
                    myDeferredDatosGenerales.resolve(data);
                } else if (!hasArchivoSelected('fileUploaderBases')) {
                    myDeferredDatosGenerales.resolve(data);
                    $('#loadingDatosGenerales').hide();
                } else if (concurso && hasArchivoSelected('fileUploaderBases')) {
                    fileUploaderBases.startUpload();
                }
            }
        }));

        fileUploaderBases = $("#fileUploaderBases").uploadFile($.extend(configUploadFile, {
            allowedTypes: 'pdf',
            acceptFiles: "application/pdf",
            url: urlSendBases,
            fileName: 'bases',
            uploadStr: "Bases",
            dynamicFormData: function () {
                return {
                    id: concurso,
                    _csrf: yii.getCsrfToken(),
                };
            },
            onSuccess:function(files, data, xhr, pd) {
                $('#loadingDatosGenerales').hide();
                $('.pagerFrmConcurso .btnNext .icon').html( $('.pagerFrmConcurso .btnNext .icon').data('cacheIco') );
                myDeferredDatosGenerales.resolve(data);
            }
        }));

        $(modal + ' .title').html('AGREGA TU CONCURSO');
        $self.find('.fa-spinner').removeClass('fa-spinner fa-pulse').addClass('fa-plus');
        $(modal).modal("show");

        // Oculta todas las páginas del formulario
        $(modal+' form .pagina').hide();

        // Muestra solo la primera página de la primera sección
        $(modal+' form > .seccion:first-child > .pagina:first-child').show().addClass('current');

        builNumbersPaginador();
    });

    return request;
}

function buildSelect2Evaluadores() {
    select2Evaluadores = $(" .modal-body [name='list_evaluadores']").select2({
        placeholder: "Escriba el nombre del evaluador que desea agregar",
        language: "es",
        ajax: {
            url: urlFindEvaluador,
            processResults: function (data) {
                return {
                    results: $.map(data, function(obj) {
                        return {
                            id: obj.id,
                            text: obj.nombre_completo,
                            byteimagen: obj.byteimagen,
                            etiquetas: obj.etiquetas
                        };
                    })
                };
            }
        },
        templateResult: function(data){
            if (data.loading) {
                return data.text;
            }

            data.col = 12;
            var $markup = $(templateItemEvaluador(data));

            return $markup;
        }
    });

    select2Evaluadores.on("select2:select", function (e) {
        if ($('#seccion_list_evaluadores .itemEvaluador[data-id='+e.params.data.id+']').length == 0) {
            e.params.data.col = 6;
            $('#seccion_list_evaluadores').append(templateItemEvaluador(e.params.data));
        }

        select2Evaluadores.val(null).trigger("change");
    });
}

function sendPreguntas() {
    var list_preguntas = gatherPreguntas();

    $('.pagerFrmConcurso .btnNext .icon').html(helpers.spinner);

    var request = $.ajax({
        type: 'POST',
        url: urlSendPreguntas,
        data: {
            "_csrf": yii.getCsrfToken(),
            "list_preguntas": list_preguntas,
            "id_concurso": concurso,
        },
        dataType: 'json',
    })
    .done(function(data, status, xhr) {
        if (data.error) {
            toastr.error(data.message);
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        toastr.error(jqXHR.responseText);
    }).always(function() {
        $('.pagerFrmConcurso .btnNext .icon').html( $('.pagerFrmConcurso .btnNext .icon').data('cacheIco') );
    });

    return request;
}

function gatherPreguntas() {
    var list_preguntas = [];

    $('#seccion_preguntas .field-input').each(function() {
        var pregunta = {
            id: $(this).data('id'),
            descripcion: $(this).find('[name=pregunta]').val(),
            ayuda: $(this).find('[name=ayuda]').val(),
            id_tipo_pregunta_concurso: $(this).find('[name=tipo]').val(),
        };

        if (pregunta.descripcion != '') {
            list_preguntas.push(pregunta);
        }
    });

    return list_preguntas;
}

function gatherEvaluadores() {
    var list_evaluadores = [];

    $('#seccion_list_evaluadores .itemEvaluador').each(function() {
        list_evaluadores.push($(this).data('id'));
    });

    return list_evaluadores;
}

function sendEvaluadores() {
    var list_evaluadores = gatherEvaluadores();

    $('.pagerFrmConcurso .btnNext .icon').html(helpers.spinner);

    var request = $.ajax({
        type: 'POST',
        url: urlSendEvaluadores,
        data: {
            "_csrf": yii.getCsrfToken(),
            "list_evaluadores": list_evaluadores,
            "id_concurso": concurso,
        },
        dataType: 'json',
    })
    .done(function(data, status, xhr) {
        if (data.error) {
            toastr.error(data.message);
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        toastr.error(jqXHR.responseText);
    }).always(function() {
        $('.pagerFrmConcurso .btnNext .icon').html( $('.pagerFrmConcurso .btnNext .icon').data('cacheIco') );
    });

    return request;
}

function validaDatosConcurso()
{
    if ($('input[name=nombre]').val() == '') {
        toastr.error('Debe especificar el nombre del concurso');
        $('input[name=nombre]').addClass('ng-invalid');
        $('.btnPrev').trigger('click');
        return false;
    }

    if ($('textarea[name=descripcion]').val() == '') {
        toastr.error('Debe especificar la descripción del concurso');
         $('textarea[name=descripcion]').addClass('ng-invalid');
        $('.btnPrev').trigger('click');
        return false;
    }

    if ($('textarea[name=premios]').val() == '') {
        toastr.error('Debe especificar los premios del concurso');
         $('textarea[name=premios]').addClass('ng-invalid');
        $('.btnPrev').trigger('click');
        return false;
    }

    if ( $('input[name=calificacion_minima_proyectos]').val() == '') {
        toastr.error('Debe especificar la calificación mínima para la evaluación automática de los proyectos');
         $('input[name=calificacion_minima_proyectos]').addClass('ng-invalid');
        return false;
    }

    if ( $('input[name=no_ganadores]').val() == '') {
        toastr.error('Debe especificar el número de ganadores');
         $('input[name=no_ganadores]').addClass('ng-invalid');
        return false;
    }

    if ( $('input[name=fecha_arranque]').val() == '') {
        toastr.error('Debe especificar la fecha de inicio del concurso');
         $('input[name=fecha_arranque]').addClass('ng-invalid');
        return false;
    }

    if ( $('input[name=fecha_cierre]').val() == '') {
        toastr.error('Debe especificar la fecha de finalización del concurso');
         $('input[name=fecha_cierre]').addClass('ng-invalid');
        return false;
    }

    if ( $('input[name=evaluadores_x_proyecto]').val() == '') {
        toastr.error('Debe especificar la cantidad de evaluadores que evaluarán a cada proyecto del concurso');
         $('input[name=evaluadores_x_proyecto]').addClass('ng-invalid');
        return false;
    }

    return true;
}

function sendDatosGenerales() {
    $('.ng-invalid').removeClass('ng-invalid');

    if (!validaDatosConcurso()) {
        return false;
    }

    $('#loadingDatosGenerales').show();
    $('.pagerFrmConcurso .btnNext .icon').html(helpers.spinner);

    if (hasArchivoSelected('fileuploaderImagen')) {
        fileUploaderImagen.startUpload();
    } else {
        sendFrmDatosGenerales();
    }

    // Devuelve un Promise para manejar la petición asincrona
    // permite saber cuando las imagenes se terminarón de subir
    return myDeferredDatosGenerales.promise();
}

function sendFrmDatosGenerales() {
    $.ajax({
        type: 'POST',
        url: urlSendDatosGenerales,
        data: infoDatosGenerales(),
        dataType: 'json',
    })
    .done(function(data, status, xhr) {
        $('#loadingDatosGenerales').hide();
        $('.pagerFrmConcurso .btnNext .icon').html( $('.pagerFrmConcurso .btnNext .icon').data('cacheIco') );

        if (data.error) {
            var message = '', errors = jQuery.parseJSON(data.message);
            for(var i in errors) {
                $('[name='+i+']').addClass('ng-invalid');
                message += errors[i]+' ';
            }
            toastr.error(message);
            // Regresa a la primera página para mostrar el error
            $('.pagerFrmConcurso .btnPrev').trigger('click');
            myDeferredDatosGenerales.resolve(data);
            concurso = data.id;
        } else if (concurso && hasArchivoSelected('fileUploaderBases')) {
            fileUploaderBases.startUpload();
        } else {
            myDeferredDatosGenerales.resolve(data);
            concurso = data.id;
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        myDeferredDatosGenerales.reject();
        toastr.error(jqXHR.responseText);
        $('#loadingDatosGenerales').hide();
        $('.pagerFrmConcurso .btnNext .icon').html( $('.pagerFrmConcurso .btnNext .icon').data('cacheIco') );
    });
}

function hasArchivoSelected(archivo) {
    return $('#container-'+archivo+' .ajax-file-upload-container > div:not(.current)').length;
}

function infoDatosGenerales() {
    return {
        concurso: concurso,
        _csrf: yii.getCsrfToken(),
        nombre: $('input[name=nombre]').val(),
        fecha_arranque: $('input[name=fecha_arranque]').val(),
        fecha_cierre: $('input[name=fecha_cierre]').val(),
        descripcion: $('textarea[name=descripcion]').val(),
        premios: $('textarea[name=premios]').val(),
        calificacion_minima_proyectos: $('input[name=calificacion_minima_proyectos]').val(),
        no_ganadores: $('input[name=no_ganadores]').val(),
        evaluadores_x_proyecto: $('input[name=evaluadores_x_proyecto]').val(),
        etiquetas: $('[name=etiquetas] option:selected').map(function() { return this.value; }).get(),
    };
}

$('#modalConfirmConcursoNuevo').on('hidden.bs.modal', function (e) {
    location.reload();
});

// Validar el formulario manualmente
//$('#contact-form').data('yiiActiveForm').submitting = true;
//$('#contact-form').yiiActiveForm('validate');
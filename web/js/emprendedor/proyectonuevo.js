'use strict';
// Utilizamos un motor de plantillas JS;
var templateFrmProyecto = Handlebars.compile( $('#modal-frm-proyecto-tpl').html() );
var fileUploaderImagen, fileUploaderLogo, proyecto = null;
var myDeferredDatosGenerales = $.Deferred();
var select2Etiquetas = null, select2Integrantes = null;
var configUploadFile = {
    multiple: false,
    dragDrop: false,
    maxFileCount: 1,
    allowedTypes: 'jpg,jpeg,png,gif',
    acceptFiles: "image/*",
    maxFileSize: 5*1024*1024,
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
var preg_geo_radial = null;
var preg_geo_poligono = null;
var preg_geo_punto = null;

$(".btnProyectoNuevo").click(function(event) {
    event.preventDefault();
    event.stopPropagation();
    loadFrmProyecto($(this));
    return false;
});

$(document).ready(function () {
    $('#modalFrmProyecto').on('click', '.pagerFrmProyecto .btnPrev', function(event) {
        event.preventDefault();

        var $currentSeccion = $('.seccion .pagina.current').parent();
        var $ante = $currentSeccion.find('.pagina.current').prev();

        // Si no hay mas páginas que mostrar
        if ($ante.length == 0) {
            // Retrocedemos a la anterior página
            $ante = $currentSeccion.prev().find('.pagina:last-child');
        }

        /*if ($currentSeccion.data('id')) {
            $('#modalFrmProyecto .progess-bar span:nth-child('+$currentSeccion.data('id')+')').removeClass('complete');
        }*/

        if ($ante.length != 0) {
            $currentSeccion.find('.pagina.current').removeClass('current').hide();
            $ante.addClass('current').show('slide', {direction: 'left'}, 'slow')
        }

        builNumbersPaginador();
    });

    $('#modalFrmProyecto').on('click', '.pagerFrmProyecto .btnNext', function(event) {
        event.preventDefault();

        // Revisa si el proceso anterior todavía no ha terminado
        if ($(this).find('.fa-spinner').length != 0) {
            // Si todavia continua en ejecución salimos
            return false;
        }

        var $currentSeccion = $('.seccion .pagina.current').parent();
        var $sig = $currentSeccion.find('.pagina.current').next();
        var promiseDatosGenerales = null;

        // Si no hay mas páginas que mostrar
        if ($sig.length == 0) {
            console.log("no hay paginas para mostrar");
            // Avanzamos a la siguiente página
            $sig = $currentSeccion.next().find('.pagina:first-child');

            // Si se completo la sección enviamos los datos
            // Si la sección que se completo es la de datos generales
            if ($currentSeccion.data('id') == -1) {
                promiseDatosGenerales = sendDatosGenerales();

                if (promiseDatosGenerales != false) {
                    console.log("promiseDatosGenerales");
                    promiseDatosGenerales.done(function(){
                        if ($currentSeccion.data('id') != '') {
                            $('#modalFrmProyecto .progess-bar span[data-seccion='+$currentSeccion.data('id')+']').addClass('complete');
                        }

                        $('.pagerFrmProyecto .btnNext .icon').html('<img src="'+homeUrl+'img/Next.png">');

                        siguientePagina($currentSeccion, $sig);
                    });
                }
            } else {
                sendSeccion($currentSeccion.data('id')).done(function(){
                    if ($currentSeccion.data('id') != '') {
                        console.log("send");
                        $('#modalFrmProyecto .progess-bar span[data-seccion='+$currentSeccion.data('id')+']').addClass('complete');
                    }
                    console.log("send2");
                    siguientePagina($currentSeccion, $sig);
                });
            }
        } else {
            console.log("siguientepagina");
            siguientePagina($currentSeccion, $sig);
        }
    });

    $('#modalFrmProyecto').on('click', '.progess-bar span', function() {
        $('.seccion .pagina.current').removeClass('current').hide();
        $('.seccion[data-id="'+$(this).data('seccion')+'"] .pagina:first-child').addClass('current').show('slide', {direction: 'right'}, 'slow');

        builNumbersPaginador();
    });

    /* Para las preguntas geograficas */
    $( "#dialogMapaPuntoRadial" ).dialog({
        autoOpen: false,
        width: 550,
        height: 400,
        position: {of: "#modalFrmProyecto .modal-body", within: "#modalFrmProyecto .modal-body"},
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
        position: {of: "#modalFrmProyecto .modal-body", within: "#modalFrmProyecto .modal-body"},
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
        position: {of: "#modalFrmProyecto .modal-body", within: "#modalFrmProyecto .modal-body"},
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

    preg_geo_radial = pregunta_geografica();
    preg_geo_radial.initMap({selectorMap: '#mapaPuntoRadial'});
    preg_geo_radial.addMenuPuntoRadial();

    preg_geo_poligono = pregunta_geografica();
    preg_geo_poligono.initMap({selectorMap: '#mapaPoligono'});
    preg_geo_poligono.addMenuPoligono();

    preg_geo_punto = pregunta_geografica();
    preg_geo_punto.initMap({selectorMap: '#mapaPunto'});
    preg_geo_punto.addMenuPunto();

});

function siguientePagina($currentSeccion, $sig) {
    if ($sig.length != 0) {
        $currentSeccion.find('.pagina.current').removeClass('current').hide();
        $sig.addClass('current').show('slide', {direction: 'right'}, 'slow');
    } else {
        $("#modalFrmProyecto").modal("hide");
        $("#modalConfirmProyectoNuevo").modal("show");
        console.log("se supone que recarga");
        location.reload();
        //toastr.success('Felicidades, proyecto registrado exitosamente');
    }

    builNumbersPaginador();
}

function builNumbersPaginador() {
    var current_seccion = $('.seccion .pagina.current').closest('.seccion');

    $('#no_current_pagina').html((current_seccion.find('.pagina').index($('.seccion .pagina.current'))+1));
    $('#total_pagina_seccion').html(current_seccion.find('.pagina').length);
}

function loadFrmProyecto($self) {
    var modal = "#modalFrmProyecto";

    var request = $.ajax({
        type: 'GET',
        url: urlGetSecciones,
        data: 'preguntaGroupByPagina=true',
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            $self.find('.fa-plus').removeClass('fa-plus').addClass('fa-spinner fa-pulse');
        }
    });

    request.fail(function (response) {
        if (typeof response.responseJSON.message !== 'undefined') {
            toastr.error('Error: ' + response.responseJSON.message);
        } else {
            toastr.error('Error: ' + response.responseText);
        }
    });

    request.done(function(data, status, xhr) {
        $(modal + ' .modal-body').html(templateFrmProyecto(data));

        /*helpers.fillSelectByAjax({
            select: modal + " .modal-body [name='etiquetas']",
            containerIndicator: $(modal + " .modal-body [name='etiquetas']").parent().find('.container-icon'),
            url: urlGetEtiquetas,
            value: 'id',
            text: 'descripcion'
        }).done(function() {
            select2Etiquetas = $(modal + " .modal-body [name='etiquetas']").select2({
                placeholder: "Seleccione una o más etiquetas",
                language: "es",
            });
        });*/

        helpers.fillSelect(modal + " .modal-body [name='etiquetas']", data.etiquetas, true, 'id', 'descripcion');

        select2Etiquetas = $(modal + " .modal-body [name='etiquetas']").select2({
            placeholder: "Seleccione una o más etiquetas",
            language: "es",
        });

        // El listado de integrantes se buscará por instant search
        select2Integrantes = $(modal + " .modal-body [name='list_integrantes']").select2({
            placeholder: "Seleccione uno o más integrantes por nombre o correo electrónico",
            language: "es",
            ajax: {
                url: urlFindByName,
                processResults: function (data) {
                    return {
                        results: $.map(data, function(obj) {
                            return { id: obj.id, text: obj.nombre_completo };
                        })
                    };
                }
            }
        });

        helpers.builderTooltipster('.modal-body .tooltipster, .select2-selection', {position: 'top'});
        // Estilizamos los checkboxes
        helpers.builderiCheck('input[name=respuesta]');

        proyecto = null;

        fileUploaderImagen = $("#fileuploaderImagen").uploadFile($.extend(configUploadFile, {
            url: urlSendDatosGenerales,
            fileName: 'imagen',
            uploadStr: "Imagen",
            dynamicFormData: infoDatosGenerales,
            onSuccess: function(files, data, xhr, pd) {
                proyecto = data.id;

                if (!data.error) {
                    $(modal + ' .modal-body .header .title').html($('input[name=nombre]').val());
                    $(modal + ' .modal-body .header img').attr('src', data.byteImagen);
                    $(modal + ' .modal-body .bgBodyInfo .body').html($('textarea[name=descripcion]').val());
                }

                if (data.error) {
                    var message = '', errors = typeof(data.message) == 'string' ? Array(data.message) : jQuery.parseJSON(data.message);
                    for(var i in errors) {
                        $('[name='+i+']').addClass('ng-invalid');
                        message += errors[i]+' ';
                    }
                    fileUploaderImagen.reset(false);
                    toastr.error(message);
                    // Regresa a la primera página para mostrar el error
                    $('.pagerFrmProyecto .btnPrev').trigger('click');
                } else if (!hasArchivoSelected('fileuploaderLogo')) {
                    myDeferredDatosGenerales.resolve();
                } else if (proyecto && hasArchivoSelected('fileuploaderLogo')) {
                    fileUploaderLogo.startUpload();
                } else {
                    myDeferredDatosGenerales.resolve();
                }
            }
        }));

        fileUploaderLogo = $("#fileuploaderLogo").uploadFile($.extend(configUploadFile, {
            url: urlSendLogo,
            fileName: 'logo',
            uploadStr: "Logo",
            dynamicFormData: function () {
                return {
                    id: proyecto,
                    _csrf: yii.getCsrfToken(),
                };
            },
            onSuccess:function(files, data, xhr, pd) {
                $('#loadingDatosGenerales').hide();
                $('.pagerFrmProyecto .btnNext .icon').html('<img src="'+homeUrl+'img/Next.png">');
                myDeferredDatosGenerales.resolve();
            }
        }));

        $(modal + ' .title').html('AGREGA TU PROYECTO');
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

function sendSeccion(seccion) {
    //var myDeferred = $.Deferred();
    var list_respuestas = gatherRespuestasSeccion(seccion);

    $('.pagerFrmProyecto .btnNext .icon').html(helpers.spinner);

    var request = $.ajax({
        type: 'POST',
        url: urlSendRespuestas,
        data: {
            "_csrf": yii.getCsrfToken(),
            "list_respuestas": list_respuestas,
            "id_proyecto": proyecto,
        },
        dataType: 'json',
    })
    .done(function(data, status, xhr) {
        if (data.error) {
            toastr.error(data.message);
        }
        //myDeferred.resolve();
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        toastr.error(jqXHR.responseText);
        //myDeferred.reject();
    }).always(function() {
        $('.pagerFrmProyecto .btnNext .icon').html('<img src="'+homeUrl+'img/Next.png">');
    });

    //return myDeferred.promise();
    return request;
}

function gatherRespuestasSeccion(seccion) {
    var list_respuestas = [];

    $('.seccion[data-id='+seccion+'] .field-input').each(function() {
        var $self = $(this);
        var respuesta = {
            id_pregunta: $self.data('pregunta'),
            valor: ''
        };

        switch (parseInt($self.data('tipopregunta'))) {
            case 1: // 1 Texto
            case 2: // 2 Numérica
            case 5: // 5 Hipervínculo
                respuesta.valor = $self.find('[name=respuesta]').val();
                break;

            case 3: // 3 Opción Múltiple
                respuesta.valor = '['+$self.find('[name=respuesta]:checked').map(function() { return this.value; }).get().join(', ')+']';
                break;

            case 4: // 4 Opción Única
                respuesta.valor = $self.find('[name=respuesta] option:selected').val();
                break;

            case 6: // 6 Punto Radial Geográfico
            case 7: // 7 Polígono Geográfico
            case 8: // 8 Punto Geográfico
                respuesta.valor = $self.data('respuesta');
                break;
        }

        if (respuesta.valor != '' && respuesta.valor != '[]' && typeof(respuesta.valor) != 'undefined') {
            list_respuestas.push(respuesta);
        }
    });

    return list_respuestas;
}

function validaDatosProyecto()
{
    var regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/;

    if ($('input[name=nombre]').val() == '') {
        toastr.error('Debe especificar el nombre del proyecto');
        $('input[name=nombre]').addClass('ng-invalid');
        $('.btnPrev').trigger('click');
        return false;
    }

    if ($('textarea[name=descripcion]').val() == '') {
        toastr.error('Debe especificar la descripción del proyecto');
         $('textarea[name=descripcion]').addClass('ng-invalid');
        $('.btnPrev').trigger('click');
        return false;
    }

    if ( $('input[name=url_video]').val() != '') {
        if ( !$('input[name=url_video]').val().match(regex)) {
            toastr.error('Debe especificar una URL válida para el video');
             $('input[name=url_video]').addClass('ng-invalid');
            $('.btnPrev').trigger('click');
            return false;
        }
    }

    return true;
}

function sendDatosGenerales() {
    if (!validaDatosProyecto()) {
        return false;
    }

    $('.ng-invalid').removeClass('ng-invalid');
    $('#loadingDatosGenerales').show();
    $('.pagerFrmProyecto .btnNext .icon').html(helpers.spinner);

    if (hasArchivoSelected('fileuploaderImagen')) {
        fileUploaderImagen.startUpload();
    } else if (hasArchivoSelected('fileuploaderLogo')) {
        fileUploaderLogo.startUpload();
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
        $('.pagerFrmProyecto .btnNext .icon').html('<img src="'+homeUrl+'img/Next.png">');

        proyecto = data.id;

        if (data.error) {
            var message = '', errors = typeof(data.message) == 'string' ? Array(data.message) : jQuery.parseJSON(data.message);
            for(var i in errors) {
                $('[name='+i+']').addClass('ng-invalid');
                message += errors[i]+' ';
            }
            toastr.error(message);
            // Regresa a la primera página para mostrar el error
            $('.pagerFrmProyecto .btnPrev').trigger('click');
            myDeferredDatosGenerales.reject();
        } else if (proyecto && hasArchivoSelected('fileuploaderLogo')) {
          myDeferredDatosGenerales.resolve();
            fileUploaderLogo.startUpload();
        }
        myDeferredDatosGenerales.resolve();
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        myDeferredDatosGenerales.reject();
        toastr.error(jqXHR.responseText);
    });
}

function hasArchivoSelected(archivo) {
    return $('#container-'+archivo+' .ajax-file-upload-container > div:not(.current)').length;
}

function infoDatosGenerales() {
    return {
        proyecto: proyecto,
        _csrf: yii.getCsrfToken(),
        nombre: $('input[name=nombre]').val(),
        descripcion: $('textarea[name=descripcion]').val(),
        url_video: $('input[name=url_video]').val(),
        etiquetas: $('[name=etiquetas] option:selected').map(function() { return this.value; }).get(),
        integrantes: $('input[name=integrantes]').val(),
        list_integrantes: $('[name=list_integrantes] option:selected').map(function() { return this.value; }).get(),
    };
}

$('#dialogMapaPuntoRadial').on('click', '.btnFinalizarMapa', function(event) {
    event.preventDefault();
    event.stopPropagation();

    $('.field-input[data-pregunta='+$(this).data('id')+']').data('respuesta', preg_geo_radial.getJsonPuntoRadial());

    //console.log(JSON.stringify($('.field-input[data-pregunta='+$(this).data('id')+']').data('respuesta')));

    $( "#dialogMapaPuntoRadial" ).dialog('close');
});

$('#dialogMapaPoligono').on('click', '.btnFinalizarMapa', function(event) {
    event.preventDefault();
    event.stopPropagation();
    var poligono = preg_geo_poligono.getJsonPoligono();

    if (poligono.length != 0) {
        $('.field-input[data-pregunta='+$(this).data('id')+']').data('respuesta', poligono);
    }

    //console.log(JSON.stringify($('.field-input[data-pregunta='+$(this).data('id')+']').data('respuesta')));

    $( "#dialogMapaPoligono" ).dialog('close');

    preg_geo_poligono.cleanMap();
});

$('#dialogMapaPunto').on('click', '.btnFinalizarMapa', function(event) {
    event.preventDefault();
    event.stopPropagation();
    var puntos = preg_geo_punto.getJsonPunto();

    if (puntos.length != 0) {
        $('.field-input[data-pregunta='+$(this).data('id')+']').data('respuesta', puntos);
    }

    //console.log(JSON.stringify($('.field-input[data-pregunta='+$(this).data('id')+']').data('respuesta')));

    $( "#dialogMapaPunto" ).dialog('close');

    preg_geo_punto.cleanMap();
});

$('#modalFrmProyecto').on('click', '.respuesta_geografica.radial', function(event) {
    $( "#dialogMapaPuntoRadial" ).dialog('open');

    var puntosRadial =  $(this).closest('.field-input').data('respuesta');

    preg_geo_radial.cleanMap();

    if (puntosRadial != '' && typeof puntosRadial!='undefined') {
        preg_geo_radial.loadPuntoRadialFromJson(puntosRadial);
    }

    $('#dialogMapaPuntoRadial .btnFinalizarMapa').data('id', $(this).data('pregunta'));
    $('#dialogMapaPuntoRadial .btnFinalizarMapa').css('display', 'inline-block');
});

$('#modalFrmProyecto').on('click', '.respuesta_geografica.poligono', function(event) {
    $( "#dialogMapaPoligono" ).dialog('open');

    var poligono =  $(this).closest('.field-input').data('respuesta');

    preg_geo_poligono.cleanMap();

    if (poligono != '' && typeof poligono!='undefined') {
        preg_geo_poligono.loadPoligonoFromJson(poligono);
    }

    $('#dialogMapaPoligono .btnFinalizarMapa').data('id', $(this).data('pregunta'));
    $('#dialogMapaPoligono .btnLimpiarMapa').data('id', $(this).data('pregunta'));
    $('#dialogMapaPoligono .btnFinalizarMapa').css('display', 'inline-block');
    $('#dialogMapaPoligono .btnLimpiarMapa').css('display', 'inline-block');
});

$('#modalFrmProyecto').on('click', '.respuesta_geografica.punto', function(event) {
    $( "#dialogMapaPunto" ).dialog('open');

    var puntos =  $(this).closest('.field-input').data('respuesta');

    preg_geo_punto.cleanMap();

    if (puntos != '' && typeof puntos!='undefined') {
        preg_geo_punto.loadPuntoFromJson(puntos);
    }

    $('#dialogMapaPunto .btnFinalizarMapa').data('id', $(this).data('pregunta'));
    $('#dialogMapaPunto .btnFinalizarMapa').css('display', 'inline-block');
});

$('#modalFrmProyecto').on('click', '#btnFinalizarFrmProyecto', function(event) {
    var $currentSeccion = $('.seccion .pagina.current').parent();
    var promiseDatosGenerales = null;

    // Enviamos la sección actual
    if ($currentSeccion.data('id') == -1) {
        promiseDatosGenerales = sendDatosGenerales();
        console.log("prefinalizado");
        if (promiseDatosGenerales != false) {
            $(this).html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
            promiseDatosGenerales.done(function(){
                $("#modalFrmProyecto").modal("hide");
                $("#modalConfirmProyectoNuevo").modal("show");
                console.log("finalizado");
                toastr.success('Felicidades, proyecto registrado exitosamente');
                location.reload();
            });
        }
    } else {
        $(this).html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
        sendSeccion($currentSeccion.data('id')).done(function(){
                $("#modalFrmProyecto").modal("hide");
                $("#modalConfirmProyectoNuevo").modal("show");
                console.log("finalizado2");
                toastr.success('Felicidades, proyecto registrado exitosamente');
                location.reload();
            });
    }
});

$('#modalConfirmProyectoNuevo').on('hidden.bs.modal', function (e) {
    location.reload();
});

// Validar el formulario manualmente
//$('#contact-form').data('yiiActiveForm').submitting = true;
//$('#contact-form').yiiActiveForm('validate');

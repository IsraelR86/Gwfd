'use strict';
// Utilizamos un motor de plantillas JS;
var templateConcurso = Handlebars.compile( $('#modal-mi-concurso-tpl').html() );

helpers.builderWaterfall('#waterfall-concurso-tpl', urlGetAllAvailables, function() {
    helpers.builderTooltipster('.iconGanadores', {position: 'left'});
    helpers.builderTooltipster('.tooltipster', {position: 'bottom'});

    $(".btnModalConcurso:not(.addedClick)").click(function(event) {
        event.preventDefault();
        event.stopPropagation();

        var $this = $(this),
            modal = "#modalInfoConcurso",
            el = $this.html(); // Cacheamos el contenido actual del elemento

        concurso = $this.data("id");

        var request = $.ajax({
            type: 'POST',
            url: urlGetById,
            data: 'id=' + $this.data("id"),
            dataType: 'json',
            beforeSend: function(xhr, settings) {
                // Reemplazamos el contenido actual del elemento por un spinner
                $this.html(helpers.spinner);
            }
        });

        request.done(function(data, status, xhr) {
            $(modal + ' .modal-body').html(templateConcurso(data));
            $('.tooltipster').tooltipster('hide');

            $(modal).on('shown.bs.modal', function(){
                $('.easy-pie-chart').easyPieChart({
                    easing: 'easeOutBounce',
                    lineWidth: 5,
                    onStep: function(from, to, percent) {
                        $(this.el).find('.number').text( ' '+parseInt($(this.el).data('percent'))+' ' );
                    }
                });
            });


          //  console.log(data.countProyectosAEvaluador!=data.countProyectosEvaluados);
          //  console.log(data.fecha_resultados != null);

            if (data.countProyectosAEvaluador!=data.countProyectosEvaluados) {
                $('.btnPublicar').hide();
            } else if (data.fecha_resultados != null){
                $('.btnPublicar').hide();
            } else {
                $('.btnPublicar').show();
            }

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
    }).addClass("addedClick");
    // Agregamos clase como bandera para evitar agregar mas de
    // una vez el evento click al mismo elemento

    $('.btnModalTest:not(.ok)').click(function(event){
        $.ajax({
            type: 'POST',
            url: urlTest,
            data: {
                param1: 'value1'
                },
            dataType: 'json',
        })
        .done(function(data, status, xhr) {
            console.log(data);
        })
        .always(function(data, status, xhr) {
            console.log(data);
        })
        .fail(function(xhr, status, error) {
            console.log(error);
        });
    }).addClass('ok');
});


$('#modalInfoConcurso').on('click', '.btnCancelarConcurso', function(){
    var $this = $(this);

    $.jAlert({'type': 'confirm', 'confirmBtnText': 'Si', 'content': 'Al cancelar el concurso no podrán inscribirse los emprendedores y tampoco podrán evaluar los jueces. <br><br>¿Está seguro de que desea cancelar el concurso?',
    'onConfirm': function() {
        $.ajax({
            type: 'POST',
            url: urlCancelarConcurso,
            data: {
                id_concurso: $this.data("id"),
                _csrf:  yii.getCsrfToken()
            },
            dataType: 'json',
            beforeSend: function(xhr, settings) {
                // Reemplazamos el contenido actual del elemento por un spinner
                $this.html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
            }
        })
        .done(function(data, status, xhr) {
            if (data.error) {
                toastr.error('Error: '+data.message);
            } else {
                toastr.success(data.message);
                $('.item[data-concurso='+$this.data("id")+'] .date').html('CANCELADO');
            }
        }).always(function(){
            $('#modalInfoConcurso').modal('hide');
        });
    }});
});

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

$('#modalInfoConcurso').on('click', '.btnGuardarEvaluadores', function(event){
    var $this = $(this);
    $this.html(helpers.spinner);

    sendEvaluadores().done(function(data){
        if (!data.error) {
            toastr.success('Evaluadores guardados exitosamente');
            $('#modalInfoConcurso').modal('hide');
        } else {
            toastr.success('ERROR: '+data.message);
        }
        $this.html('GUARDAR');
    });
});

$('#modalInfoConcurso').on('click', '.btnEditEvaluadores', function(event){
    var $this = $(this);
    event.preventDefault();
    event.stopPropagation();

    $.ajax({
        type: 'POST',
        url: urlGetEvaluadores,
        data: {
            id_concurso: $this.data("id"),
            _csrf:  yii.getCsrfToken()
        },
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $this.html(helpers.spinner);
        }
    })
    .done(function(data, status, xhr) {
        if (data.error) {
            toastr.error('Error: '+data.message);
        } else {
            $('.footer .menu').html('<a class="btnBorderRed btnGuardarEvaluadores" href="#" data-id="'+$this.data("id")+'">GUARDAR</a>');

            $('.body_mi_concurso').fadeOut('slow', function(){
                $('#modalInfoConcurso .body_mi_concurso').html('<div id="seccion_evaluadores" class="col-md-12 col-md-xs-12">'+
                    '<h3 class="text-center">Evaluadores</h3>'+templateSeccionEvaluadores(data)+'</div>');
                buildSelect2Evaluadores();

                $(this).fadeIn('slow');
                $('#modalInfoConcurso').animate({ scrollTop: 0 }, 'slow');
            });
        }
    });
});

$('#modalInfoConcurso').on('click', '.btnEditRubricas', function(){
    var $this = $(this);
    event.preventDefault();
    event.stopPropagation();

    $.ajax({
        type: 'POST',
        url: urlGetRubricas,
        data: {
            id_concurso: $this.data("id"),
            _csrf:  yii.getCsrfToken()
        },
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $this.html(helpers.spinner);
        }
    })
    .done(function(data, status, xhr) {
        if (data.error) {
            toastr.error('Error: '+data.message);
        } else {
            $('.footer .menu').html('<a class="btnBorderRed btnGuardarRubricas" href="#" data-id="'+$this.data("id")+'">GUARDAR</a>');

            $('.body_mi_concurso').fadeOut('slow', function(){
                $('#modalInfoConcurso .body_mi_concurso').html('<div id="seccion_rubricas" class="col-md-12 col-md-xs-12">'+
                    '<h3 class="text-center">Rúbricas</h3>'+templateSeccionRubricas(data)+'</div>');

                $(this).fadeIn('slow');
                $('#modalInfoConcurso').animate({ scrollTop: 0 }, 'slow', function() {
                    helpers.builderTooltipster('#modalInfoConcurso .tooltipster', {position: 'top'});
                });
            });
        }
    });
});

$('#modalInfoConcurso').on('click', '.delRubrica', function(event){
    $(this).closest('tr').fadeOut('slow', function(){ $(this).remove(); });
});

$('#modalInfoConcurso').on('click', '#btnAddRubrica', function(event){

    $('#list_rubricas table tbody').append('<tr class="rubrica">\n\
            <td class="col-md-3 nombre">'+$('#seccion_rubricas [name="nombre"]').val()+'</td>\n\
            <td class="col-md-4 descripcion">'+$('#seccion_rubricas [name="descripcion"]').val()+'</td>\n\
            <td class="col-md-2 calificacion_minima text-center">'+$('#seccion_rubricas [name="calificacion_minima"]').val()+'</td>\n\
            <td class="col-md-2 calificacion_maxima text-center">'+$('#seccion_rubricas [name="calificacion_maxima"]').val()+'</td>\n\
            <td class="col-md-1">\n\
                <span class="fa-stack icon delRubrica">\n\
                    <i class="fa fa-circle-o fa-stack-2x"></i>\n\
                    <i class="fa fa-close fa-stack-1x"></i>\n\
                </span>\n\
            </td>\n\
        </tr>');

        $('#seccion_rubricas [name="nombre"]').val('');
        $('#seccion_rubricas [name="descripcion"]').val('');
        $('#seccion_rubricas [name="calificacion_minima"]').val('');
        $('#seccion_rubricas [name="calificacion_maxima"]').val('');
});

function gatherRubricas() {
    var list_rubricas = [];

    $('#list_rubricas .rubrica').each(function() {
        var rubrica = {
            id: $(this).data('id'),
            nombre: $(this).find('.nombre').text(),
            descripcion: $(this).find('.descripcion').text(),
            calificacion_minima: $(this).find('.calificacion_minima').text(),
            calificacion_maxima: $(this).find('.calificacion_maxima').text()
        };

        if (rubrica.nombre != '') {
            list_rubricas.push(rubrica);
        }
    });

    return list_rubricas;
}

$('#modalInfoConcurso').on('click', '.btnGuardarRubricas', function(event){
    var $this = $(this);
    var list_rubricas = gatherRubricas();
    $this.html(helpers.spinner);

    var request = $.ajax({
        type: 'POST',
        url: urlSendRubricas,
        data: {
            "_csrf": yii.getCsrfToken(),
            "list_rubricas": list_rubricas,
            "id_concurso": $this.data('id'),
        },
        dataType: 'json',
    })
    .done(function(data, status, xhr) {
        if (!data.error) {
                toastr.success('Rúbricas guardados exitosamente');
                $('#modalInfoConcurso').modal('hide');
            } else {
                toastr.success('ERROR: '+data.message);
            }
            $this.html('GUARDAR');
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        toastr.error(jqXHR.responseText);
    });
});

$('#modalInfoConcurso').on('click', '.btnEditar', function(event){
    var $this = $(this);

    $this.html(helpers.spinner);
    $('.btnModalConcurso[data-id='+$(this).data('id')+']').data('cacheIco', $('.btnModalConcurso[data-id='+$(this).data('id')+']').html());
    $('.btnModalConcurso[data-id='+$(this).data('id')+']').html(helpers.spinner);
    $('#modalInfoConcurso').data('editar', true);
    $('#modalInfoConcurso').data('id', $(this).data('id'));

    $.ajax({
        type: 'POST',
        url: urlGetById,
        data: 'id=' + $this.data("id")+'&includeExtras=true',
        dataType: 'json',
    }).done(function(data, status, xhr) {
        if (data.error) {
            toastr.error(data.message);
            return false;
        }

        $('#modalInfoConcurso').modal('hide');
        $('#modalInfoConcurso').data('data', data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        toastr.error('Error al obtener los datos del concurso');
    });

});

$('#modalInfoConcurso').on('hidden.bs.modal', function (e) {
    var $this = $('#modalInfoConcurso');

    if ($(this).data('editar')) {
        loadFrmConcurso($(this)).done(function() {
            concurso = $this.data('id');

            $('#modalFrmConcurso .title').html('EDITA TU CONCURSO');

            $('#modalFrmConcurso .progess-bar span.tooltipster ').addClass('complete');

            var data = $('#modalInfoConcurso').data('data');
            $('.btnModalConcurso[data-id='+$this.data('id')+']').html( $('.btnModalConcurso[data-id='+$this.data('id')+']').data('cacheIco') );

            $('#modalFrmConcurso').find('.tooltipster[data-seccion=evaluadores]').remove();
            $('#modalFrmConcurso').find('.progess-bar').removeClass('tresSecciones').addClass('dosSecciones');
            $('#modalFrmConcurso').find('.seccion[data-id=evaluadores]').remove();

            $('input[name=nombre]').val(data.nombre);
            $('input[name=fecha_arranque]').val(data.fechaArranque);
            $('input[name=fecha_cierre]').val(data.fechaCierre);
            $('textarea[name=descripcion]').val(data.descripcion);
            $('textarea[name=premios]').val(data.premios);
            $('input[name=calificacion_minima_proyectos]').val(data.calificacion_minima_proyectos);
            $('input[name=no_ganadores]').val(data.no_ganadores);
            $('input[name=evaluadores_x_proyecto]').val(data.evaluadores_x_proyecto);

            $('#modalFrmConcurso .modal-body .header .title').html(data.nombre);
            $('#modalFrmConcurso .modal-body .header img').attr('src', data.byteImagen);
            $('#modalFrmConcurso .modal-body .bgBodyInfo .body').html(data.descripcion);

            var arrayEtiquetas = [];

            for(var etiqueta in data.etiquetas) {
                arrayEtiquetas.push(data.etiquetas[etiqueta].id);
            }

            select2Etiquetas.val(arrayEtiquetas).trigger("change");

            if (data.byteImagen != '') {
                $('#container-fileuploaderImagen .ajax-file-upload-container').html(
                    '<div class="ajax-file-upload-statusbar current" style="width: 400px;">\n\
                        <img class="ajax-file-upload-preview" src="'+data.byteImagen+'" style="width: 100px; height: 100px;">\n\
                        <div class="ajax-file-upload-filename"></div>\n\
                        <div class="ajax-file-upload-red ajax-file-upload-cancel" style="">Cancelar</div>\n\
                    </div>');
                $('#container-fileuploaderImagen .ajax-file-upload-container .ajax-file-upload-cancel').click(function() {
                    $(this).parent().remove();
                });
            }

            if (data.bases != '') {
                $('#container-fileUploaderBases .ajax-file-upload-container').html(
                    '<div class="ajax-file-upload-statusbar current" style="width: 400px;">\n\
                        <div><i class="fa fa-file-pdf-o fa-2"></i> '+data.bases+'</div>\n\
                        <div class="ajax-file-upload-filename"></div>\n\
                        <div class="ajax-file-upload-red ajax-file-upload-cancel" style="">Cancelar</div>\n\
                    </div>');
                $('#container-fileUploaderBases .ajax-file-upload-container .ajax-file-upload-cancel').click(function() {
                    $(this).parent().remove();
                });
            }

            for (var i in data.preguntas) {
                data.preguntas[i].tiposPreguntaConcurso = tiposPreguntaConcurso;
                $('#seccion_preguntas').append( templatePregunta(data.preguntas[i]) );
            }

            $('#modalFrmConcurso .btnFinalizar').remove();
            $('#modalFrmConcurso .menu').append('<a class="btnBorderRed inline-block btnActualizar" data-id="'+$this.data('id')+'" href="#">ACTUALIZAR</a>');
            $('#modalInfoConcurso').data('data', null);

            helpers.builderTooltipster('#modalInfoConcurso .tooltipster', {position: 'top'});
        });

        $(this).data('editar', false);
    }
});

$('#modalFrmConcurso').on('click', '.btnActualizar', function(event){
    $(this).html(helpers.spinner);

    sendDatosGenerales().done(function(data) {
        if (data.error) {
            toastr.error('ERROR: ' + data.message);
        } else {
            if (gatherPreguntas().length) {
                sendPreguntas().done(function(data) {
                    if (data.error) {
                        toastr.error('ERROR: ' + data.message);
                    } else {
                        toastr.success('Datos actualizados exitosamente');
                        location.reload();
                    }

                    $('#modalFrmConcurso').modal('hide');
                });
            } else {
                toastr.success('Datos actualizados exitosamente');
                location.reload();
            }
        }

        $('#modalFrmConcurso').modal('hide');
    });
});

$('#modalInfoConcurso').on('click', '.btnPublicar', function(event){
    var $this = $(this);

    $.jAlert({'type': 'confirm', 'confirmBtnText': 'Si', 'content': 'Este proceso es irreversible. <br><br>¿Esta seguro de que desea ejecutar este proceso ahora?',
    'onConfirm': function() {
        $this.html(helpers.spinner);

        $.ajax({
            type: 'POST',
            url: urlPublicar,
            data: {
                id_concurso: $this.data("id"),
                _csrf:  yii.getCsrfToken()
            },
            dataType: 'json',
        }).done(function(data, status, xhr) {
            if (data.error) {
                toastr.error(data.message);
            } else {
                toastr.success(data.message);
            }

            $('#modalInfoConcurso').modal('hide');
        }).fail(function(jqXHR, textStatus, errorThrown) {
            toastr.error('Error al realizar la petición');
        });
    }});

});

/*$('#modalInfoConcurso').on('click', '.btnAsignarEvaluadores', function(event){
    var $this = $(this);

    $this.html(helpers.spinner);

    $.ajax({
        type: 'POST',
        url: urlAsignarEvaluadores,
        data: {
            id_concurso: $this.data("id"),
            _csrf:  yii.getCsrfToken()
        },
        dataType: 'json',
    }).done(function(data, status, xhr) {
        if (data.error) {
            toastr.error(data.message);
        } else {
            toastr.success(data.message);
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        toastr.error('Error al realizar la petición');
    }).always(function(){
        $('#modalInfoConcurso').modal('hide');
    });

})*/;

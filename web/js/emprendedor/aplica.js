'use strict';
// Utilizamos un motor de plantillas JS;
var templateConcurso = Handlebars.compile( $('#modal-concurso-tpl').html() );
var templatePreguntasConcurso = Handlebars.compile( $('#modal-preguntas-concurso-tpl').html() );
var fileUploader = [];
var filesUploads = 0;
var configFileUploader = {
    multiple: false,
    dragDrop: false,
    maxFileCount: 1,
    allowedTypes: 'jpg,jpeg,png,gif,pdf',
    acceptFiles: "image/*, application/pdf, application/x-pdf",
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
    url: urlSendRespuestaArchivo,
    fileName: 'archivo',
    uploadStr: "Archivo",
    /*dynamicFormData: function () {
        console.log(this);
        return {
            pregunta: $(this).data('pregunta'),
            proyecto: $(this).data('proyecto'),
            concurso: $(this).data('concurso'),
            _csrf: yii.getCsrfToken(),
        };
    },*/
    onError: function(files, status, errMsg, pd) {
        toastr.error(errMsg);
    },
};

$(document).ready(function(){
    
        $('#welcome').modal('show');
});

var requestProyectos = $.ajax({
    type: 'GET',
    url: urlGetAllProyectos,
    data: {
        compact: true,
        _csrf: yii.getCsrfToken()
    },
});

requestProyectos.done(function(listProyectos, status, xhr) {
    
    helpers.builderWaterfall('#waterfall-concurso-tpl', urlGetAllAvailables, function() {
        $(".btnModalConcurso:not(.addedClick)").click(function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            var $this = $(this),
                modal = "#modalInfoConcurso",
                el = $this.html(); // Cacheamos el contenido actual del elemento
            
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
                data.proyectos = listProyectos.result;
                
                $(modal + ' .modal-body').html(templateConcurso(data));
                $(modal).modal("show");
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
        }).addClass("addedClick");
        // Agregamos clase como bandera para evitar agregar mas de 
        // una vez el evento click al mismo elemento
        
        helpers.builderTooltipster('.tooltipster', {position: 'bottom'});
    });
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

$('#modalInfoConcurso').on('click', '.btnAplicarConcurso', function () {
    var $this = $(this),
    modal = "#modalInfoConcurso";

    $.jAlert({'type': 'confirm', 'confirmBtnText': 'Si', 'content': 'Tu proyecto aplicará a este concurso con lo que hayas capturado del mismo hasta este momento. Significa que aunque edites tu proyecto de aquí en adelante, dichas ediciones no se verán reflejadas en esta participación. Si aún no has terminado de editar tu proyecto, te sugerimos terminar de capturar tu idea para entonces aplicar. <br><br>¿Deseas aplicar a este concurso?', 
    'onConfirm': function() {
        if ($('#proyecto_aplica').val() == '') {
            toastr.error('Debe seleccionar un proyecto para aplicar');
            
            return false;
        }
        
        $.ajax({
            type: 'POST',
            url: urlAplicar,
            data: {
                concurso: $this.data("id"),
                proyecto: $('#proyecto_aplica').val(),
                _csrf:  yii.getCsrfToken()
            },
            dataType: 'json',
            beforeSend: function(xhr, settings) {
                // Reemplazamos el contenido actual del elemento por un spinner
                $this.html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
            }
        })
        .done(function(data, status, xhr) {
            $this.html('APLICA');
            
            if (data.error) {
                toastr.error('Error: '+data.message);
                
                $('#modalInfoConcurso').modal('hide');
            } else {
                filesUploads = 0;
                fileUploader = [];
                $(modal + ' .modal-body').html(templatePreguntasConcurso(data));
                    
                if (data.preguntas.length != 0) {
                    $(".files").each(function() {
                        fileUploader.push(
                            $(this).uploadFile($.extend(configFileUploader, {
                                formData: {
                                    pregunta: $(this).data('pregunta'),
                                    proyecto: $(this).data('proyecto'),
                                    concurso: $(this).data('concurso'),
                                    _csrf: yii.getCsrfToken(),
                                },
                                onSuccess: function(files, data, xhr, pd) {
                                    filesUploads++;
                                    
                                    if (data.error) {
                                        toastr.error(data.message);
                                    }
                                    
                                    if (filesUploads >= countArchivos()) {
                                        $('.btnSendPreguntasConcurso').html('APLICAR');
                                        sendPreguntasConcurso($('.btnSendPreguntasConcurso').get(0));
                                    }
                                }
                            }))
                        );
                    })
                } /*else {
                    // Si no hay preguntas, de todas formas mostramos el 
                    $(modal + ' .modal-body').html(templatePreguntasConcurso(data));
                    //toastr.success('Aplicación exitosa al concurso. Felicitaciones....');
                    //$('#modalInfoConcurso').modal('hide');
                    //$('#modalConfirmAplica').modal('show');
                }*/
                
                helpers.builderiCheck('input[name=acepto_concurso]');
            }
        });
    }});
    
    $('.ja_wrap.ja_wrap_white').scrollTop(0);
});

$('#modalInfoConcurso').on('click', '.btnSendPreguntasConcurso', evalSendPreguntasConcurso);

function evalSendPreguntasConcurso () {
    if (!validRespuestas()) {
        toastr.error('Debe responder a todas las preguntas y adjuntar todos los archivos.');
        return false;
    }
    
    if (fileUploader.length) {
        $('.btnSendPreguntasConcurso').html(helpers.spinner);
        for (var i = fileUploader.length - 1; i >= 0; i--) {
            fileUploader[i].startUpload();
        }
    } else {
        sendPreguntasConcurso(this);
    }
}

function sendPreguntasConcurso(obj) {
    var list_respuestas = gatherRespuestas(),
        $this = $(obj),
        el = $this.html(),
        modal = "#modalInfoConcurso";
    
    return $.ajax({
        type: 'POST',
        url: urlSetPreguntasConcurso,
        data: {
            _csrf: yii.getCsrfToken(),
            proyecto: $this.data("proyecto"),
            concurso: $this.data("concurso"),
            list_respuestas: list_respuestas
        },
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $this.html(helpers.spinner);
        }
    }).done(function(data, status, xhr) {
        if (data.error) {
            toastr.error('Error:' + data.message);
        } else {
            $('#modalConfirmAplica').modal('show');
            //toastr.success('Aplicación exitosa al concurso. Felicitaciones...');
        }
    }).always(function(data, status, xhr) {
        // Restauramos el contenido del elemento por el cacheado
        $this.html(el);
        $(modal).modal("hide");
    }).fail(function(xhr, status, error) {
        toastr.error('Error al procesar los datos');
        if (config.isDebugging()) {
            console.log('Error '+status+' by helpers.builderWaterfall: '+error);
        }
    });
}

function gatherRespuestas() {
    var respuestas = [];
    
    $('#modalInfoConcurso [name=respuesta]').each(function() {
        if ($(this).val() != '') {
            respuestas.push({
                id_pregunta: $(this).data('pregunta'), 
                respuesta: $(this).val()
            });
        }
    });
    
    return respuestas;
}

function validRespuestas () {
    var valid = true;
    
    for (var index in fileUploader) {
        if (fileUploader[index].selectedFiles == 0) {
            valid = false;
        }
    }
    
    $('#modalInfoConcurso [name=respuesta]').each(function() {
        if ($(this).val() == '') {
            valid = false;
        }
    });
    
    if ($('#acepto_concurso').prop('checked') == false) {
        valid = false;
    }
    
    return valid;
}

function confirmAplicar() {
    var $this = $(this),
        el = $this.html(),
        modal = '';
        
    return $.ajax({
        type: 'POST',
        url: urlConfirmAplicar,
        data: {
            _csrf: yii.getCsrfToken(),
            proyecto: $this.data("proyecto"),
            concurso: $this.data("concurso")
        },
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $this.html(helpers.spinner);
        }
    }).done(function(data, status, xhr) {
        if (data.error) {
            toastr.error('Error:' + data.message);
        } else {
            $('#modalConfirmAplica').modal('show');
        }
    }).always(function(data, status, xhr) {
        // Restauramos el contenido del elemento por el cacheado
        $this.html(el);
        $().modal("hide");
    }).fail(function(xhr, status, error) {
        toastr.error('Error al procesar los datos');
    });
}

function countArchivos() {
    var count = 0;
    
    $('.ajax-file-upload-container').each(function(){ 
        if ($(this).html() != '') {
            count++;
        }
        
    });
    
    return count;
}
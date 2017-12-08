'use strict';

$(document).ready(function() {
    $('.btnDownloadArchivo').click(function(event){
        event.stopPropagation();
        event.preventDefault();
        
        var $self = $(this);
        var el = $self.html();
        $self.html(helpers.spinner);
    
        $.fileDownload(urlDownloadRespuestaArchivo, {
            httpMethod: "POST",
            data: {_csrf: yii.getCsrfToken(), 
                concurso: $self.data('concurso'), 
                proyecto: $self.data('proyecto'), 
                pregunta: $self.data('pregunta')},
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
    
    $('.statusProyecto').on('ifChanged', function() {
        $.post(urlSetStatusAplicacion, {
            _csrf: yii.getCsrfToken(),
            concurso: $(this).data('concurso'),
            proyecto: $(this).data('proyecto'),
            status: $(this).prop('checked') ? 1 : 0
        })
        .done(function(data) {
            if (data.error) {
                toastr.error('ERROR: '+data.message);
            }
        })
        .fail(function() {
            toastr.error('Error al procesar la solicitud');
        })
    });
    
    $('#btnAsignarEvaluadores').click(function(event){
        var $this = $(this),
            cacheIcon = $this.html();
        
        
        $this.html(helpers.spinner);
        
        $.jAlert({'type': 'confirm', 'confirmBtnText': 'Si', 'content': 'Solamente los proyectos marcados como aprobados seran evaluados por los jueces. <br><br>¿Desea continuar con el proceso?', 
            'onConfirm': function() {
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
                    $this.html(cacheIcon);
                });
            }});
        
    })
})
'use strict';

$(document).ready(function() {
    $('.btnDownloadBases').click(function(event){
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
});
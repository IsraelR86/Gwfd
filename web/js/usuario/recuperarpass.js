/* globals $ */
'use strict';

$(document).ready(function() {

    $('#formRecuperarPass').on('beforeSubmit', function (event) {
        event.stopPropagation();
        event.preventDefault();

        var $this = $('#btnSendCambiarPass'),
            cacheIcon = $this.html();

        $this.html(helpers.spinner);

        $.post(urlRecuperarPass, $('#formRecuperarPass').serialize())
            .done(function(response) {
                $.jAlert({
            		'title': 'Éxito',
            		'content': 'Se ha enviado un correo electrónico a la dirección proporcionada, revise su bandeja de entrada.',
            		'theme': 'green',
            		'onClose': function(){ window.location = "/web/site/login"; },
            	});
            })
            .fail(function (response) {
                if (typeof response.responseJSON.message != 'undefined') {
                    $.jAlert({
                		'title': 'Error',
                		'content': response.responseJSON.message,
                		'theme': 'red'
                	});
                } else {
                    $.jAlert({
                		'title': 'Error',
                		'content': response.responseText,
                		'theme': 'red'
                	});
                }
            })
            .always(function () {
                $this.html(cacheIcon);
                $('#modalRecuperarPass').modal('hide');
            });
            
        return false;
    });
});

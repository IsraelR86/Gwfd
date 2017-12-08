'use strict';
// Utilizamos un motor de plantillas JS;
var templatePuntajeProyecto= Handlebars.compile( $('#modal-puntaje-proyecto-tpl').html() );

$('.evaluacionFinalizada').click(function(event) {
    event.preventDefault();
    event.stopPropagation();
    
    var $this = $(this),
        modal = "#modalPuntajeProyecto",
        el = $this.html(); // Cacheamos el contenido actual del elemento
    
    $this.html(helpers.spinner);
    
    $.ajax({
        type: 'POST',
        url: urlGetPuntaje,
        data: {
            concurso: $this.data("c"),
            proyecto: $this.data('p'),
            _csrf:  yii.getCsrfToken()
        },
        dataType: 'json'
    })
    .done(function(data, status, xhr) {
        if (data.error) {
            toastr.error('Error: '+data.message);
        } else {
            data.url_micrositio = urlMicrositio;
            $(modal + ' .modal-body').html(templatePuntajeProyecto(data));
            $(modal).modal("show");
        }
        
        $this.html(el);
        
    });
});
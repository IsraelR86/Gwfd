'use strict';
// Utilizamos un motor de plantillas JS;
var templateConcurso = Handlebars.compile( $('#modal-concurso-tpl').html() );

helpers.builderWaterfall('#waterfall-admin-concurso-tpl', urlGetAll, function() {
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
            $(modal + ' .modal-body').html(templateConcurso(data));
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
    
    helpers.builderTooltipster('.tooltipster', {position: 'bottom'});
});

$('#modalInfoConcurso').on('click', '.btnAplicarConcurso', function () {
    var $this = $(this);
    console.log($this.data('id'));
});
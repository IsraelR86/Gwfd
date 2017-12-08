'use strict';
// Utilizamos un motor de plantillas JS;
var templateEvaluadores = Handlebars.compile( $('#modal-evaluador-tpl').html() );

helpers.builderWaterfall('#waterfall-evaluadores-tpl', urlGetAll, function() {
    
    $(".btnModalEvaluador:not(.addedClick)").click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        
        mostrarEvaluador($(this), '#modalEvaluador');
        
        return false;
    }).addClass("addedClick");
    // Agregamos clase como bandera para evitar agregar mas de 
    // una vez el evento click al mismo elemento
});

$('#modalEvaluador').on('click', '.btnEliminarEvaluador', function(event){
    var $this = $(this);
    $(this).html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
    
    $.ajax({
        type: 'POST',
        url: urlDelete,
        data: {
            evaluador: $(this).data("id"),
        },
        dataType: 'json'
    })
    .done(function(data, status, xhr) {
        if (data.error) {
            toastr.error('Error: '+data.message);
        } else {
            toastr.success(data.message);
            $('#waterfall').waterfall('removeItems', $('.item[data-evaluador='+$this.data("id")+']'));
        }
        
        $('#modalEvaluador').modal('hide');
    });
});

function mostrarEvaluador($this, modal) {
    $this.data('cacheIco', $this.html());
    var request = $.ajax({
        type: 'POST',
        url: urlGet,
        data: 'id=' + $this.data("id"),
        dataType: 'json',
        beforeSend: function(xhr, settings) {
            // Reemplazamos el contenido actual del elemento por un spinner
            $this.html(helpers.spinner);
        }
    });

    request.done(function(data, status, xhr) {
        if (data.error) {
            toastr.error(data.message);
        } else {
            if (data.length == 0) {
                toastr.error('Evaluador no disponible');
                return false;
            }
            
            $(modal + ' .modal-body').html(templateEvaluadores(data));
            $(modal).modal("show");
            
            $(modal).on('shown.bs.modal', function(){
                $('.easy-pie-chart').easyPieChart({
                    easing: 'easeOutBounce',
                    lineWidth: 5,
                    onStep: function(from, to, percent) {
                        $(this.el).find('.number').text( ' '+parseInt($(this.el).data('number'))+' ' );
                    }
                });
            })
        }
    });

    request.fail(function(xhr, status, error) {
        if (config.isDebugging()) {
            console.log('Error '+status+' by helpers.builderWaterfall: '+error);
        }
    });
    
    request.always(function() {
        $this.html($this.data('cacheIco'));
    });
}


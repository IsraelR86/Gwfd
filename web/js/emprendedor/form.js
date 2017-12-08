jQuery(document).ready(function($){
    'use strict';
    // Utilizado para los selects de Estado y Ciudad
    var selectsEdoCiudad = {
        'emprendedor-id_estado': 'emprendedor-id_ciudad',
        'emprendedor-id_estado_nacimiento': 'emprendedor-id_ciudad_nacimiento',
        'integrante-id_estado': 'integrante-id_ciudad',
        'integrante-id_estado_nacimiento': 'integrante-id_ciudad_nacimiento'
    };
    
    $('#emprendedor-id_estado, #emprendedor-id_estado_nacimiento, '+
      '#integrante-id_estado, #integrante-id_estado_nacimiento').change(function(){
        var self = this;
        var $select_destino = $('#'+selectsEdoCiudad[$(self).attr('id')]);
        // Agrega un indicador de cargando
        $select_destino.after('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
        
        // Elimina todos los options del select, excepto el primero. Antes de llamar post
        $select_destino.find('option:not(:first-child)').remove();
        
        $.post(homeUrl+'ciudad/getbyestado', {estado: $(self).val(), _csrf: yii.getCsrfToken()}, function(data, status, xhr) {
        	// Elimina todos los options del select, excepto el primero. Después de llamar post
        	$select_destino.find('option:not(:first-child)').remove();
        	
            var item = null;
            
            for (item in data) {
                $select_destino.append('<option value="'+item+'">'+data[item]+'</option>');
            }
            // Elimina el indicador de cargando
            $select_destino.parent().find('.fa-spinner').remove();
        }, 'json');
    });

	$('[name="Emprendedor[fecha_nacimiento]"]').mask('99-99-9999', {placeholder:"dd-mm-yyyy"});
	
	$('#emprendedor-id_nivel_educativo').change(function(){
	    //$('#emprendedor-universidad_otro').val('');
        $('#emprendedor-universidad_otro').prop('disabled', true);

        $("#usuario-id_universidad").change(); //Para actualizar contexto y validación

	    if ($(this).val() >= 4) {
	        $('#usuario-id_universidad').prop('disabled', false);
	    } else {
	        $('#usuario-id_universidad option:first').attr('selected', true);
	        $('#usuario-id_universidad').prop('disabled', true);
	        $('#emprendedor-universidad_otro').prop('disabled', true);
	        $('#emprendedor-universidad_otro').val("");
	    }
	});
	
	$('#usuario-id_universidad').change(function(){
	    var destinoMensaje = '#frmEmprendedor .error-summary';
	    
	    if( $(this).val() == '' && $("#emprendedor-id_nivel_educativo").val()>=4 ){
	        $('#emprendedor-universidad_otro').prop('disabled', true);
	        
	        $(destinoMensaje).show();
	        
	        if ($(destinoMensaje + ' ul li:contains("Debe especificar la universidad")').length == 0) {
	            $(destinoMensaje + ' ul').append('<li>Debe especificar la universidad</li>');
	        }
	        return;
	    } else {
	    	$(destinoMensaje).hide();
	    	$(destinoMensaje + ' ul li:contains("Debe especificar la universidad")').remove()
	    }

	    if ($(this).val() > 0) {
	        //$('#emprendedor-universidad_otro').val('');
	        $('#emprendedor-universidad_otro').prop('disabled', true);
	    } else {
	        $('#emprendedor-universidad_otro').prop('disabled', false);
	    }
	});
	
	$('#emprendedor-id_nivel_educativo').change(); //Para actualizar contexto al leer por primera vez
	
	$('#frmEmprendedor').submit(function(event){
	    var destinoMensaje = '#frmEmprendedor .error-summary';
	    
	    if ($('#emprendedor-id_nivel_educativo').val() >= 4 && $('#usuario-id_universidad').val() == 0 && $('#emprendedor-universidad_otro').val() == '') {
	        $(destinoMensaje).show();
	        
	        if ($(destinoMensaje + ' ul li:contains("Debe especificar la universidad")').length == 0) {
	            $(destinoMensaje + ' ul').append('<li>Debe especificar la universidad</li>');
	        }
	        
	        $('#usuario-id_universidad').closest('div').addClass('has-error');
	        
	        event.preventDefault();
	        return false;
	    }
	    
	    return true;
	});
	
	$('#emprendedor-curp, #emprendedor-rfc').change(function(){
		$(this).val($(this).val().toUpperCase());
	});
	
	// Para cargar las ciudades en caso de existir un error en el formulario
	for(var element in selectsEdoCiudad) {
		if ($('#'+selectsEdoCiudad[element]).val() == '' && $('#'+element).val() != '') {
			$('#'+element).trigger('change');
		}
	}

});
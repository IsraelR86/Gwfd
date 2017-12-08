$(document).ready( function(){
	/*
	$('.divInput').hover( function(){
		$(this).children('.form-inputs').css('border-color','#000');
		$(this).children('.absoluto').css('background-color','#000');
	}, function(){
		$('.form-inputs').css('border-color','#CCC');
		$('.absoluto').css('background-color','#CCC');
	});
*/
	$('#mailButton').click( function(){
		$("#modal2").show();
		$("#modal1").hide();
	});
	
	$('.button-entrar').click(function(){
		$('#modal1').hide();
		$('#modal3').show();	
	});

});

$('#form-loginn .form-inputs').keypress(function(){
	if( $(this).parent('.divInput').hasClass('errorInput') )
		$(this).parent('.divInput').removeClass('errorInput');
	
});

$('#button-loginn').click(function(event){
	event.preventDefault();
	var sas = $('#form-loginn').serialize();
	
	var email = $('#form-loginn #mailInput input').val();
	var pass = $('#form-loginn #passInput input').val();
	
	var validator = 0;
	if(pass == "" && !$('#form-loginn #passInput').hasClass('errorInput'))
	{
		$('#form-loginn #passInput').addClass('errorInput');
		validator++;
	}
	if(email == "" && !$('#form-loginn #mailInput').hasClass('errorInput'))
	{
		$('#form-loginn #mailInput').addClass('errorInput');
		validator++;
	}
	if(validator==0)
	{
		$('.errorLabel1').hide();
	}
		$('.errorLabel2').hide();
	
	if(!email == "" && !pass == "")
	{
		
		$.post(loginUrl, sas)
		  .done(function(iniciado) {
		  	//console.log(iniciado);
		  	if(iniciado == 1)
		  	{
		  	 window.location = redirectloginUrl;
		  	}else{
				$('.errorLabel2').show();
				//$('.divInput').addClass('errorInput');

		  	}
		    //alert( "Data Loaded: " + data );
		 });
		 /*
	    $.post(loginUrl, data)
	        .done(function(response) {
	        	console.log(response);
	            if (response.error) {
	                toastr.error('Error al registrar el usuario. '+response.mensaje);
	            } else {
	                //toastr.success('Registro exitoso del usuario, ahora puede iniciar sesión con su cuenta.');
	                location.href = urlLogin + '?singup=true';
	            }
	        })
	        .fail(function(response) {
	            //toastr.error('Error al registrar el usuario.');
	            console.log('fail');
	        })
	        .always(function() {
	            //btnAceptar.html(cacheIco);
	            console.log("always");
	        });
	        */
	}else{
		$('.errorLabel1').show();
	}
		
});

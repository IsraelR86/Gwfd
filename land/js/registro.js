$(document).ready( function(){
	
	$('.divInput').hover( function(){
		$(this).children('.form-inputs').css('border-color','#000');
		$(this).children('.absoluto').css('background-color','#000');
	}, function(){
		$('.form-inputs').css('border-color','#CCC');
		$('.absoluto').css('background-color','#CCC');
	});

	$('#mailButton').click( function(){
		$("#modal2").show();
		$("#modal1").hide();
	});

});

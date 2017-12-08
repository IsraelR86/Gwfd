$(document).ready(function(){
	var datos = 'js/noticias/noticias.json';
	var datos_concuross = 'js/concursos/concursos.json';

	$.getJSON(datos, function (data) {
		console.log(data.length);


		for(var i = 0; i < data.length;i++){
			console.log(data[i].titulo);

			$('<div class="col-sm-6 col-md-4" ><div class="thumbnail-img-box"><img class="imgnew" src="'+data[i].fuente+'"><h2>'+data[i].titulo.toUpperCase()+'</h2><div class="caption-img-box"><p class="parnews">'+data[i].contenido+'</p></div></div></div>').appendTo("#caja");
		}


			$('.thumbnail-img').hover(function () {
	        $(this).find('.caption-img').slideDown(600); //.fadeIn(250)
	    }, function () {
	        $(this).find('.caption-img').slideUp(800); //.fadeOut(205)
	    });

	    $('.thumbnail-img-box').hover(function () {
	        $(this).find('.caption-img-box').slideDown(900); //.fadeIn(250)
	    }, function () {
	        $(this).find('.caption-img-box').slideUp(850); //.fadeOut(205)
    });
	});

	$.getJSON(datos, function (data) {

		for(var i = 0; i < data.length;i++){
			console.log(data[i].titulo);
			if(i == 0){
				$('<div class="col-sm-6 col-md-4" ><div class="thumbnail-img-box"><img class="imgnew" src="'+data[i].fuente+'"><h2>'+data[i].titulo.toUpperCase()+'</h2><div class="caption-img-box"><p class="parnews">'+data[i].contenido+'</p></div></div></div>').appendTo("#noticiasjsone");
			}
			$('<div class="col-sm-6 col-md-4" ><div class="thumbnail-img-box"><img class="imgnew" src="'+data[i].fuente+'"><h2>'+data[i].titulo.toUpperCase()+'</h2><div class="caption-img-box"><p class="parnews">'+data[i].contenido+'</p></div></div></div>').appendTo("#noticiasjs");
		}


			$('.thumbnail-img').hover(function () {
	        $(this).find('.caption-img').slideDown(600); //.fadeIn(250)
	    }, function () {
	        $(this).find('.caption-img').slideUp(800); //.fadeOut(205)
	    });

	    $('.thumbnail-img-box').hover(function () {
	        $(this).find('.caption-img-box').slideDown(900); //.fadeIn(250)
	    }, function () {
	        $(this).find('.caption-img-box').slideUp(850); //.fadeOut(205)
    });
	});



		$.getJSON(datos_concuross, function (data) {

		for(var i = 0; i < data.length;i++){
			console.log(data[i].titulo);
			if(i == 0){
				$('<div class="col-sm-6 col-md-4" ><div class="thumbnail-img-box"><img class="imgnew" src="'+data[i].fuente+'"><h2>'+data[i].concurso+'</h2><div class="caption-img-box"><p class="parnews">'+data[i].contenido+'</p></div></div></div>').appendTo("#concursos_principal");
			}
			$('<div class="col-sm-6 col-md-4" ><div class="thumbnail-img-box"><img class="imgnew" src="'+data[i].fuente+'"><h2>'+data[i].concurso+'</h2><div class="caption-img-box"><p class="parnews">'+data[i].contenido+'</p></div></div></div>').appendTo("#concursos");
		}


			$('.thumbnail-img').hover(function () {
	        $(this).find('.caption-img').slideDown(600); //.fadeIn(250)
	    }, function () {
	        $(this).find('.caption-img').slideUp(800); //.fadeOut(205)
	    });

	    $('.thumbnail-img-box').hover(function () {
	        $(this).find('.caption-img-box').slideDown(900); //.fadeIn(250)
	    }, function () {
	        $(this).find('.caption-img-box').slideUp(850); //.fadeOut(205)
    });
	});




    $('.caption-img-box').hover(function () {
        $(this).find('.parnews').slideDown(900); //.fadeIn(250)
    }, function () {
        $(this).find('.parnews').slideUp(800); //.fadeOut(205)
    });

	 $(window).scroll(function() {
	    var winTop = $(window).scrollTop();
	     $("body").addClass("sticky-header");



	    //  var imga = $('#imagen');
		 //
	    //   imga.css({
	    //   	width:'70px',
	    //   	height: '70px'
	    //   });

	    if (winTop) {
	      $("body").addClass("sticky-header");

	    //    document.getElementById('imagen').src = "images/fwd1.png";

	       //document.getElementById('subheader').style.color = "red";
	       $('#subheader div').css('background','#000');



	var imga = $('#imagen');

	    //   imga.css({
		  //
	    //   	width:'179px',
	    //   	height: '40px'
	    //   });

	    } else {
	      $("body").removeClass("sticky-header");

	       $('#subheader div').css('background','transparent');

	    //   document.getElementById('imagen').src = "images/fwd.png";
	      var imga = $('#imagen');

	    //   imga.css({
		  //
	    //   	width:'45px',
	    //   	height: '45px',
		  //
	    //   });
	    }
	  })
	$("#form-sigin").submit(function( event ) {
		$.ajax({
		  method: "POST",
		  url: "http://gofwd.mx/web/emprendedor/registrar",
		  data: { email: $("#email").val(), password: $("#password").val() ,nombre :$("#name").val(),appat:true }
		}).done(function( msg ) {
		    if(msg.error == true){
		    	alert(msg.mensaje);


		    }else{
		    	alert(msg.mensaje);
		    }
		 });
	    return false;


	  alert("error !!");
	  event.preventDefault();
	});







$( "#hoverhi" ).mouseover(function() {
  $('#hoverhi').css({"top":"0","z-index":"3"});
  $('#tituloHover').css({"margin-top":"20px"});
}).mouseout(function() {
    $('#hoverhi').css({"left":"0","top":"90%"});
    $('#tituloHover').css({"margin-top":"0px"});

});

$('#btnVideo').click(function() {


	$('#clip')[0].play();
	$('#btnVideo').hide();
});

$('#clip').click(function() {
	$('#clip')[0].pause();
	$('#btnVideo').show();

});


$()





});

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


		//Listado de consursos emprendimientos

		$.getJSON("http://forward-2-p4scu41.c9users.io/web/api/v1/concursos?institucion=1&etiquetas=1,2,3", function (data) {

			for(var i = 0; i < data.length;i++){
				
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
/*
*
* noticias FWD 
*
*
*/
		$.getJSON("http://forward-2-p4scu41.c9users.io/web/api/v1/noticias?page=0", function (data) {
			console.log(data);
			
			for(var i = 0; i < 11;i++){
				$('<div class="col-sm-6 col-md-4" ><div class="thumbnail-img-box"><img class="imgnew" src="'+data[0].portada+'"><h2>'+data[0].titulo+'</h2><div class="caption-img-box"><p class="parnews">'+data[0].resumen+'</p></div></div></div>').appendTo("#newsFDW");
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





	// Aliados

		$.getJSON("http://forward-2-p4scu41.c9users.io/web/api/v1/aliados", function (data) {
			console.log(data);
/*
			for(var i = 0; i < data.length;i++){
				$(' <div ><img src="'+data[i].fuente+'"><h2>'+data[i].concurso+'</h2><div class="caption-img-box"><p class="parnews">'+data[i].contenido+'</p></div></div></div>').appendTo("#concursos");
			}
*/
		});



    $('.caption-img-box').hover(function () {
        $(this).find('.parnews').slideDown(900); //.fadeIn(250)
    }, function () {
        $(this).find('.parnews').slideUp(800); //.fadeOut(205)
    });

	 $(window).scroll(function() {
	    var winTop = $(window).scrollTop();
	     $("body").addClass("sticky-header");



	     var imga = $('#imagen');

	      imga.css({
	      	width:'70px',
	      	height: '70px'
	      });

	    if (winTop >= 550) {
	      $("body").addClass("sticky-header");

	       document.getElementById('imagen').src = "/web/img/landing/images/fwd1.png";

	       //document.getElementById('subheader').style.color = "red";
	       $('#subheader div').css('background','#000');



	var imga = $('#imagen');

	      imga.css({

	      	width:'179px',
	      	height: '40px'
	      });

	    } else {
	      $("body").removeClass("sticky-header");

	       $('#subheader div').css('background','transparent');

	      document.getElementById('imagen').src = "/web/img/landing/fwd.png";
	      var imga = $('#imagen');

	      imga.css({

	      	width:'45px',
	      	height: '45px',

	      });
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
		    	
				$('.divInput').hover( function(){
					$(this).children('.form-inputs').css('border-color','#ff000');
					$(this).children('.absoluto').css('background-color','#ff000');
				}, function(){
					$('.form-inputs').css('border-color','#CCC');
					$('.absoluto').css('background-color','#CCC');
				});

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


/*
$.get('http://forward-2-p4scu41.c9users.io/web/api/v1/concursos?institucion=1&etiquetas=1,2,3').done(function (data) {
		$('<div class="col-sm-6 col-md-4" ><div class="thumbnail-img-box"><img class="imgnew" src="'+data[0].fuente+'"><h2>'+data[i].concurso+'</h2><div class="caption-img-box"><p class="parnews">'+data[0].contenido+'</p></div></div></div>').appendTo("#emprendimiento");
	})
	.fail(function (response) {
		console.log(response);
	});
*/
});

$(document).ready(function() {
    var headerFixed = false;
	$(window).on('scroll', function(){
		var barra = $(window).scrollTop();
		
		if(barra>100){/*
			$('.header').css({
			    'position': 'fixed'
			});
			headerFixed = true;*/
			/*
			$('.col12').addClass('col-md-2');
			$('.icon-menu').addClass('icon-menu-block');
			$('.logo-small').removeClass('logo');
			logoAnimation();*/
		}else{/*
			$('header').removeClass('fixed');
			$('.col12').removeClass('col-md-2');
			$('.icon-menu').removeClass('icon-menu-block');
			$('.logo-small').addClass('logo');
			logoAnimationGrande();*/
		}
		if(barra<100){/*
		    if(headerFixed)
		    {
		        headerFixed = false;
    			$('.header').css({
    			    'position': 'relative'
    			});
		    }*/
		}
	});
	
});
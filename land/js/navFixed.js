// Stickey Bar
/*----------NavBars scripts --------------*/
$(document).ready(function(){
    $('.menu').addClass('menu-fixed');



    var altura = $('#navBar').offset().top; //saber altura
    

    var singup = $('.sing-up');///right bar
    var navBar = $('.navbar-default');/// resto de navbar izquierda
    var containerbar=$('#navBarlarge');


    altura -=60;
   // alert(altura);

    singup.removeClass('sing-upt');
    navBar.removeClass('navbar-defaultt');


    $(window).on('scroll',function(){
        
        if ( $(window).scrollTop() > altura) {
            $('.menu').addClass('menuu');
            

            //Transparencia de barra
            singup.removeClass('sing-upt');
            navBar.removeClass('navbar-defaultt');
            containerbar.addClass('container-fluids');




            $('.menu').addClass('menu-fixed');

        }
        else {
            
            //$('.menu').removeClass('menu-fixed');

            //Transparencia
            // singup.addClass('sing-upt');
            // navBar.addClass('navbar-defaultt');
            // containerbar.removeClass('container-fluids');


            console.log('Imagen Default FWD');



        }




    });




///----------------Width pagina ------------------



});

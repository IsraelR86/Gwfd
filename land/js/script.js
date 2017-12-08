// Stickey Bar
/*----------NavBars scripts --------------*/
$(document).ready(function(){

    var imgNav = $('.imgnvBar');

    var altura = $('#navBar').offset().top; //saber altura
    //console.log('altura: '+ altura);

    var singup = $('.sing-up');///right bar
    var navBar = $('.navbar-default');/// resto de navbar izquierda
    var containerbar=$('#navBarlarge');


    altura -=60;
   // alert(altura);

    singup.addClass('sing-upt');
    navBar.addClass('navbar-defaultt');


    $(window).on('scroll',function(){
        //console.log('altura: '+ altura);
        if ( $(window).scrollTop() > altura) {
            $('.menu').removeClass('menuu');
            //console.log('Removiendo clase menuu');

            //Transparencia de barra
            singup.removeClass('sing-upt');
            navBar.removeClass('navbar-defaultt');
            containerbar.addClass('container-fluids');

            // Cambiando Imagen del Navbar
            imgNav.attr('src','/web/img/landing/fwd.png');
            imgNav.css('width','40px');
            //console.log('Icono de FWD');


            ///////////$('.menu').addClass('menu-fixed');
            $('.menu').hide();

        }
        else {
            //console.log('altura: '+ altura);
            ///////$('.menu').removeClass('menu-fixed');
            $('.menu').show();

            //Transparencia
            singup.addClass('sing-upt');
            navBar.addClass('navbar-defaultt');
            containerbar.removeClass('container-fluids');

            // Cambiando Imagen del Navbar default
            imgNav.attr('src','/web/img/landing/fwd1.png');
            imgNav.css('width','179px');

            //console.log('Imagen Default FWD');



        }




    });




///----------------Width pagina ------------------



});

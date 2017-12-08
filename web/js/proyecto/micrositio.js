'use strict';

$(document).ready(function() {
    /*if (Cookies.get('status-sidebar') == 'active') {
        $("#menu-toggle").trigger('click');
    }*/

    $('.menu_seccion .item_menu').click(function(event) {
        event.preventDefault();
        event.stopPropagation();

        $('.seccion').hide();
        var $seccion = $('.seccion[data-id='+$(this).data('id')+']');

        $seccion.fadeIn('slow');
        $('#nombre_seccion').html($(this).data('id')+'. '+$seccion.data('nombre'));

        //Seleccionar items de la misma sección
        var grupo = $(this).data('section');
        $('.menu_seccion .item_menu').removeClass('selected').removeClass('active');
        $('.section'+grupo).addClass('selected');
        $(this).addClass('active');

        return false;
    });

    $('.menu_seccion .item_menu').hover( function(){
        var grupo = $(this).data('section');
        $('.menu_seccion .item_menu').removeClass('hselected').removeClass('hactive');
        $('.section'+grupo).addClass('hselected');
        $(this).addClass('hactive');
    }, function(){
        $('.menu_seccion .item_menu').removeClass('hselected').removeClass('hactive');
    });

    $('.menu_seccion .item_menu[data-id=1]').trigger('click');

});

$(document).ready(function ($) {
    'use strict';

    // Arregla el bug de Yii2, en la paginación que agrega doble ? a los enlaces
    $('a').each(function(){
        if ($(this).attr('href')) {
            $(this).attr('href', $(this).attr('href').replace('??', '?'));
        }
    });
    $('body').tooltip({
        selector: "[data-toggle='tooltip']"
    });

    if (typeof($.fn.jAlert) != 'undefined') {
        $.fn.jAlert.defaults.backgroundColor = 'white'; //override a default setting
    }

    // http://stackoverflow.com/questions/2196641/how-do-i-make-jquery-contains-case-insensitive-including-jquery-1-8
    $.expr[':'].containsIgnoreCase = function (n, i, m) {
        return jQuery(n).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };

    if (config.isDebugging()) {
        $(document).ajaxError(helpers.traceAjaxError);
    }
    
    /**
     * Permite mostrar y ocultar el sidebar
     */
    var $sb = $("#sidebar"),
        y_pos = 50,
        new_y_pos = 0,
        no_steps = 6;
    
    $("#menu-toggle").click(function(e) {
        var $self = $(this);
        var i = 1;
        e.preventDefault();
        $sb.toggleClass("active");
        
        if (!$sb.hasClass("active")) {
            Cookies.set('status-sidebar', '');
            $self.removeClass("active");
            
            $sb.hide("slow", function () {
                // Cambiamos la dimension del contenido principal
                $("#content")
                    .addClass("col-md-12 col-xs-12")
                    .removeClass("col-md-9 col-xs-9");
                
                // Para redimensionar el waterfall
                $(window).trigger('resize');
            });
            
            // Efecto para cambiar el icono del menu
            /*setTimeout(function convertIconOpen(){
                var actual_y_pos = $self.css('background-position').split(' ');
                new_y_pos = (parseInt(actual_y_pos[1].replace('px','')) + y_pos) + 'px';
                
                $self.css('background-position', actual_y_pos[0]+' '+new_y_pos);
                
                if (i < no_steps) setTimeout(convertIconOpen, 100);
                i++;
            }, 100);*/
        } else {
            Cookies.set('status-sidebar', 'active');
            $self.addClass("active");
            
            $sb.show("slow", function () {
                $("#content")
                    .removeClass("col-md-12 col-xs-12")
                    .addClass("col-md-9 col-xs-9");
                
                // Para redimensionar el waterfall
                $(window).trigger('resize');
            });
            
            // Efecto para cambiar el icono del menu
            /*setTimeout(function convertIconClose() {
                var actual_y_pos = $self.css('background-position').split(' ');
                new_y_pos = (parseInt(actual_y_pos[1].replace('px','')) - y_pos) + 'px';
                
                $self.css('background-position', actual_y_pos[0]+' '+new_y_pos);
                
                if (i < no_steps) setTimeout(convertIconClose, 100);
                i++;
            }, 100);*/
        }
    });
    
    // Restablece el sidebar dependiendo del ultimo status
    // Oculto en la seccion de ganadores, micrositio y perfil de evaluador
    if (Cookies.get('status-sidebar') != '' && location.href.search('ganadores') == -1 && location.href.search('micrositio') == -1 && location.href.search('evaluador/ver') == -1 && location.href.search('concurso/view') == -1) {
        //$("#menu-toggle").trigger('click');Esta linea sirve para que al iniciar la vista se despliegue el menu con el calendario automaticamente.
    }
    
    // Para el scrollbar personalizado
    $(".nano").nanoScroller({ alwaysVisible: true });
    
    // Para desplegar el body del widget_sidebar
    $('#notificaciones .header, #concursos .header, #badges .header').click(function() {
        var $widget = $(this).parent();
        $widget.toggleClass('active');
        
        if ($widget.hasClass('active')) {
            $widget.find('.body').slideDown('slow');
        } else {
            $widget.find('.body').slideUp('slow');
        }
        
        $.get(urlToogleWidgetSidebar, {widget_sidebar: $widget.attr('id'), active: Number($widget.hasClass('active'))});
    });
    
    // Para el campo de busqueda
    $('#icon_search').click(function() {
        //$('#input_search').toggle("slide");
        $('#input_search').animate({width:'toggle'}, 'slow');
    });
    
    helpers.builderTooltipster('.tooltipster');
    
    helpers.builderiCheck();
});


if (config.isDebugging()) {
    window.onerror = helpers.traceError;
}

/* http://stackoverflow.com/questions/8853396/logical-operator-in-a-handlebars-js-if-conditional */
Handlebars.registerHelper("ifCond",function(v1,operator,v2,options) {
    switch (operator)
    {
        case "==":
            return (v1==v2)?options.fn(this):options.inverse(this);

        case "!=":
            return (v1!=v2)?options.fn(this):options.inverse(this);

        case "===":
            return (v1===v2)?options.fn(this):options.inverse(this);

        case "!==":
            return (v1!==v2)?options.fn(this):options.inverse(this);

        case "&&":
            return (v1&&v2)?options.fn(this):options.inverse(this);

        case "||":
            return (v1||v2)?options.fn(this):options.inverse(this);

        case "<":
            return (v1<v2)?options.fn(this):options.inverse(this);

        case "<=":
            return (v1<=v2)?options.fn(this):options.inverse(this);

        case ">":
            return (v1>v2)?options.fn(this):options.inverse(this);

        case ">=":
         return (v1>=v2)?options.fn(this):options.inverse(this);

        default:
            return eval(""+v1+operator+v2)?options.fn(this):options.inverse(this);
    }
});

Handlebars.registerHelper('join', function(arreglo, options) {
    var result = '';
    
    for (var i in arreglo) {
        result += options.fn(arreglo[i]) + ', ';
    }
    
    return result.substr(0, result.length-2);
});

Handlebars.registerHelper('join_slash', function(arreglo, options) {
    var result = '';
    
    for (var i in arreglo) {
        result += options.fn(arreglo[i]) + ' / ';
    }
    
    return result.substr(0, result.length-2);
});

Handlebars.registerHelper('substr', function(str, start, length) {
    start = start | 0;
    length = length | 120;
    
    if (str.length > length) {
        return str.substr(start, length) + '...';
    }
    
    return str;
});

toastr.options = {
    "closeButton": true,
    "positionClass": "toast-bottom-center",
};

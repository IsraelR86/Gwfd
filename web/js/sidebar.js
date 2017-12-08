'use strict';

$(document).ready(function () {
    $("#calendario").datepicker({
        format: "dd-mm-yyyy",
        language: "es",
        todayHighlight: true,
        beforeShowDay: function (date) {
            // Obtiene la fecha que se esta dibujando en el calendario
            // formato YYYY-MM-DD, porque es el formato que utiliza MySQL
            var formattedDate = date.getFullYear()+"-"+helpers.padLeft((date.getMonth()+1),2)+"-"+helpers.padLeft(date.getDate(), 2);
            
            // Busca si la fecha a mostrar esta en la lista de las fechas a resaltar en el calendario
            var found = $.grep(fechasCalendario, function(item) { 
                return item.fecha == formattedDate; 
            });
            
            // Si lo encuentra, agrega el tooltipster
            if (found.length != 0) {
                return {
                    enabled: true,
                    classes: "tooltipster active concurso-"+found[0].id,
                    tooltip: "<div>\n\
                        <span class=\'title\'>"+found[0].titulo+"<br>"+found[0].subtitulo+"</span><hr>\n\
                        <p>"+found[0].contenido+"</p>\n\
                    </div>"
                };
            }
        },
        beforeShowMonth: function () {
            helpers.builderTooltipster("#calendario .tooltipster");
        }
    });
    
    var $icon_right = $(".datepicker .next img");

    if ($icon_right) {
        $icon_right.attr("src", urlImg+'/Next.png');
        $icon_right.css("width", "25px");
        $icon_right.css("height", "25px");
    }
    
    var $icon_left = $(".datepicker .prev img");
    if ($icon_left) {
        $icon_left.attr("src", urlImg+'/Back.png');
        $icon_left.css("width", "25px");
        $icon_left.css("height", "25px");
    }
});

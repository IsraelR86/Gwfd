'use strict';

var preg_geo_radial = null;
var preg_geo_poligono = null;
var preg_geo_punto = null;

function showDialogMapa (event) {
    switch ($(this).data('tipo')) {
        case 6: // Punto Radial Geográfico
            var puntosRadial = $(this).data('puntos');

            $('#dialogMapaPuntoRadial .btnFinalizarMapa').css('display', 'none');
            $( "#dialogMapaPuntoRadial").dialog('open');

            preg_geo_radial.cleanMap();

            if (puntosRadial != '' && typeof puntosRadial!='undefined') {
                preg_geo_radial.loadPuntoRadialFromJson(puntosRadial);
            }

            break;
        case 7: // Polígono Geográfico
            var poligono = $(this).data('puntos');

            $('#dialogMapaPoligono .btnFinalizarMapa').css('display', 'none');
            $('#dialogMapaPoligono .btnLimpiarMapa').css('display', 'none');
            $( "#dialogMapaPoligono" ).dialog('open');

            preg_geo_poligono.cleanMap();

            if (poligono != '' && typeof poligono!='undefined') {
                preg_geo_poligono.loadPoligonoFromJson(poligono);
            }
            break;
        case 8: // Punto Geográfico
            var puntos = $(this).data('puntos');

            $( "#dialogMapaPunto" ).dialog('open');
            $('#dialogMapaPunto .btnFinalizarMapa').css('display', 'none');

            preg_geo_punto.cleanMap();

            if (puntos != '' && typeof puntos!='undefined') {
                preg_geo_punto.loadPuntoFromJson(puntos);
            }
            break;
    }
}

$(document).ready(function() {
    preg_geo_radial = pregunta_geografica();
    preg_geo_radial.initMap({selectorMap: '#mapaPuntoRadial'});
    preg_geo_radial.addMenuPuntoRadial();

    preg_geo_poligono = pregunta_geografica();
    preg_geo_poligono.initMap({selectorMap: '#mapaPoligono'});
    preg_geo_poligono.addMenuPoligono();

    preg_geo_punto = pregunta_geografica();
    preg_geo_punto.initMap({selectorMap: '#mapaPunto'});
    preg_geo_punto.addMenuPunto();

    $('.respuesta_geografica').click(showDialogMapa);

});

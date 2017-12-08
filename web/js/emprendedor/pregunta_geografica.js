'use strict';

function pregunta_geografica() {
    var map = null;
    var menu = null;
    var selectorMap = null;
    var current = null;
    var jsonPuntoRadial = [];
    var jsonPoligono = [];
    var jsonPunto = [];

    var objGMaps = {
        map: function () {
            return map;
        },
        selectorMap: function () {
            return selectorMap;
        },
        initMap: function(config) {
            var defaultConfig = {
                map: {
                    options: {
                        center: [26.6709344,-108.919747],
                        //center: [25.682267,-109.3909036],
                        //center: [26.3754232,-108.2776807],
                        zoom: 5
                    },
                    events: {
                        rightclick:function(map, event){
                            current = event;
                            menu.open(event);
                        },
                        click: function(){
                            menu.close();
                        },
                        dragstart: function(){
                            menu.close();
                        },
                        zoom_changed: function(){
                            menu.close();
                        }
                    }
                }
            };

            $.extend(true, defaultConfig, config);
            selectorMap = defaultConfig.selectorMap;
            menu = new Gmap3Menu($(selectorMap));

            map = $(selectorMap).gmap3(defaultConfig);
        },

        addMenuPuntoRadial: function(config) {
            menu.add("Marcar Ubicación", "centerHere", function(){
                var radius = parseInt(prompt('Radio de influencia en metros:'));
                menu.close();

                if (!isNaN(radius)) {
                    var index = objGMaps.generateIndex();
                    objGMaps.addPuntoRadial(index, {lat: current.latLng.lat(), lng: current.latLng.lng()}, radius);
                } else {
                    alert('ERROR: Debe introducir un número entero positivo');
                }

            });
        },

        addMenuPoligono: function(config) {
            menu.add("Marcar Ubicación", "centerHere", function(){
                var index = objGMaps.generateIndex();
                objGMaps.addPuntoPoligono(index, {lat: current.latLng.lat(), lng: current.latLng.lng()});

                menu.close();
            });
        },

        addMenuPunto: function(config) {
            menu.add("Marcar Ubicación", "centerHere", function(){
                var index = objGMaps.generateIndex();

                objGMaps.addMarkerPunto(index, {lat: current.latLng.lat(), lng: current.latLng.lng()});

                menu.close();
            });
        },

        templateFrmPuntoRadial: "\n\
            <div class='form-horizontal edit_marker'>\n\
                <div class='form-group'>\n\
                    <label for='marker_{{index}}_radius' class='control-label'>Radio:</label>\n\
                    <div class='col-sm-10 form-control' placeholder='Radio' id='marker_{{index}}_radius' /><strong>{{radius}}</strong></div>\n\
                </div>\n\
                <div class='form-group'>\n\
                    <div class='col-sm-12 text-center'>\n\
                      <a href='#' class='btn btn-danger btnEliminar' data-marker-index='{{index}}' data-id='{{id}}' onclick='preg_geo_radial.delMarker({{index}}, event)'>Eliminar</a>\n\
                    </div>\n\
                </div>\n\
            </div>",

        templateFrmPoligono: "\n\
            <div class='form-horizontal edit_marker'>\n\
                <div class='form-group'>\n\
                    <div class='col-sm-12 text-center'>\n\
                      <a href='#' class='btn btn-danger btnEliminar' data-marker-index='{{index}}' data-id='{{id}}' onclick='preg_geo_poligono.delMarker({{index}}, event)'>Eliminar</a>\n\
                    </div>\n\
                </div>\n\
            </div>",

        templateFrmPunto: "\n\
            <div class='form-horizontal edit_marker'>\n\
                <div class='form-group'>\n\
                    <div class='col-sm-12 text-center'>\n\
                      <a href='#' class='btn btn-danger btnEliminar' data-marker-index='{{index}}' data-id='{{id}}' onclick='preg_geo_punto.delMarker({{index}}, event)'>Eliminar</a>\n\
                    </div>\n\
                </div>\n\
            </div>",

        addPunto: function(index, config, infowindow) {
            var options = {
                marker: {
                    latLng: [config.lat, config.lng],
                    id: index,
                    data: index,
                    events: {
                        click: function(marker, event, context){
                            var map = $(this).gmap3("get"),
                            infowindow = $(this).gmap3({get:{name:"infowindow", tag:context.id}});

                            if (infowindow) {
                                infowindow.open(map, marker);
                            }
                        }
                    },
                    callback: function(marker){
                        //console.log('callback');
                    }
                }
            };

            if (typeof infowindow != 'undefined') {
                options.infowindow =  {
                    options:{
                        content: infowindow
                    },
                    open: false,
                    tag: index
                }
            }

            $(selectorMap).gmap3(options);
        },

        addMarkerPunto: function(index, config) {
            var frmPunto = Handlebars.compile(objGMaps.templateFrmPunto);
            var infoWindow = frmPunto({
                    index: index,
                    id: index
                });

            objGMaps.addPunto(index, {lat: config.lat, lng: config.lng}, infoWindow);

            jsonPunto.push({
                id: index,
                lat: config.lat,
                lng: config.lng,
            });
        },

        addPuntoRadial: function(index, config, radius) {
            var frmPuntoRadial = Handlebars.compile(objGMaps.templateFrmPuntoRadial);
            var infoWindow = frmPuntoRadial({
                    index: index,
                    id: index,
                    radius: radius
                });

            $(selectorMap).gmap3({
                circle:{
                    options:{
                        center: [config.lat, config.lng],
                        radius : radius,
                        fillColor : "#008BB2",
                        strokeColor : "#005BB7"
                    },
                    tag: index,
                    data: index
                }
            });

            objGMaps.addPunto(index, config, infoWindow);

            jsonPuntoRadial.push({
                id: index,
                lat: config.lat,
                lng: config.lng,
                radius: radius,
            });
        },

        addPuntoPoligono: function(index, config) {
            var frmPoligono = Handlebars.compile(objGMaps.templateFrmPoligono);
            var infoWindow = frmPoligono({
                    index: index,
                    id: index
                });

            objGMaps.addPunto(index, config, infoWindow);

            jsonPoligono.push({
                id: index,
                lat: config.lat,
                lng: config.lng,
            });
        },

        drawPoligono: function(points) {
            var paths = points;

            // El ultimo punto debe ser el primero
            paths.push(paths[0]);

             $(selectorMap).gmap3({
                polygon:{
                    options:{
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#FF0000",
                        fillOpacity: 0.35,
                        paths: paths
                    }
                }
            });
        },

        cleanMap: function () {
            jsonPuntoRadial = [];
            jsonPoligono = [];
            jsonPunto = [];
            $(selectorMap).gmap3({clear:{}});
            $(selectorMap).gmap3({trigger:"resize"});
        },

        getJsonPuntoRadial: function () {
            return _.map(jsonPuntoRadial, function(marker){ return _.omit(marker, 'id'); });
        },

        getJsonPoligono: function () {
            return _.map(jsonPoligono, function(marker){ return _.omit(marker, 'id'); });
        },

        getJsonPunto: function () {
            return _.map(jsonPunto, function(marker){ return _.omit(marker, 'id'); });
        },

        generateIndex: function() {
            return parseInt(Math.round(Math.random()*10000000000000000));
        },

        loadPuntoRadialFromJson: function(jsonMarkers) {
            _.each(jsonMarkers, function (marker) {
                objGMaps.addPuntoRadial(objGMaps.generateIndex(), marker, parseInt(marker.radius));
            });
        },

        loadPoligonoFromJson: function(jsonMarkers) {
            objGMaps.drawPoligono(jsonMarkers);
        },

        loadPuntoFromJson: function(jsonMarkers) {
            _.each(jsonMarkers, function (marker) {
                objGMaps.addMarkerPunto(objGMaps.generateIndex(), marker);
            });
        },

        delMarker: function(index) {
            $(selectorMap).gmap3({clear: {id: index} }); // Elimina el marker
            $(selectorMap).gmap3({clear: {tag: [index]} }); // Elimina cualquiere elemento asociado con el marker

            // Eliminamos el elemento del arreglo json
            jsonPuntoRadial = _.reject(jsonPuntoRadial, function(mark){ return mark.id == index; });
            jsonPoligono = _.reject(jsonPoligono, function(mark){ return mark.id == index; });
            jsonPunto = _.reject(jsonPunto, function(mark){ return mark.id == index; });
        }

    };

    return objGMaps;

}
'use strict';

var helpers = {
    /**
     * Definimos un spinner como indicador de cargando contenidos
     */
    spinner: '<i class="fa fa-spinner fa-pulse fa-lg"></i>',

    /**
     * Definimos un mensaje de cargando contenidos
     */
    loadingMsg: '<div style="text-align:center;padding:10px 0; color:#999;"><img src="data:image/gif;base64,R0lGODlhEAALAPQAAP///zMzM+Li4tra2u7u7jk5OTMzM1hYWJubm4CAgMjIyE9PT29vb6KiooODg8vLy1JSUjc3N3Jycuvr6+Dg4Pb29mBgYOPj4/X19cXFxbOzs9XV1fHx8TMzMzMzMzMzMyH5BAkLAAAAIf4aQ3JlYXRlZCB3aXRoIGFqYXhsb2FkLmluZm8AIf8LTkVUU0NBUEUyLjADAQAAACwAAAAAEAALAAAFLSAgjmRpnqSgCuLKAq5AEIM4zDVw03ve27ifDgfkEYe04kDIDC5zrtYKRa2WQgAh+QQJCwAAACwAAAAAEAALAAAFJGBhGAVgnqhpHIeRvsDawqns0qeN5+y967tYLyicBYE7EYkYAgAh+QQJCwAAACwAAAAAEAALAAAFNiAgjothLOOIJAkiGgxjpGKiKMkbz7SN6zIawJcDwIK9W/HISxGBzdHTuBNOmcJVCyoUlk7CEAAh+QQJCwAAACwAAAAAEAALAAAFNSAgjqQIRRFUAo3jNGIkSdHqPI8Tz3V55zuaDacDyIQ+YrBH+hWPzJFzOQQaeavWi7oqnVIhACH5BAkLAAAALAAAAAAQAAsAAAUyICCOZGme1rJY5kRRk7hI0mJSVUXJtF3iOl7tltsBZsNfUegjAY3I5sgFY55KqdX1GgIAIfkECQsAAAAsAAAAABAACwAABTcgII5kaZ4kcV2EqLJipmnZhWGXaOOitm2aXQ4g7P2Ct2ER4AMul00kj5g0Al8tADY2y6C+4FIIACH5BAkLAAAALAAAAAAQAAsAAAUvICCOZGme5ERRk6iy7qpyHCVStA3gNa/7txxwlwv2isSacYUc+l4tADQGQ1mvpBAAIfkECQsAAAAsAAAAABAACwAABS8gII5kaZ7kRFGTqLLuqnIcJVK0DeA1r/u3HHCXC/aKxJpxhRz6Xi0ANAZDWa+kEAA7" alt=""><br />Cargando...</div>',

    /**
     * Carga los valores de un JSON a un formulario especificado
     * el JSON debe tener como clave el nombre del input al que se desea agregar el valor
     *
     * @param {string} idForm ID del formulario en el cual se cargaran los datos
     * @param {JSON} data JSON con los datos a cargar
     */
    loadJSONtoForm: function(idForm, data) {
        var itemForm = null;
        var field = null;

        for (field in data) {
            itemForm = $('#'+idForm+' [name="'+field+'"]');

            if (itemForm.length == 0) {
                if (config.isDebugging()) {
                    console.log('The field '+field+' doesn\'t could find by helpers.loadJSONtoForm');
                }
            } else {
                switch (itemForm.prop("tagName").toLowerCase()) {
                    case 'input':

                        if (field.indexOf('fecha') != -1) {
                            data[field] = helpers.transformDate(data[field], 'DD-MM-YYYY');
                        }

                        itemForm.val(data[field]);
                        break;

                    case 'select':
                        itemForm.find('option[value='+data[field]+']').prop('selected', true);
                        break;

                    default:
                        if (config.isDebugging()) {
                            console.log('Type '+itemForm.prop("tagName").toLowerCase()+' for '+field+' no supported by helpers.loadJSONtoForm');
                        }

                }
            }
        }
    },

    /**
     * Recibe una fecha en formato YYYY-MM-DD o DD-MM-YYYY y lo
     * devuelve en el formato especificado por formatOutput
     * Depende de moment.js
     *
     * @param {string} strDate
     * @param {string} formatOutput Default DD-MM-YYYY
     * @return {string}
     */
    transformDate: function(strDate, formatOutput)
    {
        formatOutput = formatOutput || 'DD-MM-YYYY';

        if (strDate == '') {
            return '';
        }

        var formatInput = '';
        var partsDate = strDate.split('-');

        if (partsDate[0].length == 4) {
            formatInput = 'YYYY-MM-DD';
        } else if (partsDate[0].length == 2) {
            formatInput = 'DD-MM-YYYY';
        } else {
            if (config.isDebugging()) {
                console.log('Date '+strDate+' invalid by helpers.transformDate');
            }
            return ''; // Formato no conocido
        }

        var fecha = moment(strDate, formatInput);

        if (fecha == false) {
            if (config.isDebugging()) {
                console.log('Date '+strDate+' invalid by helpers.transformDate');
            }
            return '';
        }

        return fecha.format(formatOutput);
    },

    /**
     * Crea un alert de Boostrap
     *
     * @param {string} message
     * @param {JSON} params Parámetros por default = {
                                alertClass: "alert-success",
                                alertIcon: "check green",
                                alertClose: true
                            }
     * @return {string}
     */
    alertBoostrap: function(message, params)
    {
        var defaultParams = {
            alertClass: "alert-success",
            alertIcon: "check green",
            alertClose: true
        };

        params = $.extend(defaultParams, params);

        return '<div class="alert alert-block '+params.alertClass+'">\n'+
            (params.alertClose ? '<button type="button" class="close" data-dismiss="alert">\n\
                <i class="ace-icon fa fa-times"></i>\n\
            </button>\n' : '')
            +'<i class="ace-icon fa fa-'+params.alertIcon+'"></i>&nbsp; \n\
            '+message+'\n\
        </div>';
    },

    /**
     * Llena un elemento select con el listado de elementos porporcionados
     *
     * @param {string} selector Selector CSS para obtener la referencia al Select
     * @param {JSON}|{array} list Listado de elementos a insertar, deben ser un arreglo asociativo value -> text
     * @param {boolean} reset Indica si el Select será limpiado antes de insertar los nuevos datos, Default true
     * @param {string} value Si los items de list es un objeto, value es el atributo que contiene el valor
     * @param {string} text Si los items de list es un objeto, text es el atributo que contiene la descripcion o etiqueta
     */
    fillSelect: function(selector, list, reset, value, text)
    {
        reset = reset || true;
        value = value || '';
        text = text || '';
        var item = null;
        var select = $(selector);

        if (reset == true) {
            select.find('option:not(:first-child)').remove();
        }

        if (list.length != 0) {
            for (item in list) {
                if (value != '' && text != '') {
                    if (list[item][value] != undefined && list[item][text] != undefined) {
                        select.append('<option value="'+list[item][value]+'">'+list[item][text]+'</option>');
                    }
                } else {
                    select.append('<option value="'+item+'">'+list[item]+'</option>');
                }
            }
        }
    },

    /**
     * Llena un select a partir de la respuesta de una solicitu Ajax
     *
     * @param {JSON} options Las opciones válidas son:
     * @param {string} select Selector CSS para referenciar al select a llenar
     * @param {boolean} resetSelect: Indica si las opciones del select serán eliminadas antes de cargar los datos, Default true
     * @param {boolean} showLoadIndicator: Indica si se mostrara un indicador de cargando mientras no se devuelva respuesta de la solicitud, Default true
     * @param {'' | string|object} containerIndicator: Indica el contenedor donde se mostrara el spinner, puede ser vacio (mostrará el indicador despues del elemento), selector u objecto (mostrará dentro del contenedor especificado)
     * @param {string} value: Si la respuesta es una lista de objetos este indica el key donde se encuentra el valor
     * @param {string} text: Si la respuesta es una lista de objetos este indica donde se encuentra el texto a utilizar dentro del option
     * @param {string} wrapper: Si la respuesta es una lista de objetos, indica la key donde se encuentra el listado de opciones
     *
     *      Los siguientes parametros son los por defecto de jQuery para la solicitud Ajax
     * @param {string} type Default POST
     * @param {string} url
     * @param {JSON} data
     * @param {string} dataType Default json
     *
     * @return {Promise} Promise del request ajax
     */
    fillSelectByAjax: function(options)
    {
        var request = null;
        var defaultOptions = {
            select: '',
            resetSelect: true,
            showLoadIndicator: true,
            containerIndicator: '',
            value: '',
            text: '',
            wrapper: '',
            type: 'POST',
            url: '',
            data: '',
            dataType: 'json',
        };

        options = $.extend(defaultOptions, options);

        // Se agrega el parametro _csrf, necesario para la solicitud Yii
        if (typeof(options.data) == 'object') {
            options.data = $.extend({_csrf: yii.getCsrfToken()}, options.data);
        } else if (typeof(options.data) == 'string') {
            options.data += '&_csrf='+yii.getCsrfToken();
        } else {
            if (config.isDebugging()) {
                console.log('options.data invalid recived by helpers.fillSelectByAjax');
            }
        }

        request = $.ajax({
            type: options.type,
            url: options.url,
            data: options.data,
            dataType: options.dataType,
            beforeSend: function(xhr, settings) {
                if (options.showLoadIndicator == true) {
                    if (options.containerIndicator) {
                        if (options.containerIndicator instanceof Object) {
                            options.containerIndicator.html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
                        } else {
                            $(options.containerIndicator).html('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
                        }
                    } else {
                        $(options.select).after('<i class="fa fa-spinner fa-pulse fa-lg"></i>');
                    }
                }
            }
        });

        request.done(function(data, status, xhr) {
            if (options.wrapper != '') {
                helpers.fillSelect(options.select, data[options.wrapper], options.resetSelect, options.value, options.text);
            } else {
                helpers.fillSelect(options.select, data, options.resetSelect, options.value, options.text);
            }
        });

        request.always(function(data, status, xhr) {
            if (options.showLoadIndicator == true) {
                $(options.select).parent().find('.fa-spinner').remove();
            }
        });

        request.fail(function(xhr, status, error) {
            if (config.isDebugging()) {
                console.log('Error '+status+' by helpers.fillSelectByAjax: '+error);
            }
        });

        // Se devuelve del Promise de la solicitud Ajax
        return request;
    },

    /**
     * Deshabilita todos los inputs dentro de un form
     *
     * @param {string} formSelector
     */
    disableAllFields: function(formSelector)
    {
        $(formSelector).find('input, select, textarea').each(function(index, element){
            $(element).prop('disabled', true);
        });
    },

    /**
     * Oculta los elementos con el selector especificada en el tiempo especificado
     *
     * @param {string} selector default .alert-hide
     * @param {int} seconds default 3s
     */
    removeItemAfterSeconds: function(selector, seconds)
    {
        selector = selector || '.hide-after-seconds';
        seconds = seconds || 3;

        setTimeout(function(){
            $(selector).fadeOut(800, function(){ $(this).remove(); });
        }, seconds * 1000);
    },

    /**
     * Obtiene información general del error y lo muestra en un alert
     *
     * @param {string} message
     * @param {string} url
     * @param {int} line
     * @param {int} col
     * @param {string} error
     */
    traceError: function (message, url, line, col, error) {
        if (message) {
            var info = "Error: " + message +"\n"+
                "Url: "+url+"\n"+
                "Line: "+line;

            info += !col ? "" : "\ncolumn: " + col;
            info += !error ? "" : "\nerror: " + error;

            console.log(info);
            alert(info);
        }
    },

    /**
     * Obtiene información general del error Ajax y lo muestra en un alert
     *
     * @param {object} event
     * @param {object} jqxhr
     * @param {object} settings
     * @param {string} thrownError
     */
    traceAjaxError: function(event, jqxhr, settings, thrownError) {
        if (thrownError != '' && jqxhr.statusText != 'abort') {
            var info = "Error Ajax: " + thrownError +"\n"+
                "status: "+jqxhr.status+"\n"+
                "statusText: "+jqxhr.statusText+"\n"+
                "url: "+settings.url+"\n"+
                "data: "+settings.data+"\n"+
                "responseText: "+(jqxhr.responseText ? jqxhr.responseText.substr(0, 1100) : '');

            console.log(info);
            alert(info);
        }
    },

    /**
     * Shorcut para inicializar el tooltipster
     *
     * @param {string} selector
     */
    builderTooltipster: function (selector, config) {
        var conf = $.extend({
            contentAsHTML: true,
            theme: 'tooltipster-light',
            maxWidth: 250,
            position: 'right'
        }, config);

        $(selector).tooltipster(conf);
    },

    /**
     * Shorcut para inicializar el waterfall de la fichas en "Aplica" y "Mis Proyectos"
     *
     * @param {string} itemTemplate Template a utilizar para mostrar los items
     * @param {string} loadPath URL desde donde se cargaran los elementos
     * @param {function} onComplete Funcion que se ejecutará cuando la carga de datos haya finalizado
     * @param {string} selector Default "#waterfall"
     */
    builderWaterfall: function (itemTemplate, loadPath, onComplete, selector) {
        var selector = selector || "#waterfall";

        $(selector).waterfall({
            loadingMsg: helpers.loadingMsg,
            itemCls: "item",
            colWidth: 274,
            gutterWidth: 15,
            gutterHeight: 15,
            checkImagesLoaded: true,
            isAnimated: true,
            maxPage: 5,
            path: function(page) {
                if (loadPath.indexOf('?') == -1) {
                    return loadPath + '?page=' + page;
                } else {
                    return loadPath + '&page=' + page;
                }
            },
            state: {
                curPage: 0
            },
            callbacks: {
                renderData: function (data, dataType) {
                    var tpl,
                        template,
                        resultNum = data.total;

                    if (resultNum == 0) {
                        $(selector).waterfall('pause', function() {
                            $('#waterfall-message').html(''); // <p style="color:#666;">No hay más datos que mostrar.</p>
                        });
                    }

                    if (dataType === 'json' ||  dataType === 'jsonp') {
                        tpl = $(itemTemplate).html();
                        template = Handlebars.compile(tpl);

                        return template(data);
                    } else {
                        return data;
                    }
                },
                loadingFinished: function($loading, isBeyondMaxPage) {
                    if ( !isBeyondMaxPage ) {
                        $loading.fadeOut();
                    } else {
                        $loading.remove();
                    }

                    // Ejecutamos la función después de la carga de datos
                    onComplete();
                },
            },
        });
    },

    /**
     * Rellena por la izquierda con un caracter hasta llegar a cierta longitud
     *
     * @param {string} nr  Cadena a rellenar
     * @param {string} n   Longitud a completar
     * @param {string} str Caracter a insertar, default 0
     *
     * @return {string} Cadena rellenada por la izquierda
     */
    padLeft: function (nr, n, str) {
        return Array(n-String(nr).length+1).join(str||'0')+nr;
    },

    /**
     * Shorcut para inicializar iCheck
     *
     * @param {string} selector Default "input"
     * @param {string} theme Default "_flat-red"
     */
    builderiCheck: function (selector, theme) {
        var selector = selector || 'input';
        var theme = theme || '_flat-red';

        $(selector).iCheck({
            checkboxClass: 'icheckbox'+theme,
            radioClass: 'iradio'+theme
        });
    },

    /**
     * Agrega la funcionalidad de mostrar/ocultar las filas de una tabla
     * El contenido de la celda (td) debe estar dentro de un div
     *
     * @param {string} trigger Selector del elemento que dispara el evento
     * @param {string} selector Selector para las flilas
     * @param {string} className Clase para agregar/eliminar de las filas
     * @param {integer} time Default 1000 ms
     */
    slideRowTable: function (trigger, selector, className, time) {
        var time = time || 1000;
        var $trs = $(selector).toggleClass(className);

        $(trigger).toggleClass(className);

        if ($(trigger).hasClass(className)) {
            // Ocultar
            $trs.find('td > div').slideUp(time);
        } else {
            // Mostrar
            $trs.find('td > div').slideDown(time);
        }
    },

    /**
     * Get the key in the query string on the method GET
     *
     * @param {type} vkey Key of paramt
     * @param {type} vdefault_ Defaut value if the no set the key
     * @returns mixed
     */
    getQuerystring: function(vkey, vdefault_) {
        if (vdefault_==null)  {
            vdefault_="";
        }
        var search = unescape(location.search);

        if (search == "") {
            return vdefault_;
        }

        search = search.substr(1);
        var params = search.split("&");

        for (var i=0; i<params.length; i++) {
            var pairs = params[i].split("=");

            if(pairs[0] == vkey) {
                return pairs[1];
            }
        }

        return vdefault_;
    }
};
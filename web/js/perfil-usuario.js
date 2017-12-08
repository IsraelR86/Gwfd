'use strict';

var fileUploaderAvatar;
var configUploadFile = {
    multiple: false,
    dragDrop: false,
    maxFileCount: 1,
    allowedTypes: 'jpg,jpeg,png,gif',
    acceptFiles: "image/*",
    maxFileSize: 5*1024*1024, 
    cancelStr: "Cancelar",
    extErrorStr: "no es aceptado. Extensiones permitidas: ",
    sizeErrorStr: "excede el tamaño límite. Tamaño límite permitido: ",
    maxFileCountErrorStr: "no esta permitido. El número máximo de archivos es: ",
    showPreview: true,
    previewHeight: "100px",
    previewWidth: "100px",
    autoSubmit: false,
    sequential: true,
    sequentialCount: 1,
    onError: function(files, status, errMsg, pd) {
        toastr.error(errMsg);
    },
};

$(document).ready(function() {
    
        fileUploaderAvatar = $("#fileuploaderAvatar").uploadFile($.extend(configUploadFile, {
            url: urlSetPerfil,
            fileName: 'avatar',
            uploadStr: "Avatar",
            dynamicFormData: infoDatosGeneralesUsuario,
            onSuccess: function(files, data, xhr, pd) {
                
                if (data.error) {
                    fileUploaderAvatar.reset(false);
                    $('#container-fileuploaderAvatar .ajax-file-upload-container > div:not(.current)').parent().html('');
                    toastr.error(data.message);
                }else{
                    toastr.success('Datos actualizados exitosamente');   
                }
                $('#btnGuardar').find('.fa-spinner').remove();
            }
        }));
        $('#welcome').modal('show');
    $.get(urlGetPerfil, function(data) {
            $("[name=email]").val(data.email);
            $("[name=nombre]").val(data.nombre);
            $("[name=appat]").val(data.appat);
            $("[name=apmat]").val(data.apmat);
            $("[name='extras[fecha_nacimiento]']").val(data.extras.fecha_nacimiento);
            $("[name='extras[id_estado]'] option[value="+data.extras.id_estado+"]").prop('selected', true);
            $("[name='extras[id_ciudad]'] option[value="+data.extras.id_ciudad+"]").prop('selected', true);
            $("[name='extras[genero]'] option[value="+data.extras.genero+"]").prop('selected', true);
            $("[name='extras[estado_civil]'] option[value="+data.extras.estado_civil+"]").prop('selected', true);
            $("[name='extras[id_nivel_educativo]'] option[value="+data.extras.id_nivel_educativo+"]").prop('selected', true);
            $("[name='extras[id_universidad]'] option[value="+data.extras.id_universidad+"]").prop('selected', true);
            $("[name='extras[universidad_otro]']").val(data.extras.universidad_otro);
            $("[name='extras[profesion]']").val(data.extras.profesion);
            $("[name='extras[curp]']").val(data.extras.curp);
            $("[name='extras[rfc]']").val(data.extras.rfc);
            $("[name='extras[tel_celular]']").val(data.extras.tel_celular);
            $("[name='extras[tel_fijo]']").val(data.extras.tel_fijo);
            $("[name='extras[id_estado_nacimiento]'] option[value="+data.extras.id_estado_nacimiento+"]").prop('selected', true);
            $("[name='extras[id_ciudad_nacimiento]'] option[value="+data.extras.id_ciudad_nacimiento+"]").prop('selected', true);
            $("[name='extras[direccion]']").val(data.extras.direccion);
            $("[name='extras[colonia]']").val(data.extras.colonia);
            $("[name='extras[cp]']").val(data.extras.cp);
            $("[name='extras[facebook]']").val(data.extras.facebook);
            $("[name='extras[twitter]']").val(data.extras.twitter);
            $("[name='extras[pagina_web]']").val(data.extras.pagina_web);
            
            if(typeof data.extras.avatar != 'undefined'){
                $('#avatarEvaluador').prop('src', data.extras.avatar);
               
                $('#container-fileuploaderAvatar .ajax-file-upload-container').html(
                   '<div class="ajax-file-upload-statusbar current" style="width: 400px;">\n\
                       <img class="ajax-file-upload-preview" src="'+data.extras.avatar+'" style="width: 100px; height: 100px;">\n\
                       <div></div>\n\
                       <div class="ajax-file-upload-red ajax-file-upload-cancel" style="">Eliminar</div>\n\
                   </div>');
               $('#container-fileuploaderAvatar .ajax-file-upload-container .ajax-file-upload-cancel').click(function() {
                   $(this).parent().remove();
               });
               
            }
            
            if(typeof data.extras.semblanza != 'undefined'){
                $("[name='extras[semblanza]']").val(data.extras.semblanza);
            }
            
            $('[name="extras[fecha_nacimiento]"]').mask('99-99-9999', {placeholder:"dd-mm-yyyy"});
            
            $('[name="extras[id_universidad]"]').select2({
                placeholder: "Seleccione su Universidad",
                language: "es",
            });
            
            $('[name="extras[id_ciudad]"]').select2({
                placeholder: "Seleccione su Ciudad",
                language: "es",
            });
            
            $('#loadingMsg').hide();
    });
    
    $('#btnGuardar').click(function(event) {
        event.preventDefault();
        
        var datos = $('#frmPerfil').serialize();
        //var nombre = $('#frmPerfil input[name="nombre"]').val();
        
        
        $(this).append(helpers.spinner);
        
        $('.ng-invalid').removeClass('ng-invalid');
        
        if(hasArchivoSelected('fileuploaderAvatar')){
            fileUploaderAvatar.startUpload();
            location.reload();
        }else{
            $.post(urlSetPerfil, datos, function(respuesta) {
                if (respuesta.error) {
                    toastr.error(respuesta.message);
                    for (var attr in respuesta.attrs) {
                        $("[name*='"+respuesta.attrs[attr]+"']").addClass('ng-invalid');
                    }
                } else {
                    toastr.success(respuesta.message);
                }
                $('#btnGuardar').find('.fa-spinner').remove();
            });
        }
    });
    
    $("[name='extras[id_estado]']").change(function(event) {
        event.preventDefault();

        helpers.fillSelectByAjax({
            select: "[name='extras[id_ciudad]']",
            containerIndicator: $("[name='extras[id_ciudad]']").parent().find('.container-icon'),
            url: urlGetCiudades,
            data: 'estado='+$(this).val()+'_csrf='+yii.getCsrfToken(),
            value: 'id', 
            text: 'descripcion'
        });
    });
    
    $("[name='extras[id_estado_nacimiento]']").change(function(event) {
        event.preventDefault();

        helpers.fillSelectByAjax({
            select: "[name='extras[id_ciudad_nacimiento]']",
            containerIndicator: $("[name='extras[id_ciudad_nacimiento]']").parent().find('.container-icon'),
            url: urlGetCiudades,
            data: 'estado='+$(this).val()+'_csrf='+yii.getCsrfToken(),
            value: 'id', 
            text: 'descripcion'
        });
    });
    
    $('#btnShowCambiarPass').click(function(event) {
        event.preventDefault();
        
        $('#modalCambiarPass').modal('show');
    });
    
    $('#btnSendCambiarPass').click(function(event) {
        event.preventDefault();
        var $this = $(this),
            cacheIco = $this.html();
        
        if ($('#new_pass').val().length < 6) {
            toastr.error('Debe escribir una contraseña de por lo menos 6 carácteres.');
            return false;
        }
        
        if ($('#new_pass').val() != $('#new_pass_confirm').val()) {
            toastr.error('La confirmación de la contraseña no coincide, verifique su contraseña.');
            return false;
        }
        
        $this.html(helpers.spinner);
        
        $.post(urlChangePass, $('#frmCambiarPass').serialize())
            .done(function(response) {
                if (response.error) {
                    toastr.error('Error: '+response.error);
                } else {
                    toastr.success('La contraseña se atualizó de forma exitosa.');
                }
            })
            .fail(function(response) {
                toastr.error('Error al intentar cambiar la contraseña, intentelo nuevamente.');
            }).always(function() {
                $this.html(cacheIco);
                $('#modalCambiarPass').modal('hide');
            });
    });
});

// http://stackoverflow.com/questions/1184624/convert-form-data-to-javascript-object-with-jquery
function infoDatosGeneralesUsuario() {
   var o = {};
   var a = $('#frmPerfil').serializeArray();
   
   $.each(a, function() {
       if (o[this.name] !== undefined) {
           if (!o[this.name].push) {
               o[this.name] = [o[this.name]];
           }
           o[this.name].push(this.value || '');
       } else {
           o[this.name] = this.value || '';
       }
   });
   
   return o;
}

function hasArchivoSelected(archivo) {
   return $('#container-'+archivo+' .ajax-file-upload-container > div:not(.current)').length;
}
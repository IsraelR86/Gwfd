$(document).ready(function() {
    $('#singupForm').on('beforeSubmit', function(e) {
        e.preventDefault();
        var data = {};
        var btnAceptar = $('#btnAceptarSingup'),
            cacheIco = $('#btnAceptarSingup').html();
            
        data.email = $('#singupform-email').val();
        data.password = $('#singupform-password').val();
        data.nombre = $('#singupform-nombre').val();
        data.appat = $('#singupform-appat').val();
        data.apmat = $('#singupform-apmat').val();
            
        btnAceptar.html(helpers.spinner);
        
        $.post(urlRegistrar, data)
            .done(function(response) {
                if (response.error) {
                    toastr.error('Error al registrar el usuario. '+response.mensaje);
                } else {
                    //toastr.success('Registro exitoso del usuario, ahora puede iniciar sesión con su cuenta.');
                    location.href = urlLogin + '?singup=true';
                }
            })
            .fail(function(response) {
                toastr.error('Error al registrar el usuario.');
            })
            .always(function() {
                btnAceptar.html(cacheIco);
            });
    })
    .on('submit', function (e) {
        e.preventDefault();
    });
});
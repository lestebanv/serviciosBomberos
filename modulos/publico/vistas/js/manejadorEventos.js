jQuery(document).ready(function($) {
    $(document).on('submit', '#frm_buscar_empresa', function(e) {
        e.preventDefault();
        console.log('Formulario enviado');
        var formData = $(this).serialize();
        $('#empresa-contenido').append('<div class="bomberos-mensaje info">Buscando empresa...</div>');
        $.ajax({
            type: 'POST',
            url: bomberosPublicoAjax.ajax_url,
            data: {
                action: 'BomberosPluginPublico',
                modulo: 'publico',
                funcionalidad: 'buscar_empresa',
                form_data: formData,
                nonce: bomberosPublicoAjax.nonce
            },
            success: function(response) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                if (response.success) {
                    $('#empresa-contenido').html(response.data.html);
                    $('#empresa-contenido').prepend('<div class="bomberos-mensaje success">' + response.data.mensaje + '</div>');
                } else {
                    $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">' + response.data.mensaje + '</div>');
                }
            },
            error: function(xhr, status, error) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                console.error('Error AJAX:', status, error, xhr.responseText);
                $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">Error en la solicitud: ' + error + '</div>');
            }
        });
    });


    $(document).on('submit', '#frm_bomberos_empresa_completa', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        alert('hola');
        $.ajax({
            type: 'POST',
            url: bomberosPublicoAjax.ajax_url,
            data: {
                action: 'BomberosPluginPublico',
                modulo: 'publico',
                funcionalidad: 'registrar_empresa_solicitud',
                form_data: formData,
                nonce: bomberosPublicoAjax.nonce
            },
            success: function(response) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                console.log('Respuesta recibida:', response);
                if (response.success) {
                    $('#empresa-contenido').html(response.data.html);
                    $('#empresa-contenido').prepend('<div class="bomberos-mensaje success">' + response.data.mensaje + '</div>');
                } else {
                    $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">' + response.data.mensaje + '</div>');
                }
            },
            error: function(xhr, status, error) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                console.error('Error AJAX:', status, error, xhr.responseText);
                $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">Error en la solicitud: ' + error + '</div>');
            }
        });
    });
});
jQuery(document).ready(function ($) {
    // Evento para el formulario de BÚSQUEDA de empresa
    $(document).on('submit', '#frm_buscar_empresa', function (e) {
        e.preventDefault();
        console.log('Formulario de búsqueda enviado');
        var formData = $(this).serialize();
        $('#empresa-contenido').append('<div class="bomberos-mensaje info">Buscando empresa...</div>');
        
        $.ajax({
            type: 'POST',
          
            url: bomberosPublicoAjaxInspeccion.ajax_url, 
            data: {
                action: 'BomberosPluginPublicoInspeccion', 
               
                modulo: 'publico', 
                funcionalidad: 'buscar_empresa',
                form_data: formData,
                nonce: bomberosPublicoAjaxInspeccion.nonce 
            },
            success: function (response) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                if (response.success) {
                    $('#empresa-contenido').html(response.data.html);
                   
                } else {
                   
                    $('#empresa-contenido').html(''); 
                    $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">' + (response.data.mensaje || 'Error desconocido.') + '</div>');
                }
            },
            error: function (xhr, status, error) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                console.error('Error AJAX (buscar_empresa):', status, error, xhr.responseText);
                $('#empresa-contenido').html(''); // Limpiar contenido en caso de error AJAX
                $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">Error en la solicitud: ' + error + '</div>');
            }
        });
    });

    // Evento para el formulario de REGISTRO de empresa y solicitud
    $(document).on('submit', '#frm_bomberos_empresa_completa', function (e) {
        e.preventDefault();
        console.log('Formulario de registro completo enviado');
        var formData = $(this).serialize();
        $('#empresa-contenido').append('<div class="bomberos-mensaje info">Registrando...</div>');

        $.ajax({
            type: 'POST',

            url: bomberosPublicoAjaxInspeccion.ajax_url,
            data: {
                action: 'BomberosPluginPublicoInspeccion', 
                modulo: 'publico',
                funcionalidad: 'registrar_empresa_solicitud',
                form_data: formData,
                nonce: bomberosPublicoAjaxInspeccion.nonce 
            },
            success: function (response) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                if (response.success) {
                 
                    $('#empresa-contenido').html(response.data.html);
                   
                } else {
                    $('#empresa-contenido').html(''); // Limpiar contenido si hay error
                    $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">' + (response.data.mensaje || 'Error desconocido.') + '</div>');
                }
            },
            error: function (xhr, status, error) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                console.error('Error AJAX (registrar_empresa_solicitud):', status, error, xhr.responseText);
                $('#empresa-contenido').html(''); 
                $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">Error en la solicitud: ' + error + '</div>');
            }
        });
    });
});
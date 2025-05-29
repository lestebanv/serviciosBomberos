// shortCodes-main.js

function manejarMensajeRespuestaAjaxPublico(response, fallbackMessage = '') {
    const $mensaje = jQuery('#bomberos-mensaje-publico');
    $mensaje.removeClass('notice-success notice-error').empty();
    let mensaje = fallbackMessage;
    let esError = true;
    if (response && typeof response === 'object') {
        mensaje = response.data?.mensaje || response.message || 'Operación completada.';
        esError = !response.success;
    }
    $mensaje.addClass(esError ? 'notice-error' : 'notice-success').html(mensaje);
    setTimeout(() => {
        $mensaje.html('|');
    }, 4000);
}

function enviarPeticionAjaxPublico(pshortcode, pplantilla, formData = '') {
    const $cuerpo = jQuery('#bomberos-shortcode-cuerpo');
    const $mensaje = jQuery('#bomberos-shortcode-mensaje');
    $mensaje.empty();

    jQuery.ajax({
        type: 'POST',
        url: bomberosPublicoAjax.ajax_url,
        data: {
            action: 'BomberosPluginPublico',
            modulo: 'publico',
            funcionalidad: pshortcode,
            plantilla: pplantilla,
            form_data: formData,
            nonce: bomberosPublicoAjax.nonce
        },
        success: function (response) {
            if (response.success && response.data?.html) {
                $cuerpo.html(response.data.html);
            } else if (!response.success) {
                $cuerpo.html('');
            }
            manejarMensajeRespuestaAjaxPublico(response);
        },
        error: function (xhr, status, error) {
            $cuerpo.html('');
            manejarMensajeRespuestaAjaxPublico(null, 'Error en la solicitud AJAX: ' + error);
        }
    });
}

// Si necesitas ejecutar otras cosas en ready, puedes hacer:
jQuery(document).ready(function ($) {
    // Código dependiente del DOM, si lo hay
});















jQuery(document).ready(function ($) {
    $(document).on('submit', '#frm_buscar_empresa', function (e) {
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
            success: function (response) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                if (response.success) {
                    $('#empresa-contenido').html(response.data.html);
                    $('#empresa-contenido').prepend('<div class="bomberos-mensaje success">' + response.data.mensaje + '</div>');
                } else {
                    $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">' + response.data.mensaje + '</div>');
                }
            },
            error: function (xhr, status, error) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                console.error('Error AJAX:', status, error, xhr.responseText);
                $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">Error en la solicitud: ' + error + '</div>');
            }
        });
    });


    $(document).on('submit', '#frm_bomberos_empresa_completa', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
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
            success: function (response) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                if (response.success) {
                    $('#empresa-contenido').html(response.data.html);
                    $('#empresa-contenido').prepend('<div class="bomberos-mensaje success">' + response.data.mensaje + '</div>');
                } else {
                    $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">' + response.data.mensaje + '</div>');
                }
            },
            error: function (xhr, status, error) {
                $('#empresa-contenido .bomberos-mensaje').remove();
                console.error('Error AJAX:', status, error, xhr.responseText);
                $('#empresa-contenido').prepend('<div class="bomberos-mensaje error">Error en la solicitud: ' + error + '</div>');
            }
        });
    });
});
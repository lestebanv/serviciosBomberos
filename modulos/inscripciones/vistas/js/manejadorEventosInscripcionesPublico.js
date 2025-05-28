// modulos/inscripciones/vistas/js/manejadorEventosInscripcionesPublico.js
jQuery(document).ready(function ($) {
    $('#form-inscripcion-curso').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $submitButton = $form.find('button[type="submit"]');
        const $mensajeDiv = $('#bomberos-inscripcion-mensaje');
        // formDataString incluirá 'funcionalidad_publica' si está como hidden input en el form
        const formDataString = $form.serialize();

        $submitButton.prop('disabled', true).text('Procesando...');
        $mensajeDiv.hide().removeClass('success error').empty();

        $.ajax({
            type: 'POST',
            url: bomberosInscripcionAjax.ajax_url, // Objeto AJAX localizado
            data: {
                action: 'bomberos_procesar_inscripcion_publica', // NUEVA ACCIÓN AJAX
                nonce_inscripcion: bomberosInscripcionAjax.nonce, // Nonce enviado con el nombre 'nonce_inscripcion'
                form_data: formDataString
            },
            success: function (response) { // wp_send_json_success envía {success: true, data: ...}
                if (response.success && response.data && response.data.html) {
                    $('#bomberos-inscripcion-curso-wrapper').html(response.data.html);
                } else {
                    let errorMessage = 'Error desconocido al procesar la inscripción.';
                    if (response.data && response.data.mensaje) {
                        errorMessage = response.data.mensaje;
                    } else if (response.data && typeof response.data === 'string') {
                        errorMessage = response.data;
                    }
                    $mensajeDiv.addClass('error').html(errorMessage).show();
                }
            },
            error: function (xhr, status, error) {
                let detailedError = 'Error en la solicitud AJAX: ' + error;
                if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.mensaje) {
                    detailedError = xhr.responseJSON.data.mensaje;
                } else if (xhr.responseJSON && xhr.responseJSON.data && typeof xhr.responseJSON.data === 'string') {
                    detailedError = xhr.responseJSON.data;
                } else if (xhr.responseText) {
                    try {
                        const parsedError = JSON.parse(xhr.responseText);
                        if (parsedError.data && parsedError.data.mensaje) { detailedError = parsedError.data.mensaje; }
                        else if (parsedError.data && typeof parsedError.data === 'string') { detailedError = parsedError.data; }
                    } catch (e) { /* no es json */ }
                }
                $mensajeDiv.addClass('error').html(detailedError).show();
            },
            complete: function () {
                if ($form.closest('#bomberos-inscripcion-curso-wrapper').length > 0) {
                    $submitButton.prop('disabled', false).text('Inscribirme');
                }
            }
        });
    });
});
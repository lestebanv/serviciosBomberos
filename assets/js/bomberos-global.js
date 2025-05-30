jQuery(document).ready(function ($) {
    // Namespace para funciones globales del plugin
    window.BomberosPlugin = window.BomberosPlugin || {};
    // Función para manejar mensajes de respuesta AJAX
    BomberosPlugin.manejarMensajeRespuestaAjax = function (response, fallbackMessage = '') {
        const $cuerpo = $('#bomberos-cuerpo');
        const $mensaje = $('#bomberos-mensaje');
        $mensaje.removeClass('notice-success notice-error').empty();

        let mensaje = fallbackMessage;
        let esError = true;

        if (response && typeof response === 'object') {
            mensaje = response.data?.mensaje || response.message || 'Operación completada.';
            esError = !response.success;
        }
        if(esError){
             $cuerpo.html("<div>Seleccione nuevamente del menu</div>");
        }
        $mensaje.addClass(esError ? 'notice-error' : 'notice-success').html(mensaje);

        // Vaciar el contenido del mensaje después de 4 segundos, pero mantener el div visible
        setTimeout(() => {
            $mensaje.html('|');
        }, 4000);
    };

    // Función para agregar indicador de carga
    BomberosPlugin.toggleLoading = function ($element, isLoading) {
        $element.toggleClass('loading', isLoading);
    };

    // Función para enviar peticiones AJAX estandarizadas
    BomberosPlugin.enviarPeticionAjax = function (modulo, funcionalidad, formData = '') {
        const $cuerpo = $('#bomberos-cuerpo');
        const $mensaje = $('#bomberos-mensaje');

        // Limpiar mensaje previo
        $mensaje.addClass('hidden').empty();

        // Enviar petición AJAX
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: modulo,
                funcionalidad: funcionalidad,
                form_data: formData,
                nonce: bomberosAjax.nonce
            },
            beforeSend: function () {
                BomberosPlugin.toggleLoading($cuerpo, true);
            },
            success: function (response) {
                BomberosPlugin.toggleLoading($cuerpo, false);
                if (response.success && response.data?.html) {
                    $cuerpo.html(response.data.html);
                } else if (!response.success) {
                    // $cuerpo.html('');
                }
                BomberosPlugin.manejarMensajeRespuestaAjax(response);
            },
            error: function (xhr, status, error) {
                BomberosPlugin.toggleLoading($cuerpo, false);
                $cuerpo.html('');
                BomberosPlugin.manejarMensajeRespuestaAjax(null, 'Error en la solicitud AJAX: ' + error);
            }
        });
    };
});
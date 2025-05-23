jQuery(document).ready(function ($) {
    // Función reutilizable para manejar mensajes
    function manejarMensajeRespuestaAjax(response) {
        const $mensajeDiv = $('#bomberos-mensaje');
        if (response.success) {
            $mensajeDiv
                .removeClass('notice-error')
                .addClass('notice notice-success')
                .html(response.data.mensaje)
                .show();
        } else {
            $mensajeDiv
                .removeClass('notice-success')
                .addClass('notice notice-error')
                .html(response.data.mensaje)
                .show();
        }
        // Ocultar el mensaje después de 3 segundos
        setTimeout(() => {
            $mensajeDiv.hide().empty();
        }, 4000);
    }

    // Manejar clics en los tabs
    $('.bomberos-tab').on('click', function () {
        // Remover clase active de todos los tabs
        $('.bomberos-tab').removeClass('active');
        // Agregar clase active al tab clicado
        $(this).addClass('active');

        // Obtener el módulo
        var modulosolicitado = $(this).data('modulo');

        // Realizar petición AJAX para cargar contenido inicial
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: modulosolicitado,
                funcionalidad: 'inicial',
                nonce: bomberosAjax.nonce
            },
            success: function (response) {
                if (response.success) {
                    $('#bomberos-cuerpo').html(response.data.html);
                    // No necesitamos manejar el mensaje aquí porque manejarRespuestaAjax lo hará
                } else {
                    $('#bomberos-cuerpo').html('');
                }
                // Usar la función reutilizable para manejar el mensaje
                manejarMensajeRespuestaAjax(response);
            },
            error: function (xhr, status, error) {
                $('#bomberos-cuerpo').html('');
                $('#bomberos-mensaje')
                    .removeClass('notice-success')
                    .addClass('notice notice-error')
                    .html('Error al cargar el contenido: ' + error)
                    .show()
                    .delay(3000)
                    .fadeOut();
            }
        });
    });

    // Cargar contenido inicial del primer tab
    $('.bomberos-tab.active').trigger('click');
});
jQuery(document).ready(function($) {
    // Manejar clics en los tabs
    $('.bomberos-tab').on('click', function() {
        // Remover clase active de todos los tabs
        $('.bomberos-tab').removeClass('active');
        // Agregar clase active al tab clicado
        $(this).addClass('active');

        // Obtener el módulo
        var modulo = $(this).data('modulo');

        // Realizar petición AJAX para cargar contenido inicial
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: modulo,
                funcionalidad: 'inicial',
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#bomberos-cuerpo').html(response.data.html);
                    $('#bomberos-mensaje').html('');
                } else {
                    $('#bomberos-mensaje').html('<div class="pqr-alert error">' + response.data.mensaje + '</div>');
                    $('#bomberos-cuerpo').html('');
                }
            },
            error: function() {
                $('#bomberos-mensaje').html('<div class="pqr-alert error">Error al cargar el contenido.</div>');
                $('#bomberos-cuerpo').html('');
            }
        });
    });

    // Cargar contenido inicial del primer tab
    $('.bomberos-tab.active').trigger('click');
});
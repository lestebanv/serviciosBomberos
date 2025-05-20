jQuery(document).ready(function($) {
    // Usar delegación de eventos para manejar dinámicamente formularios en #bomberos-cuerpo
    $(document).on('submit', '#bomberos-cuerpo .bomberos-form-empresa', function(e) {
        e.preventDefault(); // Prevenir el envío estándar del formulario

        // Obtener datos del formulario
        var formData = $(this).serializeArray();
        var modulo = 'empresas';
        var funcionalidad = 'registrar_empresa';

        // Mostrar mensaje de carga
        $('#bomberos-mensaje').html('<div> Procesando...</div>');

        // Realizar petición AJAX
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
            success: function(response) {
                if (response.success) {
                    // Actualizar contenido
                    $('#bomberos-cuerpo').html(response.data.html);
                    $('#bomberos-mensaje').html('<div class="pqr-alert success">' + response.data.mensaje + '</div>');
                } else {
                    // Mostrar mensaje de error
                    $('#bomberos-mensaje').html('<div class="pqr-alert error">' + response.data.mensaje + '</div>');
                }
            },
            error: function(xhr, status, error) {
                $('#bomberos-mensaje').html('<div class="pqr-alert error">Error en la solicitud: ' + error + '</div>');
            }
        });
    });

    // Ejemplo: Manejar clic en un botón para recargar la lista de empresas
    $(document).on('click', '#bomberos-cuerpo .bomberos-btn-listar', function(e) {
        e.preventDefault();

        var modulo = 'empresas';
        var funcionalidad = 'listar_empresas';

        // Mostrar mensaje de carga
        $('#bomberos-mensaje').html('<div class="pqr-alert info">Cargando lista...</div>');

        // Realizar petición AJAX
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: modulo,
                funcionalidad: funcionalidad,
                form_data: [],
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#bomberos-cuerpo').html(response.data.html);
                    $('#bomberos-mensaje').html('<div class="pqr-alert success">' + response.data.mensaje + '</div>');
                } else {
                    $('#bomberos-mensaje').html('<div class="pqr-alert error">' + response.data.mensaje + '</div>');
                }
            },
            error: function(xhr, status, error) {
                $('#bomberos-mensaje').html('<div class="pqr-alert error">Error en la solicitud: ' + error + '</div>');
            }
        });
    });
});
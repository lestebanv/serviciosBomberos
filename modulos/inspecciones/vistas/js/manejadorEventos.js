jQuery(document).ready(function ($) {

    function manejarMensajeRespuestaAjax(response, errorMessage) {
        const $mensajeDiv = $('#bomberos-mensaje');
        let mensaje = '';
        let isSuccess = true;

        // Si hay una respuesta estructurada (del servidor)
        if (response && typeof response === 'object') {
            isSuccess = response.success || false;
            mensaje = response.data && response.data.mensaje ? response.data.mensaje : 'Respuesta inválida del servidor';
        } else if (errorMessage) {
            // Caso de error genérico
            isSuccess = false;
            mensaje = errorMessage;
        } else {
            // Caso por defecto si no hay datos
            isSuccess = false;
            mensaje = 'Error desconocido';
        }
        if (isSuccess) {
            $mensajeDiv
                .removeClass('notice-error')
                .addClass('notice notice-success')
                .html(mensaje)
                .show();
        } else {
            $mensajeDiv
                .removeClass('notice-success')
                .addClass('notice notice-error')
                .html(mensaje)
                .show();
        }
        // Ocultar el mensaje después de 3 segundos
        setTimeout(() => {
            $mensajeDiv.hide().empty();
        }, 3000);
    }
    
    $(document).on('click', '.editar-inspeccion', function () {
        const id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'inspecciones',
                funcionalidad: 'editar_inspeccion',
                id: id,
                nonce: bomberosAjax.nonce
            },
            success: function (response) {
                if (response.success) {
                    $('#inspeccion-frm-editar').html(response.data.html).show();
                }
                manejarMensajeRespuestaAjax(response);
            }
        });
    });

    $(document).on('submit', '#form-editar-inspeccion', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        alert("enviando solicitud");
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'inspecciones',
                funcionalidad: 'actualizar_inspeccion',
                form_data: formData,
                nonce: bomberosAjax.nonce
            },
            success: function (response) {
                manejarMensajeRespuestaAjax(response);
                if (response.success) {
                    setTimeout(() => {
                        $('#inspeccion-frm-editar').hide().empty();
                        $.ajax({
                            type: 'POST',
                            url: bomberosAjax.ajax_url,
                            data: {
                                action: 'BomberosPlugin',
                                modulo: 'inspecciones',
                                funcionalidad: 'pagina_inicial',
                                nonce: bomberosAjax.nonce
                            },
                            success: function (response) {
                                if (response.success) {
                                    $('#cuerpo-listado-inspecciones').html(response.data.html);
                                };
                                manejarMensajeRespuestaAjax(response);
                            }
                        });
                    }, 2000);
                }
            },
            error: function (xhr, status, error) {
                manejarMensajeRespuestaAjax(null, 'Error al actualizar la inspección: ' + error);
            }
        });
    });

// Paginación del listado de empresas
    $(document).on('click', '.paginacion-ajax', function(e) {
        e.preventDefault();
        var pagina = $(this).data('paged');
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'inspecciones',
                funcionalidad: 'pagina_inicial',
                paged: pagina,
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#cuerpo-listado-inspecciones').html(response.data.html);
                }
                manejarMensajeRespuestaAjax(response);
           }
          
        });
    });


});
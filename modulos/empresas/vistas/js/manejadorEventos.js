jQuery(document).ready(function($) {
    // Función reutilizable para manejar mensajes
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

    // Registrar o actualizar empresa (usado también por form-crear-empresa)
    $(document).on('submit', '#empresa-formulario', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'empresas',
                funcionalidad: 'registrar_empresa',
                form_data: formData,
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#empresa-formulario').html('');
                    $('#cuerpo-listado-empresas').html(response.data.html);
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                manejarMensajeRespuestaAjax(null, 'Error al enviar los datos: ' + error);
            }
        });
    });

    // Eliminar empresa
    $(document).on('click', '.delete-empresa', function(e) {
        e.preventDefault();

        const id = $(this).data('id');

        if (!confirm('¿Estás seguro de eliminar esta empresa?')) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'empresas',
                funcionalidad: 'eliminar_empresa',
                id: id,
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#cuerpo-listado-empresas').html(response.data.html);
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                manejarMensajeRespuestaAjax(null, 'Error al eliminar la empresa: ' + error);
            }
        });
    });

    // Editar empresa
    $(document).on('click', '.editar-empresa', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'empresas',
                funcionalidad: 'editar_empresa',
                id: id,
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#empresa-frm-editar').html(response.data.html);
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                manejarMensajeRespuestaAjax(null, 'Error al cargar el formulario de edición: ' + error);
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
                modulo: 'empresas',
                funcionalidad: 'pagina_inicial',
                paged: pagina,
                nonce: bomberosAjax.nonce
            },
            beforeSend: function() {
                $('#cuerpo-listado-empresas').html('<p>Cargando...</p>');
            },
            success: function(response) {
                if (response.success) {
                    $('#cuerpo-listado-empresas').html(response.data.html);
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                $('#cuerpo-listado-empresas').html('<p>Error al cargar los datos.</p>');
                manejarMensajeRespuestaAjax(null, 'Error al cargar la página: ' + error);
            }
        });
    });

    // Enviar formulario de edición para actualizar los datos de la empresa
    $(document).on('submit', '#form-editar-empresa', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'empresas',
                funcionalidad: 'actualizar_empresa',
                form_data: formData,
                nonce: bomberosAjax.nonce
            },
            beforeSend: function() {
                $('#empresa-frm-editar').html('<p>Guardando datos...</p>');
            },
            success: function(response) {
                if (response.success) {
                    $('#empresa-frm-editar').html('');
                    $('#cuerpo-listado-empresas').html(response.data.html);
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                manejarMensajeRespuestaAjax(null, 'Error al actualizar la empresa: ' + error);
            }
        });
    });

    // Manejar el botón cancelar edición
    $(document).on('click', '.cancelar-edicion-empresa', function(e) {
        e.preventDefault();

        // Confirmar si se desea cancelar
        if (!confirm('¿Deseas cancelar la edición de la empresa?')) {
            return;
        }

        // Ocultar o limpiar el contenedor del formulario de edición
        $('#empresa-frm-editar').html('');

        // Mostrar un mensaje usando la función centralizada
        manejarMensajeRespuestaAjax({ success: true, data: { mensaje: 'Edición cancelada.' } });
    });

    // Mostrar formulario de creación de empresa
    $(document).on('click', '#btn-agregar-empresa', function(e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'empresas',
                funcionalidad: 'form_crear',
                nonce: bomberosAjax.nonce
            },
            beforeSend: function() {
                $('#empresa-frm-editar').html('<p>Cargando formulario...</p>');
            },
            success: function(response) {
                if (response.success) {
                    $('#empresa-frm-editar').html(response.data.html);
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                $('#empresa-frm-editar').html('<p>Error al cargar el formulario.</p>');
                manejarMensajeRespuestaAjax(null, 'Error al cargar el formulario: ' + error);
            }
        });
    });

    // Enviar formulario de creación de empresas
    $(document).on('submit', '#form-crear-empresa', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'empresas',
                funcionalidad: 'registrar_empresa',
                form_data: formData,
                nonce: bomberosAjax.nonce
            },
            beforeSend: function() {
                $('#empresa-frm-editar').html('<p>Guardando datos...</p>');
            },
            success: function(response) {
                if (response.success) {
                    $('#empresa-frm-editar').html('');
                    $('#cuerpo-listado-empresas').html(response.data.html);
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                manejarMensajeRespuestaAjax(null, 'Error al crear la empresa: ' + error);
            }
        });
    });
});
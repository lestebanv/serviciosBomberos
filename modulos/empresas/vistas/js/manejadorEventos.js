jQuery(document).ready(function ($) {

    function mostrarMensaje(mensaje, tipo) {
        var $msg = $('#mensaje-empresa');
        $msg.removeClass();
        $msg.html(mensaje);
        if (tipo === 'success') {
            $msg.css({ backgroundColor: '#d4edda', color: '#155724', border: '1px solid #c3e6cb' });
        } else {
            $msg.css({ backgroundColor: '#f8d7da', color: '#721c24', border: '1px solid #f5c6cb' });
        }
        $msg.fadeIn().delay(2000).fadeOut();
    }

    // Registrar o actualizar empresa
    $(document).on('submit', '#empresa-formulario', function (e) {
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
            success: function (response) {
                if (response.success) {
                    $('#empresa-formulario').html('');
                    $('#cuerpo-listado-empresas').html(response.data.html);
                    mostrarMensaje(response.data.mensaje, 'success');
                } else {
                    mostrarMensaje(response.data.mensaje, 'error');
                }
            },
            error: function () {
                mostrarMensaje('Error al enviar los datos.', 'error');
            }
        });
    });

    // Eliminar empresa
    $(document).on('click', '.delete-empresa', function (e) {
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
            beforeSend: function () {
                $('#mensaje-empresa').html('<div class="pqr-alert info">Eliminando empresa...</div>');
            },
            success: function (response) {
                if (response.success) {
                    $('#cuerpo-listado-empresas').html(response.data.html);
                    $('#mensaje-empresa').html('<div class="pqr-alert success">' + response.data.mensaje + '</div>');
                } else {
                    $('#mensaje-empresa').html('<div class="pqr-alert error">' + response.data.mensaje + '</div>');
                }
            },
            error: function (xhr) {
                $('#mensaje-empresa').html('<div class="pqr-alert error">Error en la petición AJAX.</div>');
                console.error(xhr.responseText);
            }
        });
    });
    // Editar empresa
    $(document).on('click', '.editar-empresa', function (e) {
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
            success: function (response) {
                if (response.success) {
                    $('#empresa-frm-editar').html(response.data.html);
                } else {
                    mostrarMensaje(response.data.mensaje, 'error');
                }
            }
        });
    });

    // Paginación del listado de empresas
    $(document).on('click', '.paginacion-ajax', function (e) {
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
            beforeSend: function () {
                $('#cuerpo-listado-empresas').html('<p>Cargando...</p>');
            },
            success: function (response) {
                $('#cuerpo-listado-empresas').html(response.data.html);
            },
            error: function () {
                $('#cuerpo-listado-empresas').html('<p>Error al cargar los datos.</p>');
            }
        });
    });

    // Enviar formulario de edición para actualizar los datos de la empresa
    $(document).on('submit', '#form-editar-empresa', function (e) {
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
            beforeSend: function () {
                $('#empresa-frm-editar').html('<p>Guardando datos...</p>');
            },
            success: function (response) {
                if (response.success) {
                    $('#empresa-frm-editar').html('');
                    $('#cuerpo-listado-empresas').html(response.data.html);
                    mostrarMensaje(response.data.mensaje, 'success');
                } else {
                    mostrarMensaje(response.data.mensaje || 'Error al guardar la empresa.', 'error');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                mostrarMensaje('Error de conexión al servidor.', 'error');
            }
        });
    });

    // manejar el boton cancelar edicion
    $(document).on('click', '.cancelar-edicion-empresa', function (e) {
        e.preventDefault();

        // Confirmar si se desea cancelar
        if (!confirm('¿Deseas cancelar la edición de la empresa?')) {
            return;
        }

        // Ocultar o limpiar el contenedor del formulario de edición
        $('#empresa-frm-editar').html('');

        // Mostrar un mensaje opcional
        $('#mensaje-empresa').html('<div class="pqr-alert info">Edición cancelada.</div>').fadeIn().delay(2000).fadeOut();
    });



    // Mostrar formulario de creación de empresa
    $(document).on('click', '#btn-agregar-empresa', function (e) {
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
            beforeSend: function () {
                $('#empresa-frm-editar').html('<p>Cargando formulario...</p>');
            },
            success: function (response) {
                if (response.success) {
                    $('#empresa-frm-editar').html(response.data.html);
                } else {
                    $('#empresa-frm-editar').html('<div class="notice notice-error"><p>' + response.data.mensaje + '</p></div>');
                }
            },
            error: function (xhr) {
                $('#empresa-frm-editar').html('<div class="notice notice-error"><p>Error al cargar el formulario.</p></div>');
            }
        });
    });


    // Enviar formulario de creacion de empresas
    $(document).on('submit', '#form-crear-empresa', function (e) {
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
            beforeSend: function () {
                $('#empresa-frm-editar').html('<p>Guardando datos...</p>');
            },
            success: function (response) {
                if (response.success) {
                    $('#empresa-frm-editar').html('');
                    $('#cuerpo-listado-empresas').html(response.data.html);
                    mostrarMensaje(response.data.mensaje, 'success');
                } else {
                    mostrarMensaje(response.data.mensaje || 'Error al guardar la empresa.', 'error');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                mostrarMensaje('Error de conexión al servidor.', 'error');
            }
        });
    });

});

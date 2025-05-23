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

    $(document).on('click', '#btn-agregar-curso', function() {
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'cursos',
                funcionalidad: 'form_crear',
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#curso-frm-editar').html(response.data.html).show();
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                manejarMensajeRespuestaAjax(null, 'Error al cargar el formulario de creación: ' + error);
            }
        });
    });

    $(document).on('click', '.editar-curso', function() {
        const id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'cursos',
                funcionalidad: 'editar_curso',
                id: id,
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#curso-frm-editar').html(response.data.html).show();
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                manejarMensajeRespuestaAjax(null, 'Error al cargar el formulario de edición: ' + error);
            }
        });
    });

    $(document).on('click', '.delete-curso', function() {
        if (confirm('¿Estás seguro de eliminar este curso?')) {
            const id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: bomberosAjax.ajax_url,
                data: {
                    action: 'BomberosPlugin',
                    modulo: 'cursos',
                    funcionalidad: 'eliminar_curso',
                    id: id,
                    nonce: bomberosAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#cuerpo-listado-cursos').html(response.data.html);
                    }
                    manejarMensajeRespuestaAjax(response);
                },
                error: function(xhr, status, error) {
                    manejarMensajeRespuestaAjax(null, 'Error al eliminar el curso: ' + error);
                }
            });
        }
    });

    $(document).on('click', '.paginacion-ajax', function(e) {
        e.preventDefault();
        const paged = $(this).data('paged');
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'cursos',
                funcionalidad: 'pagina_inicial',
                paged: paged,
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#cuerpo-listado-cursos').html(response.data.html);
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                manejarMensajeRespuestaAjax(null, 'Error al cargar la página: ' + error);
            }
        });
    });

    // Eventos del formulario de editar curso
    $(document).on('submit', '#form-editar-curso', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'cursos',
                funcionalidad: 'actualizar_curso',
                form_data: formData,
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                manejarMensajeRespuestaAjax(response);
                if (response.success) {
                    setTimeout(() => {
                        $('#curso-frm-editar').hide().empty();
                        $.ajax({
                            type: 'POST',
                            url: bomberosAjax.ajax_url,
                            data: {
                                action: 'BomberosPlugin',
                                modulo: 'cursos',
                                funcionalidad: 'pagina_inicial',
                                nonce: bomberosAjax.nonce
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('#cuerpo-listado-cursos').html(response.data.html);
                                }
                                manejarMensajeRespuestaAjax(response);
                            },
                            error: function(xhr, status, error) {
                                manejarMensajeRespuestaAjax(null, 'Error al recargar la lista: ' + error);
                            }
                        });
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                manejarMensajeRespuestaAjax(null, 'Error al actualizar el curso: ' + error);
            }
        });
    });

    $(document).on('click', '.cancelar-edicion-curso', function() {
        $('#bomberos-mensaje').hide().empty();
        $('#curso-frm-editar').hide().empty();
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'cursos',
                funcionalidad: 'pagina_inicial',
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#cuerpo-listado-cursos').html(response.data.html);
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                manejarMensajeRespuestaAjax(null, 'Error al recargar la lista: ' + error);
            }
        });
    });

    // Eventos del formulario de crear curso
    $(document).on('submit', '#form-crear-curso', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'cursos',
                funcionalidad: 'registrar_curso',
                form_data: formData,
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                manejarMensajeRespuestaAjax(response);
                if (response.success) {
                    $('#form-crear-curso')[0].reset();
                    setTimeout(() => {
                        $('#curso-frm-editar').hide().empty();
                        $.ajax({
                            type: 'POST',
                            url: bomberosAjax.ajax_url,
                            data: {
                                action: 'BomberosPlugin',
                                modulo: 'cursos',
                                funcionalidad: 'pagina_inicial',
                                nonce: bomberosAjax.nonce
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('#cuerpo-listado-cursos').html(response.data.html);
                                }
                                manejarMensajeRespuestaAjax(response);
                            },
                            error: function(xhr, status, error) {
                                manejarMensajeRespuestaAjax(null, 'Error al recargar la lista: ' + error);
                            }
                        });
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                manejarMensajeRespuestaAjax(null, 'Error al crear el curso: ' + error);
            }
        });
    });

    $(document).on('click', '.cancelar-creacion-curso', function() {
        $('#bomberos-mensaje').hide().empty();
        $('#curso-frm-editar').hide().empty();
        $.ajax({
            type: 'POST',
            url: bomberosAjax.ajax_url,
            data: {
                action: 'BomberosPlugin',
                modulo: 'cursos',
                funcionalidad: 'pagina_inicial',
                nonce: bomberosAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#cuerpo-listado-cursos').html(response.data.html);
                }
                manejarMensajeRespuestaAjax(response);
            },
            error: function(xhr, status, error) {
                manejarMensajeRespuestaAjax(null, 'Error al recargar la lista: ' + error);
            }
        });
    });
});
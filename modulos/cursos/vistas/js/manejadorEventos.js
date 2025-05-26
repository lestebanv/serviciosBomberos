jQuery(document).ready(function($) {
    // Función reutilizable para manejar mensajes
    
    // Boton de crear curso
    $(document).on('click', '#btn-agregar-curso', function() {
        BomberosPlugin.enviarPeticionAjax('cursos', 'form_crear');
    });

   // evento boton editar curso que envia el formulario con los datos de curso para editar
    $(document).on('click', '.editar-curso', function() {
        const id = $(this).data('id');
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData= formData +'&paged=' + encodeURIComponent($(this).data('paged'));
        BomberosPlugin.enviarPeticionAjax('cursos', 'form_crear',formData);

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
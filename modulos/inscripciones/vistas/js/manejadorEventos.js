// modulos/inscripciones/vistas/js/manejadorEventos.js
jQuery(document).ready(function($) {
    // Paginación para el listado de inscripciones en el admin
    $(document).on('click', '.paginacion-inscripciones_admin', function(e) {
        e.preventDefault();
        const paged = $(this).data('paged');
        var formData = 'paged=' + encodeURIComponent(paged);
        BomberosPlugin.enviarPeticionAjax('inscripciones', 'pagina_inicial', formData);
    });

    // Manejador para el botón Eliminar Inscripción
    $(document).on('click', '.delete-inscripcion-admin', function(e) {
        e.preventDefault();
        if (!confirm('¿Estás seguro de eliminar esta inscripción? Esta acción no se puede deshacer.')) {
            return;
        }
        const inscripcionId = $(this).data('id');
        const currentPage = $(this).data('paged');
        var formData = 'id=' + encodeURIComponent(inscripcionId) + '&paged=' + encodeURIComponent(currentPage);
        BomberosPlugin.enviarPeticionAjax('inscripciones', 'eliminar_inscripcion_admin', formData);
    });

    // Manejador para el botón Editar Inscripción (carga el formulario)
    $(document).on('click', '.editar-inscripcion-admin', function(e) {
        e.preventDefault();
        const inscripcionId = $(this).data('id');
        const currentPage = $(this).data('paged');
        var formData = 'id=' + encodeURIComponent(inscripcionId) + '&paged=' + encodeURIComponent(currentPage);
        BomberosPlugin.enviarPeticionAjax('inscripciones', 'form_editar_inscripcion_admin', formData);
    });

    // NUEVO: Manejador para el envío del formulario de edición de inscripción
    $(document).on('submit', '#form-editar-inscripcion-admin', function(e) {
        e.preventDefault();
        // El formulario ya contiene id_inscripcion y paged como campos ocultos,
        // así como los demás campos editables.
        const formData = $(this).serialize(); 
        BomberosPlugin.enviarPeticionAjax('inscripciones', 'actualizar_inscripcion_admin', formData);
    });

    // NUEVO: Manejador para el botón "Cancelar" en el formulario de edición de inscripción
    $(document).on('click', '.cancelar-edicion-inscripcion-admin', function(e) {
        e.preventDefault();
        const paged = $(this).data('paged'); // Obtener 'paged' del botón
        const formData = 'paged=' + encodeURIComponent(paged);
        BomberosPlugin.enviarPeticionAjax('inscripciones', 'pagina_inicial', formData); // Vuelve a la lista
    });
});

jQuery(document).ready(function($) {
    // Paginación para el listado de inscripciones en el admin
    $(document).on('click', '.paginacion-inscripciones', function(e) {
        e.preventDefault();
        var formData = 'actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('inscripciones', 'pagina_inicial', formData);
    });

    // Manejador para el botón Eliminar Inscripción
    $(document).on('click', '.delete-inscripcion', function(e) {
        e.preventDefault();
        if (!confirm('¿Estás seguro de eliminar esta inscripción? Esta acción no se puede deshacer.')) {
            return;
        }
        var formData = 'id=' + encodeURIComponent($(this).data('id')) ;
        formData= formData + '&actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('inscripciones', 'eliminar_inscripcion', formData);
    });

    // Manejador para el botón Editar Inscripción (carga el formulario) button editar-inscripcion
    $(document).on('click', '.editar-inscripcion', function(e) {
        e.preventDefault();
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData= formData + '&actualpagina=' + encodeURIComponent( $(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('inscripciones', 'form_editar_inscripcion', formData);
    });

    // NUEVO: Manejador para el envío del formulario de edición de inscripción
    $(document).on('submit', '#form-editar-inscripcion', function(e) {
        e.preventDefault();
        // El formulario ya contiene id_inscripcion y paged como campos ocultos,
        // así como los demás campos editables.
        const formData = $(this).serialize(); 
        BomberosPlugin.enviarPeticionAjax('inscripciones', 'actualizar_inscripcion', formData);
    });

    // NUEVO: Manejador para el botón "Cancelar" en el formulario de edición de inscripción
    $(document).on('click', '.cancelar-edicion-inscripcion', function(e) {
        e.preventDefault();
        const formData = 'actualpagina=' + encodeURIComponent( $(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('inscripciones', 'pagina_inicial', formData); // Vuelve a la lista
    });
});
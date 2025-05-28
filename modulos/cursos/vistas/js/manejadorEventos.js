jQuery(document).ready(function($) {
    /*
       Plantilla de código para construir formData manualmente:
       var formData = 'id=' + encodeURIComponent($(this).data('id'));
       formData += '&paged=' + encodeURIComponent($(this).data('paged'));
       BomberosPlugin.enviarPeticionAjax('cursos', 'funcionalidad', formData);
    */
    
    // Solicitar formulario de creación de cursos
    $(document).on('click', '#btn-agregar-curso', function() {
        // No se necesita paged aquí, ya que es un nuevo ítem.
        // El controlador de formularioCreacion no espera 'paged'.
        BomberosPlugin.enviarPeticionAjax('cursos', 'form_crear', ''); 
    });

   // Solicitar formulario de edición de cursos
    $(document).on('click', '.editar-curso', function() {
        const id = $(this).data('id');
        const paged = $(this).data('paged');
        var formData = 'id=' + encodeURIComponent(id) + '&paged=' + encodeURIComponent(paged);
        BomberosPlugin.enviarPeticionAjax('cursos', 'editar_curso', formData);
    });

    // Eliminar un curso por id
    $(document).on('click', '.delete-curso', function() {
        if (!confirm('¿Estás seguro de eliminar este curso?')) {
            return;
        }
        const id = $(this).data('id');
        const paged = $(this).data('paged'); // Para recargar la misma página de la lista
        var formData = 'id=' + encodeURIComponent(id) + '&paged=' + encodeURIComponent(paged);
        BomberosPlugin.enviarPeticionAjax('cursos', 'eliminar_curso', formData);
    });

    // Paginación de cursos
    $(document).on('click', '.paginacion-cursos', function(e) {
        e.preventDefault();
        const paged = $(this).data('paged');
        var formData = 'paged=' + encodeURIComponent(paged);
        BomberosPlugin.enviarPeticionAjax('cursos', 'pagina_inicial', formData);
    });

    // Envío del formulario de actualización
    $(document).on('submit', '#form-editar-curso', function(e) {
        e.preventDefault();
        // El formData serializado ya incluye el campo oculto 'paged'
        const formData = $(this).serialize(); 
        BomberosPlugin.enviarPeticionAjax('cursos', 'actualizar_curso', formData);
    });

    // Cancelar edición de curso
    $(document).on('click', '.cancelar-edicion-curso', function(e) { // 'e' estaba faltando
        e.preventDefault();
        const paged = $(this).data('paged'); // Obtener 'paged' del botón
        const formData = 'paged=' + encodeURIComponent(paged);
        BomberosPlugin.enviarPeticionAjax('cursos', 'pagina_inicial', formData);
    });

    // Enviar formulario para crear nuevo curso
    $(document).on('submit', '#form-crear-curso', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        // Después de crear, el controlador usualmente redirige a la página 1 de la lista.
        // Si quisiéramos pasar un 'paged' específico, tendríamos que añadirlo a formData aquí.
        BomberosPlugin.enviarPeticionAjax('cursos', 'registrar_curso', formData);
    });

   // Cancelar creación de cursos
    $(document).on('click', '.cancelar-creacion-curso', function(e) { // 'e' estaba faltando
        e.preventDefault();
        // Al cancelar la creación, volvemos a la página inicial (página 1 por defecto)
        BomberosPlugin.enviarPeticionAjax('cursos', 'pagina_inicial', ''); 
    });
});
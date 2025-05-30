jQuery(document).ready(function($) {
    /*
       plantilla de codigo
       var formData = 'id=' + encodeURIComponent($(this).data('id'));
       formData= formData +'&paged=' + encodeURIComponent($(this).data('paged'));
       BomberosPlugin.enviarPeticionAjax('cursos', 'funcionalidad', formData);


    */
    
    // solicitar formulario de creacion de cursos
    $(document).on('click', '#btn-agregar-curso', function() {
        BomberosPlugin.enviarPeticionAjax('cursos', 'form_crear');
    });

   // solicitar formulario de edicion de cursos
    $(document).on('click', '.editar-curso', function() {
        const id = $(this).data('id');
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData= formData +'&paged=' + encodeURIComponent($(this).data('paged'));
        BomberosPlugin.enviarPeticionAjax('cursos', 'editar_curso',formData);

    });
    // eliminar un curso por id
    $(document).on('click', '.delete-curso', function() {
        if (!confirm('¿Estás seguro de eliminar este curso?')) {
            return;
        }
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData= formData +'&paged=' + encodeURIComponent($(this).data('paged'));
        BomberosPlugin.enviarPeticionAjax('cursos', 'eliminar_curso', formData);
    });

    // paginacion de cursos
    $(document).on('click', '.paginacion-cursos', function(e) {
        e.preventDefault();
        var formData= 'paged=' + encodeURIComponent($(this).data('paged'));
        BomberosPlugin.enviarPeticionAjax('cursos', 'pagina_inicial', formData);
    });

    // Envio del formulario de actualizacion
    $(document).on('submit', '#form-editar-curso', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        BomberosPlugin.enviarPeticionAjax('cursos', 'actualizar_curso', formData);
    });

    $(document).on('click', '.cancelar-edicion-curso', function() {
        e.preventDefault();
        BomberosPlugin.enviarPeticionAjax('cursos', 'pagina_inicial', formData);
    });

    // Enviar formulario para crear nuevo curso
    $(document).on('submit', '#form-crear-curso', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        BomberosPlugin.enviarPeticionAjax('cursos', 'registrar_curso', formData);
    });
   //cancelar creacion de cursos
    $(document).on('click', '.cancelar-creacion-curso', function() {
        e.preventDefault();
        BomberosPlugin.enviarPeticionAjax('cursos', 'pagina_inicial', formData);
    });
});
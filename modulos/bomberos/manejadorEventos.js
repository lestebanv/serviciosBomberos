jQuery(document).ready(function ($) {
    // Registrar o actualizar bombero (usado también por form-crear-bombero)
    $(document).on('submit', '#bombero-formulario', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        BomberosPlugin.enviarPeticionAjax('bomberos', 'registrar_bombero', formData);
    });

    // Eliminar bombero
    $(document).on('click', '.delete-bombero', function (e) {
        e.preventDefault();
        if (!confirm('¿Estás seguro de eliminar esta bombero?')) {
            return;
        }
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData = formData + '&actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('bomberos', 'eliminar_bombero', formData);
    });

    // Editar bombero
    $(document).on('click', '.editar-bombero', function (e) {
        e.preventDefault();
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData= formData +'&actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('bomberos', 'editar_bombero', formData);
    });

    // Paginación del listado de bomberos
    $(document).on('click', '.paginacion-bomberos', function (e) {
        e.preventDefault();
        const formData = 'actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('bomberos', 'pagina_inicial', formData);
    });
    
     // boton de cancelar edicion
    $(document).on('click', '.cancelar-edicion-bombero', function (e) {
        e.preventDefault();
        const formData = 'actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('bomberos', 'pagina_inicial', formData);
    });
        // boton de cancelar edicion
    $(document).on('click', '.cancelar-creacion-bombero', function (e) {
        e.preventDefault();
        const formData = 'actualpagina=' + encodeURIComponent(1);
        BomberosPlugin.enviarPeticionAjax('bomberos', 'pagina_inicial', formData);
    });

    // Enviar formulario de edición para actualizar los datos de la bombero
    $(document).on('submit', '#form-editar-bombero', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        BomberosPlugin.enviarPeticionAjax('bomberos', 'actualizar_bombero', formData);
    });

    // Mostrar formulario de creación de bombero
    $(document).on('click', '#btn-agregar-bombero', function (e) {
        e.preventDefault();
        BomberosPlugin.enviarPeticionAjax('bomberos', 'form_crear', '');
    });

    // Enviar formulario de creación de bomberos
    $(document).on('submit', '#form-crear-bombero', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        BomberosPlugin.enviarPeticionAjax('bomberos', 'registrar_bombero', formData);
    });
});
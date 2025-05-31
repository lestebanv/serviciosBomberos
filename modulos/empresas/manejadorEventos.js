jQuery(document).ready(function ($) {
    // Registrar o actualizar empresa (usado también por form-crear-empresa)
    $(document).on('submit', '#empresa-formulario', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        BomberosPlugin.enviarPeticionAjax('empresas', 'registrar_empresa', formData);
    });

    // Eliminar empresa
    $(document).on('click', '.delete-empresa', function (e) {
        e.preventDefault();
        if (!confirm('¿Estás seguro de eliminar esta empresa?')) {
            return;
        }
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData = formData + '&paged=' + encodeURIComponent($(this).data('paged'));
        BomberosPlugin.enviarPeticionAjax('empresas', 'eliminar_empresa', formData);
    });

    // Editar empresa
    $(document).on('click', '.editar-empresa', function (e) {
        e.preventDefault();
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData= formData +'&paged=' + encodeURIComponent($(this).data('paged'));
        BomberosPlugin.enviarPeticionAjax('empresas', 'editar_empresa', formData);
    });

    // Paginación del listado de empresas
    $(document).on('click', '.paginacion-empresas', function (e) {
        e.preventDefault();
        const pagina = $(this).data('paged');
        const formData = 'paged=' + encodeURIComponent(pagina);
        BomberosPlugin.enviarPeticionAjax('empresas', 'pagina_inicial', formData);
    });
    
     // boton de cancelar edicion
    $(document).on('click', '.cancelar-edicion-empresa', function (e) {
        e.preventDefault();
        const pagina = $(this).data('paged');
        const formData = 'paged=' + encodeURIComponent(pagina);
        BomberosPlugin.enviarPeticionAjax('empresas', 'pagina_inicial', formData);
    });

    // Enviar formulario de edición para actualizar los datos de la empresa
    $(document).on('submit', '#form-editar-empresa', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        alert(formData);
        BomberosPlugin.enviarPeticionAjax('empresas', 'actualizar_empresa', formData);
    });

    // Mostrar formulario de creación de empresa
    $(document).on('click', '#btn-agregar-empresa', function (e) {
        e.preventDefault();
        BomberosPlugin.enviarPeticionAjax('empresas', 'form_crear', '');
    });

    // Enviar formulario de creación de empresas
    $(document).on('submit', '#form-crear-empresa', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        BomberosPlugin.enviarPeticionAjax('empresas', 'registrar_empresa', formData);
    });
});
jQuery(document).ready(function ($) {
    
    // Eliminar pqr
    $(document).on('click', '.delete-pqr', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (!confirm('¿Estás seguro de eliminar esta Solicitud PQR?')) {
            return;
        }
        var formData = 'id=' + encodeURIComponent(id);
        formData = formData+ '&actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('pqr', 'eliminar_pqr', formData);
    });

    // Editar pqr
    $(document).on('click', '.editar-pqr', function (e) {
        e.preventDefault();
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData= formData +'&actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('pqr', 'editar_pqr', formData);
    });

    // Paginación del listado de pqr
    $(document).on('click', '.paginacion-pqr', function (e) {
        e.preventDefault();
        const formData = 'actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('pqr', 'pagina_inicial', formData);
    });
    
     // boton de cancelar edicion
    $(document).on('click', '.cancelar-edicion-pqr', function (e) {
        e.preventDefault();;
        const formData = 'actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('pqr', 'pagina_inicial', formData);
    });

    // Enviar formulario de edición para actualizar los datos de la empresa
    $(document).on('submit', '#form-editar-pqr', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        BomberosPlugin.enviarPeticionAjax('pqr', 'actualizar_pqr', formData);
    });

    
});
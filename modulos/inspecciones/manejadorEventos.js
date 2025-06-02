jQuery(document).ready(function ($) {
 /*
       var formData = 'id=' + encodeURIComponent($(this).data('id'));
       formData= formData +'&paged=' + encodeURIComponent($(this).data('paged'));
       BomberosPlugin.enviarPeticionAjax('inspecciones', 'funcionalidad', formData);
 */   
    $(document).on('click', '.editar-inspeccion', function () {
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData= formData +'&paged=' + encodeURIComponent($(this).data('paged'));
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'editar_inspeccion', formData);
    });

    $(document).on('click', '.eliminar-inspeccion', function () {
        if (!confirm('¿Estás seguro de eliminar esta solicitud de inspección?')) {
            return;
        }
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData= formData +'&paged=' + encodeURIComponent($(this).data('paged'));
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'eliminar_inspeccion', formData);
    });

    $(document).on('submit', '#form-editar-inspeccion', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'actualizar_inspeccion', formData);
    });

    // Paginación del listado de inspecciones
    $(document).on('click', '.paginacion-inspecciones', function(e) {
        e.preventDefault();
        var formData= 'paged=' + encodeURIComponent($(this).data('paged'));
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'pagina_inicial', formData);  
    });

    // Boton cancelar edicion
    $(document).on('click', '.cancelar-edicion-inspeccion', function (e) {
        e.preventDefault();
        const formData = 'paged=' + encodeURIComponent($(this).data('paged'));
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'pagina_inicial', formData);
    });

    // Boton para mostrar reporte de próximas a vencer
    $(document).on('click', '#btn-reporte-inspecciones-vencer', function (e) {
        e.preventDefault();
        const pagedListado = $(this).data('paged-listado'); // Para saber a qué página volver
        const formData = 'paged_listado=' + encodeURIComponent(pagedListado);
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'reporte_proximas_vencer', formData);
    });
    
    // Boton para volver al listado desde el reporte
    $(document).on('click', '#btn-volver-listado-inspecciones', function(e) {
        e.preventDefault();
        var formData = 'paged=' + encodeURIComponent($(this).data('paged'));
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'pagina_inicial', formData);
    });
});
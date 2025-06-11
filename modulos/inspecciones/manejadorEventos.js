jQuery(document).ready(function ($) {
 /*
       var formData = 'id=' + encodeURIComponent($(this).data('id'));
       formData= formData +'&actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
       BomberosPlugin.enviarPeticionAjax('inspecciones', 'funcionalidad', formData);
 */   
    $(document).on('click', '.editar-inspeccion', function () {
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData= formData +'&actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'editar_inspeccion', formData);
    });
    $(document).on('click', '.eliminar-inspeccion', function () {
        if (!confirm('¿Estás seguro de eliminar esta solicitud de inspección?')) {
            return;
        }
        var formData = 'id=' + encodeURIComponent($(this).data('id'));
        formData= formData +'&actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'eliminar_inspeccion', formData);
    });

    $(document).on('submit', '#form-editar-inspeccion', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'actualizar_inspeccion', formData);
    });

    // Paginación del listado de empresas
    $(document).on('click', '.paginacion-inspecciones', function(e) {
        e.preventDefault();
        var formData= 'actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'pagina_inicial', formData);  
    });

      // boton cancelar edicion
    $(document).on('click', '.cancelar-edicion-inspeccion', function (e) {
        e.preventDefault();
        const formData = 'actualpagina=' + encodeURIComponent($(this).data('actualpagina'));
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'pagina_inicial', formData);
    });

    // NUEVO: Actualizar el campo de teléfono cuando se cambia el bombero seleccionado
    $(document).on('change', '#id_bombero_asignado', function() {
        // Encontrar la opción seleccionada
        const selectedOption = $(this).find('option:selected');
        // Obtener el teléfono del atributo data-telefono
        const telefono = selectedOption.data('telefono');
        // Actualizar el valor del campo de teléfono
        $('#telefono_encargado').val(telefono || ''); // Si no hay teléfono, se pone vacío
    });
});
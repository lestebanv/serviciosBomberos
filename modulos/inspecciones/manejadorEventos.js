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

   // Variable para almacenar el botón que fue presionado
    var clickedButton = null;

    // 1. Cuando se hace clic en CUALQUIER botón de envío dentro del formulario,
    // guardamos una referencia a ese botón.
    $(document).on('click', '#form-editar-inspeccion button[type="submit"]', function() {
        clickedButton = $(this);
    });

    // 2. El manejador de envío del formulario se mantiene, pero con una lógica adicional.
    $(document).on('submit', '#form-editar-inspeccion', function (e) {
        e.preventDefault();
        
        // Serializamos los campos del formulario como siempre.
        let formData = $(this).serialize();

        // 3. AHORA, si se hizo clic en un botón, añadimos su 'name' y 'value'
        // a la cadena de datos que enviaremos.
        if (clickedButton) {
            formData += '&' + encodeURIComponent(clickedButton.attr('name')) + '=' + encodeURIComponent(clickedButton.attr('value'));
        }

        // Enviamos la petición con la información completa.
        BomberosPlugin.enviarPeticionAjax('inspecciones', 'actualizar_inspeccion', formData);

        // Reseteamos la variable para el próximo envío.
        clickedButton = null;
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

      

});
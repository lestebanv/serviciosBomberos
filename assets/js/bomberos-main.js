jQuery(document).ready(function ($) {
    // Manejar clics en los tabs
    $('.bomberos-tab').on('click', function () {
        // Remover clase active de todos los tabs
        $('.bomberos-tab').removeClass('active');
        // Agregar clase active al tab clicado
        $(this).addClass('active');

        // Obtener el módulo
        var modulosolicitado = $(this).data('modulo');

        // Enviar petición AJAX usando la función global
        BomberosPlugin.enviarPeticionAjax(modulosolicitado, 'inicial');
    });

    // Cargar contenido inicial del primer tab
    $('.bomberos-tab.active').trigger('click');
});
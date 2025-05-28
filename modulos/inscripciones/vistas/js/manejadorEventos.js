// modulos/inscripciones/vistas/js/manejadorEventos.js
jQuery(document).ready(function($) {
    // Paginación para el listado de inscripciones en el admin
    $(document).on('click', '.paginacion-inscripciones_admin', function(e) {
        e.preventDefault();
        const paged = $(this).data('paged');
        var formData = 'paged=' + encodeURIComponent(paged);
        // La acción AJAX va al manejador general 'BomberosPlugin'
        // y se distingue por 'modulo' y 'funcionalidad'.
        BomberosPlugin.enviarPeticionAjax('inscripciones', 'pagina_inicial', formData);
    });


});
jQuery(document).ready(function ($) {
    
    // formato envio peticion ajax
    //   enviarPeticionAjaxPublico ('solicitudInspecciones','plantilla',formData );

    // envio del nit para buscar empresas
    $(document).on('submit', '#frm_buscar_empresa', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        enviarPeticionAjaxPublico ('solicitudInspecciones','buscar_empresa',formData );
    });

    // envio de datos para registrar en empresas e inpecciones
    $(document).on('submit', '#frm_bomberos_empresa_completa', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        enviarPeticionAjaxPublico ('solicitudInspecciones','registrar_empresa_solicitud',formData );     
    });
});
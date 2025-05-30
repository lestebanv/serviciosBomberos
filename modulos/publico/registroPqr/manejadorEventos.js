jQuery(document).ready(function ($) {
    // Manejar envío del formulario
    $('#pqr-form').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        enviarPeticionAjaxPublico ('registroPqr','registrar_pqr',formData );
    });
});
jQuery(document).ready(function ($) {
    // Manejar envío del formulario
    $('#form-inscripcion-curso').on('submit', function (e) {
      
        e.preventDefault();
        var formData = $(this).serialize();
          console.log("enviando formulario"+formData);
        enviarPeticionAjaxPublico ('registroInscripciones','registrar_inscripcion',formData );
    });
});
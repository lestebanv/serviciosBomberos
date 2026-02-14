jQuery(document).ready(function ($) {
    // Manejar envío del formulario
    $(document).on('submit', '#form-inscripcion-curso', function (e) {
        e.preventDefault();
        
        var $form = $(this);
        var $boton = $form.find('button[type="submit"]');
        var $mensaje = $('#bomberos-inscripcion-mensaje');
        
        // UI: Bloquear botón y limpiar mensajes
        $boton.prop('disabled', true).text('Procesando...');
        $mensaje.hide().removeClass('notice-error notice-success');

        var formData = $form.serialize();
        console.log("Enviando formulario inscripciones...");

        // Usamos la función global que ya tienes
        enviarPeticionAjaxPublico('registroInscripciones', 'registrar_inscripcion', formData);

        // Opcional: Restaurar botón después de unos segundos
        // (Dependiendo de cómo 'enviarPeticionAjaxPublico' maneje el DOM, 
        // a veces es mejor dejar que la respuesta Ajax restaure o reemplace el HTML)
        setTimeout(function(){
             $boton.prop('disabled', false).text('Inscribirme');
        }, 3000);
    });
});
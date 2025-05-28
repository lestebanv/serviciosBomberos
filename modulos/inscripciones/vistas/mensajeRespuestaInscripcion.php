<?php
// modulos/inscripciones/vistas/mensajeRespuestaInscripcion.php
if (!defined('ABSPATH')) {
    exit;
}
/** @var string $mensaje_vista */ // Variable que pasará el controlador
?>
<div class="bomberos-mensaje-respuesta-inscripcion">
    <p><?php echo esc_html($mensaje_vista); ?></p>
    <?php /* Puedes añadir un enlace para volver o algo más aquí */ ?>
</div>
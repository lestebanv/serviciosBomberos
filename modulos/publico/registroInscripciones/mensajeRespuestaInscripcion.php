<?php
if (!defined('ABSPATH')) {
    exit;
}
// Verificación extra para evitar pantallas en blanco si falla algo
if (!isset($objincripcion)) {
    echo "<p>Inscripción realizada, pero no se pudieron cargar los detalles.</p>";
    return;
}
?>

<div class="inscripcion-detalle" style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #c3e6cb; background-color: #d4edda; border-radius: 8px; font-family: Arial, sans-serif; color: #155724;">
    <h2 style="text-align: center; margin-top: 0; color: #155724;">¡Inscripción Exitosa!</h2>

    <div style="background-color: white; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;">
        <p><strong>Nombre del Curso:</strong> <br> <?= esc_html($objincripcion['nombre_curso']) ?></p>
        <p><strong>Nombre del Asistente:</strong> <br> <?= esc_html($objincripcion['nombre_asistente']) ?></p>
        <p><strong>Email:</strong> <br> <?= esc_html($objincripcion['email_asistente']) ?></p>
        <p><strong>Teléfono:</strong> <br> <?= esc_html($objincripcion['telefono_asistente'] ?: 'No proporcionado') ?></p>
        <p><strong>Fecha de Inscripción:</strong> <br> <?= esc_html($objincripcion['fecha_inscripcion']) ?></p>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <p>✅ Hemos enviado los detalles a tu correo electrónico.</p>
        <div style="background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin-top: 10px; font-size: 0.9em;">
            <strong>⚠️ Atención:</strong> Estar pendientes a la hora exacta del curso.
        </div>
    </div>
</div>
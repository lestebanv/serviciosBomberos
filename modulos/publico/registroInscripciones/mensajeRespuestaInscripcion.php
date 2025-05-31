<?php if ($objincripcion): ?>
<div class="inscripcion-detalle" style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; font-family: Arial, sans-serif;">
    <h2 style="text-align: center;">Detalle de Inscripción</h2>

    <p><strong>Nombre del Curso:</strong> <?= esc_html($objincripcion['nombre_curso']) ?></p>
    <p><strong>Nombre del Asistente:</strong> <?= esc_html($objincripcion['nombre_asistente']) ?></p>
    <p><strong>Email del Asistente:</strong> <?= esc_html($objincripcion['email_asistente']) ?></p>
    <p><strong>Teléfono:</strong> <?= esc_html($objincripcion['telefono_asistente']) ?: 'No proporcionado' ?></p>
    <p><strong>Fecha de Inscripción:</strong> <?= esc_html($objincripcion['fecha_inscripcion']) ?></p>
    <p><strong>Estado de la Inscripción:</strong> <?= esc_html($objincripcion['estado_inscripcion']) ?></p>
    <p><strong>Notas:</strong> <?= nl2br(esc_html($objincripcion['notas'] ?: 'Sin notas')) ?></p>
</div>
<?php else: ?>
    <p style="color: red; text-align: center;">No se encontró la inscripción.</p>
<?php endif; ?>

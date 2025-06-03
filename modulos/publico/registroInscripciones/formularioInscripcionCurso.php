<?php

if (!defined('ABSPATH')) {
    exit;
}

?>
<div id="bomberos-inscripcion-curso-wrapper">
    <h3><?php esc_html_e('Inscribirse a un Curso', 'bomberos-servicios'); ?></h3>
    
    <div id="bomberos-inscripcion-mensaje" class="bomberos-mensaje-ajax" style="display:none;"></div>

    <form id="form-inscripcion-curso" method="post">
            <label for="id_curso">Seleccione un Curso:</label><br>
            <select name="id_curso" id="id_curso" required>
                <option value="">-- Seleccionar Curso --</option>
                <?php foreach ($cursosDisponibles as $curso): ?>
                    <option value="<?php echo esc_attr($curso['id_curso']); ?>">
                        <?php 
                        $cupos_texto = ($curso['capacidad_maxima'] > 0) ? sprintf(esc_html__(' (Cupos restantes: %s)', 'bomberos-servicios'), $curso['cupos_disponibles']) : esc_html__(' (Cupos ilimitados)', 'bomberos-servicios');
                        echo esc_html($curso['nombre_curso']) . ' - ' . esc_html(date_i18n(get_option('date_format'), strtotime($curso['fecha_inicio']))) . esc_html($cupos_texto); 
                        ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="nombre_asistente">Nombre Completo: <span class="required">*</span></label><br>
            <input type="text" name="nombre_asistente" id="nombre_asistente" class="regular-text" required>
        </p>
        <p>
            <label for="email_asistente">Correo Electrónico <span class="required">*</span></label><br>
            <input type="email" name="email_asistente" id="email_asistente" class="regular-text" required>
        </p>
        <p>
            <label for="telefono_asistente">Teléfono </label><br>
            <input type="tel" name="telefono_asistente" id="telefono_asistente" class="regular-text">
        </p>
        <p>
            <button type="submit" class="button button-primary">Inscribirme</button>
        </p>
    </form>
</div>

<?php
// modulos/inscripciones/vistas/formularioInscripcionCurso.php
if (!defined('ABSPATH')) {
    exit;
}
//cursos_disponibles */ 
?>
<div id="bomberos-inscripcion-curso-wrapper">
    <h3><?php esc_html_e('Inscribirse a un Curso', 'bomberos-servicios'); ?></h3>
    
    <div id="bomberos-inscripcion-mensaje" class="bomberos-mensaje-ajax" style="display:none;"></div>

    <form id="form-inscripcion-curso" method="post">
        <?php wp_nonce_field('bomberos_inscripcion_curso_nonce_action', 'seguridad_inscripcion_curso_nonce_field'); ?>
        <input type="hidden" name="funcionalidad_publica" value="procesar_inscripcion_curso">

        <p>
            <label for="id_curso"><?php esc_html_e('Seleccione un Curso:', 'bomberos-servicios'); ?> <span class="required">*</span></label><br>
            <select name="id_curso" id="id_curso" required>
                <option value=""><?php esc_html_e('-- Seleccionar Curso --', 'bomberos-servicios'); ?></option>
                <?php foreach ($cursos_disponibles as $curso): ?>
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
            <label for="nombre_asistente"><?php esc_html_e('Nombre Completo:', 'bomberos-servicios'); ?> <span class="required">*</span></label><br>
            <input type="text" name="nombre_asistente" id="nombre_asistente" class="regular-text" required>
        </p>
        <p>
            <label for="email_asistente"><?php esc_html_e('Correo Electrónico:', 'bomberos-servicios'); ?> <span class="required">*</span></label><br>
            <input type="email" name="email_asistente" id="email_asistente" class="regular-text" required>
        </p>
        <p>
            <label for="telefono_asistente"><?php esc_html_e('Teléfono (Opcional):', 'bomberos-servicios'); ?></label><br>
            <input type="tel" name="telefono_asistente" id="telefono_asistente" class="regular-text">
        </p>
        <p>
            <button type="submit" class="button button-primary"><?php esc_html_e('Inscribirme', 'bomberos-servicios'); ?></button>
        </p>
    </form>
</div>
<style>
    #bomberos-inscripcion-curso-wrapper .required { color: red; }
    #bomberos-inscripcion-curso-wrapper .regular-text { width: 100%; max-width: 400px; padding: 8px; margin-bottom:10px; box-sizing: border-box;}
    #bomberos-inscripcion-curso-wrapper select { width: 100%; max-width: 400px; padding: 8px; margin-bottom:10px; box-sizing: border-box;}
    #bomberos-inscripcion-curso-wrapper .bomberos-mensaje-ajax { padding: 10px; margin-bottom: 15px; border: 1px solid transparent; border-radius: 4px; }
    #bomberos-inscripcion-curso-wrapper .bomberos-mensaje-ajax.success { color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; }
    #bomberos-inscripcion-curso-wrapper .bomberos-mensaje-ajax.error { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
</style>
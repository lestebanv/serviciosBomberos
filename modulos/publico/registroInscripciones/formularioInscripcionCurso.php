<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="bomberos-inscripcion-curso-wrapper" class="bomberos-container">
    <h3><?php esc_html_e('Inscribirse a un Curso', 'bomberos-servicios'); ?></h3>
    
    <!-- Contenedor para mensajes de respuesta AJAX -->
    <div id="bomberos-inscripcion-mensaje" class="bomberos-mensaje-ajax notice" style="display:none; margin-bottom: 15px; padding: 10px;"></div>

    <?php 
    // Si no hay cursos en el array (porque no hay futuros o no hay activos), mostramos aviso.
    if (empty($cursosDisponibles)): 
    ?>
        <div class="notice notice-warning inline">
            <p><?php esc_html_e('Actualmente no hay cursos programados con inscripciones abiertas.', 'bomberos-servicios'); ?></p>
        </div>
    <?php else: ?>

        <form id="form-inscripcion-curso" method="post" class="bomberos-form">
            <p>
                <label for="id_curso"><strong><?php esc_html_e('Seleccione un Curso:', 'bomberos-servicios'); ?></strong></label><br>
                <select name="id_curso" id="id_curso" required class="regular-text" style="width: 100%; max-width: 400px;">
                    <option value="">-- <?php esc_html_e('Seleccionar Curso', 'bomberos-servicios'); ?> --</option>
                    
                    <?php foreach ($cursosDisponibles as $curso): ?>
                        <?php 
                            // Lógica visual para los cupos
                            // Usamos el valor calculado en el controlador, o asumimos 0 si no existe
                            $cupos = isset($curso['cupos_disponibles']) ? (int)$curso['cupos_disponibles'] : 0;
                            $capacidad = isset($curso['capacidad_maxima']) ? (int)$curso['capacidad_maxima'] : 0;
                            
                            // Determinar si está lleno
                            $estaLleno = ($cupos <= 0);
                            
                            // Construir el texto de la opción
                            $nombre = esc_html($curso['nombre_curso']);
                            $fecha = date_i18n(get_option('date_format'), strtotime($curso['fecha_inicio']));
                            
                            if ($estaLleno) {
                                $textoOpcion = "$fecha - $nombre (AGOTADO)";
                            } else {
                                $textoOpcion = "$fecha - $nombre (Cupos disponibles: $cupos)";
                            }
                        ?>
                        
                        <option value="<?php echo esc_attr($curso['id_curso']); ?>" <?php echo $estaLleno ? 'disabled' : ''; ?> style="<?php echo $estaLleno ? 'color: #999;' : ''; ?>">
                            <?php echo $textoOpcion; ?>
                        </option>
                        
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="nombre_asistente"><?php esc_html_e('Nombre Completo:', 'bomberos-servicios'); ?> <span class="required">*</span></label><br>
                <input type="text" name="nombre_asistente" id="nombre_asistente" class="regular-text" required style="width: 100%; max-width: 400px;">
            </p>

            <p>
                <label for="email_asistente"><?php esc_html_e('Correo Electrónico:', 'bomberos-servicios'); ?> <span class="required">*</span></label><br>
                <input type="email" name="email_asistente" id="email_asistente" class="regular-text" required style="width: 100%; max-width: 400px;">
            </p>

            <p>
                <label for="telefono_asistente"><?php esc_html_e('Teléfono:', 'bomberos-servicios'); ?> <span class="required">*</span></label><br>
                <input type="tel" name="telefono_asistente" id="telefono_asistente" class="regular-text" required style="width: 100%; max-width: 400px;">
            </p>

            <p>
                <label for="notas"><?php esc_html_e('Observaciones (Opcional):', 'bomberos-servicios'); ?></label><br>
                <textarea name="notas" id="notas" class="regular-text" rows="3" style="width: 100%; max-width: 400px;"></textarea>
            </p>

            <p>
                <button type="submit" class="button button-primary"><?php esc_html_e('Inscribirme', 'bomberos-servicios'); ?></button>
            </p>
        </form>

    <?php endif; ?>
</div>
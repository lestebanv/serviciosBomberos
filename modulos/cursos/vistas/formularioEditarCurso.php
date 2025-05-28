<?php
if (!defined('ABSPATH')) {
    exit;
}
// La variable $paged debe ser pasada desde el controlador (ControladorCursos::formularioEdicion)
// Si no se pasa, se asume 1 por defecto o se podría obtener de otra manera si es necesario.
$paged = isset($paged) ? $paged : 1; 
?>
<div class="wrap">
    <h2><?php esc_html_e('Editar Curso', 'bomberos-servicios'); ?> - <?php echo esc_html($curso['nombre_curso']); ?></h2>
    <hr>
    <form id="form-editar-curso" method="post" class="bomberos-form">
        <input type="hidden" name="id_curso" value="<?php echo esc_attr($curso['id_curso']); ?>">
        <input type="hidden" name="paged" value="<?php echo esc_attr($paged); ?>"> {/* Campo oculto para paged */}
        <table class="form-table">
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="nombre_curso"><?php esc_html_e('Nombre del Curso', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="text" name="nombre_curso" id="nombre_curso" class="regular-text" value="<?php echo esc_attr($curso['nombre_curso']); ?>" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="descripcion"><?php esc_html_e('Descripción', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <textarea name="descripcion" id="descripcion" class="regular-text" rows="5"><?php echo esc_textarea($curso['descripcion']); ?></textarea>
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="fecha_inicio"><?php esc_html_e('Fecha de Inicio', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?php echo esc_attr($curso['fecha_inicio']); ?>" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="duracion_horas"><?php esc_html_e('Duración (Horas)', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="number" name="duracion_horas" id="duracion_horas" min="1" value="<?php echo esc_attr($curso['duracion_horas'] ?? ''); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="instructor"><?php esc_html_e('Instructor', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="text" name="instructor" id="instructor" class="regular-text" value="<?php echo esc_attr($curso['instructor'] ?? ''); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="lugar"><?php esc_html_e('Lugar', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="text" name="lugar" id="lugar" class="regular-text" value="<?php echo esc_attr($curso['lugar'] ?? ''); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="capacidad_maxima"><?php esc_html_e('Capacidad Máxima', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="number" name="capacidad_maxima" id="capacidad_maxima" min="1" value="<?php echo esc_attr($curso['capacidad_maxima'] ?? ''); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="estado"><?php esc_html_e('Estado', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <select name="estado" id="estado" class="regular-text" required aria-required="true">
                        <option value="planificado" <?php selected($curso['estado'], 'planificado'); ?>><?php esc_html_e('Planificado', 'bomberos-servicios'); ?></option>
                        <option value="en_curso" <?php selected($curso['estado'], 'en_curso'); ?>><?php esc_html_e('En Curso', 'bomberos-servicios'); ?></option>
                        <option value="finalizado" <?php selected($curso['estado'], 'finalizado'); ?>><?php esc_html_e('Finalizado', 'bomberos-servicios'); ?></option>
                        <option value="cancelado" <?php selected($curso['estado'], 'cancelado'); ?>><?php esc_html_e('Cancelado', 'bomberos-servicios'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary"><?php esc_html_e('Guardar Cambios', 'bomberos-servicios'); ?></button>
            <button type="button" class="button button-secondary cancelar-edicion-curso" data-paged="<?php echo esc_attr($paged); ?>"><?php esc_html_e('Cancelar', 'bomberos-servicios'); ?></button>
        </p>
        <div id="mensaje-editar-curso" class="notice" style="display: none;"></div>
    </form>
    <hr>
</div>
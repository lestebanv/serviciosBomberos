<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h2>Editar  <?php echo esc_attr($pqr['tipo_solicitud']); ?> de <?php echo esc_attr($pqr['nombre']); ?></h2><br>
        Telefono: <?php echo esc_attr($pqr['telefono']); ?><br>
        Email: <?php echo esc_attr($pqr['email']); ?><br>
        <strong><?php echo esc_attr($pqr['tipo_solicitud']); ?></strong>:<br>
         <?php echo esc_attr($pqr['contenido']); ?><br>
    <hr>
    <form id="form-editar-pqr" method="post">
        <input type="hidden" name="id" value="<?php echo esc_attr($pqr['id']); ?>">
        <input type="hidden" name="paged" value="<?php echo esc_attr($paged); ?>">

        <table class="form-table">
            <tr class="form-field">
                <th scope="row">
                    <label for="estado_solicitud">Estado de la Solicitud</label>
                </th>
                <td>
                    <select name="estado_solicitud" id="estado_solicitud">
                        <option value="Registrada" <?php selected($pqr['estado_solicitud'], 'Registrada'); ?>>Registrada</option>
                        <option value="En Proceso" <?php selected($pqr['estado_solicitud'], 'En Proceso'); ?>>En Proceso</option>
                        <option value="Cerrada" <?php selected($pqr['estado_solicitud'], 'Cerrada'); ?>>Cerrada</option>
                    </select>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="respuesta">Respuesta</label>
                </th>
                <td>
                    <textarea name="respuesta" id="respuesta" class="large-text" rows="5"><?php echo esc_textarea($pqr['respuesta']); ?></textarea>
                </td>
            </tr>
            
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">Guardar Cambios</button>
            <button type="button" class="button cancelar-edicion-pqr" data-paged="<?php echo esc_attr($paged); ?>">Cancelar</button>
        </p>

        <div id="mensaje-editar-pqr" class="notice" style="display: none;"></div>
    </form>
    <hr>
</div>

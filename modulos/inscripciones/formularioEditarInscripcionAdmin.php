<?php
if (!defined('ABSPATH')) exit;

// Definir los estados posibles que coincidan con el ENUM de la BD y la lógica del controlador
$estados_posibles_inscripcion = ['Registrada', 'Aprobada', 'Pendiente', 'Cerrada'];

?>
<div class="wrap" id="inscripcion-frm-editar-admin">
    <h3><?php esc_html_e('Editar Inscripción', 'bomberos-servicios'); ?></h3>
    <p>
        <?php esc_html_e('Editando inscripción para:', 'bomberos-servicios'); ?>
        <strong><?php echo esc_html($inscripcion['nombre_asistente']); ?></strong>
        (<?php echo esc_html($inscripcion['email_asistente']); ?>)
        <?php esc_html_e('al curso:', 'bomberos-servicios'); ?>
        <strong><?php echo esc_html($inscripcion['nombre_curso']); ?></strong>
    </p>
    <hr>

    <form id="form-editar-inscripcion-admin" method="post" class="bomberos-form">
        <input type="hidden" name="id_inscripcion" value="<?php echo esc_attr($inscripcion['id_inscripcion']); ?>">
        <input type="hidden" name="paged" value="<?php echo esc_attr($paged); ?>"> 

        <table class="form-table">
            <tbody>
                <tr class="form-field">
                    <th scope="row">
                        <label for="nombre_asistente_display"><?php esc_html_e('Nombre del Asistente', 'bomberos-servicios'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="nombre_asistente_display" class="regular-text" value="<?php echo esc_attr($inscripcion['nombre_asistente']); ?>" readonly>
                        <p class="description"><?php esc_html_e('El nombre y email del asistente no se pueden modificar desde aquí para mantener la integridad del registro original. Si es necesario, cancele esta inscripción y cree una nueva.', 'bomberos-servicios'); ?></p>
                         <input type="hidden" name="nombre_asistente" value="<?php echo esc_attr($inscripcion['nombre_asistente']); ?>">
                         <input type="hidden" name="email_asistente" value="<?php echo esc_attr($inscripcion['email_asistente']); ?>">
                    </td>
                </tr>

                <tr class="form-field">
                    <th scope="row">
                        <label for="email_asistente_display"><?php esc_html_e('Email del Asistente', 'bomberos-servicios'); ?></label>
                    </th>
                    <td>
                        <input type="email" id="email_asistente_display" class="regular-text" value="<?php echo esc_attr($inscripcion['email_asistente']); ?>" readonly>
                    </td>
                </tr>
                
              

                <tr class="form-field form-required">
                    <th scope="row">
                        <label for="estado_inscripcion"><?php esc_html_e('Estado de la Inscripción', 'bomberos-servicios'); ?></label>
                    </th>
                    <td>
                        <select name="estado_inscripcion" id="estado_inscripcion" required>
                            <?php foreach ($estados_posibles_inscripcion as $estado_val): ?>
                                <option value="<?php echo esc_attr($estado_val); ?>" <?php selected($inscripcion['estado_inscripcion'], $estado_val); ?>>
                                    <?php echo esc_html(ucfirst(str_replace('_', ' ', $estado_val))); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr class="form-field">
                    <th scope="row">
                        <label for="telefono_asistente">Telefono de contacto</label>
                    </th>
                    <td>
                        <input type="text" name="telefono_asistente" id="telefono_asistente" class="regular-text" value="<?php echo esc_attr($inscripcion['telefono_asistente'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr class="form-field">
                    <th scope="row">
                        <label for="notas">Observaciones o Notas adicionales</label>
                    </th>
                    <td>
                        <textarea name="notas" id="notas" class="regular-text" rows="5"><?php echo esc_textarea($inscripcion['notas'] ?? ''); ?></textarea>
                    </td>
                </tr>

            </tbody>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">Guardar Cambios</button>
            <button type="button" class="button button-secondary cancelar-edicion-inscripcion-admin" data-paged="<?php echo esc_attr($paged); ?>"><?php esc_html_e('Cancelar', 'bomberos-servicios'); ?></button>
        </p>
    </form>
    <hr>
</div>
<?php
if (!defined('ABSPATH')) exit;
?>
<div class="wrap" id="inscripcion-frm-editar-admin">
    <h3>Editar Inscripcion al Curso:<?php echo esc_html($inscripcion['nombre_curso']); ?></h3>
    <p>
        Editando inscripción para: <strong><?php echo esc_html($inscripcion['nombre_asistente']); ?></strong><br>
        <?php echo esc_html($inscripcion['email_asistente']); ?><br>
       
    </p>
    <hr>
    <form id="form-editar-inscripcion" method="post" >
        <input type="hidden" name="id_inscripcion" value="<?php echo esc_attr($inscripcion['id_inscripcion']); ?>">
        <input type="hidden" name="actualpagina" value="<?php echo esc_attr($actualpagina); ?>"> 

        <table class="form-table">
            <tbody>
                <tr class="form-field">
                    <th scope="row">
                        <label for="nombre_asistente">Nombre del asistente ?></label>
                    </th>
                    <td>
                        <?php echo esc_attr($inscripcion['nombre_asistente']); ?><br>
                        <input type="hidden" name="nombre_asistente" value="<?php echo esc_attr($inscripcion['nombre_asistente']); ?>">
                        <input type="hidden" name="email_asistente" value="<?php echo esc_attr($inscripcion['email_asistente']); ?>">
                    </td>
                </tr>

                <tr class="form-field">
                    <th scope="row">
                        <label for="email_asistente">Email del Asistente</label>
                    </th>
                    <td>
                        <input type="email" id="email_asistente" name="email_asistente" class="regular-text" value="<?php echo esc_attr($inscripcion['email_asistente']); ?>">
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th scope="row">
                        <label for="estado_inscripcion">Estado de la Inscripción</label>
                    </th>
                    <td>
                        <select name="estado_inscripcion" id="estado_inscripcion" required>
                            <?php foreach ($estadosPosibles as $estado): ?>
                                <option value="<?php echo esc_attr($estado); ?>" <?php selected($inscripcion['estado_inscripcion'], $estado); ?>>
                                    <?php echo esc_html(ucfirst(str_replace('_', ' ', $estado))); ?>
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
            <button type="button" class="button button-secondary cancelar-edicion-inscripcion" data-actualpagina="<?php echo esc_attr($actualpagina); ?>">Cancelar</button>
        </p>
    </form>
    <hr>
</div>
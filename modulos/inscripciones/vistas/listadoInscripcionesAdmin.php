<?php
// modulos/inscripciones/vistas/listadoInscripcionesAdmin.php
if (!defined('ABSPATH')) exit;

?>
<div class="wrap" id="cuerpo-listado-inscripciones-admin">
    <h2><?php esc_html_e('Gestión de Inscripciones a Cursos', 'bomberos-servicios'); ?></h2>

    <?php if (function_exists('barraNavegacion')) barraNavegacion('inscripciones_admin', $total_pages, $current_page); ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Curso', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Asistente', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Email', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Fecha Inscripción', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Estado', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Acciones', 'bomberos-servicios'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($lista_inscripciones)): ?>
                <tr>
                    <td colspan="6"><?php esc_html_e('No hay inscripciones registradas.', 'bomberos-servicios'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($lista_inscripciones as $inscripcion): ?>
                    <?php
                        // Generar la clase CSS para el estado
                        $clase_estado = 'estado-inscripcion-badge ' . strtolower(esc_attr($inscripcion['estado_inscripcion']));
                    ?>
                    <tr id="inscripcion-row-<?php echo esc_attr($inscripcion['id_inscripcion']); ?>">
                        <td><?php echo esc_html($inscripcion['nombre_curso']); ?></td>
                        <td><?php echo esc_html($inscripcion['nombre_asistente']); ?></td>
                        <td><?php echo esc_html($inscripcion['email_asistente']); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', strtotime($inscripcion['fecha_inscripcion']))); ?></td>
                        <td>
                            <span class="<?php echo $clase_estado; ?>">
                                <?php echo esc_html(ucfirst(str_replace('_', ' ', $inscripcion['estado_inscripcion']))); ?>
                            </span>
                        </td>
                        <td>
                            <button class="button editar-inscripcion-admin" 
                                    data-id="<?php echo esc_attr($inscripcion['id_inscripcion']); ?>"
                                    data-paged="<?php echo esc_attr($current_page); ?>">
                                <?php esc_html_e('Editar', 'bomberos-servicios'); ?>
                            </button>
                            <button class="button delete-inscripcion-admin" 
                                    data-id="<?php echo esc_attr($inscripcion['id_inscripcion']); ?>"
                                    data-paged="<?php echo esc_attr($current_page); ?>">
                                <?php esc_html_e('Eliminar', 'bomberos-servicios'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if (function_exists('barraNavegacion')) barraNavegacion('inscripciones_admin', $total_pages, $current_page, 'right'); ?>
</div>
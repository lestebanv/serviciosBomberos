<?php
// modulos/inscripciones/vistas/listadoInscripcionesAdmin.php
if (!defined('ABSPATH')) exit;
/**
 * @var array $lista_inscripciones
 * @var int $total_pages
 * @var int $current_page
 */
?>
<div class="wrap" id="cuerpo-listado-inscripciones-admin">
    <h2><?php esc_html_e('Gestión de Inscripciones a Cursos', 'bomberos-servicios'); ?></h2>

    <?php if (function_exists('barraNavegacion')) barraNavegacion('inscripciones_admin', $total_pages, $current_page); ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('ID', 'bomberos-servicios'); ?></th>
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
                    <td colspan="7"><?php esc_html_e('No hay inscripciones registradas.', 'bomberos-servicios'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($lista_inscripciones as $inscripcion): ?>
                    <tr>
                        <td><?php echo esc_html($inscripcion['id_inscripcion']); ?></td>
                        <td><?php echo esc_html($inscripcion['nombre_curso']); ?> (ID: <?php echo esc_html($inscripcion['id_curso']); ?>)</td>
                        <td><?php echo esc_html($inscripcion['nombre_asistente']); ?></td>
                        <td><?php echo esc_html($inscripcion['email_asistente']); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', strtotime($inscripcion['fecha_inscripcion']))); ?></td>
                        <td><?php echo esc_html(ucfirst($inscripcion['estado_inscripcion'])); ?></td>
                        <td>
                            <?php /* Podrías añadir botones para cambiar estado, ver detalles, eliminar, etc.
                            <button class="button button-small ver-detalle-inscripcion" data-id="<?php echo esc_attr($inscripcion['id_inscripcion']); ?>">Ver</button>
                            */ ?>
                            N/A
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if (function_exists('barraNavegacion')) barraNavegacion('inscripciones_admin', $total_pages, $current_page, 'right'); ?>
</div>
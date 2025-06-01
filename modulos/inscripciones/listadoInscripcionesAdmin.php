<?php
// modulos/inscripciones/vistas/listadoInscripcionesAdmin.php
if (!defined('ABSPATH')) exit;

?>
<div class="wrap" id="cuerpo-listado-inscripciones-admin">
    <h2><?php esc_html_e('Gestión de Inscripciones a Cursos', 'bomberos-servicios'); ?></h2>

    <?php if (function_exists('barraNavegacion')) barraNavegacion('inscripciones_admin', $total_pages, $current_page); ?>

    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th>Curso</th>
                <th>Asistente</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($lista_inscripciones)): ?>
                <tr>
                    <td colspan="4">No hay inscripciones registradas</td>
                </tr>
            <?php else: ?>
                <?php foreach ($lista_inscripciones as $inscripcion): ?>
                    <tr id="inscripcion-row-<?php echo esc_attr($inscripcion['id_inscripcion']); ?>">
                        <td><?php echo esc_html($inscripcion['nombre_curso']); ?></td>
                        
                        <td><strong><?php echo esc_html($inscripcion['estado_inscripcion']); ?></strong><br>    
                            <?php echo esc_html($inscripcion['nombre_asistente']); ?><br>
                            <?php echo esc_html($inscripcion['email_asistente']); ?><hr>
                            Fecha Inscripcion: <?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', strtotime($inscripcion['fecha_inscripcion']))); ?>
                        </td>

                        <td><?php echo esc_html($inscripcion['notas']); ?></td>
                        <td>
                            <button class="button editar-inscripcion-admin" 
                                    data-id="<?php echo esc_attr($inscripcion['id_inscripcion']); ?>"
                                    data-paged="<?php echo esc_attr($current_page); ?>">
                                Editar
                            </button>
                            <button class="button delete-inscripcion-admin" 
                                    data-id="<?php echo esc_attr($inscripcion['id_inscripcion']); ?>"
                                    data-paged="<?php echo esc_attr($current_page); ?>">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if (function_exists('barraNavegacion')) barraNavegacion('inscripciones_admin', $total_pages, $current_page, 'right'); ?>
</div>
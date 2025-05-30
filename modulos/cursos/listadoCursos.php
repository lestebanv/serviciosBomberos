<?php
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="wrap" id="cuerpo-listado-cursos">
    <div id="curso-frm-editar"></div>
    <div style="margin-bottom: 1em;">
        <button class="button button-primary" id="btn-agregar-curso"><?php esc_html_e('Agregar nuevo curso', 'bomberos-servicios'); ?></button>
    </div>
    <?php barraNavegacion('cursos',$total_pages, $current_page); ?>

    <table id="curso-table" class="wp-list-table widefat  striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Nombre del Curso', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Fecha de Inicio', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Duración (Horas)', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Estado', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Acciones', 'bomberos-servicios'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista_cursos as $curso): ?>
                <tr id="curso-row-<?php echo esc_attr($curso['id_curso']); ?>">
                    <td><?php echo esc_html($curso['nombre_curso']); ?></td>
                    <td><?php echo esc_html($curso['fecha_inicio']); ?></td>
                    <td><?php echo esc_html($curso['duracion_horas'] ?? 'N/A'); ?></td>
                    <td><?php echo esc_html($curso['estado']); ?></td>
                    <td>
                        <button class="button editar-curso" data-id="<?php echo esc_attr($curso['id_curso']); ?>">
                            <?php esc_html_e('Editar', 'bomberos-servicios'); ?>
                        </button>
                        <button class="button delete-curso" data-id="<?php echo esc_attr($curso['id_curso']); ?>">
                            <?php esc_html_e('Eliminar', 'bomberos-servicios'); ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php barraNavegacion('cursos',$total_pages, $current_page, 'right'); ?>
</div>
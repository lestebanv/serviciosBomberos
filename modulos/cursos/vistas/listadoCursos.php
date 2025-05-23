<?php
if (!defined('ABSPATH')) {
    exit;
}

function barraNavegacion($total_pages, $current_page, $align = 'left')
{
    if ($total_pages > 1) {
        $style = '';
        if ($align === 'right') {
            $style = 'float: right;';
        } elseif ($align === 'center') {
            $style = 'margin: 0 auto; text-align: center; width: fit-content;';
        } else {
            $style = 'float: left;';
        }
        $style .= ' font-size: 14px; font-weight: bold; font-family: Arial, sans-serif;';
        $pagination_args = array(
            'base' => '#%#%',
            'format' => '',
            'total' => $total_pages,
            'current' => $current_page,
            'prev_text' => __('« Anterior'),
            'next_text' => __('Siguiente »'),
            'type' => 'array',
            'aria_current' => 'page',
        );

        $links = paginate_links($pagination_args);

        echo '<div class="tablenav"><div class="tablenav-pages" style="' . esc_attr($style) . '">';

        foreach ($links as $link) {
            if (preg_match('/\#(\d+)/', $link, $matches)) {
                $page_num = intval($matches[1]);
                echo '<a href="#" class="paginacion-ajax" data-paged="' . esc_attr($page_num) . '" style="margin: 0 1px; padding: 6px 12px; background-color: #e2f0fb; color: #0073aa; text-decoration: none; border-radius: 4px;">' . wp_kses_post(strip_tags($link)) . '</a> ';
            } else {
                echo '<span class="current" style="margin: 0 1px; padding: 6px 12px; background-color: #0073aa; color: white; border-radius: 4px;">' . wp_kses_post(strip_tags($link)) . '</span> ';
            }
        }

        echo '</div></div><div style="clear: both;"></div>';
    }
}
?>

<div class="wrap" id="cuerpo-listado-cursos">
    <div id="curso-frm-editar"></div>
    <div style="margin-bottom: 1em;">
        <button class="button button-primary" id="btn-agregar-curso"><?php esc_html_e('Agregar nuevo curso', 'bomberos-servicios'); ?></button>
    </div>
    <?php barraNavegacion($total_pages, $current_page); ?>

    <table id="curso-table" class="wp-list-table widefat fixed striped">
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

    <?php barraNavegacion($total_pages, $current_page, 'right'); ?>
</div>
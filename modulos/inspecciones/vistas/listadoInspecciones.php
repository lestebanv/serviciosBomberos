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

<div class="wrap" id="cuerpo-listado-inspecciones">
    <div id="inspeccion-frm-editar"></div>
    <?php barraNavegacion($total_pages, $current_page); ?>

    <table id="inspeccion-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                
                <th><?php esc_html_e('Empresa', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Fecha de Registro/Estado', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Encargado', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Acciones', 'bomberos-servicios'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista_inspecciones as $inspeccion): ?>
                <tr id="inspeccion-row-<?php echo esc_attr($inspeccion['id_inspeccion']); ?>">
                    <td><?php echo esc_html($inspeccion['razon_social'] ?? 'N/A'); ?> <br>
                        Direccion: <?php echo esc_html($inspeccion['direccion'] ?? 'N/A'); ?> <br>
                        Barrio: <?php echo esc_html($inspeccion['barrio'] ?? 'N/A'); ?>
                    </td>
                    <td><?php echo esc_html($inspeccion['fecha_registro']); ?><br>
                         Estado <?php echo esc_html($inspeccion['estado']); ?>
                    </td>
                    <td><?php echo esc_html($inspeccion['nombre_encargado']); ?><br>
                         Estado <?php echo esc_html($inspeccion['telefono_encargado']); ?>
                    </td>
                    <td>
                        <button class="button editar-inspeccion" data-id="<?php echo esc_attr($inspeccion['id_inspeccion']); ?>">
                            <?php esc_html_e('Editar', 'bomberos-servicios'); ?>
                        </button>
                        <button class="button delete-inspeccion" data-id="<?php echo esc_attr($inspeccion['id_inspeccion']); ?>">
                            <?php esc_html_e('Eliminar', 'bomberos-servicios'); ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php barraNavegacion($total_pages, $current_page, 'right'); ?>
</div>
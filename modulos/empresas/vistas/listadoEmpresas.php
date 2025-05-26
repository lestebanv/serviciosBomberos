<?php

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
            'prev_text' => __('&laquo; Anterior'),
            'next_text' => __('Siguiente &raquo;'),
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

<div class="wrap" id="cuerpo-listado-empresas">
    <div id="empresa-frm-editar"></div>
    <div style="margin-bottom: 1em;">
        <button class="button button-primary" id="btn-agregar-empresa">Agregar nueva empresa</button>
    </div>
    <?php barraNavegacion($total_pages, $current_page); ?>

    <table id="empresa-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>NIT / Razón Social</th>
                <th>Ubicación</th>
                <th>Representante Legal</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista_empresas as $empresa): ?>
                <tr id="empresa-row-<?php echo esc_attr($empresa['id_empresa']); ?>">
                    <td>
                        <?php echo esc_html($empresa['razon_social']); ?><br>
                        <strong>NIT: <?php echo esc_html($empresa['nit']); ?></strong>                        
                    </td>
                    <td>
                        <?php echo esc_html($empresa['direccion']); ?><br>
                        Barrio: <?php echo esc_html($empresa['barrio']); ?>
                    </td>
                    <td><?php echo esc_html($empresa['representante_legal']); ?></td>
                    <td><?php echo esc_html($empresa['email']); ?></td>
                    <td>
                        <button class="button editar-empresa"
                            data-id="<?php echo esc_attr($empresa['id_empresa']);?>" data-paged="<?php echo $current_page; ?>">Editar</button>
                        <button class="button delete-empresa"
                            data-id="<?php echo esc_attr($empresa['id_empresa']); ?>" data-paged="<?php echo $current_page; ?>">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php barraNavegacion($total_pages, $current_page, 'right'); ?>
</div>
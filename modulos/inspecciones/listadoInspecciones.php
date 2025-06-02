<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap" id="cuerpo-listado-inspecciones">
    <div id="inspeccion-frm-editar"></div>
    <div style="margin-bottom: 1em;">
        <button class="button button-secondary" id="btn-reporte-inspecciones-vencer" data-paged-listado="<?php echo esc_attr($current_page); ?>">
            <?php esc_html_e('Ver Próximas a Vencer (30 días)', 'bomberos-servicios'); ?>
        </button>
    </div>
    <?php barraNavegacion('inspecciones',$total_pages, $current_page); ?>

    <table id="inspeccione-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                
                <th><?php esc_html_e('Empresa', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Fecha de Registro/Estado', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Encargado', 'bomberos-servicios'); ?></th>
                <th><?php esc_html_e('Acciones', 'bomberos-servicios'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($lista_inspecciones)): ?>
                <tr>
                    <td colspan="4"><?php esc_html_e('No hay inspecciones registradas.', 'bomberos-servicios'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($lista_inspecciones as $inspeccion): ?>
                    <tr id="inspeccion-row-<?php echo esc_attr($inspeccion['id_inspeccion']); ?>">
                        <td>
                            <?php echo esc_html($inspeccion['razon_social'] ?? 'N/A'); ?> <br>
                            Direccion: <?php echo esc_html($inspeccion['direccion'] ?? 'N/A'); ?> <br>
                            Barrio: <?php echo esc_html($inspeccion['barrio'] ?? 'N/A'); ?>
                        </td>
                        <td>
                            <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($inspeccion['fecha_registro']))); ?><br>
                            <strong>Estado:</strong> <?php echo esc_html($inspeccion['estado']); ?><br>
                            <strong>Programada:</strong> <?php echo $inspeccion['fecha_programada'] ? esc_html(date_i18n(get_option('date_format'), strtotime($inspeccion['fecha_programada']))) : 'N/P'; ?>
                        </td>
                        <td>
                            <?php echo esc_html($inspeccion['nombre_encargado']); ?><br>
                            <strong>Tel:</strong> <?php echo esc_html($inspeccion['telefono_encargado']); ?>
                        </td>
                        <td>
                            <button class="button editar-inspeccion" data-id="<?php echo esc_attr($inspeccion['id_inspeccion']); ?>" data-paged="<?php echo esc_attr($current_page); ?>">
                                Editar
                            </button>
                            <button class="button eliminar-inspeccion" data-id="<?php echo esc_attr($inspeccion['id_inspeccion']); ?>" data-paged="<?php echo esc_attr($current_page); ?>">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php barraNavegacion('inspecciones',$total_pages, $current_page, 'right'); ?>
</div>
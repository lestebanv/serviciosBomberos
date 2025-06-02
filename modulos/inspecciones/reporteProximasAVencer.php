<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap" id="cuerpo-reporte-inspecciones-vencer">
    <h2><?php esc_html_e('Reporte: Inspecciones Próximas a Vencer (Próximos 30 días)', 'bomberos-servicios'); ?></h2>
    
    <div style="margin-bottom: 1em;">
        <button class="button button-secondary" id="btn-volver-listado-inspecciones" data-paged="<?php echo esc_attr($current_page_listado); ?>">
            <?php esc_html_e('Volver al Listado Principal', 'bomberos-servicios'); ?>
        </button>
    </div>

    <?php if (empty($inspecciones_proximas)): ?>
        <p><?php esc_html_e('No hay inspecciones programadas en los próximos 30 días con estado "Registrada" o "En Proceso".', 'bomberos-servicios'); ?></p>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('ID', 'bomberos-servicios'); ?></th>
                    <th><?php esc_html_e('Empresa (NIT)', 'bomberos-servicios'); ?></th>
                    <th><?php esc_html_e('Fecha Programada', 'bomberos-servicios'); ?></th>
                    <th><?php esc_html_e('Estado', 'bomberos-servicios'); ?></th>
                    <th><?php esc_html_e('Encargado', 'bomberos-servicios'); ?></th>
                    <th><?php esc_html_e('Tel. Encargado', 'bomberos-servicios'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inspecciones_proximas as $inspeccion): ?>
                    <tr>
                        <td><?php echo esc_html($inspeccion['id_inspeccion']); ?></td>
                        <td><?php echo esc_html($inspeccion['razon_social']); ?> (<?php echo esc_html($inspeccion['nit']); ?>)</td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($inspeccion['fecha_programada']))); ?></td>
                        <td><?php echo esc_html($inspeccion['estado']); ?></td>
                        <td><?php echo esc_html($inspeccion['nombre_encargado']); ?></td>
                        <td><?php echo esc_html($inspeccion['telefono_encargado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
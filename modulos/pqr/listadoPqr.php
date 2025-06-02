<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap" id="cuerpo-listado-pqr">
    <div id="pqr-frm-responder"></div>

     <?php barraNavegacion('pqr', $total_pages, $current_page); ?>

    <table id="pqr-table" class="wp-list-table widefat  striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Solicitante</th>
                <th>Contenido</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($lista_pqr)): ?>
                <tr>
                    <td colspan="4"><?php esc_html_e('No hay PQR registradas.', 'bomberos-servicios'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($lista_pqr as $pqr): ?>
                    <tr id="pqr-row-<?php echo esc_attr($pqr['id']); ?>">
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', strtotime($pqr['fecha_registro']))); ?></td>
                        <td>
                            <?php echo esc_html($pqr['nombre']); ?><br>
                            <strong>Email:</strong> <?php echo esc_html($pqr['email']); ?><br>
                            <strong>Telefono:</strong> <?php echo esc_html($pqr['telefono']); ?>
                        </td>
                        <td>
                            <strong><?php echo esc_html($pqr['tipo_solicitud']); ?></strong> <br>
                            <strong>Estado actual:</strong> <?php echo esc_html($pqr['estado_solicitud'] ); ?><hr>
                            <strong>Solicitud:</strong> <?php echo nl2br(esc_html($pqr['contenido'])); ?><hr>
                            <strong>Respuesta:</strong> <?php echo nl2br(esc_html($pqr['respuesta'] ?? 'Sin respuesta')); ?><hr>  
                        </td>
                        <td>
                                <button class="button editar-pqr"
                                    data-id="<?php echo esc_attr($pqr['id']); ?>"
                                    data-paged="<?php echo esc_attr($current_page); ?>">Editar</button>
                            <button class="button delete-pqr"
                                data-id="<?php echo esc_attr($pqr['id']); ?>"
                                data-paged="<?php echo esc_attr($current_page); ?>">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php barraNavegacion('pqr', $total_pages, $current_page, 'right'); ?>
</div>
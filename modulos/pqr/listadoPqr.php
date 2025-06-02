<div class="wrap" id="cuerpo-listado-pqr">
    <div id="pqr-frm-responder"></div>

     <?php barraNavegacion('pqr', $totalpaginas, $actualpagina); ?>

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
            <?php foreach ($listaPqrs as $pqr): ?>
                <tr id="pqr-row-<?php echo esc_attr($pqr['id']); ?>">
                    <td><?php echo esc_html($pqr['fecha_registro']); ?></td>
                    <td>
                        <?php echo esc_html($pqr['nombre']); ?><br>
                        <strong>Email:</strong><?php echo esc_html($pqr['email']); ?><br>
                        <strong>Telefono:</strong> <?php echo esc_html($pqr['telefono']); ?>
                    </td>
                    <td>
                        <strong><?php echo esc_html($pqr['tipo_solicitud']); ?></strong> <br>
                        <strong>Estado actual:</strong> <?php echo esc_html($pqr['estado_solicitud'] ); ?><hr>
                        <strong>Solicitud:</strong> <?php echo esc_html($pqr['contenido']); ?><hr>
                        <strong>Respuesta:</strong> <?php echo esc_html($pqr['respuesta']); ?><hr>  
                    </td>
                    <td>
                            <button class="button editar-pqr"
                                data-id="<?php echo esc_attr($pqr['id']); ?>"
                                data-actualpagina="<?php echo esc_attr($actualpagina); ?>">Editar</button>
                        <button class="button delete-pqr"
                            data-id="<?php echo esc_attr($pqr['id']); ?>"
                            data-actualpagina="<?php echo esc_attr($actualpagina); ?>">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php barraNavegacion('pqr', $totalpaginas, $actualpagina, 'right'); ?>
</div>

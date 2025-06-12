<?php
if (!defined('ABSPATH'))   exit;
?>
<div class="wrap" id="cuerpo-listado-inspecciones">
    <div id="inspeccion-frm-editar"></div>
    <?php barraNavegacion('inspecciones',$totalpaginas, $actualpagina); ?>

    <table id="inspeccione-table" class="wp-list-table widefat striped">
        <thead>
            <tr>
                
                <th>Empresa</th>
                <th>Estado</th>
                <th>Persona Encargada</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaInspecciones as $inspeccion): ?>
                <tr id="inspeccion-row-<?php echo esc_attr($inspeccion['id_inspeccion']); ?>">
                    <td><?php echo esc_html($inspeccion['razon_social'] ?? 'N/A'); ?> <br>
                        Direccion: <?php echo esc_html($inspeccion['direccion'] ?? 'N/A'); ?> <br>
                        Barrio: <?php echo esc_html($inspeccion['barrio'] ?? 'N/A'); ?>
                    </td>
                    <td><strong><?php echo esc_html($inspeccion['estado']); ?></strong><br>
                        Registro: <?php echo esc_html($inspeccion['fecha_registro']); ?><br>
                        Programada: <?php echo esc_html($inspeccion['fecha_programada'] ?? 'N/P'); ?><br>
                        Inspector: <?php echo esc_html($inspeccion['nombre_bombero_asignado'] ?? 'N/P'); ?><br>
                        Certificación: <?php echo esc_html($inspeccion['fecha_expedicion'] ?? 'N/A'); ?>
                         
                    </td>
                    <td>
                        <strong>Encargado:</strong> <?php echo esc_html($inspeccion['nombre_encargado']); ?><br>
                         Tel: <?php echo esc_html($inspeccion['telefono_encargado']); ?><br><hr>
                       
                         
                    </td>
                    <td>
                        <button class="button editar-inspeccion" data-id="<?php echo esc_attr($inspeccion['id_inspeccion']); ?>" data-actualpagina="<?php echo esc_html($actualpagina); ?>">
                            Editar
                        </button>
                        <button class="button eliminar-inspeccion" data-id="<?php echo esc_attr($inspeccion['id_inspeccion']); ?>" data-actualpagina="<?php echo esc_html($actualpagina); ?>">
                            Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php barraNavegacion('inspecciones',$totalpaginas, $actualpagina, 'right'); ?>
</div>


<div class="wrap" id="cuerpo-listado-empresas">
    <div id="empresa-frm-editar"></div>
    <div style="margin-bottom: 1em;">
        <button class="button button-primary" id="btn-agregar-empresa">Agregar nueva empresa</button>
    </div>
    <?php barraNavegacion('empresas',$totalpaginas, $actualpagina); ?>

    <table id="empresa-table" class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th>NIT / Razón Social</th>
                <th>Ubicación</th>
                <th>Representante Legal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaEmpresas as $empresa): ?>
                <tr id="empresa-row-<?php echo esc_attr($empresa['id_empresa']); ?>">
                    <td>
                        <?php echo esc_html($empresa['razon_social']); ?><br>
                        <strong>NIT: <?php echo esc_html($empresa['nit']); ?></strong>                        
                    </td>
                    <td>
                        <?php echo esc_html($empresa['direccion']); ?><br>
                        Barrio: <?php echo esc_html($empresa['barrio']); ?>
                    </td>
                    <td><?php echo esc_html($empresa['representante_legal']); ?><br>
                        <?php echo esc_html($empresa['email']); ?></td>
                    <td>
                        <button class="button editar-empresa"
                            data-id="<?php echo esc_attr($empresa['id_empresa']);?>" data-actualpagina="<?php echo $actualpagina; ?>">Editar</button>
                        <button class="button delete-empresa"
                            data-id="<?php echo esc_attr($empresa['id_empresa']); ?>" data-actualpagina="<?php echo $actualpagina; ?>">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php barraNavegacion('empresas',$totalpaginas, $actualpagina, 'right'); ?>
</div>
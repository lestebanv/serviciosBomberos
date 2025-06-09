

<div class="wrap" id="cuerpo-listado-bomberos">
    <div id="bombero-frm-editar"></div>
    <div style="margin-bottom: 1em;">
        <button class="button button-primary" id="btn-agregar-bombero">Agregar nueva bombero</button>
    </div>
    <?php barraNavegacion('bomberos',$totalpaginas, $actualpagina); ?>

    <table id="bombero-table" class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th>Datos  Personales</th>
                <th>Datos de Contacto</th>
                <th>Vinculación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaBomberos as $bombero): ?>
                <tr id="bombero-row-<?php echo esc_attr($bombero['id_bombero']); ?>">
                    <td>
                        <?php echo esc_html($bombero['apellidos'].', '.$bombero['nombres']); ?><br>
                         <?php echo esc_html($bombero['tipo_documento'].'No. '.$bombero['numero_documento']); ?><br>
                          Genero: <?php echo esc_html($bombero['genero']); ?><br>
                          Fecha de nacimiento:<?php echo esc_html($bombero['fecha_nacimiento']); ?><br>
                         RH:<strong> <?php echo esc_html(' '.$bombero['grupo_sanguineo'].$bombero['rh']); ?></strong><br>                    
                    </td>
                    
                    <td>
                        Direccion:<?php echo esc_html($bombero['direccion']); ?><br>
                       Telefono: <?php echo esc_html($bombero['telefono']); ?><br>
                       Email:<?php echo esc_html($bombero['email']); ?><br>
                    </td>
                        <td>
                    Fecha ingreso: <?php echo esc_html($bombero['fecha_ingreso']); ?><br>
                     Rango: <?php echo esc_html($bombero['rango']); ?><br>
                     Estado:<?php echo esc_html($bombero['estado']); ?><br>
                     
                       Observaciones:<?php echo esc_html($bombero['observaciones']); ?><br>
                     </td>
                       
                     
                      
                    <td>
                        <button class="button editar-bombero"
                            data-id="<?php echo esc_attr($bombero['id_bombero']);?>" data-actualpagina="<?php echo $actualpagina; ?>">Editar</button>
                        <button class="button delete-bombero"
                            data-id="<?php echo esc_attr($bombero['id_bombero']); ?>" data-actualpagina="<?php echo $actualpagina; ?>">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php barraNavegacion('bomberos',$totalpaginas, $actualpagina, 'right'); ?>
</div>
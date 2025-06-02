<?php

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h2>Datos de la Empresa con NIT <?php echo esc_html($empresa['nit']); ?></h2>
    <hr>
    <form id="form-editar-empresa" method="post" class="bomberos-form">
        <input type="hidden" name="id_empresa" value="<?php echo esc_attr($empresa['id_empresa']); ?>">
        <input type="hidden" name="actualpagina" value="<?php echo esc_attr($actualpagina); ?>">
        <table class="form-table">
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="razon_social"><?php esc_html_e('Razón Social', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="text" name="razon_social" id="razon_social" class="regular-text" value="<?php echo esc_attr($empresa['razon_social']); ?>" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="representante_legal"><?php esc_html_e('Representante Legal', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="text" name="representante_legal" id="representante_legal" class="regular-text" value="<?php echo esc_attr($empresa['representante_legal']); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="direccion"><?php esc_html_e('Dirección', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="text" name="direccion" id="direccion" class="regular-text" value="<?php echo esc_attr($empresa['direccion']); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="barrio"><?php esc_html_e('Barrio', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="text" name="barrio" id="barrio" class="regular-text" value="<?php echo esc_attr($empresa['barrio']); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="email"><?php esc_html_e('Email', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="email" name="email" id="email" class="regular-text" value="<?php echo esc_attr($empresa['email']); ?>">
                </td>
            </tr>
           
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary"><?php esc_html_e('Guardar Cambios', 'bomberos-servicios'); ?></button>
            <button type="button" class="button button-secondary cancelar-edicion-empresa"  data-actualpagina="<?php echo esc_attr($actualpagina); ?>">Cancelar</button>
        </p>
        <div id="mensaje-editar-empresa" class="notice" style="display: none;"></div>
    </form>
    <?php if (!$listaInspecciones):?>
           <hr> Aun no hay Inspecciones registradas para esta empresas <hr>
    <?php else:?>
    <hr> Inspecciones Solicitadas por esta empresa<hr>
    <table class="wp-list-table widefat striped">
    <thead>
        <tr>
            <th scope="col">No</th>
            <th scope="col">Persona Encargada</th>
            <th scope="col">Fecha Registrada</th>
            <th scope="col">Fecha Programada</th>
            <th scope="col">Fecha Expedicion certificación</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $i=1;
            foreach ($listaInspecciones as $inspeccion):
        ?>
        <tr>
            <td><?php echo esc_html($i); ?></td>
            <td><?php echo esc_html($inspeccion['nombre_encargado']); ?><br>
                <?php echo esc_html($inspeccion['telefono_encargado']); ?>
            </td>
            <td><?php echo esc_html($inspeccion['fecha_registro']); ?></td>
            <td><?php echo esc_html($inspeccion['fecha_programada']); ?></td>
            <td><?php echo esc_html($inspeccion['fecha_expedicion']); ?></td>
        </tr>
        <?php $i=$i+1;
              endforeach; ?>
    </tbody>
</table>
<?php endif;?>
</div>
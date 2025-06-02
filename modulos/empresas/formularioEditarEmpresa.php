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
        <input type="hidden" name="paged" value="<?php echo esc_attr($paged); ?>">
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
            <button type="button" class="button button-secondary cancelar-edicion-empresa"  data-paged="<?php echo esc_attr($paged); ?>">Cancelar</button>
        </p>
        <div id="mensaje-editar-empresa" class="notice" style="display: none;"></div>
    </form>
    <hr>

    <?php if (!empty($inspecciones_empresa)): ?>
        <h3><?php esc_html_e('Inspecciones Solicitadas por esta Empresa', 'bomberos-servicios'); ?></h3>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('ID Inspección', 'bomberos-servicios'); ?></th>
                    <th><?php esc_html_e('Fecha Registro', 'bomberos-servicios'); ?></th>
                    <th><?php esc_html_e('Fecha Programada', 'bomberos-servicios'); ?></th>
                    <th><?php esc_html_e('Estado', 'bomberos-servicios'); ?></th>
                    <th><?php esc_html_e('Encargado', 'bomberos-servicios'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inspecciones_empresa as $inspeccion): ?>
                    <tr>
                        <td><?php echo esc_html($inspeccion['id_inspeccion']); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($inspeccion['fecha_registro']))); ?></td>
                        <td><?php echo esc_html($inspeccion['fecha_programada'] ? date_i18n(get_option('date_format'), strtotime($inspeccion['fecha_programada'])) : 'N/A'); ?></td>
                        <td><?php echo esc_html($inspeccion['estado']); ?></td>
                        <td><?php echo esc_html($inspeccion['nombre_encargado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><?php esc_html_e('Esta empresa no tiene inspecciones registradas.', 'bomberos-servicios'); ?></p>
    <?php endif; ?>
    <hr>
</div>
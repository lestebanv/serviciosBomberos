<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Editar solicitud de inspección de la empresa <?php echo esc_html($inspeccion['razon_social']); ?></h1>

    <form id="form-editar-inspeccion" method="post">
        <input type="hidden" name="id_inspeccion" value="<?php echo esc_attr($inspeccion['id_inspeccion']); ?>">
        <input type="hidden" name="paged" value="<?php echo esc_attr($paged); ?>">
        <h2>Detalles de la empresa:</h2>
        <strong>NIT: </strong> <?php echo esc_html($inspeccion['nit']); ?><br>
        <strong>Dirección: </strong><?php echo esc_html($inspeccion['direccion']); ?><br>
        <strong>Barrio: <?php echo esc_html($inspeccion['barrio']); ?></br>

        <h2>Detalles de la inspección:</h2>
        <table class="form-table">
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="nombre_encargado">Nombre del encargado de atender la inspección</label>
                </th>
                <td>
                    <input type="text" name="nombre_encargado" id="nombre_encargado" class="regular-text" value="<?php echo esc_attr($inspeccion['nombre_encargado']); ?>" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="telefono_encargado"><?php esc_html_e('Teléfono del Encargado', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="text" name="telefono_encargado" id="telefono_encargado" class="regular-text" value="<?php echo esc_attr($inspeccion['telefono_encargado']); ?>" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="fecha_programada">Fecha Programada</label>
                </th>
                <td>
                    <input type="date" name="fecha_programada" id="fecha_programada" value="<?php echo esc_attr($inspeccion['fecha_programada'] ?? ''); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="fecha_expedicion">Fecha de Certificación</label>
                </th>
                <td>
                    <input type="date" name="fecha_expedicion" id="fecha_expedicion" value="<?php echo esc_attr($inspeccion['fecha_expedicion'] ?? ''); ?>">
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="estado">Estado</label>
                </th>
                <td>
                    <select name="estado" id="estado" class="regular-text" required aria-required="true">
                        <option value="Registrada" <?php selected($inspeccion['estado'], 'Registrada'); ?>><?php esc_html_e('Registrada', 'bomberos-servicios'); ?></option>
                        <option value="En Proceso" <?php selected($inspeccion['estado'], 'En Proceso'); ?>><?php esc_html_e('En Proceso', 'bomberos-servicios'); ?></option>
                        <option value="Cerrada" <?php selected($inspeccion['estado'], 'Cerrada'); ?>><?php esc_html_e('Cerrada', 'bomberos-servicios'); ?></option>
                    </select>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" class="button button-primary" value="<?php esc_attr_e('Guardar Cambios', 'bomberos-servicios'); ?>">
            <input type="button" class="button button-secondary cancelar-edicion-inspeccion" data-paged="<?php esc_attr($paged); ?>" value="Cancelar">
        </p>
        <div id="mensaje-editar-inspeccion" class="notice hidden"></div>
    </form>
</div>
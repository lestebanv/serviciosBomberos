<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h2>Editar  <?php echo esc_attr($pqr['tipo_solicitud']); ?> de <?php echo esc_attr($pqr['nombre']); ?></h2><br>
        Telefono: <?php echo esc_attr($pqr['telefono']); ?><br>
        Email: <?php echo esc_attr($pqr['email']); ?><br>
        <strong><?php echo esc_attr($pqr['tipo_solicitud']); ?></strong>:<br>
         <?php echo esc_attr($pqr['contenido']); ?><br>
    <hr>
    <form id="form-editar-pqr" method="post">
        <input type="hidden" name="id" value="<?php echo esc_attr($pqr['id']); ?>">
        <input type="hidden" name="actualpagina" value="<?php echo esc_attr($actualpagina); ?>">

        <table class="form-table">
            <tr class="form-field">
                <th scope="row">
                    <label for="estado_solicitud">Estado de la Solicitud</label>
                </th>
                <td>
                    <select name="estado_solicitud" id="estado_solicitud">
                        
                     <?php foreach ($estado_solicitudValidos as $estado_solicitud): ?>
                             <option value="<?php echo $estado_solicitud;?>" <?php selected($pqr['estado_solicitud'],$estado_solicitud); ?>> <?php echo $estado_solicitud;?> </option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="respuesta">Respuesta</label>
                </th>
                <td>
                    <textarea name="respuesta" id="respuesta" class="large-text" rows="5"><?php echo esc_textarea($pqr['respuesta']); ?></textarea>
                </td>
            </tr>
            
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">Guardar Cambios</button>
            <button type="button" class="button cancelar-edicion-pqr" data-actualpagina="<?php echo esc_attr($actualpagina); ?>">Cancelar</button>
        </p>
    </form>
</div>

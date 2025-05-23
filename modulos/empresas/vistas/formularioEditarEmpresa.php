<?php if (!defined('ABSPATH')) exit; ?>
<p> Datos de la empresa con NIT <?php echo esc_attr($empresa['nit']); ?><p>
    <hr>
<form id="form-editar-empresa">
    <input type="hidden" name="id_empresa" value="<?php echo esc_attr($empresa['id_empresa']); ?>">
    <div class="form-group">
        <label for="razon_social">Razon Social</label>
        <input type="text" name="razon_social" id="razon_social" class="regular-text" value="<?php echo esc_attr($empresa['razon_social']); ?>" required>
    </div>
    <div class="form-group">
        <label for="representante_legal">Representante Legal</label>
        <input type="text" name="representante_legal" id="representante_legal" class="regular-text" value="<?php echo esc_attr($empresa['representante_legal']); ?>">
    </div>
    <div class="form-group">
        <label for="direccion">Dirección</label>
        <input type="text" name="direccion" id="direccion" class="regular-text" value="<?php echo esc_attr($empresa['direccion']); ?>">
    </div>
    <div class="form-group">
        <label for="barrio">Barrio</label>
        <input type="text" name="barrio" id="barrio" class="regular-text" value="<?php echo esc_attr($empresa['barrio']); ?>">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="text" name="email" id="email" class="regular-text" value="<?php echo esc_attr($empresa['email']); ?>">
    </div>
   

    <div class="form-group">
        <button type="submit" class="button button-primary">Guardar cambios</button>
        <button type="button" class="button cancelar-edicion-empresa">Cancelar</button>
    </div>

    <div id="mensaje-editar-empresa"></div>
</form>
<hr>
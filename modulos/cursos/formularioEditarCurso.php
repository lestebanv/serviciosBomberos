<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h2>Editar Curso - <?php echo esc_html($curso['nombre_curso']); ?></h2>
    <hr>
    <form id="form-editar-curso" method="post" class="bomberos-form">
        <input type="hidden" name="id_curso" value="<?php echo esc_attr($curso['id_curso']); ?>">
        <input type="hidden" name="actualpagina" value="<?php echo esc_attr($actualpagina); ?>">
        <table class="form-table">
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="nombre_curso">Nombre del curso</label>
                </th>
                <td>
                    <input type="text" name="nombre_curso" id="nombre_curso" class="regular-text" value="<?php echo esc_attr($curso['nombre_curso']); ?>" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="descripcion">Descripción</label>
                </th>
                <td>
                    <textarea name="descripcion" id="descripcion" class="regular-text" rows="5"><?php echo esc_textarea($curso['descripcion']); ?></textarea>
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="fecha_inicio">Fecha de Inicio</label>
                </th>
                <td>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?php echo esc_attr($curso['fecha_inicio']); ?>" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="duracion_horas">Duración (Horas)</label>
                </th>
                <td>
                    <input type="number" name="duracion_horas" id="duracion_horas" min="1" value="<?php echo esc_attr($curso['duracion_horas'] ?? ''); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="instructor">Instructor</label>
                </th>
                <td>
                    <input type="text" name="instructor" id="instructor" class="regular-text" value="<?php echo esc_attr($curso['instructor'] ?? ''); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="lugar">Lugar</label>
                </th>
                <td>
                    <input type="text" name="lugar" id="lugar" class="regular-text" value="<?php echo esc_attr($curso['lugar'] ?? ''); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="capacidad_maxima">Capacidad Máxima</label>
                </th>
                <td>
                    <input type="number" name="capacidad_maxima" id="capacidad_maxima" min="1" value="<?php echo esc_attr($curso['capacidad_maxima'] ?? ''); ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="estado">Estado</label>
                </th>
                <td>
                    <select name="estado" id="estado" class="regular-text" required aria-required="true">
                       <?php foreach ($estadosValidos as $estado): ?>
                        <option value="<?php echo $estado;?>" <?php selected($curso['estado'], $estado); ?>><?php echo $estado;?></option>
                     <?php endforeach; ?> 
                    
                    </select>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary" >Guardar Cambios</button>
            <button type="button" class="button button-secondary cancelar-edicion-curso" data-actualpagina="<?php echo esc_attr($actualpagina); ?>">Cancelar</button>
        </p>
    </form>

    <?php if (!$listaInscripciones):?>
           <hr> Aun no hay Inscritos en este curso <hr>
    <?php else:?>
    <hr> Inscritos en este curso<hr>
    <table class="wp-list-table widefat striped">
    <thead>
        <tr>
            <th scope="col">No</th>
            <th scope="col">Nombre</th>
            <th scope="col">Telefono</th>
            <th scope="col">email</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $i=1;
            foreach ($listaInscripciones as $persona):
        ?>
        <tr>
            <td><?php echo esc_html($i); ?></td>
            <td><?php echo esc_html($persona['nombre_asistente']); ?></td>
            <td><?php echo esc_html($persona['telefono_asistente']); ?></td>
            <td><?php echo esc_html($persona['email_asistente']); ?></td>
        </tr>
        <?php $i=$i+1;
              endforeach; ?>
    </tbody>
</table>
<?php endif;?>
 
</div>
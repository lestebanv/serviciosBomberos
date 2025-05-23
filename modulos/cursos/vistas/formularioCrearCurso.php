<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h2><?php esc_html_e('Crear Nuevo Curso', 'bomberos-servicios'); ?></h2>
    <hr>
    <form id="form-crear-curso" method="post" class="bomberos-form">
        <table class="form-table">
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="nombre_curso"><?php esc_html_e('Nombre del Curso', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="text" name="nombre_curso" id="nombre_curso" class="regular-text" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="descripcion"><?php esc_html_e('Descripción', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <textarea name="descripcion" id="descripcion" class="regular-text" rows="5"></textarea>
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="fecha_inicio"><?php esc_html_e('Fecha de Inicio', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="duracion_horas"><?php esc_html_e('Duración (Horas)', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="number" name="duracion_horas" id="duracion_horas" min="1">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="instructor"><?php esc_html_e('Instructor', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="text" name="instructor" id="instructor" class="regular-text">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="lugar"><?php esc_html_e('Lugar', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="text" name="lugar" id="lugar" class="regular-text">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="capacidad_maxima"><?php esc_html_e('Capacidad Máxima', 'bomberos-servicios'); ?></label>
                </th>
                <td>
                    <input type="number" name="capacidad_maxima" id="capacidad_maxima" min="1">
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary"><?php esc_html_e('Crear Curso', 'bomberos-servicios'); ?></button>
            <button type="button" class="button button-secondary cancelar-creacion-curso"><?php esc_html_e('Cancelar', 'bomberos-servicios'); ?></button>
        </p>
        <div id="mensaje-crear-curso" class="notice" style="display: none;"></div>
    </form>
    <hr>
</div>
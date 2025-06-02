<?php
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="wrap" id="cuerpo-listado-cursos">
    <div id="curso-frm-editar"></div>
    <div style="margin-bottom: 1em;">
        <button class="button button-primary" id="btn-agregar-curso"><?php esc_html_e('Agregar nuevo curso', 'bomberos-servicios'); ?></button>
    </div>
    <?php barraNavegacion('cursos',$totalpaginas, $actualpagina); ?>

    <table id="curso-table" class="wp-list-table widefat  striped">
        <thead>
            <tr>
                <th>Nombre del Curso</th>
                <th>Fecha de Inicio</th>
                <th>Duración (Horas)</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaCursos as $curso): ?>
                <tr id="curso-row-<?php echo esc_attr($curso['id_curso']); ?>">
                    <td><?php echo esc_html($curso['nombre_curso']); ?></td>
                    <td><?php echo esc_html($curso['fecha_inicio']); ?></td>
                    <td><?php echo esc_html($curso['duracion_horas'] ?? 'N/A'); ?></td>
                    <td><?php echo esc_html($curso['estado']); ?></td>
                    <td>
                        <button class="button editar-curso" data-id="<?php echo esc_attr($curso['id_curso']); ?>" data-actualpagina="<?php echo $actualpagina; ?>">
                            Editar
                        </button>
                        <button class="button delete-curso" data-id="<?php echo esc_attr($curso['id_curso']); ?>" data-actualpagina="<?php echo $actualpagina; ?>">
                            Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php barraNavegacion('cursos',$totalpaginas, $actualpagina, 'right'); ?>
</div>
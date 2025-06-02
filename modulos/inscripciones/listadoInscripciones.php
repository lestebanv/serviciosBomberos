<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap" id="cuerpo-listado-inscripciones-admin">
    <h2>Gestión de Inscripciones a Cursos</h2>
    <?php  barraNavegacion('inscripciones', $totalpaginas, $actualpagina); ?>
    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th>Curso</th>
                <th>Asistente</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
                <?php foreach ($listaInscripciones as $inscripcion): ?>
                    <tr id="inscripcion-row-<?php echo esc_attr($inscripcion['id_inscripcion']); ?>">
                        <td><?php echo esc_html($inscripcion['nombre_curso']); ?></td>
                        
                        <td><strong><?php echo esc_html($inscripcion['estado_inscripcion']); ?></strong><br>    
                            <?php echo esc_html($inscripcion['nombre_asistente']); ?><br>
                            <?php echo esc_html($inscripcion['email_asistente']); ?><hr>
                            Fecha Inscripcion: <?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', strtotime($inscripcion['fecha_inscripcion']))); ?>
                        </td>

                        <td><?php echo esc_html($inscripcion['notas']); ?></td>
                        <td>
                            <button class="button editar-inscripcion" 
                                    data-id="<?php echo esc_attr($inscripcion['id_inscripcion']); ?>"
                                    data-actualpagina="<?php echo esc_attr($actualpagina); ?>">
                                Editar
                            </button>
                            <button class="button delete-inscripcion" 
                                    data-id="<?php echo esc_attr($inscripcion['id_inscripcion']); ?>"
                                    data-actualpagina="<?php echo esc_attr($actualpagina); ?>">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
        </tbody>
    </table>
    <?php  barraNavegacion('inscripciones', $totalpaginas, $actualpagina, 'right'); ?>
</div>


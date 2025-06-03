<p> La siguiente <strong> <?php echo esc_html($objpqr['tipo_solicitud']);?> </strong>
 fue registrada con fecha <strong><?php echo esc_html($objpqr['fecha_registro']);?></strong></p>
<table>
    <tr>
        <th>Nombre:</th>
        <td><?php echo esc_html($objpqr['nombre']);?></td>
    </tr>
    <tr>
        <th>Telefono:</th>
        <td><?php echo  esc_html($objpqr['telefono']);?></td>
    </tr>
    <tr>
            <th>Correo:</th>
        <td><?php echo  esc_html($objpqr['email']);?></td>
    </tr>
    <tr>
        <th>Contenido:</th>
        <td><?php echo esc_html($objpqr['contenido']);?></td>
    </tr>
</table>
<hr>
<p>Estaremos prestos a responder al correo <?php echo  esc_html($objpqr['email']);?> </p>
<hr>
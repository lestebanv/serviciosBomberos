<div class="bomberos-contenido" id="empresas-contenido">
<h2>Lista de Empresas</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>NIT</th>
                <th>Razón Social</th>
                <th>Dirección</th>
                <th>Barrio</th>
                <th>Representante Legal</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($empresas as $empresa) : ?>
                <tr>
                    <td><?php echo esc_html($empresa['nit']); ?></td>
                    <td><?php echo esc_html($empresa['razon_social']); ?></td>
                    <td><?php echo esc_html($empresa['direccion']); ?></td>
                    <td><?php echo esc_html($empresa['barrio']); ?></td>
                    <td><?php echo esc_html($empresa['representante_legal']); ?></td>
                    <td><?php echo esc_html($empresa['email']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>         
</div>
<div class="wrap">
    <h1>Su solicitud de inspeccion se ha registrado con fecha <?php echo esc_html($resultado['fecha_registro']); ?></h1>

    <?php if (!empty($resultado)) : ?>
        <h2>Empresa</h2>
        <ul>
            <li><strong>NIT:</strong> <?php echo esc_html($resultado['nit']); ?></li>
            <li><strong>Razón Social:</strong> <?php echo esc_html($resultado['razon_social']); ?></li>
            <li><strong>Dirección:</strong> <?php echo esc_html($resultado['direccion']); ?></li>
            <li><strong>Barrio:</strong> <?php echo esc_html($resultado['barrio']); ?></li>
            <li><strong>Representante Legal:</strong> <?php echo esc_html($resultado['representante_legal']); ?></li>
            <li><strong>Email:</strong> <?php echo esc_html($resultado['email']); ?></li>
        </ul>

        <h2>Inspección Pendiente de programar</h2>
        <ul>
            <li><strong>Encargado de la Empresa:</strong> <?php echo esc_html($resultado['nombre_encargado']); ?></li>
            <li><strong>Teléfono Encargado:</strong> <?php echo esc_html($resultado['telefono_encargado']); ?></li>
        </ul>
        <p> Pronto estaremos comunicandonos para acordar fecha de visita
    <?php else : ?>
        <p>No se pudo registrar su solicitud ... agradecemos .</p>
    <?php endif; ?>
</div>
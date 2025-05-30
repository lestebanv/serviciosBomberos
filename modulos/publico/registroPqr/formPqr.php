<div id="pqr-registro-form" class="wrap">
    <h2>Registre aquí su petición, queja, reclamo, felicitación o sugerencia:</h2>

    <div id="pqr-response" class="notice" style="display:none;"></div>

    <form id="pqr-form" method="post">
        <?php wp_nonce_field('pqr_form_action', 'pqr_nonce'); ?>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="pqr-nombre">Nombre</label></th>
                <td><input type="text" id="pqr-nombre" name="nombre" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="pqr-telefono">Teléfono</label></th>
                <td><input type="text" id="pqr-telefono" name="telefono" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="pqr-email">Email</label></th>
                <td><input type="email" id="pqr-email" name="email" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="pqr-tipo_solicitud">Tipo de Solicitud</label></th>
                <td>
                    <select id="pqr-tipo_solicitud" name="tipo_solicitud" required>
                        <option value="">Seleccione...</option>
                        <option value="Peticion">Petición</option>
                        <option value="Queja">Queja</option>
                        <option value="Reclamo">Reclamo</option>
                        <option value="Sugerencia">Sugerencia</option>
                        <option value="Felicitacion">Felicitación</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pqr-contenido">Contenido</label></th>
                <td><textarea id="pqr-contenido" name="contenido" required rows="5" class="large-text"></textarea></td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">Enviar</button>
        </p>
    </form>
</div>

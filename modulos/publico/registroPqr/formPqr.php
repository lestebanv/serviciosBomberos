<div id="pqr-registro-form" class="wrap">
    <h2>Registre aquí su petición, queja, reclamo, felicitación o sugerencia:</h2>
    <form id="pqr-form" method="post">
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
                       <?php foreach($tipo_solicitudValidos as $tiposolicitud): ?>
                        <option value="<?php echo $tiposolicitud?>"><?php echo $tiposolicitud?></option>
                       <?php endforeach; ?>
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

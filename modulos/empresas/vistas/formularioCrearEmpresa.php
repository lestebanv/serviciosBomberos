<div class="wrap">
    <h2>Registrar Nueva Empresa</h2>

    <div id="mensaje-empresa"></div>

    <form id="form-crear-empresa" method="post">
        <table class="form-table">
            <tr>
                <th><label for="nit">NIT</label></th>
                <td><input type="text" id="nit" name="nit" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="razon_social">Razón Social</label></th>
                <td><input type="text" id="razon_social" name="razon_social" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="direccion">Dirección</label></th>
                <td><input type="text" id="direccion" name="direccion" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="barrio">Barrio</label></th>
                <td><input type="text" id="barrio" name="barrio" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="representante_legal">Representante Legal</label></th>
                <td><input type="text" id="representante_legal" name="representante_legal" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="email">Email</label></th>
                <td><input type="email" id="email" name="email" class="regular-text" required></td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">Guardar Empresa</button>
            <button type="button" class="button cancelar-edicion-empresa">Cancelar</button>
            
        </p>
    </form>
    <hr>
</div>

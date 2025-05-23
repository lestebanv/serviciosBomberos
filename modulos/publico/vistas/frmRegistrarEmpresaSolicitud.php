
<div>
    <p>Su Empresa aun no esta registrada en nuestro sistema <br>
    Favor diligenciar la siguiente información:</p>
    <form id="frm_bomberos_empresa_completa">
        <input type="hidden" name="nit" id="nit" value="<?php echo $nit; ?>"  />
        <table class="form-table">

            <tr class="form-field form-required">
                <th scope="row"><label for="razon_social">Razón Social</label></th>
                <td><input type="text" name="razon_social" id="razon_social" required /><br>
                    NIT: <?php echo $nit; ?>
            </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row"><label for="direccion">Dirección</label></th>
                <td><input type="text" name="direccion" id="direccion" required /></td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row"><label for="barrio">Barrio</label></th>
                <td><input type="text" name="barrio" id="barrio" required /></td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row"><label for="representante_legal">Representante Legal</label></th>
                <td><input type="text" name="representante_legal" id="representante_legal" required /></td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row"><label for="email">Email</label></th>
                <td><input type="email" name="email" id="email" required /></td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row"><label for="nombre_encargado">Nombre persona encargada <br> de atender la inspeccion</label></th>
                <td><input type="text" name="nombre_encargado" id="nombre_encargado" required /></td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row"><label for="telefono_encargado">Telefono persona encargada</label></th>
                <td><input type="text" name="telefono_encargado" id="telefono_encargado" required /></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button button-primary" value="Registrar Solicitud de Inspección" />
        </p>
    </form>
</div>
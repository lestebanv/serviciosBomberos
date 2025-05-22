
<div>
   <p> La empresa con NIT: <?php echo $nit; ?> ya esta registrada <br> 
   <?php 
         echo 'Razón social: ' . $empresa['razon_social'].'<br>';
         echo 'Dirección: ' . $empresa['direccion'].'<br>';
         echo 'Barrio: ' . $empresa['Barrio'].'<br>'
    ?>
    </p> Favor diligenciar los datos de la solicitud de inspección
    <form id="frm_bomberos_empresa_completa">
        <input type="hidden" name="id_empresa" id="id_empresa" value="<?php echo $empresa['id_empresa']; ?>">
        <table class="form-table">
            <tr class="form-field form-required">
                <th scope="row">Razon Social</th>
                <td>aqui razon social</td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">Direccion</th>
                <td>aqui direccion</td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">Barrio</th>
                <td>aqui barrio</td>
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
            <input type="submit" class="button button-primary" value="Registrar Empresa" />
        </p>
    </form>
</div>
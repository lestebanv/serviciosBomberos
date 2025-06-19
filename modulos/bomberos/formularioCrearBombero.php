<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h2>Crear Nuevo bombero</h2>
    <hr>
    <form id="form-crear-bombero" method="post" class="bomberos-form">

        <table class="form-table">
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="nombres">Nombre</label>
                </th>
                <td>
                    <input type="text" name="nombres" id="nombres" class="regular-text" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="apellidos">Apellidos</label>
                </th>
                 <td>
                    <input type="text" name="apellidos" id="apellidos" class="regular-text" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="tipo_documento">Tipo Documento</label>
                </th>
                  <td>
                    <select name="tipo_documento" id="tipo_documento" class="regular-text" required aria-required="true">
                        <?php foreach($tipoDocumentoValidos as $documento): ?>
                        <option value="<?php echo $documento?>"><?php echo $documento?></option>
                       <?php endforeach; ?>
                       
                    </select>
                </td>
            </tr>
             <tr class="form-field form-required">
                <th scope="row">
                    <label for="numero_documento">Numero de Documento</label>
                </th>
                 <td>
                    <input type="text" name="numero_documento" id="numero_documento" class="regular-text" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="fecha_nacimiento">Fecha Nacimiento</label>
                </th>
                <td>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required aria-required="true">
                </td>
            </tr>

            <tr class="form-field form-required">
                        <th scope="row">
                            <label for="genero">Genero</label>
                        </th>
                          <td>
                    <select name="genero" id="genero" class="regular-text" required aria-required="true">
                        <?php foreach($generosValidos as $genero): ?>
                        <option value="<?php echo $genero; ?>"><?php echo $genero; ?></option>
                        <?php endforeach;?>
                        
                       
                    </select>
                </td>
                    </tr>

                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="direccion">Direccion</label>
                        </th>
                        <td>
                            <input type="text" name="direccion" id="direccion" class="regular-text" required aria-required="true">
                        </td>
                    </tr>

               <tr class="form-field form-required">
                        <th scope="row">
                            <label for="telefono">Telefono</label>
                        </th>
                        <td>
                            <input type="text" name="telefono" id="telefono" class="regular-text" required aria-required="true">
                        </td>
                    </tr>

                     <tr class="form-field form-required">
                        <th scope="row">
                            <label for="email">Email</label>
                        </th>
                        <td>
                            <input type="text" name="email" id="email" class="regular-text" required aria-required="true">
                        </td>
                    </tr>

               <tr class="form-field form-required">
                        <th scope="row">
                            <label for="grupo_sanguineo">Grupo Sanguineo</label>
                        </th>
                        <td>
                    <select name="grupo_sanguineo" id="grupo_sanguineo" class="regular-text" required aria-required="true">
                        <?php foreach ( $tiposSangreValidos as $genero):?>
                        <option value="<?php echo $genero; ?>"><?php echo $genero; ?></option>
                       <?php endforeach;?>
                       
                    </select>
                </td>
                    </tr> 
                    
                     


                     <tr class="form-field form-required">
                        <th scope="row">
                            <label for="rango">Rango</label>
                        </th>
                        <td>
                    <select name="rango" id="rango" class="regular-text" required aria-required="true">
                            <?php foreach ($rangoValidos as $rango): ?>
                             <option value="<?php echo $rango;?>" <?php selected($bombero['rango'],$rango); ?>> <?php echo $rango;?> </option>
                        <?php endforeach;?>
                    </select>
                </td>
                    </tr>  
                    
                      <tr class="form-field form-required">
                        <th scope="row">
                            <label for="estado">Estado</label>
                        </th>
                         <td>
                    <select name="estado" id="estado" class="regular-text" required aria-required="true">
                        <?php foreach($estadosValidos as $estado): ?>
                        <option value="<? echo $estado;?>"><?php echo $estado;?></option>
                        <?php endforeach;?>
                       
                    </select>
                </td>
                    </tr>       
       
         <tr class="form-field form-required">
                <th scope="row">
                    <label for="fecha_ingreso">Fecha Ingreso</label>
                </th>
                <td>
                    <input type="date" name="fecha_ingreso" id="fecha_ingreso" required aria-required="true">
                </td>
            </tr>
 <tr class="form-field form-required">
                        <th scope="row">
                            <label for="observaciones" >Observaciones</label>
                        </th>
                        <td>
                             <textarea name="observaciones" id="observaciones" class="regular-text" rows="5"></textarea>
                        </td>
                    </tr>    


            
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary">Registrar bombero</button>
            <button type="button" class="button button-secondary cancelar-creacion-bombero" data-actualpagina="1">Cancelar</button>
        </p>
        <div id="mensaje-crear-bombero" class="notice" style="display: none;"></div>
    </form>
    <hr>
</div>
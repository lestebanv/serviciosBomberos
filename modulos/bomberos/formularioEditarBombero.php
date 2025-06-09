<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h2>Editar  bombero</h2>
    <hr>
    <form id="form-editar-bombero" method="post" class="bomberos-form">
<input type="hidden" name="actualpagina" value="<?php echo  ($actualpagina);?>">
<input type="hidden" name="id_bombero" value="<?php echo($bombero['id_bombero']);?>">
        <table class="form-table">
            <tr class="form-field form-required">
                <th scope="row">
                    Documento:<?php echo($bombero['tipo_documento']);?><br>
                    No.<?php echo($bombero['numero_documento']);?><br>
                    <label for="nombres">Nombre</label>
                </th>
                <td>
                    <input type="text" name="nombres" id="nombres" class="regular-text" value="<?php echo($bombero['nombres']);?>" required aria-required="true">
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="apellidos">Apellidos</label>
                </th>
                 <td>
                    <input type="text" name="apellidos" id="apellidos" class="regular-text" value="<?php echo($bombero['apellidos']);?>" required aria-required="true">
                </td>
            </tr>
            
             
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="fecha_nacimiento">Fecha Nacimiento</label>
                </th>
                <td>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"  value="<?php echo($bombero['fecha_nacimiento']);?>" required aria-required="true">
                </td>
            </tr>

            <tr class="form-field form-required">
                        <th scope="row">
                            <label for="genero">Genero</label>
                        </th>
                          <td>
                    <select name="genero" id="genero" class="regular-text" value="<?php echo($bombero['genero']);?>"  required aria-required="true">
                        <option value="Masculino" <?php selected($bombero['genero'], 'Masculino'); ?>>Masculino</option>
                        <option value="Femenino" <?php selected($bombero['genero'], 'Femenino'); ?>>Femenino</option>
                        
                       
                    </select>
                </td>
                    </tr>

                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="direccion">Direccion</label>
                        </th>
                        <td>
                            <input type="text" name="direccion" id="direccion" class="regular-text" value="<?php echo($bombero['direccion']);?>" required aria-required="true">
                        </td>
                    </tr>

               <tr class="form-field form-required">
                        <th scope="row">
                            <label for="telefono">Telefono</label>
                        </th>
                        <td>
                            <input type="text" name="telefono" id="telefono" class="regular-text" value="<?php echo($bombero['telefono']);?>" required aria-required="true">
                        </td>
                    </tr>

                     <tr class="form-field form-required">
                        <th scope="row">
                            <label for="email">Email</label>
                        </th>
                        <td>
                            <input type="text" name="email" id="email" class="regular-text"  value="<?php echo($bombero['email']);?>" required aria-required="true">
                        </td>
                    </tr>

               <tr class="form-field form-required">
                        <th scope="row">
                            <label for="grupo_sanguineo">Grupo Sanguineo</label>
                        </th>
                        <td>
                    <select name="grupo_sanguineo" id="grupo_sanguineo" class="regular-text"  value="<?php echo($bombero['grupo_sanguineo']);?>" required aria-required="true">
                        <option value="A" <?php selected($bombero['grupo_sanguineo'], 'A'); ?>>A</option>
                        <option value="B" <?php selected($bombero['tipo_documento'], 'B'); ?>>B</option>
                        <option value="AB" <?php selected($bombero['tipo_documento'], 'AB'); ?>>AB</option>
                        <option value="O" <?php selected($bombero['tipo_documento'], 'O'); ?>>O</option>
                       
                    </select>
                </td>
                    </tr> 
                    
                     <tr class="form-field form-required">
                        <th scope="row">
                            <label for="rh">RH</label>
                        </th>
                        <td>
                    <select name="rh" id="rh" class="regular-text"  value="<?php echo($bombero['rh']);?>" required aria-required="true">
                        <option value="+" <?php selected($bombero['rh'], '+'); ?>>+</option>
                        <option value="-" <?php selected($bombero['rh'], '-'); ?>>-</option>
                        
                       
                    </select>
                </td>
                    </tr>  


                     <tr class="form-field form-required">
                        <th scope="row">
                            <label for="rango">Rango</label>
                        </th>
                        <td>
                    <select name="rango" id="rango" class="regular-text" value="<?php echo($bombero['rango']);?>" required aria-required="true">
                        <option value="Comandante_Bomberos" <?php selected($bombero['rango'], 'Comandante_Bomberos'); ?>>Comandante Bomberos</option>
                        <option value="Subcomandante_Bomberos" <?php selected($bombero['rango'], 'Subcomandante_Bomberos'); ?>>Subcomandante Bomberos</option>
                        <option value="Capitan_Bomberos" <?php selected($bombero['rango'], 'Capitan_Bomberos'); ?>>Capitán Bomberos</option>
                        <option value="Teniente_Bomberos" <?php selected($bombero['rango'], 'Teniente_Bomberos'); ?>>Teniente Bomberos</option>
                       
                    </select>
                </td>
                    </tr>  
                    
                      <tr class="form-field form-required">
                        <th scope="row">
                            <label for="estado">Estado</label>
                        </th>
                         <td>
                    <select name="estado" id="estado" class="regular-text" value="<?php echo($bombero['estado']);?>"  required aria-required="true">
                        <option value="soltero" <?php selected($bombero['estado'], 'soltero'); ?>>Soltero</option>
                        <option value="casado" <?php selected($bombero['estado'], 'casado'); ?>>Casado</option>
                        <option value="separado" <?php selected($bombero['estado'], 'separado'); ?>>Separado</option>
                        <option value="divorciado" <?php selected($bombero['estado'], 'divorciado'); ?>>Divorciado</option>
                        <option value="viudo" <?php selected($bombero['estado'], 'viudo'); ?>>Viudo</option>
                       
                    </select>
                </td>
                    </tr>       
       
         <tr class="form-field form-required">
                <th scope="row">
                    <label for="fecha_ingreso">Fecha Ingreso</label>
                </th>
                <td>
                    <input type="date" name="fecha_ingreso" id="fecha_ingreso" value="<?php echo($bombero['fecha_ingreso']);?>"  required aria-required="true">
                </td>
            </tr>
 <tr class="form-field form-required">
                        <th scope="row">
                            <label for="observaciones" >Observaciones</label>
                        </th>
                        <td>
                             <textarea name="observaciones" id="observaciones" class="regular-text" value="<?php echo($bombero['observaciones']);?>" rows="5"><?php echo esc_textarea($bombero['observaciones'] ?? ''); ?></textarea>
                        </td>
                    </tr>    


            
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary">Guardar Cabios</button>
            <button type="button" class="button button-secondary cancelar-edicion-bombero" data-actualpagina="<?php echo esc_attr($actualpagina); ?>">Cancelar</button>
        </p>
        <div id="mensaje-crear-bombero" class="notice" style="display: none;"></div>
    </form>
    <hr>
</div>
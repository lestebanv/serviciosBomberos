    <form id="frm_buscar_empresa">
            <label for="nit">NIT de la Empresa Solicitante:</label>
                <input type="text" id="nit" name="nit" class="regular-text" required
                pattern="^\d{9}-\d{1}$"
                title="El NIT debe tener 9 dígitos, un guion y un dígito final. Ejemplo: 700987654-4">
            
        <button type="submit" >Enviar</button>
    </form> 
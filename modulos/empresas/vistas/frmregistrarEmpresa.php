<div class="bomberos-contenido">
    <h2>Registrar Nueva Empresa</h2>
    <form class="bomberos-form-empresa">
        <div class="form-group">
            <label for="nit">NIT:</label>
            <input type="text" name="nit" id="nit" required>
        </div>
        <div class="form-group">
            <label for="razon_social">Razón Social:</label>
            <input type="text" name="razon_social" id="razon_social" required>
        </div>
        <div class="form-group">
            <label for="direccion">Dirección:</label>
            <input type="text" name="direccion" id="direccion" required>
        </div>
        <div class="form-group">
            <label for="barrio">Barrio:</label>
            <input type="text" name="barrio" id="barrio" required>
        </div>
        <div class="form-group">
            <label for="representante_legal">Representante Legal:</label>
            <input type="text" name="representante_legal" id="representante_legal" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <button type="submit" class="button button-primary">Registrar Empresa</button>
    </form>

    <h2>Lista de Empresas</h2>
    <button class="bomberos-btn-listar button button-secondary">Cargar Lista</button>
</div>
<style>
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
}
.form-group input {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
}
</style>
<h1 class="nombre-pagina">Nuevo Proveedor</h1>
<p class="descripcion-pagina">Llena el formulario para registrar un nuevo proveedor</p>

<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php';
?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre del proveedor" value="<?php echo s($proveedor->nombre ?? ''); ?>" required>
    </div>

    <div class="campo">
        <label for="contacto">Contacto:</label>
        <input type="text" id="contacto" name="contacto" placeholder="Persona de contacto" value="<?php echo s($proveedor->contacto ?? ''); ?>" required>
    </div>

    <div class="campo">
        <label for="telefono">Teléfono:</label>
        <input type="tel" id="telefono" name="telefono" placeholder="Teléfono del proveedor" value="<?php echo s($proveedor->telefono ?? ''); ?>" required>
    </div>

    <div class="campo">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Email del proveedor" value="<?php echo s($proveedor->email ?? ''); ?>" required>
    </div>

    <div class="campo">
        <label for="direccion">Dirección:</label>
        <textarea id="direccion" name="direccion" placeholder="Dirección del proveedor"><?php echo s($proveedor->direccion ?? ''); ?></textarea>
    </div>

    <div class="opciones">
        <input type="submit" class="boton" value="Guardar Proveedor">
        <a href="/admin/gestionar-proveedores" class="boton-cancelar">Volver</a>
    </div>
</form>

<h1 class="nombre-pagina">Nuevo Cliente</h1>
<p class="descripcion-pagina">Llena el formulario para registrar un nuevo cliente</p>

<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php';
?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre del cliente" value="<?php echo s($cliente->nombre ?? ''); ?>" required>
    </div>

    <div class="campo">
        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" placeholder="Apellido del cliente" value="<?php echo s($cliente->apellido ?? ''); ?>" required>
    </div>

    <div class="campo">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Email del cliente" value="<?php echo s($cliente->email ?? ''); ?>" required>
    </div>

    <div class="campo">
        <label for="telefono">Teléfono:</label>
        <input type="tel" id="telefono" name="telefono" placeholder="Teléfono del cliente" value="<?php echo s($cliente->telefono ?? ''); ?>">
    </div>

    <div class="campo">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Contraseña del cliente" required>
    </div>

    <div class="opciones">
        <input type="submit" class="boton" value="Guardar Cliente">
    </div>

    <div class="acciones">
        <a href="/admin/gestionar-clientes" class="boton-cancelar">Volver</a>
    </div>
</form>

<h1 class="nombre-pagina">Nuevo Terapeuta</h1>
<p class="descripcion-pagina">Llena todos los campos para registrar un nuevo terapeuta</p>

<?php include_once __DIR__ . '/../templates/barra.php'; ?>
<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<form class="formulario" method="POST" action="/admin/crear-terapeuta">
    <div class="campo">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
    </div>
    <div class="campo">
        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required>
    </div>
    <div class="campo">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="campo">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="campo">
        <label for="telefono">Teléfono:</label>
        <input type="tel" id="telefono" name="telefono">
    </div>
    <div class="campo">
        <label for="especialidad">Especialidad:</label>
        <input type="text" id="especialidad" name="especialidad">
    </div>
    <input type="submit" class="boton" value="Crear Terapeuta">
</form>

<div class="acciones">
    <a href="/admin/gestionar-terapeutas" class="boton-cancelar">Volver</a>
</div>

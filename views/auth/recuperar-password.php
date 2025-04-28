<h1 class="nombre-pagina">Recuperar Contraseña</h1>
<p class="descripcion-pagina">Ingresa tu nuevo password a continuación</p>

<?php 
    include_once __DIR__ . '/../templates/alertas.php'; 
?>

<?php if($error) return; ?>

<form class="formulario" method="POST">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
    
    <div class="campo">
        <label for="password">Nueva Contraseña</label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Tu Nueva Contraseña"
        />
    </div>

    <input type="submit" class="boton" value="Guardar Nueva Contraseña">

</form>

<div class="acciones">
    <a href="/">¿Ya tienes cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes cuenta? Regístrate</a>
</div>
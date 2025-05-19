<?php
$claseImagen = $claseImagen ?? 'imagen';
?>

<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesión con tus datos</p>

<?php 
    include_once __DIR__ . '/../templates/alertas.php';
?>

<?php if (isset($exito) && $exito): ?>
    <!-- <div class="alerta exito">¡Registro exitoso! Por favor, inicia sesión.</div> -->
    <div class="alerta exito">
        <?php echo $mensaje_exito; ?>
    </div>

<?php endif; ?>


<form class="formulario" method="POST" action="/">
    <div class="campo">
        <label for="email">Email</label>
        <input 
            type="email"
            id="email" 
            placeholder="Tu email"
            name="email"
            value="<?php echo s($auth->email); ?>"
        />
    </div>

    <div class="campo">
        <label for="password">Contraseña</label>
        <input 
            type="password"
            id="password" 
            placeholder="Tu contraseña"
            name="password"
        />
    </div>
    <input type="submit" value="Iniciar sesión" class="boton">
</form>

<div class="acciones">
    <a href="/crear-cuenta">¿Aún no tienes cuenta? Regístrate</a>
    <a href="/olvide">¿Olvidaste tu contraseña?</a>
</div>

<?php
$script = "
<script>
    // Hacer que las alertas desaparezcan después de 3 segundos
    document.addEventListener('DOMContentLoaded', function() {
        const alertas = document.querySelectorAll('.alerta');
        alertas.forEach(alerta => {
            setTimeout(() => {
                alerta.remove();
            }, 3000);
        });
    });
</script>
";
?>

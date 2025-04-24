<?php
// $exito se pasa desde LoginController::login
?>

<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesión con tus datos</p>

<?php if ($exito): ?>
    <div class="alerta exito">¡Registro exitoso! Por favor, inicia sesión.</div>
<?php endif; ?>

<form class="formulario" method="POST" action="/">
    <div class="campo">
        <label for="email">Email</label>
        <input 
            type="email"
            id="email" 
            placeholder="Tu email"
            name="email"
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
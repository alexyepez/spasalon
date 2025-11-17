<!-- Agregamos un enlace para Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<!-- Estilos para el botón -->
<style>
    .volver-contenedor {
        margin: 2rem auto 1rem;
        text-align: center;
    }

    .boton-volver {
        display: inline-block;
        padding: 1rem 2rem;
        background-color: #ff7f00;
        color: white;
        text-decoration: none;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .boton-volver:hover {
        background-color: #ff6347;
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .boton-volver i {
        margin-right: 0.5rem;
    }
</style>

<!-- Añadimos el botón de volver arriba, antes del formulario -->
<div class="volver-contenedor">
    <a href="/" class="boton-volver">
        <i class="bi bi-arrow-left-circle"></i> Volver a la página principal
    </a>
</div>

<h1 class="nombre-pagina">Crear cuenta</h1>
<p class="descripcion">Por favor, complete el siguiente formulario para crear una cuenta.</p>

<?php 
    include_once __DIR__ . '/../templates/alertas.php';
?>

<form class="formulario" method="POST" action="/crear-cuenta">
    <div class="campo">
        <label for="nombre">Nombre</label>
        <input 
            type="text" 
            id="nombre" 
            name="nombre" 
            placeholder="Tu Nombre"
            value="<?php echo s($usuario->nombre); ?>"
        />
    </div>

    <div class="campo">
        <label for="apellido">Apellido</label>
        <input 
            type="text" 
            id="apellido" 
            name="apellido" 
            placeholder="Tu Apellido"
            value="<?php echo s($usuario->apellido); ?>"
        />
    </div>

    <div class="campo">
        <label for="telefono">Telefono</label>
        <input 
            type="tel" 
            id="telefono" 
            name="telefono" 
            placeholder="Tu Teléfono"
            value="<?php echo s($usuario->telefono); ?>"
        />
    </div>

    <div class="campo">
        <label for="email">Email</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="Tu-Email"
            value="<?php echo s($usuario->email); ?>"
        />
    </div>

    <div class="campo">
        <label for="password">Contraseña</label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Tu Contraseña"
        />
    </div>

    <input type="submit" class="boton" value="Crear Cuenta" />
    <?php if (!empty($errores)): ?>
        <div class="alerta error">
            <?php foreach ($errores as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


</form>

<div class="acciones">
    <a href="/login">¿Ya tienes cuenta? Inicia Sesión</a>
    <a href="/olvide">¿Olvidaste tu contraseña?</a>
</div>
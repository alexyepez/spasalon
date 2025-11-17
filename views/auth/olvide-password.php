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

<h1 class="nombre-pagina">Olvidé mi Contraseña</h1>
<p class="descripcion-pagina">Ingresa tu email a continuación para recuperar tu contraseña</p>

<?php 
    include_once __DIR__ . '/../templates/alertas.php'; 
?>

<form class="formulario" action="/olvide" method="POST">
    <div class="campo">
        <label for="email">Email</label>
        <input 
            type="email"
            id="email" 
            placeholder="Tu email"          
            name="email"
        />
    </div>
    <input type="submit" class="boton"value="Enviar Instrucciones">
</form>

<div class="acciones">
    <a href="/login">¿Ya tienes cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes cuenta? Regístrate</a>
</div>
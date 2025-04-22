<h1 class="nombre-pagina">Olvidé mi Contraseña</h1>
<p class="descripcion-pagina">Ingresa tu email a continuación para recuperar tu contraseña</p>

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
    <a href="/">¿Ya tienes cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes cuenta? Regístrate</a>
</div>
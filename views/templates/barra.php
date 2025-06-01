<div class="barra">
    <h2 class="subtitulo">Bienvenido(a), <?php echo $nombre ?? ''; ?></h2>

    <a class="boton" href="/logout">Cerrar Sesión</a>
</div>

<?php
if (isset($_SESSION['admin'])) { ?>
    <div class="barra-servicios">
        <a class="boton" href="/admin">Ver Citas</a>
        <a class="boton" href="/servicios">Ver Servicios</a>
        <a class="boton" href="/servicios/crear">Nuevo Servicio</a>
    </div>

<?php } ?>

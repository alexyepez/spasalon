<?php include_once __DIR__ . '/../templates/alertas.php'; ?>
<?php include_once __DIR__ . '/../templates/barra.php'; ?>

    <h1 class="nombre-pagina">Crear Membresía</h1>
    <p class="descripcion-pagina">Llena el siguiente formulario para crear una membresía</p>

    <form class="formulario" method="POST" action="/membresias/crear">
        <div class="campo">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre de la membresía" value="<?php echo $membresia->nombre; ?>">
        </div>

        <div class="campo">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" placeholder="Descripción de la membresía"><?php echo $membresia->descripcion; ?></textarea>
        </div>

        <div class="campo">
            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" placeholder="Precio de la membresía" value="<?php echo $membresia->precio; ?>">
        </div>

        <div class="campo">
            <label for="beneficios">Beneficios</label>
            <textarea id="beneficios" name="beneficios" placeholder="Beneficios de la membresía"><?php echo $membresia->beneficios; ?></textarea>
        </div>

        <div class="campo">
            <label for="descuento">Porcentaje de Descuento (%)</label>
            <input type="number" id="descuento" name="descuento" placeholder="Porcentaje de descuento" value="<?php echo $membresia->descuento ?? '0'; ?>" min="0" max="100">
            <p class="descripcion-campo">Descuento que se aplicará a productos y servicios para miembros con esta membresía.</p>
        </div>

        <div class="opciones">
            <input type="submit" class="boton" value="Guardar Membresía">
            <a href="/membresias" class="boton-cancelar">Cancelar</a>
        </div>
    </form>

<?php
$script = "";
?>
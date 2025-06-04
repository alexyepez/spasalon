<h1 class="nombre-pagina">Nuevo Producto en Inventario</h1>
<p class="descripcion-pagina">Llena el formulario para registrar un nuevo producto</p>

<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php';
?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="producto">Producto:</label>
        <input type="text" id="producto" name="producto" placeholder="Nombre del producto" value="<?php echo s($inventario->producto ?? ''); ?>" required>
    </div>

    <div class="campo">
        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" placeholder="Descripción del producto" required><?php echo s($inventario->descripcion ?? ''); ?></textarea>
    </div>

    <div class="campo">
        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" step="0.01" min="0" placeholder="Precio del producto" value="<?php echo s($inventario->precio ?? ''); ?>" required>
    </div>

    <div class="campo">
        <label for="cantidad">Cantidad:</label>
        <input type="number" id="cantidad" name="cantidad" min="1" placeholder="Cantidad disponible" value="<?php echo s($inventario->cantidad ?? '1'); ?>" required>
    </div>

    <div class="campo">
        <label for="proveedor_id">Proveedor:</label>
        <select id="proveedor_id" name="proveedor_id" required>
            <option value="">Seleccione un proveedor</option>
            <?php foreach ($proveedores as $proveedor): ?>
                <option value="<?php echo $proveedor->id; ?>" <?php echo ($inventario->proveedor_id ?? '') === $proveedor->id ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($proveedor->nombre); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="campo">
        <label for="fecha_ingreso">Fecha de Ingreso:</label>
        <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo s($inventario->fecha_ingreso ?? date('Y-m-d')); ?>" required>
    </div>

    <div class="opciones">
        <input type="submit" class="boton" value="Guardar Producto">
        <a href="/admin/gestionar-inventario" class="boton-cancelar">Volver</a>
    </div>
</form>
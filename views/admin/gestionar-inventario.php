<h1 class="nombre-pagina">Gestionar Inventario</h1>
<p class="descripcion-pagina">Administra los productos en inventario</p>

<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php';
?>

<div class="contenedor-alertas"></div>

<div class="barra-servicios">
    <a href="/admin/crear-inventario" class="boton">Nuevo Producto</a>
</div>

<div class="listado-inventario">
    <?php if (empty($inventario)): ?>
        <p class="text-center alerta info">No hay productos en inventario.</p>
    <?php else: ?>
        <?php foreach ($inventario as $item): ?>
            <?php
            $proveedor = $item->getProveedor();
            $nombreProveedor = $proveedor ? $proveedor->nombre : 'Proveedor no disponible';
            ?>
            <div class="inventario-item" data-id-inventario="<?php echo $item->id; ?>">
                <div class="inventario-info">
                    <h3><?php echo htmlspecialchars($item->producto); ?></h3>
                    <p><strong>Descripción:</strong> <span><?php echo htmlspecialchars($item->descripcion); ?></span></p>
                    <p><strong>Precio:</strong> <span>$<?php echo htmlspecialchars($item->precio); ?></span></p>
                    <p><strong>Cantidad:</strong> <span><?php echo htmlspecialchars($item->cantidad); ?></span></p>
                    <p><strong>Proveedor:</strong> <span><?php echo htmlspecialchars($nombreProveedor); ?></span></p>
                    <p><strong>Fecha de Ingreso:</strong> <span><?php echo date('d/m/Y', strtotime($item->fecha_ingreso)); ?></span></p>
                </div>
                <div class="inventario-acciones">
                    <button class="boton editar-inventario"
                            data-id="<?php echo $item->id; ?>"
                            data-producto="<?php echo htmlspecialchars($item->producto); ?>"
                            data-descripcion="<?php echo htmlspecialchars($item->descripcion); ?>"
                            data-precio="<?php echo htmlspecialchars($item->precio); ?>"
                            data-cantidad="<?php echo htmlspecialchars($item->cantidad); ?>"
                            data-proveedor-id="<?php echo htmlspecialchars($item->proveedor_id); ?>"
                            data-fecha-ingreso="<?php echo htmlspecialchars($item->fecha_ingreso); ?>">
                        Editar
                    </button>
                    <button class="boton-eliminar eliminar-inventario" data-id="<?php echo $item->id; ?>">
                        Eliminar
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal para Editar Inventario -->
<div id="modal-inventario" class="modal" style="display: none;">
    <div class="modal-contenido">
        <h2>Editar Producto</h2>
        <form class="formulario" id="form-editar-inventario">
            <input type="hidden" name="id" id="edit-inventario-id">

            <div class="campo">
                <label for="edit-producto">Producto:</label>
                <input type="text" id="edit-producto" name="producto" required>
            </div>

            <div class="campo">
                <label for="edit-descripcion">Descripción:</label>
                <textarea id="edit-descripcion" name="descripcion" required></textarea>
            </div>

            <div class="campo">
                <label for="edit-precio">Precio:</label>
                <input type="number" id="edit-precio" name="precio" step="0.01" min="0" required>
            </div>

            <div class="campo">
                <label for="edit-cantidad">Cantidad:</label>
                <input type="number" id="edit-cantidad" name="cantidad" min="0" required>
            </div>

            <div class="campo">
                <label for="edit-proveedor_id">Proveedor:</label>
                <select id="edit-proveedor_id" name="proveedor_id" required>
                    <option value="">Seleccione un proveedor</option>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <option value="<?php echo $proveedor->id; ?>"><?php echo htmlspecialchars($proveedor->nombre); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="edit-fecha_ingreso">Fecha de Ingreso:</label>
                <input type="date" id="edit-fecha_ingreso" name="fecha_ingreso" required>
            </div>

            <div class="acciones">
                <button type="submit" class="boton">Guardar Cambios</button>
                <button type="button" id="btn-cerrar-modal-inventario" class="boton-cancelar">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php
$script = "
    <script src='/build/js/proveedor.js'></script>
    ";
?>
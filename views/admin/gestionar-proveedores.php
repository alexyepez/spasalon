<h1 class="nombre-pagina">Gestionar Proveedores</h1>
<p class="descripcion-pagina">Administra los proveedores del spa</p>

<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php';
?>

<div class="contenedor-alertas"></div>

<div class="barra-servicios">
    <a href="/admin/crear-proveedor" class="boton">Nuevo Proveedor</a>
</div>

<div class="listado-proveedores">
    <?php if (empty($proveedores)): ?>
        <p class="text-center alerta info">No hay proveedores registrados.</p>
    <?php else: ?>
        <?php foreach ($proveedores as $proveedor): ?>
            <div class="proveedor" data-id-proveedor="<?php echo $proveedor->id; ?>">
                <div class="proveedor-info">
                    <h3><?php echo htmlspecialchars($proveedor->nombre); ?></h3>
                    <p><strong>Contacto:</strong> <span><?php echo htmlspecialchars($proveedor->contacto); ?></span></p>
                    <p><strong>Teléfono:</strong> <span><?php echo htmlspecialchars($proveedor->telefono) ?: 'No especificado'; ?></span></p>
                    <p><strong>Email:</strong> <span><?php echo htmlspecialchars($proveedor->email) ?: 'No especificado'; ?></span></p>
                    <p><strong>Dirección:</strong> <span><?php echo htmlspecialchars($proveedor->direccion) ?: 'No especificada'; ?></span></p>
                </div>
                <div class="proveedor-acciones">
                    <button class="boton editar-proveedor"
                            data-id="<?php echo $proveedor->id; ?>"
                            data-nombre="<?php echo htmlspecialchars($proveedor->nombre); ?>"
                            data-contacto="<?php echo htmlspecialchars($proveedor->contacto); ?>"
                            data-telefono="<?php echo htmlspecialchars($proveedor->telefono); ?>"
                            data-email="<?php echo htmlspecialchars($proveedor->email); ?>"
                            data-direccion="<?php echo htmlspecialchars($proveedor->direccion); ?>">
                        Editar
                    </button>
                    <button class="boton-eliminar eliminar-proveedor" data-id="<?php echo $proveedor->id; ?>">
                        Eliminar
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal para Editar Proveedor -->
<div id="modal-proveedor" class="modal" style="display: none;">
    <div class="modal-contenido">
        <h2>Editar Proveedor</h2>
        <form class="formulario" id="form-editar-proveedor">
            <input type="hidden" name="id" id="edit-proveedor-id">
            <div class="campo">
                <label for="edit-nombre">Nombre:</label>
                <input type="text" id="edit-nombre" name="nombre" required>
            </div>
            <div class="campo">
                <label for="edit-contacto">Contacto:</label>
                <input type="text" id="edit-contacto" name="contacto" required>
            </div>
            <div class="campo">
                <label for="edit-telefono">Teléfono:</label>
                <input type="tel" id="edit-telefono" name="telefono" required>
            </div>
            <div class="campo">
                <label for="edit-email">Email:</label>
                <input type="email" id="edit-email" name="email" required>
            </div>
            <div class="campo">
                <label for="edit-direccion">Dirección:</label>
                <textarea id="edit-direccion" name="direccion"></textarea>
            </div>
            <div class="acciones">
                <button type="submit" class="boton">Guardar Cambios</button>
                <button type="button" id="btn-cerrar-modal-proveedor" class="boton-cancelar">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php
$script = "
    <script src='/build/js/proveedor.js'></script>
    ";
?>
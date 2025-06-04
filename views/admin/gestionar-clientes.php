<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php';
?>

<h1 class="nombre-pagina">Gestionar Clientes</h1>
<p class="descripcion-pagina">Administra la información de los clientes</p>

<div class="barra-servicios">
    <a href="/admin/crear-cliente" class="boton">Nuevo Cliente</a>
</div>


<div class="listado-clientes">
    <?php if (empty($clientes)): ?>
        <p class="text-center alerta info">No hay clientes registrados.</p>
    <?php else: ?>
        <?php foreach ($clientes as $cliente): ?>
            <div class="cliente" data-id-cliente="<?php echo $cliente->id; ?>">
                <div class="cliente-info">
                    <h3><?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellido); ?></h3>
                    <p><strong>Email:</strong> <span><?php echo htmlspecialchars($cliente->email); ?></span></p>
                    <p><strong>Teléfono:</strong> <span><?php echo htmlspecialchars($cliente->telefono ?? 'No especificado'); ?></span></p>
                </div>
                <div class="cliente-acciones">
                    <button class="boton editar-cliente"
                            data-id="<?php echo $cliente->id; ?>"
                            data-nombre="<?php echo htmlspecialchars($cliente->nombre); ?>"
                            data-apellido="<?php echo htmlspecialchars($cliente->apellido); ?>"
                            data-email="<?php echo htmlspecialchars($cliente->email); ?>"
                            data-telefono="<?php echo htmlspecialchars($cliente->telefono ?? ''); ?>">
                        Editar
                    </button>
                    <?php if ($cliente->cliente_id): ?>
                        <a class="boton" href="/admin/clientes/membresias?cliente_id=<?php echo $cliente->cliente_id; ?>">Membresías</a>
                    <?php else: ?>
                        <button class="boton-desactivado" disabled>Membresías</button>
                    <?php endif; ?>
                    <button class="boton-eliminar eliminar-cliente" data-id="<?php echo $cliente->id; ?>">
                        Eliminar
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal para Editar Cliente -->
<div id="modal-cliente" class="modal" style="display: none;">
    <div class="modal-contenido">
        <h2>Editar Cliente</h2>
        <form class="formulario" id="form-editar-cliente">
            <input type="hidden" name="id" id="edit-cliente-id">
            <div class="campo">
                <label for="edit-nombre">Nombre:</label>
                <input type="text" id="edit-nombre" name="nombre" required>
            </div>
            <div class="campo">
                <label for="edit-apellido">Apellido:</label>
                <input type="text" id="edit-apellido" name="apellido" required>
            </div>
            <div class="campo">
                <label for="edit-email">Email:</label>
                <input type="email" id="edit-email" name="email" required>
            </div>
            <div class="campo">
                <label for="edit-telefono">Teléfono:</label>
                <input type="tel" id="edit-telefono" name="telefono">
            </div>
            <div class="acciones">
                <button type="submit" class="boton">Guardar Cambios</button>
                <button type="button" id="btn-cerrar-modal-cliente" class="boton-cancelar">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php
$script = "
    <script src='/build/js/cliente.js'></script>
    ";
?>

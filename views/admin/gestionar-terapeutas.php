<h1 class="nombre-pagina">Gestionar Terapeutas</h1>
<p class="descripcion-pagina">Administra los terapeutas</p>

<?php
    include_once __DIR__ . '/../templates/barra.php';
    include_once __DIR__ . '/../templates/alertas.php';
    use Model\Usuario;
?>

<div class="barra-servicios">
    <a href="/admin/crear-terapeuta" class="boton">Nuevo Terapeuta</a>
</div>

<div class="listado-terapeutas">
    <?php if (empty($terapeutas)): ?>
        <p class="text-center alerta info">No hay terapeutas registrados.</p>
    <?php else: ?>
        <?php foreach ($terapeutas as $terapeuta): ?>
            <?php
            $usuario = Usuario::find($terapeuta->usuario_id);
            if (!$usuario) continue;
            ?>
            <div class="terapeuta" data-id-terapeuta="<?php echo $terapeuta->id; ?>">
                <div class="terapeuta-info">
                    <h3><?php echo htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido); ?></h3>
                    <p><strong>Especialidad:</strong> <span><?php echo htmlspecialchars($terapeuta->especialidad ?? 'No especificada'); ?></span></p>
                    <p><strong>Email:</strong> <span><?php echo htmlspecialchars($usuario->email); ?></span></p>
                    <p><strong>Teléfono:</strong> <span><?php echo htmlspecialchars($usuario->telefono ?? 'No especificado'); ?></span></p>
                </div>
                <div class="terapeuta-acciones">
                    <button class="boton editar-terapeuta"
                            data-id="<?php echo $terapeuta->id; ?>"
                            data-nombre="<?php echo htmlspecialchars($usuario->nombre); ?>"
                            data-apellido="<?php echo htmlspecialchars($usuario->apellido); ?>"
                            data-email="<?php echo htmlspecialchars($usuario->email); ?>"
                            data-telefono="<?php echo htmlspecialchars($usuario->telefono ?? ''); ?>"
                            data-especialidad="<?php echo htmlspecialchars($terapeuta->especialidad ?? ''); ?>">
                        Editar
                    </button>
                    <button class="boton-eliminar eliminar-terapeuta" data-id="<?php echo $terapeuta->id; ?>">
                        Eliminar
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h2 class="nombre-pagina">Asignar Citas</h2>
<p class="descripcion-pagina">Asigna citas a terapeutas</p>

<?php if (empty($citasSinTerapeuta)): ?>
    <p class="text-center alerta info">No hay citas pendientes de asignación.</p>
<?php else: ?>
    <form class="formulario" id="form-asignar-cita">
        <div class="campo">
            <label for="cita_id">Cita:</label>
            <select id="cita_id" name="cita_id" required>
                <option value="">Seleccione una cita</option>
                <?php foreach ($citasSinTerapeuta as $cita): ?>
                    <?php
                    $fecha = date("d/m/Y", strtotime($cita->fecha));
                    $hora = substr($cita->hora, 0, 5);
                    $cliente = Usuario::find($cita->cliente_id);
                    if (!$cliente) continue;
                    ?>
                    <option value="<?php echo $cita->id; ?>">
                        Cita #<?php echo $cita->id; ?> -
                        <?php echo $cliente->nombre . ' ' . $cliente->apellido; ?> -
                        <?php echo $fecha; ?> -
                        <?php echo $hora; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label for="terapeuta_id">Terapeuta:</label>
            <select id="terapeuta_id" name="terapeuta_id" required>
                <option value="">Seleccione un terapeuta</option>
                <?php foreach ($terapeutas as $terapeuta): ?>
                    <?php
                    $usuario = Usuario::find($terapeuta->usuario_id);
                    if (!$usuario) continue;
                    ?>
                    <option value="<?php echo $terapeuta->id; ?>">
                        <?php echo $usuario->nombre . ' ' . $usuario->apellido; ?> -
                        <?php echo $terapeuta->especialidad ?? 'Sin especialidad'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="submit" class="boton" value="Asignar Cita">
    </form>
<?php endif; ?>

<!-- Modal para Editar Terapeuta -->
<div id="modal-terapeuta" class="modal" style="display: none;">
    <div class="modal-contenido">
        <h2>Editar Terapeuta</h2>
        <form class="formulario" id="form-editar-terapeuta">
            <input type="hidden" name="id" id="edit-terapeuta-id">
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
            <div class="campo">
                <label for="edit-especialidad">Especialidad:</label>
                <input type="text" id="edit-especialidad" name="especialidad">
            </div>
            <div class="acciones">
                <button type="submit" class="boton">Guardar Cambios</button>
                <button type="button" id="btn-cerrar-modal-terapeuta" class="boton-cancelar">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php
$script = "
    <script src='/build/js/admin.js'></script>
    ";
?>

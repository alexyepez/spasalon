<h1 class="nombre-pagina">Crear Recordatorio</h1>
<p class="descripcion-pagina">Programa un nuevo recordatorio para una cita</p>

<?php
include_once __DIR__ . '/../../templates/barra.php';
include_once __DIR__ . '/../../templates/alertas.php';
?>

<form class="formulario" method="POST" id="form-crear-recordatorio">
    <div class="campo">
        <label for="cliente_id">Cliente:</label>
        <select id="cliente_id" name="cliente_id" required>
            <option value="">Seleccione un cliente</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?php echo $cliente->id; ?>" <?php echo $recordatorio->cliente_id == $cliente->id ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellido); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="campo">
        <label for="cita_id">Cita:</label>
        <select id="cita_id" name="cita_id" required>
            <option value="">Seleccione una cita</option>
            <?php foreach ($citas as $cita): ?>
                <option value="<?php echo $cita->id; ?>" <?php echo $recordatorio->cita_id == $cita->id ? 'selected' : ''; ?>>
                    ID: <?php echo $cita->id; ?> -
                    Cliente: <?php echo htmlspecialchars($cita->nombreCliente ?? 'Sin cliente'); ?> -
                    Fecha: <?php echo date('d/m/Y', strtotime($cita->fecha)); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="campo">
        <label for="fecha">Fecha de envío:</label>
        <input type="date" id="fecha" name="fecha" value="<?php echo $recordatorio->fecha ?? ''; ?>" required>
    </div>

    <div class="campo">
        <label for="medio">Medio de envío:</label>
        <select id="medio" name="medio" required>
            <option value="email" <?php echo $recordatorio->medio == 'email' ? 'selected' : ''; ?>>Email</option>
            <option value="sms" <?php echo $recordatorio->medio == 'sms' ? 'selected' : ''; ?>>SMS</option>
        </select>
    </div>

    <div class="opciones">
        <input type="submit" class="boton" value="Guardar Recordatorio">
        <a href="/admin/recordatorios" class="boton-cancelar">Volver</a>
    </div>
</form>

<div id="detalles-cita" class="oculto">
    <!-- Aquí se mostrarán los detalles de la cita seleccionada -->
</div>

<?php
$script = "
    <script src='/build/js/recordatorio.js'></script>
    ";
?>
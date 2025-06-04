<?php
use Model\Membresia;
?>
<?php include_once __DIR__ . '/../../templates/barra.php'; ?>
<?php include_once __DIR__ . '/../../templates/alertas.php'; ?>

    <h1 class="nombre-pagina">Asignar Membresía</h1>
    <p class="descripcion-pagina">
        Cliente: <span><?php echo $usuario->nombre . ' ' . $usuario->apellido; ?></span>
    </p>

    <div class="barra-servicios">
        <a class="boton-secundario" href="/admin/clientes/membresias?cliente_id=<?php echo $cliente->id; ?>">
            Volver a Membresías del Cliente
        </a>
    </div>

    <!-- Formulario para asignar membresía -->
    <form class="formulario" method="POST">
        <div class="campo">
            <label for="membresia_id">Seleccione Membresía:</label>
            <select name="membresia_id" id="membresia_id">
                <option value="">-- Seleccione --</option>
                <?php foreach($membresias as $membresia): ?>
                    <option value="<?php echo $membresia->id; ?>"
                        <?php echo $clienteMembresia->membresia_id === $membresia->id ? 'selected' : ''; ?>>
                        <?php echo $membresia->nombre . ' - $' . number_format($membresia->precio, 2) . ' - ' . $membresia->descuento . '% descuento'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="fecha_inicio">Fecha de Inicio:</label>
            <input
                    type="date"
                    id="fecha_inicio"
                    name="fecha_inicio"
                    value="<?php echo $clienteMembresia->fecha_inicio ?? date('Y-m-d'); ?>"
            >
        </div>

        <div class="campo">
            <label for="fecha_fin">Fecha de Finalización:</label>
            <input
                    type="date"
                    id="fecha_fin"
                    name="fecha_fin"
                    value="<?php echo $clienteMembresia->fecha_fin ?? ''; ?>"
            >
        </div>

        <input type="submit" class="boton" value="Asignar Membresía">
    </form>

<?php
$script = "<script src='/build/js/clientes-membresias.js'></script>";
?>
<h1 class="nombre-pagina">Panel de Administración</h1>

<?php include_once __DIR__ . '/../templates/barra.php'; ?>

<h2>Buscar Citas</h2>

<div class="busqueda">
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha ?? ''; ?>">
        </div>
    </form>
</div>

<div id="citas-admin">
    <ul class="citas">
        <?php
        // Variables para el seguimiento de citas
        $idCitaActual = 0;
        $totalOriginal = 0;
        $totalConDescuento = 0;
        $tieneMembresia = false;
        $procesandoCita = false;

        for ($i = 0; $i < count($citas); $i++) {
        $cita = $citas[$i];

        // Determinar si estamos cambiando de cita
        $nuevaCita = ($idCitaActual !== $cita->id);

        // Determinar si es la última aparición de esta cita
        $ultimaAparicion = ($i === count($citas) - 1 || $citas[$i + 1]->id !== $cita->id);

        // Si se comienza una nueva cita
        if ($nuevaCita) {
        // Si ya se estaba procesando otra cita, cerrarla primero
        if ($procesandoCita) {
            // Mostrar totales para la cita anterior
            if ($tieneMembresia) {
                echo '<p class="total-original">Total original: <span>$' . number_format($totalOriginal, 2) . '</span></p>';
                echo '<p class="total">Total con descuento: <span>$' . number_format($totalConDescuento, 2) . '</span></p>';
                echo '<p class="ahorro">Ahorro: <span>$' . number_format($totalOriginal - $totalConDescuento, 2) . '</span></p>';
            } else {
                echo '<p class="total">Total: <span>$' . number_format($totalOriginal, 2) . '</span></p>';
            }

            // Formulario para eliminar
            echo '<form action="api/eliminar" method="POST">';
            echo '<input type="hidden" name="id" value="' . $idCitaActual . '">';
            echo '<input type="submit" class="boton-eliminar" value="Eliminar">';
            echo '</form>';

            // Cerrar el elemento de la cita anterior
            echo '</li>';
        }

        // Reiniciar contadores y variables
        $totalOriginal = 0;
        $totalConDescuento = 0;
        $idCitaActual = $cita->id;
        $procesandoCita = true;

        // Determinar si esta cita tiene membresía
        $tieneMembresia = isset($cita->descuentoPorcentaje) && $cita->descuentoPorcentaje > 0;

        // Abrir nuevo elemento de cita
        ?>
        <li>
            <p>ID: <span><?php echo $cita->id; ?></span></p>
            <p>Hora: <span><?php echo substr($cita->hora, 0, 5); ?></span></p>
            <p>Cliente: <span><?php echo $cita->cliente; ?></span></p>
            <p>Email: <span><?php echo $cita->email; ?></span></p>
            <p>Teléfono: <span><?php echo $cita->telefono; ?></span></p>

            <?php if ($tieneMembresia): ?>
                <p class="membresia-activa">
                    <span class="membresia-activa-label">Membresía activa:</span>
                    <strong><?php echo $cita->nombreMembresia; ?></strong>
                    <span class="descuento-badge"><?php echo $cita->descuentoPorcentaje; ?>% descuento</span>
                </p>
            <?php endif; ?>

            <h3>Servicios:</h3>
            <?php
            }

            // Acumular totales para cada servicio
            $precioOriginal = (float)$cita->precio;
            $totalOriginal += $precioOriginal;

            if ($tieneMembresia) {
                $precioConDescuento = (float)$cita->precioConDescuento;
                $totalConDescuento += $precioConDescuento;
            } else {
                $totalConDescuento += $precioOriginal;
            }

            // Mostrar el servicio
            if ($tieneMembresia) {
                ?>
                <p class="servicio">
                    <?php echo $cita->nombre_servicio; ?>:
                    <span class="precio-original">$<?php echo number_format($precioOriginal, 2); ?></span>
                    <span class="precio-descuento">$<?php echo number_format($cita->precioConDescuento, 2); ?></span>
                </p>
                <?php
            } else {
                ?>
                <p class="servicio"><?php echo $cita->nombre_servicio . ": $" . number_format($precioOriginal, 2); ?></p>
                <?php
            }

            // Si es la última aparición de esta cita, mostrar totales y cerrar
            if ($ultimaAparicion) {
                if ($tieneMembresia) {
                    echo '<p class="total-original">Total original: <span>$' . number_format($totalOriginal, 2) . '</span></p>';
                    echo '<p class="total">Total con descuento: <span>$' . number_format($totalConDescuento, 2) . '</span></p>';
                    echo '<p class="ahorro">Ahorro: <span>$' . number_format($totalOriginal - $totalConDescuento, 2) . '</span></p>';
                } else {
                    echo '<p class="total">Total: <span>$' . number_format($totalOriginal, 2) . '</span></p>';
                }

                // Formulario para eliminar
                echo '<form action="api/eliminar" method="POST">';
                echo '<input type="hidden" name="id" value="' . $cita->id . '">';
                echo '<input type="submit" class="boton-eliminar" value="Eliminar">';
                echo '</form>';
                echo '</li>';

                // Marcar que ya no estamos procesando una cita
                $procesandoCita = false;
            }
            }
            ?>
    </ul>
</div>

<?php $script = "<script src='/build/js/buscador.js'></script>"; ?>
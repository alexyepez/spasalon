<h1 class="nombre-pagina">Historial de Citas</h1>
<p class="descripcion-pagina">Visualiza todas las citas registradas en el sistema.</p>

<?php include_once __DIR__ . '/../templates/barra.php'; ?>

<?php if(empty($citas)): ?>
    <p class="text-center">No hay citas registradas para mostrar.</p>
<?php else: ?>
    <div id="citas-admin">
        <ul class="citas">
            <?php foreach($citas as $cita): ?>
                <li>
                    <p>ID: <span><?php echo $cita->id; ?></span></p>
                    <p>Cliente: <span><?php echo htmlspecialchars($cita->cliente); ?></span></p>
                    <p>Fecha: <span><?php echo htmlspecialchars($cita->fecha); ?></span></p>
                    <p>Hora: <span><?php echo htmlspecialchars(substr($cita->hora, 0, 5)); ?></span></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
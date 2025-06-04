<?php
use Model\Membresia;
?>
<?php include_once __DIR__ . '/../../templates/barra.php'; ?>
<?php include_once __DIR__ . '/../../templates/alertas.php'; ?>

    <h1 class="nombre-pagina">Membresías del Cliente</h1>
    <p class="descripcion-pagina">
        Cliente: <span><?php echo $usuario->nombre . ' ' . $usuario->apellido; ?></span>
    </p>

    <div class="barra-servicios">
        <a class="boton" href="/admin/clientes/membresia/crear?cliente_id=<?php echo $cliente->id; ?>">
            Asignar Nueva Membresía
        </a>
        <a class="boton-secundario" href="/admin/gestionar-clientes">
            Volver a Clientes
        </a>
    </div>

<?php if ($membresiaActiva): ?>
    <div class="alerta-contenedor-exito">
        <div class="alerta exito">
            <?php
            // Código de depuración (quítalo cuando todo funcione)
            // echo '<pre>'; print_r($membresiaActiva); echo '</pre>';
            ?>
            <p>Este cliente tiene una membresía activa: <strong><?php
                    if (is_object($membresiaActiva) && isset($membresiaActiva->membresia_id) && $membresiaActiva->membresia_id) {
                        $membresia = Membresia::find($membresiaActiva->membresia_id);
                        echo $membresia ? $membresia->nombre : 'Desconocida';
                    } else {
                        echo 'Membresía desconocida';
                    }
                    ?></strong></p>
            <p>Vence el: <strong><?php
                    if (is_object($membresiaActiva) && isset($membresiaActiva->fecha_fin) && $membresiaActiva->fecha_fin) {
                        echo date('d/m/Y', strtotime($membresiaActiva->fecha_fin));
                    } else {
                        echo 'Fecha no disponible';
                    }
                    ?></strong></p>
        </div>
    </div>
<?php else: ?>
    <div class="alerta-contenedor-info">
        <div class="alerta info">
            <p>Este cliente no tiene ninguna membresía activa actualmente.</p>
        </div>
    </div>
<?php endif; ?>

<h2>Historial de Membresías</h2>

<?php if (empty($membresias)): ?>
    <p class="text-center">Este cliente no tiene membresías asignadas.</p>
<?php else: ?>
    <ul class="listado-membresias">
        <?php foreach($membresias as $cm): ?>
            <?php
            // Verificar que cm tenga las propiedades necesarias antes de intentar acceder a ellas
            if (is_object($cm) && property_exists($cm, 'membresia_id') && $cm->membresia_id) {
                $membresia = Membresia::find($cm->membresia_id);
                $estaActiva = method_exists($cm, 'estaActiva') ? $cm->estaActiva() : false;

                // Solo mostrar esta membresía si podemos encontrar sus datos
                if ($membresia):
                    ?>
                    <li class="membresia-item <?php echo $estaActiva ? 'activa' : ''; ?>">
                        <div class="membresia-info">
                            <h3><?php echo $membresia->nombre; ?></h3>
                            <p>Precio: <span>$<?php echo number_format($membresia->precio, 2); ?></span></p>
                            <p>Descuento: <span><?php echo $membresia->descuento; ?>%</span></p>
                            <p>Fecha inicio: <span><?php echo date('d/m/Y', strtotime($cm->fecha_inicio)); ?></span></p>
                            <p>Fecha fin: <span><?php echo date('d/m/Y', strtotime($cm->fecha_fin)); ?></span></p>
                            <p>Estado: <span class="estado <?php echo $estaActiva ? 'activo' : 'inactivo'; ?>">
                            <?php echo $estaActiva ? 'Activa' : 'Inactiva'; ?>
                        </span></p>
                        </div>

                        <div class="acciones">
                            <form action="/admin/clientes/membresias/eliminar" method="POST" class="formulario-eliminar">
                                <input type="hidden" name="id" value="<?php echo $cm->id; ?>">
                                <input type="submit" class="boton-eliminar" value="Eliminar">
                            </form>
                        </div>
                    </li>
                <?php
                endif;
            }
            ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
$script = "<script src='/build/js/clientes-membresias.js'></script>";
?>
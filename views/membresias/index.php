<?php include_once __DIR__ . '/../templates/barra.php'; ?>
<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <h1 class="nombre-pagina">Membresías</h1>
    <p class="descripcion-pagina">Administración de Membresías</p>

    <div class="barra-servicios">
        <a href="/membresias/crear" class="boton">Nueva Membresía</a>
    </div>

<?php if(empty($membresias)): ?>
    <p class="text-center">No hay membresías registradas aún</p>
<?php else: ?>
    <ul class="membresias">
        <?php foreach ($membresias as $membresia) { ?>
            <li class="membresia">
                <div class="membresia-contenido">
                    <h3><?php echo $membresia->nombre; ?></h3>
                    <p class="precio">$<?php echo number_format($membresia->precio, 2); ?></p>

                    <?php if(isset($membresia->descuento) && $membresia->descuento > 0): ?>
                        <p class="descuento">Descuento: <span><?php echo $membresia->descuento; ?>%</span></p>
                    <?php endif; ?>


                    <?php
                    //error_log("Membresía {$membresia->id} tiene clientesActivos = " .
                        (isset($membresia->clientesActivos) ? $membresia->clientesActivos : 'no definido'));
                    ?>


                    <?php if(isset($membresia->clientesActivos)): ?>
                        <p class="clientes-activos">Clientes activos: <span><?php echo $membresia->clientesActivos; ?></span></p>
                    <?php endif; ?>

                    <?php if($membresia->descripcion): ?>
                        <div class="descripcion">
                            <h4>Descripción:</h4>
                            <p><?php echo $membresia->descripcion; ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if($membresia->beneficios): ?>
                        <div class="beneficios">
                            <h4>Beneficios:</h4>
                            <p><?php echo $membresia->beneficios; ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="acciones">
                    <a class="boton" href="/membresias/actualizar?id=<?php echo $membresia->id; ?>">Actualizar</a>

                    <form action="/membresias/eliminar" method="POST" class="formulario-eliminar">
                        <input type="hidden" name="id" value="<?php echo $membresia->id; ?>">
                        <input type="submit" value="Eliminar" class="boton-eliminar">
                    </form>
                </div>
            </li>
        <?php } ?>
    </ul>
    <a href="/admin/gestionar-clientes" class="boton-cancelar">Ir a Clientes</a>
<?php endif; ?>

<?php
$script = "<script src='/build/js/membresias.js'></script>";
?>
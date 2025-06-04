<h1 class="nombre-pagina"><?php echo $titulo; ?></h1>

<div class="contenedor-mensaje">
    <?php if(isset($resultado) && $resultado): ?>
        <p class="alerta exito">Tu cita ha sido confirmada exitosamente.</p>

        <div class="informacion-cita">
            <?php if(isset($cita->fecha)): ?>
                <p><span>Fecha:</span> <?php echo date('d/m/Y', strtotime($cita->fecha)); ?></p>
            <?php endif; ?>

            <?php if(isset($cita->hora)): ?>
                <p><span>Hora:</span> <?php echo $cita->hora; ?></p>
            <?php endif; ?>

            <?php if(isset($cita->id)): ?>
                <p><span>ID de Cita:</span> <?php echo $cita->id; ?></p>
            <?php endif; ?>
        </div>

        <p class="descripcion-pagina">Gracias por confirmar tu asistencia. Te esperamos en Luminous Spa.</p>

        <!-- Eliminar la verificación de sesión y siempre mostrar el mismo botón -->
        <div class="acciones">
            <a href="/" class="boton">Volver al Inicio</a>
        </div>

    <?php else: ?>
        <p class="alerta error">Ha ocurrido un error al confirmar tu cita. Por favor, intenta nuevamente o contáctanos directamente.</p>

        <div class="acciones">
            <a href="/" class="boton">Volver al Inicio</a>
        </div>
    <?php endif; ?>
</div>
<?php 
    include_once __DIR__ . '/../templates/alertas.php';

?>

<!-- Vista: Panel del Terapeuta -->
<h1 class="nombre-pagina">Panel del Terapeuta</h1>
<p class="descripcion-pagina">Bienvenido a tu panel personal. Aquí puedes ver tu información y tus citas asignadas.</p>

<div class="contenedor seccion">
    <section class="info-personal">
        <h2 class="subtitulo">Información Personal</h2>
        <div class="campo">
            <label>Nombre:</label>
            <span><!-- <?= $terapeuta->nombre ?? '' ?> --></span>
        </div>
        <div class="campo">
            <label>Especialidad:</label>
            <span><!-- <?= $terapeuta->especialidad ?? '' ?> --></span>
        </div>
    </section>

    <section class="citas-asignadas">
        <h2 class="subtitulo">Citas Asignadas</h2>
        <!-- Aquí puedes iterar sobre las citas del terapeuta -->
        <!--
        <?php foreach($citas as $cita): ?>
            <div class="cita">
                <p><strong>Cliente:</strong> <?= $cita->cliente_nombre ?></p>
                <p><strong>Fecha:</strong> <?= $cita->fecha ?></p>
                <p><strong>Hora:</strong> <?= $cita->hora ?></p>
            </div>
        <?php endforeach; ?>
        -->
        <p class="alerta info">No hay citas asignadas.</p>
    </section>
</div>

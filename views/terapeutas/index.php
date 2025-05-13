<?php 
// Agrega esto al inicio para asegurar que se cargue el layout correctamente
$script = '<script src="/build/js/terapeuta.js"></script>';
?>

<?php 
include_once __DIR__ . '/../templates/alertas.php';
?>

<!-- Vista: Panel del Terapeuta -->
<h1 class="nombre-pagina">Panel del Terapeuta</h1>
<h2 class="subtitulo">Bienvenido(a), <?php echo htmlspecialchars($colaborador->getUsuario()->nombre . ' ' . $colaborador->getUsuario()->apellido); ?></h2>
<p class="descripcion-pagina">Aquí puedes ver tu información y tus citas asignadas.</p>

<div class="contenedor seccion">
    <!-- Información personal del terapeuta -->
    <section class="info-personal">
        <h2 class="subtitulo">Información Personal</h2>
        <div class="campo">
            <label>Nombre:</label>
            <span><?php echo htmlspecialchars($colaborador->getUsuario()->nombre . ' ' . $colaborador->getUsuario()->apellido); ?></span>
        </div>
        <div class="campo">
            <label>Especialidad:</label>
            <span><?php echo htmlspecialchars($colaborador->especialidad ?? 'No especificada'); ?></span>
        </div>
    </section>

    <!-- Citas asignadas al terapeuta -->
    <section class="citas-asignadas">
        <h2 class="subtitulo">Citas Asignadas</h2>
        <?php if (empty($citas)): ?>
            <p class="alerta info">No tienes citas asignadas.</p>
        <?php else: ?>
            <ul class="lista-citas">
                <?php foreach ($citas as $cita): ?>
                    <?php
                    // Validar cliente
                    $cliente = Cliente::find($cita->cliente_id);
                    $clienteNombre = $cliente && $cliente->getUsuario() ? $cliente->getUsuario()->nombre . ' ' . $cliente->getUsuario()->apellido : 'Cliente desconocido';

                    // Validar servicios
                    $servicios = CitaServicio::where('cita_id', $cita->id) ?? [];
                    $servicioNombres = array_map(function($cs) {
                        $servicio = Servicio::find($cs->servicio_id);
                        return $servicio ? $servicio->nombre : 'Servicio desconocido';
                    }, $servicios);

                    // Validar estado
                    $estados = ['Pendiente', 'Confirmada', 'Cancelada'];
                    $estadoTexto = isset($estados[$cita->estado]) ? $estados[$cita->estado] : 'Desconocido';
                    ?>
                    <li class="cita">
                        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($clienteNombre); ?></p>
                        <p><strong>Fecha:</strong> <?php echo htmlspecialchars($cita->fecha); ?></p>
                        <p><strong>Hora:</strong> <?php echo htmlspecialchars($cita->hora); ?></p>
                        <p><strong>Servicios:</strong> <?php echo htmlspecialchars(implode(', ', $servicioNombres)); ?></p>
                        <p><strong>Estado:</strong> <?php echo htmlspecialchars($estadoTexto); ?></p>
                        <button class="boton registrar-tratamiento">Registrar Tratamiento</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>
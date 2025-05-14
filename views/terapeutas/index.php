<?php

require_once __DIR__ . '/../../vendor/autoload.php';
use Model\Cliente;
use Model\CitaServicio;
use Model\Servicio;


?>


<h1 class="nombre-pagina">Panel del Terapeuta</h1>
<h2 class="subtitulo">Bienvenido(a), <?php echo htmlspecialchars($colaborador->getUsuario()->nombre . ' ' . $colaborador->getUsuario()->apellido); ?></h2>
<p class="descripcion-pagina">Administra tus citas y registra los tratamientos</p>

<div id="app">
    <nav class="tabs">
        <button class="actual" type="button" data-paso="1">Información Personal</button>
        <button type="button" data-paso="2">Citas Asignadas</button>
        <button type="button" data-paso="3">Historial</button>
    </nav>

    <div id="paso-1" class="seccion mostrar">
        <h2>Información Personal</h2>
        <p class="text-center">Datos del terapeuta</p>

        <div class="info-personal">
            <div class="campo">
                <label>Nombre Completo:</label>
                <span><?php echo htmlspecialchars($colaborador->getUsuario()->nombre . ' ' . $colaborador->getUsuario()->apellido); ?></span>
            </div>
            <div class="campo">
                <label>Especialidad:</label>
                <span><?php echo htmlspecialchars($colaborador->especialidad ?? 'No especificada'); ?></span>
            </div>
            <!-- Adición de campos relevantes -->
        </div>
    </div>

    <div id="paso-2" class="seccion">
        <h2>Citas Asignadas</h2>
        <p class="text-center">Gestiona tus citas del día</p>

        <div class="listado-citas">
            <?php if (empty($citas)): ?>
                <p class="text-center alerta info">No tienes citas asignadas para hoy.</p> <!-- Usar clase info o similar -->
            <?php else: ?>
                <?php foreach ($citas as $cita): ?>
                    <?php
                    $cliente = Cliente::find($cita->cliente_id);
                    $clienteNombre = 'Cliente desconocido';
                    if ($cliente && $cliente->getUsuario()) {
                        $clienteNombre = $cliente->getUsuario()->nombre . ' ' . $cliente->getUsuario()->apellido;
                    } else if ($cliente) {
                        // Si existe el cliente pero no el usuario asociado (caso raro, pero para evitar error)
                        $clienteNombre = 'Cliente ID: ' . $cita->cliente_id . ' (sin datos de usuario)';
                    } else {
                        $clienteNombre = 'Cliente desconocido (ID: ' . $cita->cliente_id . ')';
                    }


                    $servicios = CitaServicio::whereAll('cita_id', $cita->id) ?? [];
                    $servicioNombres = array_map(function($cs) {
                        $servicio = Servicio::find($cs->servicio_id);
                        return $servicio ? $servicio->nombre : 'Servicio desconocido';
                    }, $servicios);

                    $estados = ['Pendiente', 'Confirmada', 'Cancelada']; // índice 1 sea "Confirmada"
                    $estadoTexto = $estados[$cita->estado] ?? 'Desconocido'; // Usar ?? para default
                    $estadoClase = strtolower(str_replace(' ', '-', $estadoTexto)); // ej: 'pendiente', 'confirmada'
                    ?>

                    <div class="cita">
                        <div class="cita-info">
                            <h3>Cita #<?php echo htmlspecialchars($cita->id); ?></h3>
                            <p class="estado <?php echo htmlspecialchars($estadoClase); ?>"><?php echo htmlspecialchars($estadoTexto); ?></p>

                            <div class="campo">
                                <label>Cliente:</label>
                                <span><?php echo htmlspecialchars($clienteNombre); ?></span>
                            </div>

                            <div class="campo">
                                <label>Hora:</label>
                                <span><?php echo htmlspecialchars(date('h:i A', strtotime($cita->hora))); ?></span> <!-- Formatear hora -->
                            </div>

                            <div class="campo">
                                <label>Servicios:</label>
                                <span><?php echo htmlspecialchars(implode(', ', $servicioNombres)); ?></span>
                            </div>
                        </div>

                        <div class="cita-acciones"
                            <button class="boton" data-id-cita="<?php echo htmlspecialchars($cita->id); ?>">
                                Registrar Tratamiento
                            </button>
                            <button class="boton-secundario" data-id-cita="<?php echo htmlspecialchars($cita->id); ?>">
                                Ver Detalles
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div id="paso-3" class="seccion">
        <h2>Historial de Tratamientos</h2>
        <p class="text-center">Registro histórico de tratamientos realizados</p>
        <div id="historial-tratamientos" class="listado-historico">
            <!-- Se llenará dinámicamente con JavaScript si se implementas cargarHistorialTratamientos -->
            <p>El historial se cargará aquí.</p>
        </div>
    </div>

    <div class="paginacion">
        <button id="anterior" class="boton">&laquo; Anterior</button>
        <button id="siguiente" class="boton">Siguiente &raquo;</button>
    </div>
</div>

<?php
// Asegúrate que $colaborador->id exista y sea el correcto.
$terapeutaIdScript = isset($colaborador) && property_exists($colaborador, 'id') ? htmlspecialchars($colaborador->id) : 'null';
$script = "
<script>
    window.terapeutaId = " . $terapeutaIdScript . ";
</script>
<script src='/build/js/terapeuta.js'></script>
";

?>
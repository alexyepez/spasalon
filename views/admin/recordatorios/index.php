<h1 class="nombre-pagina">Gestionar Recordatorios</h1>
<p class="descripcion-pagina">Administra los recordatorios para citas</p>

<?php
include_once __DIR__ . '/../../templates/barra.php';
include_once __DIR__ . '/../../templates/alertas.php';
?>

<div class="contenedor-alertas"></div>

<div class="barra-servicios">
    <a href="/admin/recordatorios/crear" class="boton">Nuevo Recordatorio</a>
    <a href="/admin/recordatorios/enviar" class="boton">Enviar Recordatorios Pendientes</a>
</div>

<div class="listado-recordatorios">
    <?php if (empty($recordatorios)): ?>
        <p class="text-center alerta info">No hay recordatorios programados.</p>
    <?php else: ?>
        <h2>Recordatorios programados</h2>
        <div class="filtros">
            <button class="boton-filtro" data-filtro="todos">Todos</button>
            <button class="boton-filtro" data-filtro="pendientes">Pendientes</button>
            <button class="boton-filtro" data-filtro="enviados">Enviados</button>
        </div>

        <?php foreach ($recordatorios as $recordatorio): ?>
            <?php
            $cita = $recordatorio->getCita();
            $cliente = $recordatorio->getCliente();

            // Asegúrate de que obtenemos el nombre del cliente correctamente
            $nombreCliente = 'Cliente no disponible';
            if ($cliente && isset($cliente->nombre) && isset($cliente->apellido)) {
                $nombreCliente = $cliente->nombre . ' ' . $cliente->apellido;
            }

            $estadoClase = $recordatorio->enviado ? 'recordatorio-enviado' : 'recordatorio-pendiente';
            ?>
            <div class="recordatorio <?php echo $estadoClase; ?>" data-id-recordatorio="<?php echo $recordatorio->id; ?>">
                <div class="recordatorio-info">
                    <h3>Recordatorio #<?php echo $recordatorio->id; ?></h3>
                    <p><strong>Cliente:</strong> <span><?php echo htmlspecialchars($nombreCliente); ?></span></p>
                    <?php if ($cita): ?>
                        <p><strong>Cita:</strong> <span>ID: <?php echo htmlspecialchars($recordatorio->cita_id); ?> -
                            Fecha: <?php echo date('d/m/Y H:i', strtotime($cita->fecha . ' ' . $cita->hora)); ?></span>
                        </p>
                    <?php else: ?>
                        <p><strong>Cita ID:</strong> <span><?php echo htmlspecialchars($recordatorio->cita_id); ?> (Cita no disponible)</span></p>
                    <?php endif; ?>
                    <p><strong>Fecha de envío:</strong> <span><?php echo date('d/m/Y', strtotime($recordatorio->fecha)); ?></span></p>
                    <p><strong>Estado:</strong> <span class="estado-<?php echo $recordatorio->enviado ? 'enviado' : 'pendiente'; ?>">
                        <?php echo $recordatorio->enviado ? 'Enviado' : 'Pendiente'; ?>
                    </span></p>
                    <p><strong>Medio:</strong> <span><?php echo htmlspecialchars($recordatorio->medio); ?></span></p>
                </div>

                <div class="recordatorio-acciones">
                    <?php if (!$recordatorio->enviado): ?>
                        <button class="boton enviar-recordatorio" data-id="<?php echo $recordatorio->id; ?>">
                            Enviar ahora
                        </button>
                    <?php endif; ?>
                    <button class="boton-eliminar eliminar-recordatorio" data-id="<?php echo $recordatorio->id; ?>">
                        Eliminar
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$script = "
    <script src='/build/js/recordatorio.js'></script>
    ";
?>
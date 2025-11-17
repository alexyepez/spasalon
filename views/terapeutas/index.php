
<?php
// Se configura la zona horaria para Colombia
date_default_timezone_set('America/Bogota');
$fechaHoy = date('Y-m-d');


require_once __DIR__ . '/../../vendor/autoload.php';
use Model\Cliente;
use Model\CitaServicio;
use Model\Servicio;
use Model\Familiar;

?>

<h1 class="nombre-pagina">Panel del Terapeuta</h1>
<div class="barra">
    <h2 class="subtitulo">Bienvenido(a), <?php echo htmlspecialchars($colaborador->getUsuario()->nombre . ' ' . $colaborador->getUsuario()->apellido); ?></h2>

    <a class="boton" href="/logout">Cerrar Sesión</a>
</div>

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

                    // Verificar si la cita es para un familiar
                    if ($cita->familiar_id) {
                        // Buscar el familiar asociado a la cita
                        $familiar = Familiar::find($cita->familiar_id);
                        if ($familiar) {
                            $clienteNombre = $familiar->nombre . ' ' . $familiar->apellido . ' (Familiar de ';
                            if ($cliente && $cliente->getUsuario()) {
                                $clienteNombre .= $cliente->getUsuario()->nombre . ' ' . $cliente->getUsuario()->apellido;
                            }
                            $clienteNombre .= ')';
                        }
                    } else {
                        // La cita es para el cliente
                        if ($cliente && $cliente->getUsuario()) {
                            $clienteNombre = $cliente->getUsuario()->nombre . ' ' . $cliente->getUsuario()->apellido;
                        } else if ($cliente) {
                            $clienteNombre = 'Cliente ID: ' . $cita->cliente_id . ' (sin datos de usuario)';
                        } else {
                            $clienteNombre = 'Cliente desconocido (ID: ' . $cita->cliente_id . ')';
                        }
                    }

                    $servicios = CitaServicio::whereAll('cita_id', $cita->id) ?? [];
                    $servicioNombres = array_map(function($cs) {
                        $servicio = Servicio::find($cs->servicio_id);
                        return $servicio ? $servicio->nombre : 'Servicio desconocido';
                    }, $servicios);

                    $estados = ['Pendiente', 'Confirmada', 'Cancelada'];
                    $estadoTexto = $estados[$cita->estado] ?? 'Desconocido';
                    $estadoClase = strtolower(str_replace(' ', '-', $estadoTexto));
                    ?>

                    <div class="cita">
                        <div class="cita-info">
                            <h3>Cita #<?php echo htmlspecialchars($cita->id); ?></h3>
                            <p class="estado <?php echo htmlspecialchars($estadoClase); ?>"><?php echo htmlspecialchars($estadoTexto); ?></p>
                            <div class="campo">
                                <label>Paciente:</label>
                                <span>
                                    <?php
                                    if (isset($cita->familiar) && $cita->familiar) {
                                        echo htmlspecialchars($cita->familiar->nombre . ' ' . $cita->familiar->apellido .
                                            ' (Familiar de ' . $cita->cliente->getUsuario()->nombre . ')');
                                    } elseif (isset($cita->cliente) && $cita->cliente) {
                                        echo htmlspecialchars($cita->cliente->getUsuario()->nombre . ' ' .
                                            $cita->cliente->getUsuario()->apellido);
                                    } else {
                                        echo 'Paciente desconocido';
                                    }
                                    ?>
                                </span>
                            </div>

                            <div class="campo">
                                <label>Hora:</label>
                                <span><?php echo htmlspecialchars(date('h:i A', strtotime($cita->hora))); ?></span>
                            </div>

                            <div class="campo">
                                <label>Servicios:</label>
                                <span><?php echo htmlspecialchars(implode(', ', $servicioNombres)); ?></span>
                            </div>
                        </div>

                        <div class="cita-acciones">
                            <!-- Mostrar acciones según el estado actual -->
                            <?php if($cita->estado == 0): // Si está pendiente ?>
                                <button class="boton registrar-tratamiento" data-id-cita="<?php echo htmlspecialchars($cita->id); ?>">
                                    Registrar Tratamiento
                                </button>
                                <button class="boton-secundario ver-detalles" data-id-cita="<?php echo htmlspecialchars($cita->id); ?>">
                                    Ver Detalles
                                </button>
                                <button class="boton-cancelar cancelar-cita" data-id-cita="<?php echo htmlspecialchars($cita->id); ?>">
                                    Cancelar Cita
                                </button>
                            <?php elseif($cita->estado == 1): // Si está confirmada ?>
                                <button class="boton-secundario ver-detalles" data-id-cita="<?php echo htmlspecialchars($cita->id); ?>">
                                    Ver Detalles
                                </button>
                                <span class="badge confirmada">Confirmada ✓</span>
                            <?php else: // Si está cancelada ?>
                                <button class="boton-secundario ver-detalles" data-id-cita="<?php echo htmlspecialchars($cita->id); ?>">
                                    Ver Detalles
                                </button>
                                <span class="badge cancelada">Cancelada ✗</span>
                            <?php endif; ?>
                            <!-- Nuevo botón para ver recomendaciones (solo si la cita está confirmada) -->
                            <?php if($cita->estado == 1 && isset($cita->cliente) && $cita->cliente): ?>
                                <button class="boton-recomendacion ver-recomendaciones"
                                        data-cliente-id="<?php echo htmlspecialchars($cita->cliente_id); ?>"
                                        data-cliente-nombre="<?php echo htmlspecialchars($cita->cliente->getUsuario()->nombre . ' ' . $cita->cliente->getUsuario()->apellido); ?>">
                                    <i class="fas fa-magic"></i> Recomendaciones IA
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Indicador de scrool hacia abajo -->
        <div class="scroll-indicator scroll-down">
            <span>Más citas abajo</span>
            <div class="arrow-down"></div>
        </div>

        <!-- Indicador de scroll hacia arriba -->
        <div class="scroll-indicator scroll-up">
            <span>Más citas arriba</span>
            <div class="arrow-up"></div>
        </div>
    </div>

    <div id="paso-3" class="seccion">
        <h2>Historial de Tratamientos</h2>
        <p class="text-center">Registro histórico de tratamientos realizados</p>
        <div id="historial-tratamientos" class="listado-historico">
            <!-- Se llenará dinámicamente con JavaScript al implementar cargarHistorialTratamientos -->
            <p>El historial se cargará aquí.</p>
        </div>

        <!-- Indicador de scroll hacia abajo para historial -->
        <div id="historial-scroll-down" class="scroll-indicator scroll-down">
            <span>Más tratamientos abajo</span>
            <div class="arrow-down"></div>
        </div>

        <!-- Indicador de scroll hacia arriba para historial -->
        <div id="historial-scroll-up" class="scroll-indicator scroll-up">
            <span>Más tratamientos arriba</span>
            <div class="arrow-up"></div>
        </div>
    </div>

    <div class="paginacion">
        <button id="anterior" class="boton">&laquo; Anterior</button>
        <button id="siguiente" class="boton">Siguiente &raquo;</button>
    </div>

    <!-- Modal para Recomendaciones de IA -->
    <div id="modal-recomendaciones" class="modal-recomendaciones" style="display: none;">
        <div class="modal-contenido">
            <div class="modal-encabezado">
                <h3>Recomendaciones Personalizadas</h3>
                <button type="button" id="btn-cerrar-modal-recomendaciones" class="btn-cerrar">&times;</button>
            </div>

            <div class="modal-cuerpo">
                <p id="cliente-recomendaciones-nombre" class="text-center"></p>

                <!-- Loader para cuando se están generando las recomendaciones -->
                <div id="recomendaciones-loading" class="text-center" style="display: none;">
                    <div class="spinner">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                    <p>Generando recomendaciones personalizadas...</p>
                </div>

                <!-- Contenedor para las recomendaciones generadas -->
                <div id="recomendaciones-contenedor" class="recomendaciones-grid">
                    <!-- Se llenará con JavaScript -->
                </div>

                <!-- Mensaje cuando no hay recomendaciones -->
                <div id="recomendaciones-vacio" class="alerta info text-center" style="display: none;">
                    <p>No hay recomendaciones disponibles para este cliente.</p>
                </div>

                <!-- Mensaje de error -->
                <div id="recomendaciones-error" class="alerta error text-center" style="display: none;">
                    <p>Ocurrió un error al obtener las recomendaciones.</p>
                </div>
            </div>

            <div class="modal-pie">
                <button type="button" id="btn-generar-recomendaciones" class="boton">
                    Generar Nuevas Recomendaciones
                </button>
                <button type="button" id="btn-cerrar-recomendaciones" class="boton-cancelar">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Tratamiento -->
<div id="modal-tratamiento" class="modal-tratamiento" style="display: none;">
    <form id="form-tratamiento" class="formulario">
        <div id="alerta-modal-tratamiento"></div>
        <input type="hidden" name="cita_id" id="tratamiento-cita-id">
        <input type="hidden" name="registrar_tratamiento" value="1">

        <div class="campo">
            <label for="tratamiento-fecha">Fecha:</label>
            <input type="date" name="fecha" id="tratamiento-fecha" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="campo">
            <label for="tratamiento-notas">Notas:</label>
            <textarea id="tratamiento-notas" name="notas" placeholder="Describa el tratamiento realizado"></textarea>
        </div>

        <button type="submit" class="boton">Guardar</button>
        <button type="button" id="btn-cerrar-modal-tratamiento" class="boton boton-cancelar">Cancelar</button>
    </form>
</div>


<?php
// Se asegura que $colaborador->id exista y sea el correcto.
$terapeutaIdScript = isset($colaborador) && property_exists($colaborador, 'id') ? htmlspecialchars($colaborador->id) : 'null';
$script = "
<script>
    window.terapeutaId = " . $terapeutaIdScript . ";
</script>
<script src='/build/js/terapeuta.js'></script>
<script src='/build/js/recomendaciones.js'></script>
";

?>
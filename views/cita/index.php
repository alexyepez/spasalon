<h1 class="nombre-pagina">Crear nueva cita</h1>
<!--<h2 class="subtitulo">Bienvenido a tu panel de citas</h2>-->
<div class="barra">
    <h2 class="subtitulo">Bienvenido(a), <?php echo $nombre ?? ''; ?></h2>

    <a class="boton" href="/logout">Cerrar Sesión</a>
</div>
<p class="descripcion-pagina">Elige tus servicios e ingresa tus datos</p>

<div id="app">
    <nav class="tabs">
        <button class="actual" type="button" data-paso="1">Servicios</button>
        <button type="button" data-paso="2">Datos y cita</button>
        <button type="button" data-paso="3">Resumen</button>
    </nav>

    <div id="paso-1" class="seccion">
        <h2>Servicios</h2>
        <p class="text-center">Elige tus servicios a continuación</p>
        <div id="servicios" class="listado-servicios"></div>
    </div>

    <div id="paso-2" class="seccion">
        <h2>Tus datos y cita</h2>
        <p class="text-center">Ingresa tus datos y fecha de tu cita</p>

        <section class="familiares">
            <h3>Mis familiares</h3>
            <!-- Aquí se mostrará la alerta general -->
            <div id="alerta-familiares"></div>
            <button id="btn-agregar-familiar" class="boton boton-agregar" type="button">Agregar Familiar</button>
            <ul id="lista-familiares"></ul>
        </section>

        <!-- Modal para agregar/editar familiar -->
        <div id="modal-familiar" class="modal-familiar" style="display:none;">
            <form id="form-familiar" class="formulario">
                <div id="alerta-modal"></div>
                <input type="hidden" name="id" id="familiar-id">
                <div class="campo">
                    <label for="familiar-nombre">Nombre:</label>
                    <input type="text" name="nombre" id="familiar-nombre" required>
                </div>
                <div class="campo">
                    <label for="familiar-apellido">Apellido:</label>
                    <input type="text" name="apellido" id="familiar-apellido" required>
                </div>
                <div class="campo">
                    <label for="familiar-parentesco">Parentesco:</label>
                    <input type="text" name="parentesco" id="familiar-parentesco" required>
                </div>
                <div class="campo">
                    <label for="familiar-fecha-nacimiento">Fecha de nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" id="familiar-fecha-nacimiento">
                </div>
                <div class="campo">
                    <label for="familiar-telefono">Teléfono:</label>
                    <input type="text" name="telefono" id="familiar-telefono">
                </div>
                <button type="submit" class="boton">Guardar</button>
                <button type="button" id="btn-cerrar-modal" class="boton boton-cancelar">Cancelar</button>
            </form>
        </div>

        <form class="formulario">
            <div class="campo">
                <label for="persona">¿Para quién es la cita?</label>
                <select id="persona" name="persona">
                    <option value="<?php echo htmlspecialchars($cliente->id); ?>"><?php echo htmlspecialchars($nombre); ?> (Tú)</option>
                    <?php foreach ($familiares as $familiar): ?>
                        <option value="<?php echo htmlspecialchars($familiar->id); ?>">
                            <?php echo htmlspecialchars($familiar->nombre . ' ' . $familiar->apellido); ?> (<?php echo htmlspecialchars($familiar->parentesco); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="fecha">Fecha de la cita</label>
                <input type="date" id="fecha" name="fecha"
                min="<?php echo date('Y-m-d', strtotime('+1 day') ); ?>"
                />
            </div>

            <div class="campo">
                <label for="hora">Hora de la cita</label>
                <input id="hora"
                type="time" name="hora"
                />
            </div>
            <!-- Campo para seleccionar el terapeuta -->
            <!--input type="submit" value="Reservar Cita"-->

        </form>
    </div>

    <div id="paso-3" class="seccion contenido-resumen">
        <h2>Resumen</h2>
        <p class="text-center">Verifica que la información sea correcta</p>
    </div>

    <div class="paginacion">
        <button id="anterior" class="boton" >&laquo; Anterior</button>
        <button id="siguiente" class="boton" > Siguiente &raquo;</button>

    </div>
</div>

<script>
    window.clienteId = "<?php echo htmlspecialchars($cliente->id); ?>";
</script>

<?php
    $script = "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script src='build/js/app.js'></script>
    <script src='build/js/familiares.js'></script>
    ";
?>
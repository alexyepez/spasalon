<div class="barra">
    <h2 class="subtitulo">Bienvenido(a), <?php echo $nombre ?? ''; ?></h2>

    <a class="boton" href="/logout">Cerrar Sesión</a>
</div>

<?php if (isset($_SESSION['admin'])) { ?>
<div class="barra-admin">
    <!-- Menú desplegable para Agenda -->
    <div class="dropdown">
        <button class="dropbtn">
            <i class="fa fa-calendar"></i> Agenda
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-content">
            <a href="/admin">Ver Citas</a>
            <a href="/admin/historial-citas">Historial de Citas</a>
            <!-- Puedes agregar más opciones aquí -->
        </div>
    </div>

    <!-- Menú desplegable para Servicios -->
    <div class="dropdown">
        <button class="dropbtn">
            <i class="fa fa-list"></i> Servicios
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-content">
            <a href="/servicios">Ver Servicios</a>
            <a href="/servicios/crear">Nuevo Servicio</a>
            <!-- <a href="/servicios/categorias">Categorías</a> -->
            <!-- Puedes agregar más opciones aquí -->
        </div>
    </div>

    <!-- Menú desplegable para Personal -->
    <div class="dropdown">
        <button class="dropbtn">
            <i class="fa fa-users"></i> Personal
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-content">
            <a href="/admin/gestionar-terapeutas">Ver Terapeutas</a>
            <a href="/admin/crear-terapeuta">Nuevo Terapeuta</a>
            <a href="/admin/gestionar-clientes">Clientes</a>
            <a href="/admin/crear-cliente">Nuevo Cliente</a>
            <a href="/membresias">Membresías</a>
            <!-- Puedes agregar más opciones aquí -->
        </div>
    </div>

    <!-- Menú desplegable para Proveedores-->
    <div class="dropdown">
        <button class="dropbtn">
            <i class="fa fa-chart-bar"></i> Proveedores
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-content">
            <a href="/admin/gestionar-proveedores">Proveedores</a>
            <a href="/admin/gestionar-inventario">Inventario</a>
            <!-- Puedes agregar más opciones aquí -->
        </div>
    </div>

    <!-- Menú desplegable para Notificaciones -->
    <div class="dropdown">
        <button class="dropbtn">
            <i class="fa fa-comments"></i> Notificaciones
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-content">
            <a href="/admin/recordatorios"><i class="fa fa-bell"></i> Recordatorios</a>
            <a href="/admin/recordatorios/crear"><i class="fa fa-plus-circle"></i> Nuevo Recordatorio</a>
            <a href="/admin/recordatorios/enviar"><i class="fa fa-paper-plane"></i> Enviar Pendientes</a>
            <!-- <a href="/admin/mensajes"><i class="fa fa-message"></i> Mensajes</a> -->
            <!-- <a href="/admin/correo"><i class="fa fa-envelope-circle-check"></i> Correo</a> -->
        </div>
    </div>
</div>

    <!-- JavaScript para mejorar la experiencia en dispositivos móviles -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // En móviles, cerrar menú al hacer clic en una opción
            const dropdownLinks = document.querySelectorAll('.dropdown-content a');
            dropdownLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const dropdown = this.closest('.dropdown');
                    if (window.innerWidth <= 768) {
                        dropdown.querySelector('.dropdown-content').style.display = 'none';
                        setTimeout(() => {
                            dropdown.querySelector('.dropdown-content').style.display = '';
                        }, 100);
                    }
                });
            });

            // Resaltar la opción de menú activa
            const currentPath = window.location.pathname;
            document.querySelectorAll('.dropdown-content a').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                    link.closest('.dropdown').querySelector('.dropbtn').style.backgroundColor = '#e67300';
                }
            });
        });
    </script>
<?php } ?>

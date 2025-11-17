document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let clienteIdActual = null;
    let clienteNombreActual = null;

    // Elementos del DOM
    const modalRecomendaciones = document.getElementById('modal-recomendaciones');
    const clienteNombreElement = document.getElementById('cliente-recomendaciones-nombre');
    const recomendacionesLoading = document.getElementById('recomendaciones-loading');
    const recomendacionesContenedor = document.getElementById('recomendaciones-contenedor');
    const recomendacionesVacio = document.getElementById('recomendaciones-vacio');
    const recomendacionesError = document.getElementById('recomendaciones-error');
    const btnGenerarRecomendaciones = document.getElementById('btn-generar-recomendaciones');
    const btnCerrarRecomendaciones = document.getElementById('btn-cerrar-recomendaciones');
    const btnCerrarModalRecomendaciones = document.getElementById('btn-cerrar-modal-recomendaciones');

    // Event Listeners
    document.querySelectorAll('.ver-recomendaciones').forEach(btn => {
        btn.addEventListener('click', function() {
            clienteIdActual = this.dataset.clienteId;
            clienteNombreActual = this.dataset.clienteNombre;
            abrirModalRecomendaciones();
        });
    });

    if (btnCerrarRecomendaciones) {
        btnCerrarRecomendaciones.addEventListener('click', cerrarModalRecomendaciones);
    }

    if (btnCerrarModalRecomendaciones) {
        btnCerrarModalRecomendaciones.addEventListener('click', cerrarModalRecomendaciones);
    }

    if (btnGenerarRecomendaciones) {
        btnGenerarRecomendaciones.addEventListener('click', generarRecomendaciones);
    }

    // Funciones
    function abrirModalRecomendaciones() {
        if (!clienteIdActual) return;

        // Mostrar nombre del cliente
        clienteNombreElement.textContent = `Recomendaciones para: ${clienteNombreActual}`;

        // Resetear estado
        recomendacionesContenedor.innerHTML = '';
        recomendacionesVacio.style.display = 'none';
        recomendacionesError.style.display = 'none';
        recomendacionesLoading.style.display = 'block';

        // Mostrar modal
        modalRecomendaciones.style.display = 'block';

        // Cargar recomendaciones existentes
        cargarRecomendaciones();
    }

    function cerrarModalRecomendaciones() {
        modalRecomendaciones.style.display = 'none';
        clienteIdActual = null;
        clienteNombreActual = null;
    }

    /*
    async function cargarRecomendaciones() {
        if (!clienteIdActual) return;

        try {
            const formData = new FormData();
            formData.append('cliente_id', clienteIdActual);

            const respuesta = await fetch('/api/recomendaciones/obtener', {
                method: 'POST',
                body: formData
            });

            const resultado = await respuesta.json();

            recomendacionesLoading.style.display = 'none';

            if (resultado.resultado && resultado.recomendaciones && resultado.recomendaciones.length > 0) {
                mostrarRecomendaciones(resultado.recomendaciones);
            } else {
                recomendacionesVacio.style.display = 'block';
            }
        } catch (error) {
            console.error('Error al cargar recomendaciones:', error);
            recomendacionesLoading.style.display = 'none';
            recomendacionesError.style.display = 'block';
        }
    }
    */

    async function cargarRecomendaciones() {
        if (!clienteIdActual) return;

        try {
            console.log('Iniciando carga de recomendaciones para cliente ID:', clienteIdActual);

            const formData = new FormData();
            formData.append('cliente_id', clienteIdActual);

            console.log('Enviando solicitud a /api/recomendaciones/obtener');
            const respuesta = await fetch('/api/recomendaciones/obtener', {
                method: 'POST',
                body: formData
            });

            console.log('Respuesta recibida, status:', respuesta.status);
            const resultadoTexto = await respuesta.text(); // Primero obtener texto para ver si es JSON válido
            console.log('Respuesta texto:', resultadoTexto);

            try {
                const resultado = JSON.parse(resultadoTexto);
                console.log('Respuesta parseada:', resultado);

                recomendacionesLoading.style.display = 'none';

                if (resultado.resultado && resultado.recomendaciones && resultado.recomendaciones.length > 0) {
                    console.log('Mostrando', resultado.recomendaciones.length, 'recomendaciones');
                    mostrarRecomendaciones(resultado.recomendaciones);
                } else {
                    console.log('No hay recomendaciones para mostrar');
                    recomendacionesVacio.style.display = 'block';
                }
            } catch (jsonError) {
                console.error('Error al parsear JSON:', jsonError);
                recomendacionesLoading.style.display = 'none';
                recomendacionesError.style.display = 'block';
            }
        } catch (error) {
            console.error('Error al cargar recomendaciones:', error);
            recomendacionesLoading.style.display = 'none';
            recomendacionesError.style.display = 'block';
        }
    }

    /*
    async function generarRecomendaciones() {
        if (!clienteIdActual) return;

        // Resetear estado
        recomendacionesContenedor.innerHTML = '';
        recomendacionesVacio.style.display = 'none';
        recomendacionesError.style.display = 'none';
        recomendacionesLoading.style.display = 'block';

        try {
            const formData = new FormData();
            formData.append('cliente_id', clienteIdActual);

            const respuesta = await fetch('/api/recomendaciones/generar', {
                method: 'POST',
                body: formData
            });

            const resultado = await respuesta.json();

            recomendacionesLoading.style.display = 'none';

            if (resultado.resultado && resultado.recomendaciones && resultado.recomendaciones.length > 0) {
                cargarRecomendaciones(); // Recargar las recomendaciones desde la BD
            } else {
                if (resultado.mensaje) {
                    recomendacionesError.querySelector('p').textContent = resultado.mensaje;
                }
                recomendacionesError.style.display = 'block';
            }
        } catch (error) {
            console.error('Error al generar recomendaciones:', error);
            recomendacionesLoading.style.display = 'none';
            recomendacionesError.style.display = 'block';
        }
    }
    */

    async function generarRecomendaciones() {
        if (!clienteIdActual) return;

        // Resetear estado
        recomendacionesContenedor.innerHTML = '';
        recomendacionesVacio.style.display = 'none';
        recomendacionesError.style.display = 'none';
        recomendacionesLoading.style.display = 'block';

        try {
            console.log('Iniciando generación de recomendaciones para cliente ID:', clienteIdActual);

            const formData = new FormData();
            formData.append('cliente_id', clienteIdActual);

            console.log('Enviando solicitud a /api/recomendaciones/generar');
            const respuesta = await fetch('/api/recomendaciones/generar', {
                method: 'POST',
                body: formData
            });

            console.log('Respuesta recibida, status:', respuesta.status);
            const resultadoTexto = await respuesta.text();
            console.log('Respuesta texto:', resultadoTexto);

            try {
                const resultado = JSON.parse(resultadoTexto);
                console.log('Respuesta parseada:', resultado);

                recomendacionesLoading.style.display = 'none';

                if (resultado.resultado && resultado.recomendaciones && resultado.recomendaciones.length > 0) {
                    console.log('Recomendaciones generadas con éxito, recargando...');
                    cargarRecomendaciones();
                } else {
                    if (resultado.mensaje) {
                        console.error('Error de API:', resultado.mensaje);
                        recomendacionesError.querySelector('p').textContent = resultado.mensaje;
                    }
                    recomendacionesError.style.display = 'block';
                }
            } catch (jsonError) {
                console.error('Error al parsear JSON:', jsonError);
                recomendacionesLoading.style.display = 'none';
                recomendacionesError.style.display = 'block';
            }
        } catch (error) {
            console.error('Error al generar recomendaciones:', error);
            recomendacionesLoading.style.display = 'none';
            recomendacionesError.style.display = 'block';
        }
    }


    function mostrarRecomendaciones(recomendaciones) {
        recomendacionesContenedor.innerHTML = '';

        recomendaciones.forEach(recomendacion => {
            // Determinar clase CSS para prioridad
            let prioridadClase = '';
            switch (parseInt(recomendacion.prioridad)) {
                case 5: prioridadClase = 'prioridad-muy-alta'; break;
                case 4: prioridadClase = 'prioridad-alta'; break;
                case 3: prioridadClase = 'prioridad-media'; break;
                case 2: prioridadClase = 'prioridad-baja'; break;
                case 1: prioridadClase = 'prioridad-muy-baja'; break;
                default: prioridadClase = ''; break;
            }

            // Manejar la fecha correctamente
            let fechaFormateada = 'Fecha no disponible';

            if (recomendacion.fecha_creacion) {
                console.log('Fecha original:', recomendacion.fecha_creacion); // Para debug

                // Intentar convertir la fecha
                const fecha = new Date(recomendacion.fecha_creacion);

                // Comprobar si la fecha es válida (no es 1969/1970)
                if (fecha && fecha.getFullYear() > 2000) {
                    fechaFormateada = fecha.toLocaleDateString();
                } else {
                    // Intentar con formato SQL (YYYY-MM-DD HH:MM:SS)
                    const partes = recomendacion.fecha_creacion.split(/[- :]/);
                    if (partes.length >= 3) {
                        // Crear fecha con año, mes (0-11) y día
                        const fechaSQL = new Date(
                            parseInt(partes[0]), // año
                            parseInt(partes[1])-1, // mes (restamos 1 porque en JS los meses van de 0-11)
                            parseInt(partes[2])  // día
                        );
                        if (fechaSQL && fechaSQL.getFullYear() > 2000) {
                            fechaFormateada = fechaSQL.toLocaleDateString();
                        }
                    }
                }
            }

            // Preparar información del creador
            let creadoPor = '';
            if (recomendacion.generado_por_ia == 1) {
                creadoPor = 'IA';
                if (recomendacion.colaborador_nombre) {
                    creadoPor += ' (solicitado por ' + recomendacion.colaborador_nombre +
                        ' ' + recomendacion.colaborador_apellido + ')';
                }
            } else {
                creadoPor = recomendacion.colaborador_nombre + ' ' + recomendacion.colaborador_apellido;
            }

            // Crear elemento de recomendación
            const recomendacionElement = document.createElement('div');
            recomendacionElement.className = `recomendacion-tarjeta ${prioridadClase}`;

            // Crear contenido HTML
            let servicioInfo = '';
            if (recomendacion.servicio_nombre) {
                servicioInfo = `
                    <div class="recomendacion-servicio">
                        <h4>${recomendacion.servicio_nombre}</h4>
                        <p class="recomendacion-meta">
                            ${recomendacion.duracion ? 'Duración: ' + recomendacion.duracion + ' min • ' : ''}
                            ${recomendacion.precio ? 'Precio: $' + recomendacion.precio : ''}
                        </p>
                    </div>
                `;
            }

            recomendacionElement.innerHTML = `
                <div class="recomendacion-encabezado">
                    <div class="recomendacion-prioridad" title="Prioridad: ${recomendacion.prioridad}">
                        ${'★'.repeat(parseInt(recomendacion.prioridad))}
                        ${'☆'.repeat(5 - parseInt(recomendacion.prioridad))}
                    </div>
                </div>
                
                ${servicioInfo}
                
                <div class="recomendacion-descripcion">
                    <p>${recomendacion.descripcion}</p>
                </div>
                
                <div class="recomendacion-justificacion">
                    <details>
                        <summary>Ver justificación</summary>
                        <p>${recomendacion.justificacion}</p>
                    </details>
                </div>
                
                <div class="recomendacion-meta">
                    <small>Generado por ${creadoPor} • ${fechaFormateada}</small>
                </div>
            `;

            recomendacionesContenedor.appendChild(recomendacionElement);
        });

        recomendacionesContenedor.style.display = 'grid';
    }
});
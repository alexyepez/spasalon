let pasoTerapeuta = 1;
const pasoInicialTerapeuta = 1;
const pasoFinalTerapeuta = 3; // Asumiendo 3 pestañas: Información, Citas, Historial

document.addEventListener('DOMContentLoaded', function() {
    console.log('Terapeuta script loaded');
    iniciarAppTerapeuta();
});

function iniciarAppTerapeuta() {
    mostrarSeccionTerapeuta(); // Muestra la sección inicial
    tabsTerapeuta(); // Configura los listeners para los tabs
    botonesPaginadorTerapeuta(); // Configura los listeners para los botones de paginación
    paginaSiguienteTerapeuta();
    paginaAnteriorTerapeuta();

    // Asignar eventos a los botones de acciones de citas (si se generan dinámicamente o son muchos)
    asignarEventosBotonesCitas();
}

function mostrarSeccionTerapeuta() {
    const seccionAnterior = document.querySelector('#app .seccion.mostrar');
    if (seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    }

    const pasoSelector = `#paso-${pasoTerapeuta}`;
    const seccion = document.querySelector(`#app .seccion${pasoSelector}`);
    if (seccion) {
        seccion.classList.add('mostrar');
    } else {
        console.error('No se encontró la sección:', pasoSelector);
    }

    const tabAnterior = document.querySelector('#app .tabs button.actual');
    if (tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    const tab = document.querySelector(`#app .tabs button[data-paso="${pasoTerapeuta}"]`);
    if (tab) {
        tab.classList.add('actual');
    } else {
        console.error('No se encontró el tab:', `[data-paso="${pasoTerapeuta}"]`);
    }

    // Inicializar indicadores según la pestaña
    if (pasoTerapeuta === 2) {
        // Indicadores para citas
        setTimeout(inicializarIndicadorScroll, 100);
    } else if (pasoTerapeuta === 3) {
        // Cargar historial y sus indicadores
        cargarHistorialTratamientos();
        // setTimeout para dar tiempo a que el historial se cargue
        setTimeout(inicializarIndicadoresScrollHistorial, 500);
    }
}

function tabsTerapeuta() {
    const botones = document.querySelectorAll('#app .tabs button');
    botones.forEach(boton => {
        boton.addEventListener('click', function(e) {
            pasoTerapeuta = parseInt(e.target.dataset.paso);
            mostrarSeccionTerapeuta();
            botonesPaginadorTerapeuta();
        });
    });
}

function botonesPaginadorTerapeuta() {
    const paginaAnterior = document.querySelector('#app .paginacion #anterior');
    const paginaSiguiente = document.querySelector('#app .paginacion #siguiente');

    if (!paginaAnterior || !paginaSiguiente) {
        console.error('No se encontraron los botones de paginación');
        return;
    }

    paginaAnterior.classList.remove('ocultar');
    paginaSiguiente.classList.remove('ocultar');

    if (pasoTerapeuta === pasoInicialTerapeuta) {
        paginaAnterior.classList.add('ocultar');
    } else if (pasoTerapeuta === pasoFinalTerapeuta) {
        paginaSiguiente.classList.add('ocultar');
        // Aquí podrías llamar a una función para cargar el historial si es necesario
        // cargarHistorialTratamientos(); 
    }
}

function paginaAnteriorTerapeuta() {
    const paginaAnterior = document.querySelector('#app .paginacion #anterior');
    if (!paginaAnterior) return;
    paginaAnterior.addEventListener('click', function() {
        if (pasoTerapeuta <= pasoInicialTerapeuta) return;
        pasoTerapeuta--;
        botonesPaginadorTerapeuta();
        mostrarSeccionTerapeuta();
    });
}

function paginaSiguienteTerapeuta() {
    const paginaSiguiente = document.querySelector('#app .paginacion #siguiente');
    if (!paginaSiguiente) return;
    paginaSiguiente.addEventListener('click', function() {
        if (pasoTerapeuta >= pasoFinalTerapeuta) return;
        pasoTerapeuta++;
        botonesPaginadorTerapeuta();
        mostrarSeccionTerapeuta();
    });
}

// Función para manejar el registro de tratamiento mediante SweetAlert2
async function registrarTratamiento(citaId) {
    //console.log('Iniciando registro de tratamiento para cita:', citaId);

    //error_log('Fecha recibida: ' . $_POST['fecha']);

    // Obtener la fecha actual y restar un día para compensar el desfase
    const ahora = new Date();
    ahora.setDate(ahora.getDate()); // Usamos la fecha actual

    const año = ahora.getFullYear();
    const mes = String(ahora.getMonth() + 1).padStart(2, '0');
    const dia = String(ahora.getDate()).padStart(2, '0');

    // Formato YYYY-MM-DD
    const fechaActual = `${año}-${mes}-${dia}`;

    console.log("Fecha que se usará en el modal:", fechaActual);


    const { value: formValues, isConfirmed } = await Swal.fire({
        title: 'Registrar Tratamiento',
        html:
            `<form id="swal-tratamiento-form">
                <div class="swal2-input-container">
                    <label for="swal-fecha">Fecha del tratamiento</label>
                    <input id="swal-fecha" type="date" class="swal2-input" value="${fechaActual}" required>
                </div>
                <div class="swal2-input-container">
                    <label for="swal-notas">Notas del tratamiento</label>
                    <textarea id="swal-notas" class="swal2-textarea" placeholder="Describa el tratamiento realizado"></textarea>
                </div>
            </form>`,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ff7f00',
        preConfirm: () => {
            const fecha = document.getElementById('swal-fecha').value;
            const notas = document.getElementById('swal-notas').value;

            if (!fecha) {
                Swal.showValidationMessage('La fecha es obligatoria');
                return false;
            }

            return { fecha, notas };
        }
    });

    if (!isConfirmed || !formValues) {
        //console.log('Usuario canceló el registro de tratamiento');
        return;
    }

    try {
        // Mostrar indicador de carga
        Swal.fire({
            title: 'Procesando...',
            text: 'Registrando el tratamiento',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData();
        formData.append('cita_id', citaId);
        formData.append('fecha', formValues.fecha);
        formData.append('notas', formValues.notas);
        formData.append('registrar_tratamiento', '1');

        console.log('Enviando datos:', {
            cita_id: citaId,
            fecha: formValues.fecha,
            notas: formValues.notas
        });

        const respuesta = await fetch('/terapeuta/index', {
            method: 'POST',
            body: formData
        });

        // Si la respuesta no es un JSON (porque redirige), recargar la página
        if (respuesta.redirected || respuesta.url.includes('?exito=')) {
            console.log('Tratamiento registrado correctamente, redirigiendo...');
            Swal.fire({
                title: '¡Tratamiento Registrado!',
                text: 'El tratamiento ha sido registrado correctamente.',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ff7f00',
            }).then(() => {
                window.location.reload();
            });
            return;
        }

        // Obtener texto de la respuesta
        const respuestaTexto = await respuesta.text();
        console.log('Respuesta del servidor:', respuestaTexto);

        // Buscar el contenido JSON en la respuesta
        let jsonStr = respuestaTexto;

        // Si la respuesta contiene avisos/errores de PHP, extraer solo la parte JSON
        if (respuestaTexto.includes('<br />') || respuestaTexto.includes('<b>')) {
            // Buscar texto que parece ser JSON (comienza con { y termina con })
            const jsonMatch = respuestaTexto.match(/(\{.*\})/s);
            if (jsonMatch) {
                jsonStr = jsonMatch[0];
                console.log('JSON extraído de la respuesta:', jsonStr);
            } else {
                console.error('No se pudo extraer JSON de la respuesta');
                Swal.fire({
                    title: 'Error',
                    text: 'Error al procesar la respuesta del servidor',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ff7f00',
                });
                return;
            }
        }

        // Intentar analizar el JSON extraído
        try {
            const resultado = JSON.parse(jsonStr);
            if (resultado.resultado) {
                Swal.fire({
                    title: '¡Tratamiento Registrado!',
                    text: 'El tratamiento ha sido registrado correctamente.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ff7f00',
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: resultado.mensaje || 'Error al registrar el tratamiento',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ff7f00',
                });
            }
        } catch (e) {
            console.error('Error al analizar respuesta JSON:', e);
            Swal.fire({
                title: 'Error',
                text: 'Error al procesar la respuesta del servidor',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ff7f00',
            });
        }
    } catch (error) {
        console.error('Error al enviar solicitud:', error);
        Swal.fire({
            title: 'Error',
            text: `Error de conexión: ${error.message}`,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ff7f00',
        });
    }
}

/*
function verDetalles(citaId) {
    console.log('Viendo detalles de cita:', citaId);
    // Lógica para ver detalles (puede ser un modal o redirigir)
    alert(`Ver detalles para cita ID: ${citaId}. Implementar lógica.`);
}
*/

// Función para ver detalles de una cita
async function verDetalles(citaId) {
    console.log('Viendo detalles de cita:', citaId);

    // Mostrar indicador de carga
    Swal.fire({
        title: 'Cargando...',
        text: 'Obteniendo detalles de la cita',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const url = `/api-cita.php?id=${citaId}`;
        console.log('URL de petición:', url);

        const respuesta = await fetch(url);
        console.log('Respuesta status:', respuesta.status);

        // Intentamos primero obtener la respuesta como texto para debugging
        const textoRespuesta = await respuesta.text();
        console.log('Texto respuesta:', textoRespuesta);

        // Intentamos parsear la respuesta como JSON
        let cita;
        try {
            cita = JSON.parse(textoRespuesta);
        } catch (e) {
            throw new Error(`Respuesta no válida: ${textoRespuesta.substring(0, 100)}...`);
        }

        if (cita.error) {
            throw new Error(cita.error);
        }

        // Construir HTML para el modal
        let contenidoHTML = `
            <div class="detalles-cita">
                <p><strong>Cliente:</strong> ${cita.cliente_nombre} ${cita.cliente_apellido}</p>
                <p><strong>Fecha:</strong> ${cita.fecha}</p>
                <p><strong>Hora:</strong> ${cita.hora}</p>
                <p><strong>Estado:</strong> ${
            cita.estado === 0 ? 'Pendiente' :
                cita.estado === 1 ? 'Confirmada' : 'Cancelada'
        }</p>
                
                <h4>Servicios:</h4>
                <ul class="lista-servicios">
        `;

        // Agregar cada servicio
        cita.servicios.forEach(servicio => {
            contenidoHTML += `
                <li>${servicio.nombre} - $${servicio.precio}</li>
            `;
        });

        contenidoHTML += `
                </ul>
            </div>
        `;

        // Mostrar modal con detalles
        Swal.fire({
            title: `Cita #${citaId}`,
            html: contenidoHTML,
            icon: 'info',
            confirmButtonColor: '#ff7f00',
            confirmButtonText: 'Cerrar'
        });

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: `No se pudieron cargar los detalles: ${error.message}`,
            icon: 'error',
            confirmButtonColor: '#ff7f00',
            confirmButtonText: 'OK'
        });
    }
}


function asignarEventosBotonesCitas() {
    // Botones de registro de tratamiento
    document.querySelectorAll('.registrar-tratamiento').forEach(boton => {
        const citaId = boton.getAttribute('data-id-cita');
        if (citaId) {
            boton.addEventListener('click', () => registrarTratamiento(citaId));
        }
    });

    // Botones de ver detalles
    document.querySelectorAll('.ver-detalles').forEach(boton => {
        const citaId = boton.getAttribute('data-id-cita');
        if (citaId) {
            boton.addEventListener('click', () => verDetalles(citaId));
        }
    });

    // Botones de cancelar cita
    document.querySelectorAll('.cancelar-cita').forEach(boton => {
        const citaId = boton.getAttribute('data-id-cita');
        if (citaId) {
            boton.addEventListener('click', () => cancelarCita(citaId));
        }
    });
}

// Función para confirmar tratamiento (cambia el estado a Confirmada)
async function confirmarTratamiento(citaId) {
    console.log('Intentando registrar tratamiento para cita:', citaId);

    const { isConfirmed } = await Swal.fire({
        title: 'Confirmar Tratamiento',
        text: '¿Desea registrar este tratamiento como completado? Esto confirmará la cita.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ff7f00',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar'
    });

    if (!isConfirmed) {
        return;
    }

    try {
        // Mostrar indicador de carga
        Swal.fire({
            title: 'Procesando...',
            text: 'Registrando el tratamiento',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData();
        formData.append('cita_id', citaId);
        formData.append('accion', 'confirmar');

        const respuesta = await fetch('/api/citas/estado', {
            method: 'POST',
            body: formData
        });

        // Obtener el texto de la respuesta para diagnóstico
        const respuestaTexto = await respuesta.text();
        console.log("Texto de respuesta:", respuestaTexto);

        // Intentar parsear la respuesta como JSON
        let resultado;
        try {
            resultado = JSON.parse(respuestaTexto);
            console.log("Resultado JSON:", resultado);
        } catch (e) {
            console.error("Error al parsear respuesta como JSON:", e);
            Swal.fire({
                title: 'Error',
                text: 'Error en la respuesta del servidor',
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (resultado.resultado) {
            Swal.fire({
                title: '¡Tratamiento Registrado!',
                text: 'El tratamiento ha sido registrado correctamente y la cita ha sido confirmada.',
                icon: 'success',
                confirmButtonColor: '#ff7f00',
                confirmButtonText: 'OK'
            }).then(() => {
                // Recargar la página para ver los cambios
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: resultado.mensaje || 'Error al registrar el tratamiento',
                icon: 'error',
                confirmButtonColor: '#ff7f00',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'Error de conexión',
            icon: 'error',
            confirmButtonColor: '#ff7f00',
            confirmButtonText: 'OK'
        });
    }
}

// Función para cancelar una cita
async function cancelarCita(citaId) {
    // Usar SweetAlert2 en lugar de prompt
    console.log("Iniciando proceso de cancelación para cita ID:", citaId);

    const { value: motivo, isConfirmed } = await Swal.fire({
        title: 'Cancelar Cita',
        text: 'Por favor, indique el motivo de la cancelación:',
        input: 'textarea',
        inputPlaceholder: 'Escribe el motivo aquí...',
        showCancelButton: true,
        confirmButtonColor: '#ff7f00',
        cancelButtonColor: '#999',
        confirmButtonText: 'Cancelar Cita',
        cancelButtonText: 'Volver',
        inputValidator: (value) => {
            if (!value.trim()) {
                return 'Por favor ingresa un motivo para la cancelación';
            }
        }
    });

    // Si el usuario canceló el diálogo o no confirmó
    if (!isConfirmed) {
        console.log("Usuario canceló el diálogo");
        return;
    }

    //console.log("Motivo ingresado:", motivo);

    try {
        const formData = new FormData();
        formData.append('cita_id', citaId);
        formData.append('accion', 'cancelar');
        formData.append('motivo', motivo);

        // Log para verificar los datos que se envían
        /*
        console.log("Datos a enviar:", {
            cita_id: citaId,
            accion: 'cancelar',
            motivo: motivo
        });*/

        // Mostrar indicador de carga
        Swal.fire({
            title: 'Procesando...',
            text: 'Cancelando la cita',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        //console.log("Enviando solicitud a /api/citas/estado");
        const respuesta = await fetch('/api/citas/estado', {
            method: 'POST',
            body: formData
        });

        /*
        console.log("Respuesta recibida:", respuesta);
        console.log("Status:", respuesta.status);
        console.log("OK:", respuesta.ok);
        */

        // Verificar si la respuesta es válida
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        // Intentar parsear la respuesta JSON
        let resultado;
        try {
            resultado = await respuesta.json();
            //console.log("Resultado JSON:", resultado);
        } catch (jsonError) {
            //console.error("Error al parsear JSON:", jsonError);
            const textoRespuesta = await respuesta.text();
            //console.log("Texto de respuesta:", textoRespuesta);
            throw new Error("Error al procesar la respuesta del servidor");
        }

        if (resultado.resultado) {
            //console.log("Cita cancelada exitosamente");
            // Mostrar mensaje de éxito
            Swal.fire({
                title: '¡Cita Cancelada!',
                text: 'La cita ha sido cancelada correctamente',
                icon: 'success',
                confirmButtonColor: '#ff7f00',
                confirmButtonText: 'OK'
            }).then(() => {
                //console.log("Recargando página...");
                // Recargar la página para ver los cambios
                window.location.reload();
            });
        } else {
            //console.error("Error en resultado:", resultado.mensaje);
            // Mostrar mensaje de error
            Swal.fire({
                title: 'Error',
                text: resultado.mensaje || 'Error al cancelar la cita',
                icon: 'error',
                confirmButtonColor: '#ff7f00',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        console.error("Error en try/catch:", error);
        console.error("Mensaje de error:", error.message);
        console.error("Stack trace:", error.stack);

        Swal.fire({
            title: 'Error',
            text: `Error de conexión: ${error.message}`,
            icon: 'error',
            confirmButtonColor: '#ff7f00',
            confirmButtonText: 'OK'
        });
    }
}

// Función para mostrar alertas
function mostrarAlerta(mensaje, tipo) {
    const alertaPrevia = document.querySelector('.alerta-js');
    if (alertaPrevia) {
        alertaPrevia.remove();
    }

    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta-js', tipo);

    // Ubicar la alerta en la parte superior de la sección visible
    const seccionActual = document.querySelector(`#paso-${pasoTerapeuta}`);
    seccionActual.insertBefore(alerta, seccionActual.firstChild);

    // Quitar la alerta después de 3 segundos
    setTimeout(() => {
        alerta.remove();
    }, 3000);
}

function inicializarIndicadorScroll() {
    const listadoCitas = document.querySelector('.listado-citas');
    const indicadorAbajo = document.querySelector('.scroll-indicator.scroll-down');
    const indicadorArriba = document.querySelector('.scroll-indicator.scroll-up');

    if (!listadoCitas || !indicadorAbajo || !indicadorArriba) return;

    // Ocultar ambos indicadores al inicio
    indicadorAbajo.classList.add('hide');
    indicadorArriba.classList.add('hide');

    // Función para verificar la posición de scroll y mostrar/ocultar indicadores
    function actualizarIndicadores() {
        const scrollTop = listadoCitas.scrollTop;
        const scrollHeight = listadoCitas.scrollHeight;
        const clientHeight = listadoCitas.clientHeight;

        // Verificar si hay contenido por encima y por debajo de la vista actual
        const hayContenidoArriba = scrollTop > 20; // Margen de 20px para mejor experiencia
        const hayContenidoAbajo = Math.ceil(scrollTop + clientHeight) < scrollHeight - 20;

        // Actualizar visibilidad de los indicadores
        if (hayContenidoArriba) {
            indicadorArriba.classList.remove('hide');
        } else {
            indicadorArriba.classList.add('hide');
        }

        if (hayContenidoAbajo) {
            indicadorAbajo.classList.remove('hide');
        } else {
            indicadorAbajo.classList.add('hide');
        }

        // Si no hay scroll necesario, ocultar ambos indicadores
        if (scrollHeight <= clientHeight) {
            indicadorArriba.classList.add('hide');
            indicadorAbajo.classList.add('hide');
        }
    }

    // Verificar al cargar y cuando se hace scroll
    actualizarIndicadores();
    listadoCitas.addEventListener('scroll', actualizarIndicadores);

    // También verificar si el tamaño de la ventana cambia
    window.addEventListener('resize', actualizarIndicadores);

    // Hacer clic en el indicador inferior hará scroll suave hacia abajo
    indicadorAbajo.addEventListener('click', () => {
        listadoCitas.scrollBy({
            top: 300, // Desplazar 300px hacia abajo
            behavior: 'smooth'
        });
    });

    // Hacer clic en el indicador superior hará scroll suave hacia arriba
    indicadorArriba.addEventListener('click', () => {
        listadoCitas.scrollBy({
            top: -300, // Desplazar 300px hacia arriba
            behavior: 'smooth'
        });
    });
}

async function cargarHistorialTratamientos() {
    try {
        console.log('Cargando historial de tratamientos para terapeuta:', window.terapeutaId);
        const respuesta = await fetch(`/api/tratamientos?terapeutaId=${window.terapeutaId}`);

        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const tratamientos = await respuesta.json();
        //console.log('Tratamientos obtenidos:', tratamientos);

        const historial = document.querySelector('#historial-tratamientos');
        historial.innerHTML = '';

        if (!tratamientos || tratamientos.length === 0) {
            historial.innerHTML = '<p class="text-center alerta info">No hay tratamientos registrados.</p>';
            return;
        }

        const ul = document.createElement('ul');
        ul.className = 'listado-tratamientos';

        tratamientos.forEach(tratamiento => {
            const li = document.createElement('li');
            li.className = 'tratamiento-item';

            // Determinar el texto del encabezado basado en si hay un cita_id o no
            const headerText = tratamiento.cita_id
                ? `Cita #${tratamiento.cita_id}`
                : `Tratamiento #${tratamiento.id}`;

            li.innerHTML = `
                <div class="tratamiento-info">
                    <h3>${headerText}</h3>
                    <p><strong>Fecha:</strong> ${formatearFecha(tratamiento.fecha)}</p>
                    <p><strong>Notas:</strong> ${tratamiento.notas || 'Sin notas'}</p>
                </div>
            `;

            ul.appendChild(li);
        });

        historial.appendChild(ul);

        // Inicializar indicadores de scroll para el historial
        setTimeout(inicializarIndicadoresScrollHistorial, 100);
    } catch (error) {
        console.error('Error al cargar historial de tratamientos:', error);
        const historial = document.querySelector('#historial-tratamientos');
        historial.innerHTML = `<p class="text-center alerta error">Error al cargar historial: ${error.message}</p>`;
    }
}

function inicializarIndicadoresScrollHistorial() {
    //DEBUG DE SCROLLS, ELIMINAR DESPUÉS
    console.log('Inicializando indicadores de scroll para historial');

    const listadoHistorial = document.querySelector('.listado-historico');
    const indicadorAbajo = document.querySelector('#historial-scroll-down');
    const indicadorArriba = document.querySelector('#historial-scroll-up');

    console.log('Elementos encontrados:', {
        listadoHistorial: !!listadoHistorial,
        indicadorAbajo: !!indicadorAbajo,
        indicadorArriba: !!indicadorArriba
    });

    if (!listadoHistorial || !indicadorAbajo || !indicadorArriba) {
        console.error('No se encontraron elementos necesarios para los indicadores de scroll del historial');
        return;
    }

    //if (!listadoHistorial) return;

    // Verificar si ya existen los indicadores, y si no, crearlos
    //let indicadorAbajo = document.querySelector('#historial-scroll-down');
    //let indicadorArriba = document.querySelector('#historial-scroll-up');

    if (!indicadorAbajo) {
        indicadorAbajo = document.createElement('div');
        indicadorAbajo.id = 'historial-scroll-down';
        indicadorAbajo.className = 'scroll-indicator scroll-down';
        indicadorAbajo.innerHTML = '<span>Más tratamientos abajo</span><div class="arrow-down"></div>';
        listadoHistorial.parentNode.appendChild(indicadorAbajo);
    }

    if (!indicadorArriba) {
        indicadorArriba = document.createElement('div');
        indicadorArriba.id = 'historial-scroll-up';
        indicadorArriba.className = 'scroll-indicator scroll-up';
        indicadorArriba.innerHTML = '<span>Más tratamientos arriba</span><div class="arrow-up"></div>';
        listadoHistorial.parentNode.appendChild(indicadorArriba);
    }

    // Ocultar ambos indicadores al inicio
    indicadorAbajo.classList.add('hide');
    indicadorArriba.classList.add('hide');

    // Función para verificar la posición de scroll y mostrar/ocultar indicadores
    function actualizarIndicadores() {
        const scrollTop = listadoHistorial.scrollTop;
        const scrollHeight = listadoHistorial.scrollHeight;
        const clientHeight = listadoHistorial.clientHeight;

        // Verificar si hay contenido por encima y por debajo de la vista actual
        const hayContenidoArriba = scrollTop > 20; // Margen de 20px para mejor experiencia
        const hayContenidoAbajo = Math.ceil(scrollTop + clientHeight) < scrollHeight - 20;

        // Actualizar visibilidad de los indicadores
        if (hayContenidoArriba) {
            indicadorArriba.classList.remove('hide');
        } else {
            indicadorArriba.classList.add('hide');
        }

        if (hayContenidoAbajo) {
            indicadorAbajo.classList.remove('hide');
        } else {
            indicadorAbajo.classList.add('hide');
        }

        // Si no hay scroll necesario, ocultar ambos indicadores
        if (scrollHeight <= clientHeight) {
            indicadorArriba.classList.add('hide');
            indicadorAbajo.classList.add('hide');
        }
    }

    // Verificar al cargar y cuando se hace scroll
    actualizarIndicadores();
    listadoHistorial.addEventListener('scroll', actualizarIndicadores);

    // También verificar si el tamaño de la ventana cambia
    window.addEventListener('resize', actualizarIndicadores);

    // Hacer clic en el indicador inferior hará scroll suave hacia abajo
    indicadorAbajo.addEventListener('click', () => {
        listadoHistorial.scrollBy({
            top: 300, // Desplazar 300px hacia abajo
            behavior: 'smooth'
        });
    });

    // Hacer clic en el indicador superior hará scroll suave hacia arriba
    indicadorArriba.addEventListener('click', () => {
        listadoHistorial.scrollBy({
            top: -300, // Desplazar 300px hacia arriba
            behavior: 'smooth'
        });
    });
}

// Inicializar la funcionalidad cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar las pestañas
    tabsTerapeuta();

    // Agregar listeners a los botones de registrar tratamiento
    const botonesRegistrar = document.querySelectorAll('.registrar-tratamiento');
    botonesRegistrar.forEach(boton => {
        boton.addEventListener('click', () => {
            const citaId = boton.dataset.idCita;
            registrarTratamiento(citaId);
        });
    });

    // Agregar listeners a los botones de cancelar cita
    const botonesCancelar = document.querySelectorAll('.cancelar-cita');
    botonesCancelar.forEach(boton => {
        boton.addEventListener('click', () => {
            const citaId = boton.dataset.idCita;
            cancelarCita(citaId);
        });
    });

    // Cargar historial de tratamientos cuando se seleccione esa pestaña
    const pestanaHistorial = document.querySelector('[data-paso="3"]');
    if (pestanaHistorial) {
        pestanaHistorial.addEventListener('click', () => {
            cargarHistorialTratamientos();
        });
    }

    // Si estamos en la pestaña 3 al cargar, iniciar carga del historial
    if (pasoTerapeuta === 3) {
        cargarHistorialTratamientos();
    }
});


// Función auxiliar para formatear fechas
function formatearFecha(fechaStr) {
    const fecha = new Date(fechaStr);
    return fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}
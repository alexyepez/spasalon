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

    // Inicializar el indicador de scroll solo para la página de citas
    if (pasoTerapeuta === 2) {
        setTimeout(inicializarIndicadorScroll, 100);
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
    // No es necesario llamar a mostrarSeccionTerapeuta() aquí de nuevo si tabsTerapeuta ya lo hace.
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

// --- Funciones para acciones de citas ---
function registrarTratamiento(citaId) {
    console.log('Intentando registrar tratamiento para cita:', citaId);
    // Lógica para registrar tratamiento (puede ser un modal, etc.)
    // Ejemplo: podrías mostrar un modal que esté oculto
    // const modal = document.getElementById('modal-registrar-tratamiento');
    // if (modal) {
    //     // Cargar datos de la cita en el modal si es necesario
    //     modal.style.display = 'block';
    // }
    alert(`Registrar tratamiento para cita ID: ${citaId}. Implementar lógica.`);
}

function verDetalles(citaId) {
    console.log('Viendo detalles de cita:', citaId);
    // Lógica para ver detalles (puede ser un modal o redirigir)
    alert(`Ver detalles para cita ID: ${citaId}. Implementar lógica.`);
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

// Función para registrar tratamiento (cambia el estado a Confirmada)
async function registrarTratamiento(citaId) {
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

/*
function asignarEventosBotonesCitas() {
    // Seleccionar todos los botones de "Registrar Tratamiento"
    // Es importante que estos botones no tengan el atributo onclick en el HTML
    const botonesRegistrar = document.querySelectorAll('.cita-acciones button.boton');
    botonesRegistrar.forEach(boton => {
        // Verificar si el botón es para registrar tratamiento (podrías añadir una clase específica)
        if (boton.textContent.trim().toLowerCase().includes('registrar tratamiento')) {
            const citaId = boton.getAttribute('data-id-cita');
            if (citaId) {
                // Remover cualquier listener onclick inline si existiera accidentalmente
                boton.onclick = null;
                boton.addEventListener('click', () => registrarTratamiento(citaId));
            }
        }
    });

    // Seleccionar todos los botones de "Ver Detalles"
    const botonesDetalles = document.querySelectorAll('.cita-acciones button.boton-secundario');
    botonesDetalles.forEach(boton => {
        const citaId = boton.getAttribute('data-id-cita');
        if (citaId) {
            // Remover cualquier listener onclick inline
            boton.onclick = null;
            boton.addEventListener('click', () => verDetalles(citaId));
        }
    });
}
*/

// (Opcional) Si necesitas cargar el historial dinámicamente cuando se llega al paso 3:
// async function cargarHistorialTratamientos() {
//     const terapeutaId = window.terapeutaId; // Asegúrate que window.terapeutaId esté disponible
//     if (!terapeutaId) {
//         console.error('ID del terapeuta no disponible');
//         return;
//     }
//     try {
//         const respuesta = await fetch(`/api/terapeutas/historial?terapeuta_id=${terapeutaId}`);
//         if (!respuesta.ok) {
//             throw new Error(`Error HTTP: ${respuesta.status}`);
//         }
//         const historiales = await respuesta.json();
//         const contenedorHistorial = document.getElementById('historial-tratamientos');
//         contenedorHistorial.innerHTML = ''; // Limpiar anterior

//         if (historiales.length === 0) {
//             contenedorHistorial.innerHTML = '<p>No hay tratamientos registrados.</p>';
//             return;
//         }

//         const ul = document.createElement('ul');
//         historiales.forEach(item => {
//             const li = document.createElement('li');
//             // Formatear y mostrar la información del historial como desees
//             li.textContent = `Fecha: ${item.fecha}, Cliente: ${item.cliente_nombre}, Tratamiento: ${item.descripcion}`;
//             ul.appendChild(li);
//         });
//         contenedorHistorial.appendChild(ul);
//     } catch (error) {
//         console.error('Error al cargar el historial de tratamientos:', error);
//         const contenedorHistorial = document.getElementById('historial-tratamientos');
//         contenedorHistorial.innerHTML = '<p class="alerta error">No se pudo cargar el historial.</p>';
//     }
// }
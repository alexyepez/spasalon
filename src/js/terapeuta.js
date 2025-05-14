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
    // Si los botones ya existen al cargar la página, podemos hacerlo más directo.
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
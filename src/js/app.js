let paso = 1;
const pasoInicial = 1; // Paso inicial
const pasoFinal = 3; // Paso final

// Variables para almacenar los datos del formulario
const cita = {
    //id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: [],
};

document.addEventListener('DOMContentLoaded', function() {
    const clienteId = window.clienteId;
    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion();
    tabs(); // Cambia la sección cuando se presiona un tab
    botonesPaginador(); // Cambia la sección cuando se presiona un botón de paginación
    paginaSiguiente(); // Cambia la sección cuando se presiona el botón "Siguiente"
    paginaAnterior(); // Cambia la sección cuando se presiona el botón "Anterior"
    consultarAPI(); // Llama a la función para consultar la API en el backend de php
    //idCliente();
    nombreCliente(); // Adiciona el nombre del cliente al objeto cita
    seleccionarFecha(); // Adiciona la fecha de la cita al objeto cita
    seleccionarHora(); // Adiciona la hora de la cita al objeto cita
    mostrarResumen(); // Muestra el resumen de la cita
    reservarCita(); // Reserva la cita
}

function mostrarSeccion() {
    // Ocultar la sección que tenga la clase de 'mostrar'
    const seccionAnterior = document.querySelector('.mostrar'); // Selecciona todas las secciones que tienen la clase 'mostrar'
    if (seccionAnterior) {
        seccionAnterior.classList.remove('mostrar'); // Elimina la clase 'mostrar' de todas las secciones
    }
    
    // Selecciona la sección a mostrar según el paso
    // `#paso-${paso}` es un selector de CSS que selecciona el elemento con id paso-1, paso-2, etc.
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar'); // Agrega la clase 'mostrar' a la sección seleccionada

    // Elimina la clase 'actual' de los demás tabs
    const tabAnterior = document.querySelector('.actual'); // Selecciona todos los tabs
    if(tabAnterior) {
        tabAnterior.classList.remove('actual'); // Elimina la clase 'actual' de los tabs que no son el actual
    }

    // Resalta el tab activo
    const tab = document.querySelector(`[data-paso="${paso}"]`); // Selecciona el tab correspondiente al paso actual
    tab.classList.add('actual'); // Agrega la clase 'actual' al tab seleccionado
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button');
    botones.forEach(boton => {
        boton.addEventListener('click', function(e) {
            //console.log('Botón presionado:', parseInt(e.target.dataset.paso));
            paso = parseInt( e.target.dataset.paso );
            mostrarSeccion();

            botonesPaginador(); // Cambia la sección cuando se presiona un botón de paginación
        });
    })
}

function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior'); // Selecciona el botón "Anterior"
    const paginaSiguiente = document.querySelector('#siguiente'); // Selecciona el botón "Siguiente"
    if (paso === 1) {
        paginaAnterior.classList.add('ocultar'); // Oculta el botón "Anterior" en el primer paso
        paginaSiguiente.classList.remove('ocultar'); // Muestra el botón "Siguiente" en el primer paso
    } else if (paso === 3) {
        paginaAnterior.classList.remove('ocultar'); // Muestra el botón "Anterior" en el último paso
        paginaSiguiente.classList.add('ocultar'); // Oculta el botón "Siguiente" en el último paso
        mostrarResumen(); // Muestra el resumen de la cita
    } else {
        paginaAnterior.classList.remove('ocultar'); // Muestra el botón "Anterior" en los pasos intermedios
        paginaSiguiente.classList.remove('ocultar'); // Muestra el botón "Siguiente" en los pasos intermedios
    }

    mostrarSeccion(); // Muestra la sección correspondiente al paso actual
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior'); // Selecciona el botón "Anterior"
    paginaAnterior.addEventListener('click', function() {
        if (paso <= pasoInicial) return; // Verifica si el paso actual es mayor que el paso inicial
            paso--; // Decrementa el paso
            botonesPaginador(); // Cambia la sección cuando se presiona un botón de paginación
    });
}

function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente'); // Selecciona el botón "Siguiente"
    paginaSiguiente.addEventListener('click', function() {
        if (paso >= pasoFinal) return; // Verifica si el paso actual es mayor que el paso inicial
            paso++; // Decrementa el paso
            botonesPaginador(); // Cambia la sección cuando se presiona un botón de paginación
    });
}

async function consultarAPI() {
    try {
        const url = `${location.origin}/api/servicios`; // URL de la API
        const resultado = await fetch(url); // Realiza una solicitud a la API
        const servicios = await resultado.json(); // Convierte la respuesta a JSON
        mostrarServicios(servicios); // Muestra el resultado en la consola

    } catch (error) {
        console.log(error); // Muestra el error en la consola
    }
}

function mostrarServicios(servicios) {
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        // Crear elemento para el precio
        const precioServicio = document.createElement('DIV'); // Cambiado a DIV para mejor estructura
        precioServicio.classList.add('precio-servicio');

        // Calcular precio con descuento si existe
        if (window.descuentoMembresia && window.descuentoMembresia > 0) {
            const descuento = (precio * window.descuentoMembresia) / 100;
            const precioConDescuento = parseFloat((precio - descuento).toFixed(2));

            // Crear contenedor para precio original (primera línea)
            const precioOriginalDiv = document.createElement('DIV');
            precioOriginalDiv.classList.add('precio-linea');

            const precioOriginalSpan = document.createElement('SPAN');
            precioOriginalSpan.classList.add('precio-original');
            precioOriginalSpan.textContent = `$${precio}`;

            precioOriginalDiv.appendChild(precioOriginalSpan);

            // Crear contenedor para precio con descuento (segunda línea)
            const precioDescuentoDiv = document.createElement('DIV');
            precioDescuentoDiv.classList.add('precio-linea');

            const precioDescuentoSpan = document.createElement('SPAN');
            precioDescuentoSpan.classList.add('precio-con-descuento');
            precioDescuentoSpan.textContent = `$${precioConDescuento.toFixed(2)}`;

            // Etiqueta de descuento (en la misma línea que el precio con descuento)
            const descuentoEtiqueta = document.createElement('SPAN');
            descuentoEtiqueta.classList.add('etiqueta-descuento');
            descuentoEtiqueta.textContent = `-${window.descuentoMembresia}%`;

            precioDescuentoDiv.appendChild(precioDescuentoSpan);
            precioDescuentoDiv.appendChild(descuentoEtiqueta);

            // Añadir ambas líneas al contenedor de precio
            precioServicio.appendChild(precioOriginalDiv);
            precioServicio.appendChild(precioDescuentoDiv);

            // Al hacer clic, guardar el servicio con la información de descuento
            servicioDiv.onclick = function() {
                seleccionarServicio({
                    id,
                    nombre,
                    precio: precioConDescuento,
                    precioOriginal: precio,
                    descuentoAplicado: window.descuentoMembresia
                });
            };
        } else {
            // Mostrar precio normal
            precioServicio.textContent = `$${precio}`;

            // Al hacer clic, guardar el servicio normal
            servicioDiv.onclick = function() {
                seleccionarServicio({id, nombre, precio});
            };
        }

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
    });
}

function seleccionarServicio(servicio) {
    const { id } = servicio; // Desestructura el objeto servicio para obtener el id
    const { servicios } = cita; // Desestructura el objeto cita para obtener los servicios

    // Identifica el div al que se le da click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`); // Crea un nuevo elemento div para el servicio seleccionado

    // Verifica si el servicio ya está seleccionado
    if ( servicios.some(agregado => agregado.id === id)) {
        // Si el servicio ya está seleccionado, lo elimina del array de servicios
        cita.servicios = servicios.filter(agregado => agregado.id !== id); // Filtra los servicios para eliminar el seleccionado
        divServicio.classList.remove('seleccionado'); // Elimina la clase 'seleccionado' del div creado
    }
    else {
        // Si el servicio no está seleccionado, lo agrega al array de servicios
        cita.servicios = [...servicios, servicio]; // Agrega el servicio seleccionado al array de servicios
        divServicio.classList.add('seleccionado'); // Agrega la clase 'seleccionado' al div creado
    }
}

/*
function idCliente() {
    cita.id = document.querySelector('#persona').value;
}
*/

function nombreCliente() {  
    const select = document.querySelector('#persona');
    const nombre = select.options[select.selectedIndex].textContent;
    cita.nombre = nombre;
    select.addEventListener('change', function() {
        const nombre = select.options[select.selectedIndex].textContent;
        cita.nombre = nombre;
    });
}

function seleccionarFecha() {
    const inputFecha = document.querySelector('#fecha'); // Selecciona el valor del input de fecha
    inputFecha.addEventListener('input', function(e) {
        const dia = new Date(e.target.value).getUTCDay(); // Obtiene el día de la semana de la fecha seleccionada
        if ([0].includes(dia)) {
            e.target.value = ''; // Si el día es domingo, se limpia el campo de fecha
            mostrarAlerta('Los Domingos no están permitidos', 'error', '.formulario'); // Muestra un mensaje de error
        } else {
            cita.fecha = e.target.value; // Asigna la fecha al objeto cita
        }
    });
}

function seleccionarHora() {
    const inputHora = document.querySelector('#hora'); // Selecciona el valor del input de hora
    inputHora.addEventListener('input', function(e) {
        const horaCita = e.target.value; // Obtiene la hora seleccionada
        const hora = horaCita.split(':'); // Separa la hora y los minutos
        if (hora[0] < 10 || hora[0] > 18) {
            e.target.value = ''; // Si la hora es menor a 10 o mayor a 18, se limpia el campo de hora
            mostrarAlerta('La cita debe estar entre las 10:00 am y las 6:00 pm', 'error', '.formulario');
        } else {
            cita.hora = e.target.value; // Asigna la hora al objeto cita
        }
    });
}


function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');

    // Limpiar el contenido HTML
    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }

    // Verificar que se hayan completado todos los campos
    if(Object.values(cita).includes('') || cita.servicios.length === 0) {
        mostrarAlerta('Faltan datos de servicios, fecha u hora', 'error', '.contenido-resumen', false);
        return;
    }

    const { nombre, fecha, hora, servicios } = cita;

    // Heading de servicios
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    // Variables para calcular totales
    let precioTotal = 0;
    let precioOriginalTotal = 0;
    let hayDescuento = false;

    // Iterar sobre los servicios
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio;
        const precioOriginal = servicio.precioOriginal || precio;
        const descuentoAplicado = servicio.descuentoAplicado || 0;

        if (servicio.precioOriginal && servicio.descuentoAplicado) {
            hayDescuento = true;
            precioOriginalTotal += parseFloat(precioOriginal);
        }

        precioTotal += parseFloat(precio);

        const divServicio = document.createElement('DIV');
        divServicio.classList.add('contenedor-servicio');

        const nombreServicio = document.createElement('P');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');

        if (servicio.precioOriginal && servicio.descuentoAplicado) {
            // Si hay descuento, mostrar ambos precios
            precioServicio.innerHTML = `
                <span>Precio:</span> 
                <span class="precio-original">$${precioOriginal}</span> 
                <span class="precio-final">$${parseFloat(precio).toFixed(2)}</span>
                <span class="etiqueta-descuento">-${descuentoAplicado}%</span>
            `;
        } else {
            precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;
        }

        divServicio.appendChild(nombreServicio);
        divServicio.appendChild(precioServicio);
        resumen.appendChild(divServicio);
    });

    // Mostrar total con descuento si aplica
    if (hayDescuento) {
        const divTotal = document.createElement('DIV');
        divTotal.classList.add('contenedor-total');

        const totalOriginalP = document.createElement('P');
        totalOriginalP.innerHTML = `<span>Total original:</span> <span class="precio-original">$${precioOriginalTotal.toFixed(2)}</span>`;

        const totalFinalP = document.createElement('P');
        totalFinalP.innerHTML = `<span>Total con descuento:</span> <span class="precio-final">$${precioTotal.toFixed(2)}</span>`;

        const ahorroP = document.createElement('P');
        const ahorro = precioOriginalTotal - precioTotal;
        ahorroP.innerHTML = `<span>Ahorro:</span> <span class="precio-ahorro">$${ahorro.toFixed(2)}</span>`;

        const notaMembresia = document.createElement('P');
        notaMembresia.classList.add('nota-membresia');
        notaMembresia.textContent = `Descuento aplicado por membresía`;

        divTotal.appendChild(totalOriginalP);
        divTotal.appendChild(totalFinalP);
        divTotal.appendChild(ahorroP);
        divTotal.appendChild(notaMembresia);
        resumen.appendChild(divTotal);
    }

    // Heading para datos de cita
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de la cita';
    resumen.appendChild(headingCita);

    // Formatear nombre
    const nombreCita = document.createElement('P');
    nombreCita.innerHTML = `<span>Nombre:</span> ${nombre}`;

    // Formatear fecha
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date(Date.UTC(year, mes, dia));
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const fechaFormateada = fechaUTC.toLocaleDateString('es-CO', opciones);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    // Formatear hora
    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    // Boton para crear una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    // Agregar al resumen
    resumen.appendChild(nombreCita);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(botonReservar);
}

// Función nueva para reservar la cita
async function reservarCita() {
    const { servicios, fecha, hora } = cita;
    const selectPersona = document.querySelector('#persona');
    const personaId = selectPersona ? selectPersona.value : null;
    const esFamiliar = personaId !== window.clienteId;

    if (servicios.length === 0 || !personaId || !fecha || !hora) {
        mostrarAlerta('Por favor, completa todos los campos.', 'error', '.contenido-resumen');
        return;
    }

    const datos = {
        servicios: servicios.map(servicio => servicio.id),
        clienteId: window.clienteId, // ID del cliente logueado (siempre)
        familiarId: esFamiliar ? personaId : null, // null si es el cliente, ID del familiar si no
        fecha,
        hora,
        membresiaId: window.idMembresia || null, // ID de la membresía si existe
        descuento: window.descuentoMembresia || 0 // Porcentaje de descuento
    };

    try {
        const respuesta = await fetch('/api/citas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (resultado.resultado) {
            Swal.fire({
                title: "Cita Creada!",
                icon: "success",
                texto: "Tu cita fue creada exitosamente.",
                confirmButtonColor: '#ff7f00',
                button: "OK",
            }).then(() => {
                window.location.href = '/cita';
            });
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: resultado.mensaje || "Error al reservar la cita!",
                confirmButtonColor: '#ff7f00',
                button: "OK",
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error de conexión al reservar la cita!",
            confirmButtonColor: '#ff7f00',
            button: "OK",
        });
    }
}

// Función para mostrar la alerta
function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
    // Elimina la alerta anterior si existe
    const alertaPrevia = document.querySelector('.alerta'); // Selecciona la alerta anterior
    if (alertaPrevia) {
        alertaPrevia.remove(); // Si existe, la elimina
    }

    // Scripting para crear la alerta
    const alerta = document.createElement('DIV'); // Crea un nuevo elemento div
    alerta.textContent = mensaje; // Asigna el mensaje al contenido del div creado
    alerta.classList.add('alerta'); // Agrega la clase 'alerta' al div creado
    alerta.classList.add(tipo); // Agrega la clase 'error' al div creado
    const referencia = document.querySelector(elemento); // Selecciona el elemento de referencia
    referencia.appendChild(alerta);

    // Desaparece la alerta después de 3 segundos
    if (desaparece) {
        setTimeout(() => {
            alerta.remove(); // Elimina la alerta después de 3 segundos
        }, 3000);
    }
}
let paso = 1;
const pasoInicial = 1; // Paso inicial
const pasoFinal = 3; // Paso final

// Variables para almacenar los datos del formulario
const cita = {
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
        const url = 'http://localhost:3000/api/servicios'; // URL de la API
        const resultado = await fetch(url); // Realiza una solicitud a la API
        const servicios = await resultado.json(); // Convierte la respuesta a JSON
        mostrarServicios(servicios); // Muestra el resultado en la consola

    } catch (error) {
        console.log(error); // Muestra el error en la consola
    }
}

function mostrarServicios(servicios) {
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio; // Desestructura el objeto servicio
        // Crea un nuevo elemento div para cada servicio
        const nombreServicio = document.createElement('P'); // Crea un nuevo elemento p
        nombreServicio.classList.add('nombre-servicio'); // Agrega la clase 'servicio' al div creado
        nombreServicio.textContent = nombre; // Asigna el nombre del servicio al contenido del div

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$ ${precio}`;
        
        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function() {
            seleccionarServicio(servicio); // Asigna la función seleccionarServicio al evento onclick del div creado
        }

        servicioDiv.appendChild(nombreServicio); // Agrega el nombre del servicio al div creado
        servicioDiv.appendChild(precioServicio); // Agrega el precio del servicio al div creado

        document.querySelector('#servicios').appendChild(servicioDiv); // Agrega el div creado al contenedor de servicios
        
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
function nombreCliente() {  
    const nombre = document.querySelector('#cliente').value;
    cita.nombre = nombre; // Asigna el valor del input al objeto cita
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
    const resumen = document.querySelector('.contenido-resumen'); // Selecciona el contenedor del resumen

    // Elimina el contenido previo del resumen
    while (resumen.firstChild) {
        resumen.removeChild(resumen.firstChild); // Elimina el contenido previo del resumen
    }

    if (Object.values(cita).includes('') || cita.servicios.length === 0) {
        // Si hay algún campo vacío, no muestra el resumen
        mostrarAlerta('Faltan datos de servicio, fecha u hora', 'error', '.contenido-resumen', false); // Muestra un mensaje de error
        return; // Sale de la función si hay campos vacíos
    }
    
    // Formatear el div de resumen
    const { nombre, fecha, hora, servicios } = cita; // Desestructura el objeto cita para obtener los valores

    // Heading para los servicios seleccionados
    const headingServicios = document.createElement('H3'); // Crea un nuevo elemento h3 para el encabezado de servicios
    headingServicios.textContent = 'Resumen de Servicios'; // Asigna el texto al encabezado
    resumen.appendChild(headingServicios); // Agrega el encabezado al contenedor del resumen

    // Itera sobre los servicios seleccionados y los agrega al resumen
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio; // Desestructura el objeto servicio para obtener los valores
        
        const contenedorServicio = document.createElement('DIV'); // Crea un nuevo elemento div para el servicio
        contenedorServicio.classList.add('contenedor-servicio'); // Agrega la clase 'contenedor-servicio' al div creado
        
        const textoServicio = document.createElement('P'); // Crea un nuevo elemento p para el nombre del servicio
        textoServicio.textContent = nombre; // Asigna el nombre del servicio al contenido del div creado
        
        const precioServicio = document.createElement('P'); // Crea un nuevo elemento p para el precio del servicio
        precioServicio.innerHTML = `<span>Precio:</span> $ ${precio}`; // Asigna el precio del servicio al contenido del div creado
        
        contenedorServicio.appendChild(textoServicio); // Agrega el nombre del servicio al div creado
        contenedorServicio.appendChild(precioServicio); // Agrega el precio del servicio al div creado

        resumen.appendChild(contenedorServicio); // Agrega el div creado al contenedor del resumen
    });

    // Heading para Cita Resumen
    const headingCita = document.createElement('H3'); // Crea un nuevo elemento h3 para el encabezado de la cita
    headingCita.textContent = 'Resumen de la cita'; // Asigna el texto al encabezado
    resumen.appendChild(headingCita); // Agrega el encabezado al contenedor del resumen

    const nombreCliente = document.createElement('P'); // Crea un nuevo elemento p para el nombre del cliente
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`; // Asigna el nombre del cliente al contenido del p creado
    
    // Formatear la fecha
    const fechaObj = new Date(fecha); // Crea un nuevo objeto de fecha a partir de la fecha seleccionada
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const año = fechaObj.getFullYear();

    const fechaUTC = new Date(Date.UTC(año, mes, dia)); // Crea un nuevo objeto de fecha UTC a partir de la fecha seleccionada

    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }; // Opciones para formatear la fecha
    const fechaFormateada = fechaUTC.toLocaleDateString('es-CO', opciones);

    const fechaCita = document.createElement('P'); // Crea un nuevo elemento p para la fecha de la cita
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`; // Asigna la fecha de la cita al contenido del p creado

    const horaCita = document.createElement('P'); // Crea un nuevo elemento p para la hora de la cita
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`; // Asigna la hora de la cita al contenido del p creado

    // Botón para confirmar cita
    const botonReservar = document.createElement('BUTTON'); // Crea un nuevo elemento button para reservar la cita
    botonReservar.classList.add('boton'); // Agrega la clase 'button' al botón creado
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita; // Asigna la función reservarCita al evento onclick del botón creado

    resumen.appendChild(nombreCliente); // Agrega el nombre del cliente al contenedor del resumen
    resumen.appendChild(fechaCita); // Agrega la fecha de la cita al contenedor del resumen
    resumen.appendChild(horaCita); // Agrega la hora de la cita al contenedor del resumen
    resumen.appendChild(botonReservar); // Agrega el botón de reservar al contenedor del resumen
}

// Función nueva para reservar la cita
async function reservarCita(e) {
    e.preventDefault();

    const { servicios, fecha, hora } = cita;
    const selectPersona = document.querySelector('#persona');
    const personaId = selectPersona.value;
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
        hora
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
            mostrarAlerta('Cita reservada exitosamente.', 'exito', '.contenido-resumen');
            setTimeout(() => {
                window.location.href = '/cita';
            }, 3000);
        } else {
            mostrarAlerta(resultado.mensaje || 'Error al reservar la cita', 'error', '.contenido-resumen');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarAlerta('Error de conexión al reservar la cita', 'error', '.contenido-resumen');
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
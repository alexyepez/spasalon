let paso = 1;
const pasoInicial = 1; // Paso inicial
const pasoFinal = 3; // Paso final

document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion();
    tabs(); // Cambia la sección cuando se presiona un tab
    botonesPaginador(); // Cambia la sección cuando se presiona un botón de paginación
    paginaSiguiente(); // Cambia la sección cuando se presiona el botón "Siguiente"
    paginaAnterior(); // Cambia la sección cuando se presiona el botón "Anterior"
    consultarAPI(); // Llama a la función para consultar la API en el backend de php
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

        const precioServicio = document.createElement('P'); // Crea un nuevo elemento p
        precioServicio.classList.add('precio-servicio'); // Agrega la clase 'servicio' al div creado
        precioServicio.textContent = `$ ${precio}`; // Asigna el nombre del servicio al contenido del div
        
        const servicioDiv = document.createElement('DIV'); // Crea un nuevo elemento div
        servicioDiv.classList.add('servicio'); // Agrega la clase 'servicio' al div creado
        servicioDiv.dataset.idServicio = id; // Asigna el id del servicio al div creado

        servicioDiv.appendChild(nombreServicio); // Agrega el nombre del servicio al div creado
        servicioDiv.appendChild(precioServicio); // Agrega el precio del servicio al div creado

        document.querySelector('#servicios').appendChild(servicioDiv); // Agrega el div creado al contenedor de servicios
        
    });
}
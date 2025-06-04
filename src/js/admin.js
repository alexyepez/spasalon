
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado');
    iniciarApp();

    // Si estamos en la página de gestión de terapeutas
    if (document.querySelector('#form-asignar-cita') || document.querySelector('.listado-terapeutas')) {
        console.log('Página de gestión de terapeutas detectada');
        iniciarGestionTerapeutas();
    }
});


function iniciarApp() {
    console.log('Iniciando app');
}

function iniciarGestionTerapeutas() {
    console.log('Iniciando gestión de terapeutas');

    const formAsignarCita = document.querySelector('#form-asignar-cita');
    const formEditarTerapeuta = document.querySelector('#form-editar-terapeuta');
    const botonesEditar = document.querySelectorAll('.editar-terapeuta');
    const botonesEliminar = document.querySelectorAll('.eliminar-terapeuta');
    const cerrarModalBtn = document.querySelector('#btn-cerrar-modal-terapeuta');
    const modal = document.querySelector('#modal-terapeuta');

    console.log('Botones editar encontrados:', botonesEditar.length);
    console.log('Botones eliminar encontrados:', botonesEliminar.length);

    // Configurar formulario de asignar cita
    if (formAsignarCita) {
        console.log('Configurando formulario de asignar cita');
        formAsignarCita.addEventListener('submit', asignarCitaAterapeuta);
    }

    // Configurar botones de editar terapeuta
    if (botonesEditar.length > 0) {
        console.log('Configurando botones de editar');
        botonesEditar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                console.log('Botón editar clickeado');
                mostrarModalEditar(e);
            });
        });
    }

    // Configurar botones de eliminar terapeuta
    if (botonesEliminar.length > 0) {
        console.log('Configurando botones de eliminar');
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                console.log('Botón eliminar clickeado');
                confirmarEliminarTerapeuta(e);
            });
        });
    }

    // Configurar formulario de editar terapeuta
    if (formEditarTerapeuta) {
        console.log('Configurando formulario de editar terapeuta');
        formEditarTerapeuta.addEventListener('submit', actualizarTerapeuta);
    }

    // Configurar botón de cerrar modal
    if (cerrarModalBtn) {
        console.log('Configurando botón cerrar modal');
        cerrarModalBtn.addEventListener('click', () => {
            console.log('Cerrando modal');
            modal.style.display = 'none';
        });
    }
}


// Función para mostrar el modal de editar terapeuta
function mostrarModalEditar(e) {
    const terapeuta = e.currentTarget;
    const id = terapeuta.dataset.id;
    const nombre = terapeuta.dataset.nombre;
    const apellido = terapeuta.dataset.apellido;
    const email = terapeuta.dataset.email;
    const telefono = terapeuta.dataset.telefono;
    const especialidad = terapeuta.dataset.especialidad;

    // Rellenar el formulario con los datos del terapeuta
    document.querySelector('#edit-terapeuta-id').value = id;
    document.querySelector('#edit-nombre').value = nombre;
    document.querySelector('#edit-apellido').value = apellido;
    document.querySelector('#edit-email').value = email;
    document.querySelector('#edit-telefono').value = telefono;
    document.querySelector('#edit-especialidad').value = especialidad;

    // Mostrar el modal
    document.querySelector('#modal-terapeuta').style.display = 'block';
}

/// Función para actualizar terapeuta
// Función para actualizar terapeuta
async function actualizarTerapeuta(e) {
    e.preventDefault();
    console.log('Actualizando terapeuta...');

    const datos = new FormData(e.target);

    try {
        console.log('Enviando datos al servidor...');
        const url = '/admin/actualizar-terapeuta';

        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        console.log('Respuesta recibida, estado:', respuesta.status);

        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const textoRespuesta = await respuesta.text();
        console.log('Respuesta del servidor (texto):', textoRespuesta);

        // Intentar parsear como JSON
        let resultado;
        try {
            resultado = JSON.parse(textoRespuesta);
            console.log('Respuesta del servidor (JSON):', resultado);
        } catch (error) {
            console.error('Error al parsear respuesta JSON:', error);
            console.error('Texto recibido:', textoRespuesta);
            throw new Error('La respuesta del servidor no es JSON válido');
        }

        if (resultado.resultado) {
            // Éxito
            mostrarAlerta('Terapeuta actualizado correctamente', 'exito', '.modal-contenido');

            // Actualizar la vista después de 2 segundos
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Error del servidor
            mostrarAlerta(resultado.mensaje || 'Error al actualizar terapeuta', 'error', '.modal-contenido');
        }
    } catch (error) {
        console.error('Error en try/catch:', error);
        mostrarAlerta(`Error: ${error.message}`, 'error', '.modal-contenido');
    }
}



// Función para confirmar eliminar terapeuta
function confirmarEliminarTerapeuta(e) {
    if (confirm('¿Estás seguro de eliminar este terapeuta? Esta acción no se puede deshacer.')) {
        const id = e.currentTarget.dataset.id;
        eliminarTerapeuta(id);
    }
}

// Función para eliminar terapeuta
async function eliminarTerapeuta(id) {
    console.log('Eliminando terapeuta con ID:', id);

    try {
        const datos = new FormData();
        datos.append('id', id);

        console.log('Enviando solicitud para eliminar terapeuta...');
        const url = '/admin/eliminar-terapeuta';
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        console.log('Respuesta recibida, estado:', respuesta.status);

        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const textoRespuesta = await respuesta.text();
        console.log('Respuesta del servidor (texto):', textoRespuesta);

        // Intentar parsear como JSON
        let resultado;
        try {
            resultado = JSON.parse(textoRespuesta);
            console.log('Respuesta del servidor (JSON):', resultado);
        } catch (error) {
            console.error('Error al parsear respuesta JSON:', error);
            console.error('Texto recibido:', textoRespuesta);
            throw new Error('La respuesta del servidor no es JSON válido');
        }

        if (resultado.resultado) {
            // Éxito
            mostrarAlerta('Terapeuta eliminado correctamente', 'exito', '.listado-terapeutas');

            // Actualizar la vista después de 2 segundos
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Error del servidor
            mostrarAlerta(resultado.mensaje || 'Error al eliminar terapeuta', 'error', '.listado-terapeutas');
        }
    } catch (error) {
        console.error('Error en try/catch:', error);
        mostrarAlerta(`Error: ${error.message}`, 'error', '.listado-terapeutas');
    }
}


// Función para asignar cita a terapeuta
async function asignarCitaAterapeuta(e) {
    e.preventDefault();

    const datos = new FormData(this);

    try {
        const url = '/admin/asignar-cita';
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();

        if (resultado.resultado) {
            // Éxito
            mostrarAlerta('Cita asignada correctamente', 'exito', '#form-asignar-cita');

            // Actualizar la vista después de 2 segundos
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Error
            mostrarAlerta(resultado.mensaje || 'Error al asignar cita', 'error', '#form-asignar-cita');
        }
    } catch (error) {
        console.error(error);
        mostrarAlerta('Error en la comunicación con el servidor', 'error', '#form-asignar-cita');
    }
}

// Función para mostrar alertas (asumiendo que ya tienes esta función)
// Función para mostrar alertas
function mostrarAlerta(mensaje, tipo, elemento) {
    console.log('Mostrando alerta:', mensaje, tipo, elemento);

    // Eliminar alertas previas
    const alertaPrevia = document.querySelector('.alerta');
    if (alertaPrevia) {
        alertaPrevia.remove();
    }

    // Crear alerta
    const alerta = document.createElement('DIV');
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);
    alerta.textContent = mensaje;

    // Insertar alerta
    const referencia = document.querySelector(elemento);
    if (!referencia) {
        console.error('Elemento de referencia no encontrado:', elemento);
        return;
    }

    referencia.parentElement.insertBefore(alerta, referencia);

    // Quitar alerta después de 5 segundos
    setTimeout(() => {
        alerta.remove();
    }, 5000);
}

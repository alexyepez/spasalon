document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
    console.log('Iniciando app de gestión de clientes');

    // Configurar formulario de editar cliente
    const formEditarCliente = document.querySelector('#form-editar-cliente');
    if (formEditarCliente) {
        formEditarCliente.addEventListener('submit', actualizarCliente);
    }

    // Configurar botones de editar cliente
    const botonesEditar = document.querySelectorAll('.editar-cliente');
    if (botonesEditar.length > 0) {
        botonesEditar.forEach(boton => {
            boton.addEventListener('click', mostrarModalEditar);
        });
    }

    // Configurar botones de eliminar cliente
    const botonesEliminar = document.querySelectorAll('.eliminar-cliente');
    if (botonesEliminar.length > 0) {
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', confirmarEliminarCliente);
        });
    }

    // Configurar botón de cerrar modal
    const cerrarModalBtn = document.querySelector('#btn-cerrar-modal-cliente');
    if (cerrarModalBtn) {
        cerrarModalBtn.addEventListener('click', () => {
            document.querySelector('#modal-cliente').style.display = 'none';
        });
    }
}

// Función para mostrar el modal de editar cliente
function mostrarModalEditar(e) {
    console.log('Mostrando modal de editar cliente');

    const cliente = e.currentTarget;
    const id = cliente.dataset.id;
    const nombre = cliente.dataset.nombre;
    const apellido = cliente.dataset.apellido;
    const email = cliente.dataset.email;
    const telefono = cliente.dataset.telefono;

    console.log('Datos del cliente:', { id, nombre, apellido, email, telefono });

    // Rellenar el formulario con los datos del cliente
    document.querySelector('#edit-cliente-id').value = id;
    document.querySelector('#edit-nombre').value = nombre;
    document.querySelector('#edit-apellido').value = apellido;
    document.querySelector('#edit-email').value = email;
    document.querySelector('#edit-telefono').value = telefono;

    // Mostrar el modal
    document.querySelector('#modal-cliente').style.display = 'block';
}

// Función para actualizar cliente
async function actualizarCliente(e) {
    e.preventDefault();
    console.log('Actualizando cliente...');

    const datos = new FormData(e.target);

    try {
        console.log('Enviando datos al servidor...');
        const url = '/admin/actualizar-cliente';

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
            mostrarAlerta('Cliente actualizado correctamente', 'exito', '.modal-contenido');

            // Actualizar la vista después de 2 segundos
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Error del servidor
            mostrarAlerta(resultado.mensaje || 'Error al actualizar cliente', 'error', '.modal-contenido');
        }
    } catch (error) {
        console.error('Error en try/catch:', error);
        mostrarAlerta(`Error: ${error.message}`, 'error', '.modal-contenido');
    }
}

// Función para confirmar eliminar cliente
function confirmarEliminarCliente(e) {
    if (confirm('¿Estás seguro de eliminar este cliente? Esta acción no se puede deshacer y eliminará todas sus citas e información asociada.')) {
        const id = e.currentTarget.dataset.id;
        eliminarCliente(id);
    }
}

// Función para eliminar cliente
async function eliminarCliente(id) {
    console.log('Eliminando cliente con ID:', id);

    try {
        const datos = new FormData();
        datos.append('id', id);

        console.log('Enviando solicitud para eliminar cliente...');
        const url = '/admin/eliminar-cliente';
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
            mostrarAlerta('Cliente eliminado correctamente', 'exito', '.listado-clientes');

            // Actualizar la vista después de 2 segundos
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Error del servidor
            mostrarAlerta(resultado.mensaje || 'Error al eliminar cliente', 'error', '.listado-clientes');
        }
    } catch (error) {
        console.error('Error en try/catch:', error);
        mostrarAlerta(`Error: ${error.message}`, 'error', '.listado-clientes');
    }
}

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

    // Insertar alerta en el DOM
    const referencia = document.querySelector(elemento);
    if (!referencia) {
        console.error('Elemento de referencia no encontrado:', elemento);
        // Intentar mostrar la alerta en el body como último recurso
        document.body.prepend(alerta);
        return;
    }

    if (referencia.parentElement) {
        referencia.parentElement.insertBefore(alerta, referencia);
    } else {
        console.error('Elemento padre no encontrado para:', elemento);
        document.body.prepend(alerta);
    }

    // Quitar alerta después de 5 segundos
    setTimeout(() => {
        alerta.remove();
    }, 5000);
}
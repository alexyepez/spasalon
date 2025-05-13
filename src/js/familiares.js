document.addEventListener('DOMContentLoaded', () => {
    const clienteId = window.clienteId;
    cargarFamiliares(clienteId);

    document.getElementById('btn-agregar-familiar').onclick = () => mostrarModalFamiliar();
    
    document.getElementById('btn-cerrar-modal').onclick = cerrarModalFamiliar;

    document.getElementById('form-familiar').onsubmit = async function(e) {
        e.preventDefault();
        const datos = new FormData(this);
        datos.append('cliente_id', clienteId);

        let url = '/api/familiares/crear';
        let mensaje = 'Familiar agregado exitosamente';
        if (datos.get('id')) {
            url = '/api/familiares/actualizar';
            mensaje = 'Familiar actualizado exitosamente';
        }

        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const data = await respuesta.json();
        if (data.resultado) {
            cerrarModalFamiliar(); // Cierra el modal
            await cargarFamiliares(clienteId); // Espera a que se recargue la lista
            //console.log(document.querySelector('#alerta-familiares'));
            mostrarAlerta(mensaje, 'exito', '#alerta-familiares'); // Muestra la alerta en el contenedor principal
            
        } else if (data.mensaje) {
            mostrarAlerta(data.mensaje, 'error', '#alerta-modal');
        }
    };

    async function cargarFamiliares(clienteId) {
        const respuesta = await fetch(`/api/familiares?cliente_id=${clienteId}`);
        const familiares = await respuesta.json();

        // Verifica si la respuesta es un array y tiene elementos
        if (familiares.length === 0) {
            mostrarAlerta('No hay familiares registrados', 'info', '.familiares');
        }

        const lista = document.getElementById('lista-familiares');
        lista.innerHTML = '';
        familiares.forEach(familiar => {
            const li = document.createElement('li');
            li.textContent = `${familiar.nombre} ${familiar.apellido ? familiar.apellido : ''} (${familiar.parentesco})`;

            // Botón editar
            const btnEditar = document.createElement('button');
            btnEditar.textContent = 'Editar';
            btnEditar.className = 'boton';
            btnEditar.onclick = () => mostrarModalFamiliar(familiar);

            // Botón eliminar
            const btnEliminar = document.createElement('button');
            btnEliminar.textContent = 'Eliminar';
            btnEliminar.className = 'boton boton-cancelar';
            btnEliminar.onclick = async () => {
                const mensaje = `¿Eliminar a ${familiar.nombre} ${familiar.apellido ? familiar.apellido : ''} (${familiar.parentesco})?`;
                if (confirm(mensaje)) {
                    const datos = new FormData();
                    datos.append('id', familiar.id);
                    const respuesta = await fetch('/api/familiares/eliminar', { method: 'POST', body: datos });
                    const data = await respuesta.json();
                    if (data.resultado) {
                        mostrarAlerta('Familiar eliminado exitosamente', 'exito', '.familiares');
                        await cargarFamiliares(clienteId);
                    } else {
                        mostrarAlerta('Ocurrió un error al eliminar el familiar', 'error', '.familiares');
                    }
                }
            };

            li.appendChild(btnEditar);
            li.appendChild(btnEliminar);
            lista.appendChild(li);
        });

        // Actualiza el select de persona en el formulario de cita
        actualizarSelectPersona(familiares, clienteId);
    }

    function mostrarModalFamiliar(familiar = null) {
        document.getElementById('modal-familiar').style.display = 'block';
        document.getElementById('familiar-id').value = familiar ? familiar.id : '';
        document.getElementById('familiar-nombre').value = familiar ? familiar.nombre : '';
        document.getElementById('familiar-apellido').value = familiar ? familiar.apellido || '' : '';
        document.getElementById('familiar-parentesco').value = familiar ? familiar.parentesco : '';
        document.getElementById('familiar-fecha-nacimiento').value = familiar ? (familiar.fecha_nacimiento || '') : '';
        document.getElementById('familiar-telefono').value = familiar ? familiar.telefono || '' : '';
    }

   function cerrarModalFamiliar() {
        document.getElementById('modal-familiar').style.display = 'none';
        document.getElementById('form-familiar').reset();
        document.getElementById('alerta-modal').innerHTML = ''; // Limpia la alerta del modal
    }

    // Actualiza el select de persona en el formulario de cita
    function actualizarSelectPersona(familiares, clienteId) {
        const select = document.getElementById('persona');
        const nombreCliente = select.options[0].textContent; // Mantener el texto original del cliente
        select.innerHTML = '';
        // Opción para el cliente
        const opcionCliente = document.createElement('option');
        opcionCliente.value = clienteId;
        opcionCliente.textContent = nombreCliente;
        select.appendChild(opcionCliente);

        // Opciones para familiares
        familiares.forEach(familiar => {
            const option = document.createElement('option');
            option.value = familiar.id;
            option.textContent = `${familiar.nombre} ${familiar.apellido ? familiar.apellido : ''} (${familiar.parentesco})`;
            select.appendChild(option);
        });
    }

    // Muestra la alerta en el contenedor principal
    function mostrarAlerta(mensaje, tipo, selector) {
        const referencia = document.querySelector(selector);
        const alertaPrevia = referencia.querySelector('.alerta');
        if (alertaPrevia) alertaPrevia.remove();

        const alerta = document.createElement('DIV');
        alerta.textContent = mensaje;
        alerta.classList.add('alerta', tipo);
        referencia.appendChild(alerta);

        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }
});
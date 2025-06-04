document.addEventListener('DOMContentLoaded', function() {
    iniciarRecordatorios();
});

function iniciarRecordatorios() {
    const formCrearRecordatorio = document.querySelector('#form-crear-recordatorio');
    const botonesEliminarRecordatorio = document.querySelectorAll('.eliminar-recordatorio');
    const botonesEnviarRecordatorio = document.querySelectorAll('.enviar-recordatorio');
    const botonesFiltro = document.querySelectorAll('.boton-filtro');

    // Una única declaración para los selectores
    const selectCliente = document.querySelector('#cliente_id');
    const selectCita = document.querySelector('#cita_id');

    // Configurar formulario de creación
    if (formCrearRecordatorio) {
        formCrearRecordatorio.addEventListener('submit', function(e) {
            // Validaciones personalizadas si son necesarias
        });
    }

    // Configurar botones de eliminar
    if (botonesEliminarRecordatorio) {
        botonesEliminarRecordatorio.forEach(boton => {
            boton.addEventListener('click', function() {
                const id = this.dataset.id;
                eliminarRecordatorio(id);
            });
        });
    }

    // Configurar botones de enviar individualmente
    if (botonesEnviarRecordatorio) {
        botonesEnviarRecordatorio.forEach(boton => {
            boton.addEventListener('click', function() {
                const id = this.dataset.id;
                enviarRecordatorio(id);
            });
        });
    }

    // Configurar botones de filtro
    if (botonesFiltro) {
        botonesFiltro.forEach(boton => {
            boton.addEventListener('click', function() {
                const filtro = this.dataset.filtro;
                aplicarFiltro(filtro);

                // Marcar el botón activo
                botonesFiltro.forEach(btn => btn.classList.remove('activo'));
                this.classList.add('activo');
            });
        });
    }

    // Mostrar detalles al cambiar la cita seleccionada
    if (selectCita) {
        selectCita.addEventListener('change', mostrarDetallesCita);
    }

    // Filtrar citas según el cliente seleccionado
    if (selectCliente && selectCita) {
        selectCliente.addEventListener('change', function() {
            const clienteId = this.value;

            // Ocultar todas las opciones de citas excepto la primera (placeholder)
            Array.from(selectCita.options).forEach((option, index) => {
                if (index === 0) return; // Mantener la opción "Seleccione una cita"

                const citaCompleta = option.textContent;
                if (clienteId === '' || citaCompleta.includes(`Cliente: ${this.options[this.selectedIndex].textContent.trim()}`)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });

            // Restablecer la selección de cita
            selectCita.value = '';

            // Ocultar detalles de cita
            const detallesCita = document.getElementById('detalles-cita');
            if (detallesCita) {
                detallesCita.classList.add('oculto');
            }
        });
    }
}

function aplicarFiltro(filtro) {
    const recordatorios = document.querySelectorAll('.recordatorio');

    recordatorios.forEach(recordatorio => {
        if (filtro === 'todos') {
            recordatorio.style.display = 'flex';
        } else if (filtro === 'pendientes' && recordatorio.classList.contains('recordatorio-pendiente')) {
            recordatorio.style.display = 'flex';
        } else if (filtro === 'enviados' && recordatorio.classList.contains('recordatorio-enviado')) {
            recordatorio.style.display = 'flex';
        } else {
            recordatorio.style.display = 'none';
        }
    });
}

function mostrarDetallesCita() {
    const citaId = this.value;
    const detallesCita = document.getElementById('detalles-cita');

    if (!citaId) {
        detallesCita.classList.add('oculto');
        return;
    }

    // Obtener el texto de la opción seleccionada
    const opcionSeleccionada = this.options[this.selectedIndex];
    const textoCita = opcionSeleccionada.textContent;

    // Extraer información
    const infoCliente = textoCita.includes('Cliente:') ?
        textoCita.split('Cliente:')[1].split('-')[0].trim() : 'No disponible';
    const infoFecha = textoCita.includes('Fecha:') ?
        textoCita.split('Fecha:')[1].trim() : 'No disponible';

    // Mostrar detalles
    detallesCita.innerHTML = `
        <h3>Detalles de la Cita</h3>
        <p><strong>ID:</strong> <span class="valor-destacado">${citaId}</span></p>
        <p><strong>Cliente:</strong> <span class="valor-destacado">${infoCliente}</span></p>
        <p><strong>Fecha:</strong> <span class="valor-destacado">${infoFecha}</span></p>
        <p class="aviso">Se enviará un recordatorio al cliente para esta cita</p>
    `;

    detallesCita.classList.remove('oculto');
}

function enviarRecordatorio(id) {
    if (confirm('¿Deseas enviar este recordatorio ahora?')) {
        fetch('/admin/enviar-recordatorio', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
            .then(response => response.json())
            .then(resultado => {
                if (resultado.resultado) {
                    mostrarAlerta('Recordatorio enviado exitosamente', 'exito', '.contenedor-alertas');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    mostrarAlerta(resultado.mensaje || 'Error al enviar el recordatorio', 'error', '.contenedor-alertas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error al enviar el recordatorio', 'error', '.contenedor-alertas');
            });
    }
}

function eliminarRecordatorio(id) {
    if (confirm('¿Estás seguro de eliminar este recordatorio?')) {
        fetch('/admin/eliminar-recordatorio', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
            .then(response => response.json())
            .then(resultado => {
                if (resultado.resultado) {
                    mostrarAlerta('Recordatorio eliminado exitosamente', 'exito', '.contenedor-alertas');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    mostrarAlerta(resultado.mensaje || 'Error al eliminar el recordatorio', 'error', '.contenedor-alertas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error al eliminar el recordatorio', 'error', '.contenedor-alertas');
            });
    }
}

function mostrarAlerta(mensaje, tipo, elemento) {
    // Limpiar alertas previas
    const alertaPrevia = document.querySelector(`${elemento} .alerta`);
    if (alertaPrevia) {
        alertaPrevia.remove();
    }

    // Crear alerta
    const alerta = document.createElement('DIV');
    alerta.classList.add('alerta', tipo);
    alerta.textContent = mensaje;

    // Insertar en el DOM
    document.querySelector(elemento).appendChild(alerta);

    // Eliminar después de 3 segundos
    setTimeout(() => {
        alerta.remove();
    }, 3000);
}
document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
    // Inicializar los eventos para editar/eliminar proveedores
    iniciarProveedores();
    // Inicializar los eventos para editar/eliminar inventario
    iniciarInventario();
}

function iniciarProveedores() {
    const formEditarProveedor = document.querySelector('#form-editar-proveedor');
    const cerrarModalProveedorBtn = document.querySelector('#btn-cerrar-modal-proveedor');
    const modalProveedor = document.querySelector('#modal-proveedor');
    const botonesEditarProveedor = document.querySelectorAll('.editar-proveedor');
    const botonesEliminarProveedor = document.querySelectorAll('.eliminar-proveedor');

    // Configurar botones de editar proveedor
    if (botonesEditarProveedor) {
        botonesEditarProveedor.forEach(boton => {
            boton.addEventListener('click', function() {
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;
                const contacto = this.dataset.contacto;
                const telefono = this.dataset.telefono;
                const email = this.dataset.email;
                const direccion = this.dataset.direccion;

                // Llenar el formulario con los datos actuales
                document.querySelector('#edit-proveedor-id').value = id;
                document.querySelector('#edit-nombre').value = nombre;
                document.querySelector('#edit-contacto').value = contacto;
                document.querySelector('#edit-telefono').value = telefono || '';
                document.querySelector('#edit-email').value = email || '';
                document.querySelector('#edit-direccion').value = direccion || '';

                // Mostrar el modal
                modalProveedor.style.display = 'block';
            });
        });
    }

    // Configurar botones de eliminar proveedor
    if (botonesEliminarProveedor) {
        botonesEliminarProveedor.forEach(boton => {
            boton.addEventListener('click', function() {
                const id = this.dataset.id;
                eliminarProveedor(id);
            });
        });
    }

    // Cerrar modal de proveedor
    if (cerrarModalProveedorBtn) {
        cerrarModalProveedorBtn.addEventListener('click', () => {
            modalProveedor.style.display = 'none';
            formEditarProveedor.reset();
        });
    }

    // Enviar formulario para actualizar proveedor
    if (formEditarProveedor) {
        formEditarProveedor.addEventListener('submit', async (e) => {
            e.preventDefault();
            const datos = new FormData(formEditarProveedor);

            try {
                const respuesta = await fetch('/admin/actualizar-proveedor', {
                    method: 'POST',
                    body: datos
                });

                const resultado = await respuesta.json();

                if (resultado.resultado) {
                    modalProveedor.style.display = 'none';
                    mostrarAlerta('Proveedor actualizado exitosamente', 'exito', '.contenedor-alertas');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    mostrarAlerta(resultado.mensaje || 'Error al actualizar el proveedor', 'error', '.contenedor-alertas');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta('Error al actualizar el proveedor', 'error', '.contenedor-alertas');
            }
        });
    }
}

function iniciarInventario() {
    const formEditarInventario = document.querySelector('#form-editar-inventario');
    const cerrarModalInventarioBtn = document.querySelector('#btn-cerrar-modal-inventario');
    const modalInventario = document.querySelector('#modal-inventario');
    const botonesEditarInventario = document.querySelectorAll('.editar-inventario');
    const botonesEliminarInventario = document.querySelectorAll('.eliminar-inventario');

    // Configurar botones de editar inventario
    if (botonesEditarInventario) {
        botonesEditarInventario.forEach(boton => {
            boton.addEventListener('click', function() {
                const id = this.dataset.id;
                const producto = this.dataset.producto;
                const descripcion = this.dataset.descripcion;
                const precio = this.dataset.precio;
                const cantidad = this.dataset.cantidad;
                const proveedorId = this.dataset.proveedorId;
                const fechaIngreso = this.dataset.fechaIngreso;

                // Llenar el formulario con los datos actuales
                document.querySelector('#edit-inventario-id').value = id;
                document.querySelector('#edit-producto').value = producto;
                document.querySelector('#edit-descripcion').value = descripcion;
                document.querySelector('#edit-precio').value = precio;
                document.querySelector('#edit-cantidad').value = cantidad;
                document.querySelector('#edit-proveedor_id').value = proveedorId;
                document.querySelector('#edit-fecha_ingreso').value = fechaIngreso;

                // Mostrar el modal
                modalInventario.style.display = 'block';
            });
        });
    }

    // Configurar botones de eliminar inventario
    if (botonesEliminarInventario) {
        botonesEliminarInventario.forEach(boton => {
            boton.addEventListener('click', function() {
                const id = this.dataset.id;
                eliminarInventario(id);
            });
        });
    }

    // Cerrar modal de inventario
    if (cerrarModalInventarioBtn) {
        cerrarModalInventarioBtn.addEventListener('click', () => {
            modalInventario.style.display = 'none';
            formEditarInventario.reset();
        });
    }

    // Enviar formulario para actualizar inventario
    if (formEditarInventario) {
        formEditarInventario.addEventListener('submit', async (e) => {
            e.preventDefault();
            const datos = new FormData(formEditarInventario);

            try {
                const respuesta = await fetch('/admin/actualizar-inventario', {
                    method: 'POST',
                    body: datos
                });

                const resultado = await respuesta.json();

                if (resultado.resultado) {
                    modalInventario.style.display = 'none';
                    mostrarAlerta('Producto actualizado exitosamente', 'exito', '.contenedor-alertas');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    mostrarAlerta(resultado.mensaje || 'Error al actualizar el producto', 'error', '.contenedor-alertas');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta('Error al actualizar el producto', 'error', '.contenedor-alertas');
            }
        });
    }
}

function eliminarProveedor(id) {
    if (confirm('¿Estás seguro de eliminar este proveedor?')) {
        fetch('/admin/eliminar-proveedor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
            .then(response => response.json())
            .then(resultado => {
                if (resultado.resultado) {
                    mostrarAlerta('Proveedor eliminado exitosamente', 'exito', '.contenedor-alertas');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    mostrarAlerta(resultado.mensaje || 'Error al eliminar el proveedor', 'error', '.contenedor-alertas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error al eliminar el proveedor', 'error', '.contenedor-alertas');
            });
    }
}

function eliminarInventario(id) {
    if (confirm('¿Estás seguro de eliminar este producto del inventario?')) {
        fetch('/admin/eliminar-inventario', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
            .then(response => response.json())
            .then(resultado => {
                if (resultado.resultado) {
                    mostrarAlerta('Producto eliminado exitosamente', 'exito', '.contenedor-alertas');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    mostrarAlerta(resultado.mensaje || 'Error al eliminar el producto', 'error', '.contenedor-alertas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error al eliminar el producto', 'error', '.contenedor-alertas');
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
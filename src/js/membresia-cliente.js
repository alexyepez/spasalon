document.addEventListener('DOMContentLoaded', function() {
    const membresiaSelect = document.querySelector('#membresia_id');
    const fechaInicio = document.querySelector('#fecha_inicio');
    const fechaFin = document.querySelector('#fecha_fin');

    const resumenNombre = document.querySelector('#resumen-nombre');
    const resumenPrecio = document.querySelector('#resumen-precio');
    const resumenDescuento = document.querySelector('#resumen-descuento');
    const resumenDuracion = document.querySelector('#resumen-duracion');

    // Actualizar resumen cuando cambie la selección
    function actualizarResumen() {
        if (membresiaSelect.value === '') {
            resumenNombre.textContent = '-';
            resumenPrecio.textContent = '-';
            resumenDescuento.textContent = '-';
            resumenDuracion.textContent = '-';
            return;
        }

        const opcionSeleccionada = membresiaSelect.options[membresiaSelect.selectedIndex];
        const nombre = opcionSeleccionada.text.split(' - ')[0];
        const precio = opcionSeleccionada.dataset.precio;
        const descuento = opcionSeleccionada.dataset.descuento;

        // Calcular duración
        let duracion = '-';
        if (fechaInicio.value && fechaFin.value) {
            const inicio = new Date(fechaInicio.value);
            const fin = new Date(fechaFin.value);
            const diferenciaMilisegundos = fin - inicio;
            const diferenciaDias = Math.ceil(diferenciaMilisegundos / (1000 * 60 * 60 * 24));

            if (diferenciaDias > 0) {
                const años = Math.floor(diferenciaDias / 365);
                const meses = Math.floor((diferenciaDias % 365) / 30);
                const dias = Math.floor((diferenciaDias % 365) % 30);

                let duracionTexto = '';
                if (años > 0) duracionTexto += `${años} año${años !== 1 ? 's' : ''} `;
                if (meses > 0) duracionTexto += `${meses} mes${meses !== 1 ? 'es' : ''} `;
                if (dias > 0) duracionTexto += `${dias} día${dias !== 1 ? 's' : ''}`;

                duracion = duracionTexto.trim();
            } else if (diferenciaDias === 0) {
                duracion = '1 día';
            } else {
                duracion = 'Fecha de fin debe ser posterior a la de inicio';
            }
        }

        resumenNombre.textContent = nombre;
        resumenPrecio.textContent = `$${parseFloat(precio).toFixed(2)}`;
        resumenDescuento.textContent = `${descuento}%`;
        resumenDuracion.textContent = duracion;
    }

    if (membresiaSelect && fechaInicio && fechaFin) {
        membresiaSelect.addEventListener('change', actualizarResumen);
        fechaInicio.addEventListener('change', actualizarResumen);
        fechaFin.addEventListener('change', actualizarResumen);

        // Inicializar resumen
        actualizarResumen();
    }
});
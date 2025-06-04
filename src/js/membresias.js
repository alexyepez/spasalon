document.addEventListener('DOMContentLoaded', function() {
    const formulariosEliminar = document.querySelectorAll('.formulario-eliminar');

    formulariosEliminar.forEach(formulario => {
        formulario.addEventListener('submit', function(e) {
            e.preventDefault();

            const confirmar = confirm('¿Estás seguro de eliminar esta membresía? Esta acción no se puede deshacer.');

            if (confirmar) {
                this.submit();
            }
        });
    });
});
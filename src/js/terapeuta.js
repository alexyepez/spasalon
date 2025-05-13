document.addEventListener('DOMContentLoaded', function() {
    gestionarTratamientos();
});

function gestionarTratamientos() {
    const registrarBotones = document.querySelectorAll('.registrar-tratamiento');
    const modal = document.querySelector('#modal-tratamiento');
    const cerrarModalBtn = document.querySelector('#btn-cerrar-modal-tratamiento');

    registrarBotones.forEach(boton => {
        boton.addEventListener('click', (e) => {
            const citaId = e.target.closest('.cita-item').dataset.citaId;
            document.querySelector('#tratamiento-cita-id').value = citaId;
            modal.style.display = 'block';
        });
    });

    cerrarModalBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });
}
/*
|--------------------------------------------------------------------------
| Gestión de citas - funciones principales
| Administra el formulario de creación y la simulación de agenda semanal.
|--------------------------------------------------------------------------
*/

const formCitas = document.getElementById('formCitas');
const agendaGrid = document.getElementById('agendaGrid');
const btnRefrescarAgenda = document.getElementById('btnRefrescarAgenda');

const agendaState = {
    bloques: [],
};

function generarBloques() {
    const dias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'];
    agendaState.bloques = dias.map((dia) => ({ dia, cupos: Math.floor(Math.random() * 6) + 1 }));
}

function renderAgenda() {
    if (!agendaGrid) return;
    if (agendaState.bloques.length === 0) {
        agendaGrid.innerHTML = '<p class="agenda-grid__empty">Aún no se han generado bloques de agenda.</p>';
        return;
    }

    agendaGrid.innerHTML = agendaState.bloques
        .map(
            (bloque) => `
                <article class="agenda-card">
                    <h4>${bloque.dia}</h4>
                    <p>${bloque.cupos} cupos disponibles</p>
                </article>
            `,
        )
        .join('');
}

function handleSubmitCita(event) {
    event.preventDefault();
    const formData = new FormData(event.currentTarget);
    const payload = Object.fromEntries(formData.entries());
    console.table(payload);
    event.currentTarget.reset();
}

function bootstrapCitas() {
    generarBloques();
    renderAgenda();

    formCitas?.addEventListener('submit', handleSubmitCita);
    btnRefrescarAgenda?.addEventListener('click', () => {
        generarBloques();
        renderAgenda();
    });
}

window.addEventListener('DOMContentLoaded', bootstrapCitas);

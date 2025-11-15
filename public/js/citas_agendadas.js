/*
|--------------------------------------------------------------------------
| Citas agendadas - tablero kanban
| Simula el llenado de columnas y el filtrado por estado o texto.
|--------------------------------------------------------------------------
*/

const citasColumns = {
    pending: document.getElementById('columnPending'),
    done: document.getElementById('columnDone'),
    cancelled: document.getElementById('columnCancelled'),
};

const counters = {
    pending: document.getElementById('countPending'),
    done: document.getElementById('countDone'),
    cancelled: document.getElementById('countCancelled'),
};

const busquedaCitas = document.getElementById('busquedaCitas');
const estadoCita = document.getElementById('estadoCita');

const mockCitas = [
    { id: 1, mascota: 'Luna', propietario: 'Ana', estado: 'pending' },
    { id: 2, mascota: 'Max', propietario: 'Luis', estado: 'done' },
    { id: 3, mascota: 'Nina', propietario: 'Carlos', estado: 'cancelled' },
];

function renderColumns(filterText = '', filterEstado = 'all') {
    Object.values(citasColumns).forEach((column) => column && column.replaceChildren());

    const filtradas = mockCitas.filter((cita) => {
        const texto = filterText.toLowerCase();
        const coincideTexto = `${cita.mascota} ${cita.propietario}`.toLowerCase().includes(texto);
        const coincideEstado = filterEstado === 'all' || cita.estado === filterEstado;
        return coincideTexto && coincideEstado;
    });

    const contador = { pending: 0, done: 0, cancelled: 0 };

    if (filtradas.length === 0) {
        Object.entries(citasColumns).forEach(([estado, column]) => {
            if (!column) return;
            column.innerHTML = '<p class="citas-column__empty">No hay registros.</p>';
            counters[estado].textContent = '0';
        });
        return;
    }

    filtradas.forEach((cita) => {
        const column = citasColumns[cita.estado];
        if (!column) return;

        const card = document.createElement('article');
        card.className = 'cita-card';
        card.innerHTML = `
            <h4>${cita.mascota}</h4>
            <p>Propietario: ${cita.propietario}</p>
        `;

        column.appendChild(card);
        contador[cita.estado] += 1;
    });

    Object.entries(counters).forEach(([estado, element]) => {
        element.textContent = String(contador[estado]);
    });
}

function bootstrapCitasAgendadas() {
    renderColumns();
    busquedaCitas?.addEventListener('input', () => {
        renderColumns(busquedaCitas.value, estadoCita?.value ?? 'all');
    });
    estadoCita?.addEventListener('change', () => {
        renderColumns(busquedaCitas?.value ?? '', estadoCita.value);
    });
}

window.addEventListener('DOMContentLoaded', bootstrapCitasAgendadas);

/*
|--------------------------------------------------------------------------
| Historias registradas - render dinámico
| Genera cards ficticias para ejemplificar la modularidad del tablero.
|--------------------------------------------------------------------------
*/

const historiasGrid = document.getElementById('historiasGrid');
const ordenHistorias = document.getElementById('ordenHistorias');
const estadoHistorias = document.getElementById('estadoHistorias');

const mockHistorias = [
    { numero: 'HC-0001', mascota: 'Luna', estado: 'Activa', propietario: 'Ana Flores' },
    { numero: 'HC-0002', mascota: 'Simba', estado: 'Activa', propietario: 'Luis Pérez' },
    { numero: 'HC-0003', mascota: 'Max', estado: 'Archivada', propietario: 'Claudia Ruiz' },
];

function ordenarHistorias(collection, orden) {
    const base = [...collection];
    if (orden === 'alphabetic') {
        return base.sort((a, b) => a.mascota.localeCompare(b.mascota));
    }
    if (orden === 'older') {
        return base.reverse();
    }
    return base;
}

function filtrarHistorias(collection, estado) {
    if (estado === 'all') {
        return collection;
    }
    return collection.filter((item) => {
        if (estado === 'active') return item.estado === 'Activa';
        if (estado === 'archived') return item.estado === 'Archivada';
        return true;
    });
}

function renderHistoriasRegistradas() {
    if (!historiasGrid) return;

    const orden = ordenHistorias?.value ?? 'recent';
    const estado = estadoHistorias?.value ?? 'all';

    const historias = filtrarHistorias(ordenarHistorias(mockHistorias, orden), estado);

    if (historias.length === 0) {
        historiasGrid.innerHTML = `
            <article class="historia-card historia-card--empty">
                <p>No se encontraron historias.</p>
                <span>Prueba cambiando los filtros.</span>
            </article>
        `;
        return;
    }

    const template = historias
        .map(
            (historia) => `
                <article class="historia-card">
                    <span class="historia-card__number">${historia.numero}</span>
                    <h3>${historia.mascota}</h3>
                    <p>Propietario: <strong>${historia.propietario}</strong></p>
                    <span class="historia-card__status">${historia.estado}</span>
                </article>
            `,
        )
        .join('');

    historiasGrid.innerHTML = template;
}

function bootstrapHistoriasRegistradas() {
    ordenHistorias?.addEventListener('change', renderHistoriasRegistradas);
    estadoHistorias?.addEventListener('change', renderHistoriasRegistradas);
    renderHistoriasRegistradas();
}

window.addEventListener('DOMContentLoaded', bootstrapHistoriasRegistradas);

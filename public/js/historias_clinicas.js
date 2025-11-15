/*
|--------------------------------------------------------------------------
| Historias clínicas - lógica básica
| Maneja la creación del registro local y la actualización de la tabla.
|--------------------------------------------------------------------------
*/

const historiaState = {
    registros: [],
};

const historiaForm = document.getElementById('formHistoriaClinica');
const historiaTableBody = document.getElementById('historiaTableBody');
const busquedaHistorias = document.getElementById('busquedaHistorias');

/**
 * Renderiza la tabla de historias a partir del estado actual.
 */
function renderHistorias(filter = '') {
    const fragment = document.createDocumentFragment();
    const registros = historiaState.registros.filter((historia) => {
        if (!filter) return true;
        const term = filter.toLowerCase();
        return (
            historia.numero.toLowerCase().includes(term) ||
            historia.mascota.toLowerCase().includes(term) ||
            historia.propietario.toLowerCase().includes(term)
        );
    });

    if (registros.length === 0) {
        const emptyRow = document.createElement('tr');
        emptyRow.innerHTML = '<td colspan="5" class="historia-table__empty">Sin coincidencias.</td>';
        fragment.appendChild(emptyRow);
    } else {
        registros.forEach((historia) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${historia.numero}</td>
                <td>${historia.mascota}</td>
                <td>${historia.propietario}</td>
                <td>${historia.estado}</td>
                <td><button class="btn btn-secondary" data-id="${historia.numero}">Detalle</button></td>
            `;
            fragment.appendChild(row);
        });
    }

    historiaTableBody.replaceChildren(fragment);
}

/**
 * Maneja el envío del formulario y persiste el registro en memoria.
 */
function handleHistoriaSubmit(event) {
    event.preventDefault();
    const formData = new FormData(event.currentTarget);

    const registro = {
        numero: formData.get('numero_historia') ?? '',
        mascota: formData.get('paciente') ?? '',
        propietario: formData.get('propietario') ?? '',
        notas: formData.get('notas') ?? '',
        estado: 'Activa',
    };

    historiaState.registros.unshift(registro);
    renderHistorias(busquedaHistorias?.value || '');
    event.currentTarget.reset();
}

/**
 * Inicializa los listeners del módulo.
 */
function bootstrapHistoriasClinicas() {
    if (historiaForm) {
        historiaForm.addEventListener('submit', handleHistoriaSubmit);
    }

    if (busquedaHistorias) {
        busquedaHistorias.addEventListener('input', (event) => {
            renderHistorias(event.target.value);
        });
    }

    renderHistorias();
}

window.addEventListener('DOMContentLoaded', bootstrapHistoriasClinicas);

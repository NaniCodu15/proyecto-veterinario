const links = Array.from(document.querySelectorAll('.sidebar-menu a.nav-link'));
const sections = Array.from(document.querySelectorAll('#main-content .section'));
const configElement = document.getElementById('dashboard-config');
const btnIrHistorias = document.querySelector('.btn-ir-historias');
const listaCitasProximas = document.getElementById('citasProximasLista');

let dashboardConfig = window.dashboardConfig;

if (!dashboardConfig || typeof dashboardConfig !== 'object') {
    dashboardConfig = {};
    if (configElement) {
        try {
            dashboardConfig = JSON.parse(configElement.textContent || '{}');
        } catch (error) {
            dashboardConfig = {};
        }
    }
    window.dashboardConfig = dashboardConfig;
}

const citasUpcomingUrl = dashboardConfig.citasUpcomingUrl || '';
let citasProximasIntervalId = null;

function showSection(key) {
    sections.forEach(sec => {
        const activa = sec.id === `section-${key}`;
        sec.style.display = activa ? 'block' : 'none';
        sec.classList.toggle('active', activa);
    });
}

function clearActiveLinks() {
    links.forEach(link => link.classList.remove('active', 'nav-link--parent-active'));
}

function setActiveLink(link) {
    if (!link) {
        return;
    }

    clearActiveLinks();
    link.classList.add('active');

    const parentSection = link.dataset.parent;
    if (parentSection) {
        const parentLink = document.querySelector(`.sidebar-menu a.nav-link[data-section="${parentSection}"]`);
        parentLink?.classList.add('nav-link--parent-active');
    } else if (link.closest('.sidebar-item--has-submenu')) {
        link.classList.add('nav-link--parent-active');
    }
}

function manejarNavegacion(link) {
    if (!link) {
        return;
    }

    const key = link.dataset.section;
    if (!key) {
        return;
    }

    setActiveLink(link);
    showSection(key);

    if ((key === 'historias' || key === 'historias-registradas') && typeof window.cargarHistorias === 'function') {
        window.cargarHistorias();
    }

    if ((key === 'citas' || key === 'citas-agendadas') && typeof window.cargarCitas === 'function') {
        window.cargarCitas(window.citasBusquedaActual || '');
    }
}

function navegarAHistorias() {
    const linkHistorias = document.querySelector('.sidebar-menu a[data-section="historias"]');
    if (linkHistorias) {
        manejarNavegacion(linkHistorias);
    }
}

window.navegarAHistorias = navegarAHistorias;

links.forEach(link => {
    link.addEventListener('click', event => {
        event.preventDefault();
        manejarNavegacion(link);
    });
});

if (btnIrHistorias) {
    btnIrHistorias.addEventListener('click', event => {
        event.preventDefault();
        navegarAHistorias();
    });
}

function formatearFechaCorta(fechaIso = '', fechaLegible = '', fechaCorta = '') {
    if (fechaLegible) {
        return fechaLegible;
    }

    if (fechaCorta) {
        return fechaCorta;
    }

    if (!fechaIso) {
        return '--/--';
    }

    const partes = fechaIso.split('-');
    if (partes.length !== 3) {
        return fechaIso;
    }

    const [anio, mes, dia] = partes;
    return `${dia}/${mes}`;
}

function formatearHoraCita(hora = '') {
    if (!hora) {
        return '--:--';
    }

    return hora.toString().padStart(5, '0');
}

function parseFechaIso(fecha = '') {
    if (!fecha) {
        return null;
    }

    const partes = fecha.split('-').map(parte => parseInt(parte, 10));
    if (partes.length !== 3 || partes.some(num => Number.isNaN(num))) {
        return null;
    }

    const [anio, mes, dia] = partes;
    const date = new Date(Date.UTC(anio, mes - 1, dia));
    return Number.isNaN(date.getTime()) ? null : date;
}

function obtenerClaseEstadoCitaProxima(estado = '') {
    const normalizado = String(estado || '').trim().toLowerCase();

    switch (normalizado) {
        case 'atendida':
            return 'is-done';
        case 'reprogramada':
            return 'is-rescheduled';
        case 'cancelada':
            return 'is-cancelled';
        case 'pendiente':
        default:
            return 'is-pending';
    }
}

function renderCitasProximas(lista = []) {
    if (!listaCitasProximas) {
        return;
    }

    listaCitasProximas.innerHTML = '';

    if (!lista.length) {
        const item = document.createElement('li');
        item.className = 'appointment-list__item appointment-list__item--empty';
        item.innerHTML = `
            <div>
                <p>No hay citas próximas registradas.</p>
                <span>Agenda una nueva cita para mantener una atención oportuna.</span>
            </div>
        `;
        listaCitasProximas.appendChild(item);
        return;
    }

    const fragment = document.createDocumentFragment();
    const ordenadas = [...lista].sort((a, b) => {
        const fechaA = parseFechaIso(a?.fecha);
        const fechaB = parseFechaIso(b?.fecha);

        if (fechaA && fechaB && fechaA.getTime() !== fechaB.getTime()) {
            return fechaA - fechaB;
        }

        if (fechaA && !fechaB) {
            return -1;
        }

        if (!fechaA && fechaB) {
            return 1;
        }

        const horaA = (a?.hora || '').toString();
        const horaB = (b?.hora || '').toString();
        return horaA.localeCompare(horaB);
    });

    ordenadas.forEach(cita => {
        const item = document.createElement('li');
        item.className = 'appointment-list__item';

        const timeWrapper = document.createElement('div');
        timeWrapper.className = 'appointment-list__time';

        const hora = document.createElement('span');
        hora.className = 'appointment-list__hour';
        hora.textContent = formatearHoraCita(cita?.hora);
        timeWrapper.appendChild(hora);

        const fecha = document.createElement('span');
        fecha.className = 'appointment-list__date';
        fecha.textContent = formatearFechaCorta(cita?.fecha, cita?.fecha_legible, cita?.fecha_corta);
        timeWrapper.appendChild(fecha);

        const details = document.createElement('div');
        details.className = 'appointment-list__details';

        const pet = document.createElement('p');
        pet.className = 'appointment-list__pet';
        const mascota = cita?.mascota ?? 'Sin mascota';
        const motivo = cita?.motivo ? ` · ${cita.motivo}` : '';
        pet.textContent = `${mascota}${motivo}`;
        details.appendChild(pet);

        const owner = document.createElement('span');
        owner.className = 'appointment-list__owner';
        owner.textContent = `Propietario: ${cita?.propietario ?? 'Sin propietario'}`;
        details.appendChild(owner);

        const status = document.createElement('span');
        status.className = `appointment-list__status ${obtenerClaseEstadoCitaProxima(cita?.estado)}`;
        status.textContent = cita?.estado ?? 'Pendiente';

        item.appendChild(timeWrapper);
        item.appendChild(details);
        item.appendChild(status);
        fragment.appendChild(item);
    });

    listaCitasProximas.appendChild(fragment);
}

async function cargarCitasProximas() {
    if (!citasUpcomingUrl || !listaCitasProximas) {
        return;
    }

    try {
        const response = await fetch(citasUpcomingUrl, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            throw new Error('No se pudieron obtener las citas próximas.');
        }

        const data = await response.json();
        const lista = Array.isArray(data?.data) ? data.data : [];
        renderCitasProximas(lista);
    } catch (error) {
        console.error(error);
    }
}

function iniciarActualizacionCitasProximas() {
    if (!listaCitasProximas || !citasUpcomingUrl) {
        return;
    }

    if (citasProximasIntervalId) {
        window.clearInterval(citasProximasIntervalId);
    }

    cargarCitasProximas();
    citasProximasIntervalId = window.setInterval(() => {
        cargarCitasProximas();
    }, 60000);
}

const enlaceInicio = document.querySelector('.sidebar-menu a[data-section="inicio"]');

document.addEventListener('DOMContentLoaded', () => {
    if (enlaceInicio) {
        manejarNavegacion(enlaceInicio);
    }

    if (typeof window.cargarHistorias === 'function') {
        window.cargarHistorias();
    }

    if (typeof window.cargarCitas === 'function') {
        window.cargarCitas(window.citasBusquedaActual || '');
    }

    iniciarActualizacionCitasProximas();
});

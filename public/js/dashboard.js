const links = Array.from(document.querySelectorAll('.sidebar-menu a.nav-link'));
const sections = Array.from(document.querySelectorAll('#main-content .section'));

const configElement = document.getElementById('dashboard-config');
let dashboardConfig = {};

if (configElement) {
    try {
        dashboardConfig = JSON.parse(configElement.textContent || '{}');
    } catch (error) {
        dashboardConfig = {};
    }
}

const historiaListUrl = dashboardConfig.historiaListUrl || '';
const historiaStoreUrl = dashboardConfig.historiaStoreUrl || '';
const historiaBaseUrl = dashboardConfig.historiaBaseUrl || '';
const consultaStoreUrl = dashboardConfig.consultaStoreUrl || '';
const citasStoreUrl = dashboardConfig.citasStoreUrl || '';
const citasListUrl = dashboardConfig.citasListUrl || '';
const citasEstadoBaseUrl = dashboardConfig.citasEstadoBaseUrl || '';
const citasBaseUrl = dashboardConfig.citasBaseUrl || '';
const citasUpcomingUrl = dashboardConfig.citasUpcomingUrl || '';
const backupGenerateUrl = dashboardConfig.backupGenerateUrl || '';
const backupListUrl = dashboardConfig.backupListUrl || '';

const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

let historiaEditandoId = null;
let historiaPorAnularId = null;
let proximoNumeroHistoria = 'HC-00001';
let citasBusquedaActual = '';
let citasCache = [];
let citaDetalleSeleccionada = null;
let citaSeleccionadaParaEstado = null;
let citasProximasIntervalId = null;
let historiasDisponibles = [];
let historiasRegistradas = [];
let terminoBusquedaHistorias = '';
let historiaDetalleActual = null;
let consultasDetalleActual = [];
let respaldosCargados = false;
let historiaSeleccionadaParaCita = null;
let tomSelectHistoria = null;

function hayModalVisible() {
    return Array.from(document.querySelectorAll('.modal')).some(modalEl => modalEl.style.display === 'block');
}

function actualizarEstadoBodyModal() {
    if (hayModalVisible()) {
        document.body.classList.add('modal-open');
    } else {
        document.body.classList.remove('modal-open');
    }
}

function abrirModalGenerico(modalElement) {
    if (!modalElement) {
        return;
    }

    modalElement.style.display = 'block';
    modalElement.setAttribute('aria-hidden', 'false');
    actualizarEstadoBodyModal();
}

function cerrarModalGenerico(modalElement) {
    if (!modalElement) {
        return;
    }

    modalElement.style.display = 'none';
    modalElement.setAttribute('aria-hidden', 'true');
    actualizarEstadoBodyModal();
}

function debounce(fn, delay = 300) {
    let timeoutId;

    return (...args) => {
        window.clearTimeout(timeoutId);
        timeoutId = window.setTimeout(() => {
            fn.apply(null, args);
        }, delay);
    };
}

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

    if (key === 'historias' || key === 'historias-registradas') {
        if (typeof cargarHistorias === 'function') {
            cargarHistorias();
        }
    }

    if (key === 'citas' || key === 'citas-agendadas') {
        if (typeof cargarCitas === 'function') {
            cargarCitas(citasBusquedaActual);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    manejarNavegacion(document.querySelector('.sidebar-menu a[data-section="inicio"]'));

    if (typeof cargarHistorias === 'function') {
        cargarHistorias();
    }

    if (typeof cargarCitas === 'function') {
        cargarCitas();
    }

    if (typeof iniciarActualizacionCitasProximas === 'function') {
        iniciarActualizacionCitasProximas();
    }
});

links.forEach(link => {
    link.addEventListener('click', event => {
        event.preventDefault();
        manejarNavegacion(link);
    });
});

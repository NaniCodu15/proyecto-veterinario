const tablaCitas = document.getElementById('tablaCitas');
const buscarCitasInput = document.getElementById('buscarCitas');
const citasListadoMensaje = document.getElementById('citasListadoMensaje');
const listaCitasProximas = document.getElementById('citasProximasLista');
const modalDetalleCita = document.getElementById('modalDetalleCita');
const modalEstadoCita = document.getElementById('modalEstadoCita');
const formEstadoCita = document.getElementById('formEstadoCita');
const selectEstadoCita = document.getElementById('selectEstadoCita');
const reprogramarCampos = document.getElementById('reprogramarCampos');
const reprogramarFechaInput = document.getElementById('citaReprogramadaFecha');
const reprogramarHoraInput = document.getElementById('citaReprogramadaHora');

const detalleCamposCita = modalDetalleCita ? {
    id: modalDetalleCita.querySelector('[data-detalle="id"]'),
    numero_historia: modalDetalleCita.querySelector('[data-detalle="numero_historia"]'),
    mascota: modalDetalleCita.querySelector('[data-detalle="mascota"]'),
    propietario: modalDetalleCita.querySelector('[data-detalle="propietario"]'),
    propietario_telefono: modalDetalleCita.querySelector('[data-detalle="propietario_telefono"]'),
    fecha_legible: modalDetalleCita.querySelector('[data-detalle="fecha_legible"]'),
    hora: modalDetalleCita.querySelector('[data-detalle="hora"]'),
    estado: modalDetalleCita.querySelector('[data-detalle="estado"]'),
    motivo: modalDetalleCita.querySelector('[data-detalle="motivo"]'),
} : {};

function resetCamposReprogramar() {
    if (reprogramarCampos) {
        reprogramarCampos.hidden = true;
    }

    if (reprogramarFechaInput) {
        reprogramarFechaInput.value = '';
        reprogramarFechaInput.removeAttribute('required');
    }

    if (reprogramarHoraInput) {
        reprogramarHoraInput.value = '';
        reprogramarHoraInput.removeAttribute('required');
    }
}

function toggleCamposReprogramar(estado) {
    const esReprogramada = String(estado || '').toLowerCase() === 'reprogramada';

    if (reprogramarCampos) {
        reprogramarCampos.hidden = !esReprogramada;
    }

    if (reprogramarFechaInput) {
        if (esReprogramada) {
            reprogramarFechaInput.setAttribute('required', 'required');
        } else {
            reprogramarFechaInput.removeAttribute('required');
        }
    }

    if (reprogramarHoraInput) {
        if (esReprogramada) {
            reprogramarHoraInput.setAttribute('required', 'required');
        } else {
            reprogramarHoraInput.removeAttribute('required');
        }
    }
}

function mostrarMensajeListadoCitas(texto, tipo = 'info') {
    if (!citasListadoMensaje) {
        return;
    }

    citasListadoMensaje.textContent = texto;
    citasListadoMensaje.classList.remove('citas-alert--info', 'citas-alert--error', 'citas-alert--success', 'is-visible');

    const clase = tipo === 'error'
        ? 'citas-alert--error'
        : tipo === 'success'
            ? 'citas-alert--success'
            : 'citas-alert--info';

    citasListadoMensaje.classList.add(clase, 'is-visible');
    citasListadoMensaje.hidden = false;

    window.clearTimeout(mostrarMensajeListadoCitas.timeoutId);
    mostrarMensajeListadoCitas.timeoutId = window.setTimeout(() => {
        limpiarMensajeListadoCitas();
    }, 5000);
}

function limpiarMensajeListadoCitas() {
    if (!citasListadoMensaje) {
        return;
    }

    citasListadoMensaje.hidden = true;
    citasListadoMensaje.classList.remove('is-visible', 'citas-alert--info', 'citas-alert--error', 'citas-alert--success');
    citasListadoMensaje.textContent = '';
}

function obtenerClaseEstadoCita(estado = '') {
    const normalizado = String(estado || '').trim().toLowerCase();

    switch (normalizado) {
        case 'atendida':
            return 'cita-status--success';
        case 'reprogramada':
            return 'cita-status--warning';
        case 'cancelada':
            return 'cita-status--danger';
        case 'pendiente':
        default:
            return 'cita-status--pending';
    }
}

function obtenerPrioridadEstadoCita(estado = '') {
    const normalizado = String(estado || '').trim().toLowerCase();
    switch (normalizado) {
        case 'pendiente':
            return 0;
        case 'reprogramada':
            return 1;
        case 'atendida':
            return 2;
        case 'cancelada':
            return 3;
        default:
            return 4;
    }
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

function ordenarCitasPorPrioridad(lista = []) {
    if (!Array.isArray(lista)) {
        return [];
    }

    return [...lista].sort((a, b) => {
        const prioridadA = obtenerPrioridadEstadoCita(a?.estado);
        const prioridadB = obtenerPrioridadEstadoCita(b?.estado);

        if (prioridadA !== prioridadB) {
            return prioridadA - prioridadB;
        }

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
}

function crearFilaCita(cita = {}) {
    const fila = document.createElement('tr');
    fila.dataset.citaId = cita.id ?? '';

    const crearCeldaTexto = (valor, clase = '') => {
        const celda = document.createElement('td');
        if (clase) {
            celda.classList.add(clase);
        }
        celda.textContent = valor ?? '—';
        return celda;
    };

    fila.appendChild(crearCeldaTexto(cita.id ?? '—'));
    fila.appendChild(crearCeldaTexto(cita.mascota ?? '—'));
    fila.appendChild(crearCeldaTexto(cita.propietario ?? '—'));
    fila.appendChild(crearCeldaTexto(cita.fecha_legible ?? cita.fecha ?? '—'));
    fila.appendChild(crearCeldaTexto(cita.hora ?? '—'));

    const motivoCell = crearCeldaTexto(cita.motivo ?? '—', 'citas-table__motivo');
    if (cita.motivo) {
        motivoCell.title = cita.motivo;
    }
    fila.appendChild(motivoCell);

    const estadoCell = document.createElement('td');
    const estadoPill = document.createElement('span');
    estadoPill.className = `cita-status ${obtenerClaseEstadoCita(cita.estado)}`;
    estadoPill.textContent = cita.estado ?? 'Pendiente';
    estadoCell.appendChild(estadoPill);
    fila.appendChild(estadoCell);

    const accionesCell = document.createElement('td');
    accionesCell.classList.add('citas-table__acciones');

    const accionesWrapper = document.createElement('div');
    accionesWrapper.className = 'citas-actions';

    const whatsappLink = document.createElement('a');
    whatsappLink.className = 'citas-accion__whatsapp';
    whatsappLink.innerHTML = '<i class="fab fa-whatsapp"></i>';
    whatsappLink.setAttribute('aria-label', 'Contactar por WhatsApp');

    if (cita.propietario_whatsapp) {
        const mensajeWhatsapp = `Hola ${cita.propietario ?? ''}, te contactamos de la veterinaria respecto a la cita de ${cita.mascota ?? 'tu mascota'}.`;
        whatsappLink.href = `https://wa.me/${cita.propietario_whatsapp}?text=${encodeURIComponent(mensajeWhatsapp)}`;
        whatsappLink.target = '_blank';
        whatsappLink.rel = 'noopener noreferrer';
        whatsappLink.title = 'Contactar por WhatsApp';
    } else {
        whatsappLink.href = '#';
        whatsappLink.classList.add('is-disabled');
        whatsappLink.setAttribute('aria-disabled', 'true');
        whatsappLink.title = 'Teléfono no disponible';
    }

    const btnDetalles = document.createElement('button');
    btnDetalles.type = 'button';
    btnDetalles.className = 'btn btn-outline btn-sm btnVerCita';
    btnDetalles.innerHTML = '<i class="fas fa-eye"></i> Ver detalles';

    const btnEstado = document.createElement('button');
    btnEstado.type = 'button';
    btnEstado.className = 'btn btn-warning btn-sm btnEstadoCita';
    btnEstado.innerHTML = '<i class="fas fa-exchange-alt"></i> Cambiar estado';

    const btnAnular = document.createElement('button');
    btnAnular.type = 'button';
    btnAnular.className = 'btn btn-danger btn-sm btnAnularCita';
    btnAnular.innerHTML = '<i class="fas fa-ban"></i> Anular';

    if (String(cita.estado || '').trim().toLowerCase() === 'atendida') {
        btnEstado.disabled = true;
        btnEstado.classList.add('is-disabled');
        btnEstado.setAttribute('aria-disabled', 'true');
        btnEstado.title = 'Las citas atendidas no pueden modificarse.';
    }

    accionesWrapper.appendChild(whatsappLink);
    accionesWrapper.appendChild(btnDetalles);
    accionesWrapper.appendChild(btnEstado);
    accionesWrapper.appendChild(btnAnular);
    accionesCell.appendChild(accionesWrapper);
    fila.appendChild(accionesCell);

    return fila;
}

function renderCitas(lista = []) {
    if (!tablaCitas) {
        return;
    }

    tablaCitas.innerHTML = '';

    if (!lista.length) {
        const fila = document.createElement('tr');
        fila.className = 'citas-table__empty';
        const celda = document.createElement('td');
        celda.colSpan = 8;
        celda.textContent = 'No hay citas registradas todavía.';
        fila.appendChild(celda);
        tablaCitas.appendChild(fila);
        return;
    }

    const fragment = document.createDocumentFragment();
    ordenarCitasPorPrioridad(lista).forEach(cita => {
        fragment.appendChild(crearFilaCita(cita));
    });

    tablaCitas.appendChild(fragment);
}

function obtenerCitaPorId(id) {
    return citasCache.find(cita => String(cita?.id ?? '') === String(id));
}

function escribirDetalleCita(cita) {
    if (!detalleCamposCita) {
        return;
    }

    if (detalleCamposCita.id) {
        detalleCamposCita.id.textContent = cita?.id ?? '—';
    }

    if (detalleCamposCita.numero_historia) {
        detalleCamposCita.numero_historia.textContent = cita?.numero_historia ?? '—';
    }

    if (detalleCamposCita.mascota) {
        detalleCamposCita.mascota.textContent = cita?.mascota ?? '—';
    }

    if (detalleCamposCita.propietario) {
        detalleCamposCita.propietario.textContent = cita?.propietario ?? '—';
    }

    if (detalleCamposCita.propietario_telefono) {
        detalleCamposCita.propietario_telefono.textContent = cita?.propietario_telefono ?? '—';
    }

    if (detalleCamposCita.fecha_legible) {
        detalleCamposCita.fecha_legible.textContent = cita?.fecha_legible ?? cita?.fecha ?? '—';
    }

    if (detalleCamposCita.hora) {
        detalleCamposCita.hora.textContent = cita?.hora ?? '—';
    }

    if (detalleCamposCita.estado) {
        detalleCamposCita.estado.textContent = cita?.estado ?? '—';
    }

    if (detalleCamposCita.motivo) {
        detalleCamposCita.motivo.textContent = cita?.motivo ?? '—';
    }
}

function obtenerClaseEstadoCitaProxima(estado = '') {
    const normalizado = String(estado || '').trim().toLowerCase();

    switch (normalizado) {
        case 'atendida':
            return 'status-pill status-pill--success';
        case 'reprogramada':
            return 'status-pill status-pill--warning';
        case 'cancelada':
            return 'status-pill status-pill--danger';
        default:
            return 'status-pill status-pill--pending';
    }
}

function formatearFechaCorta(fechaIso = '', fechaLegible = '', fechaCorta = '') {
    if (fechaCorta) {
        return fechaCorta;
    }

    if (fechaLegible) {
        return fechaLegible;
    }

    const fecha = parseFechaIso(fechaIso);
    if (!fecha) {
        return fechaIso || '—';
    }

    return fecha.toLocaleDateString('es-PE', { day: '2-digit', month: 'short' });
}

function formatearHoraCita(hora = '') {
    if (!hora) {
        return '—';
    }

    const [h, m] = hora.split(':');
    if (h === undefined || m === undefined) {
        return hora;
    }

    const horaNumero = parseInt(h, 10);
    if (Number.isNaN(horaNumero)) {
        return hora;
    }

    const periodo = horaNumero >= 12 ? 'p.m.' : 'a.m.';
    const hora12 = ((horaNumero + 11) % 12) + 1;
    return `${hora12}:${m} ${periodo}`;
}

function renderCitasProximas(lista = []) {
    if (!listaCitasProximas) {
        return;
    }

    listaCitasProximas.innerHTML = '';

    if (!lista.length) {
        const vacio = document.createElement('li');
        vacio.className = 'citas-proximas__empty';
        vacio.innerHTML = '<i class="fas fa-calendar-check"></i><p>No hay citas próximas.</p>';
        listaCitasProximas.appendChild(vacio);
        return;
    }

    const fragment = document.createDocumentFragment();
    lista.forEach(cita => {
        const item = document.createElement('li');
        item.className = 'citas-proximas__item';

        const header = document.createElement('div');
        header.className = 'citas-proximas__header';

        const titulo = document.createElement('h4');
        titulo.textContent = cita.mascota ?? '—';

        const estado = document.createElement('span');
        estado.className = obtenerClaseEstadoCitaProxima(cita.estado);
        estado.textContent = cita.estado ?? 'Pendiente';

        header.append(titulo, estado);

        const info = document.createElement('div');
        info.className = 'citas-proximas__info';

        const fecha = document.createElement('span');
        fecha.innerHTML = `<i class="fas fa-calendar-day"></i> ${formatearFechaCorta(cita.fecha, cita.fecha_legible, cita.fecha_corta)}`;

        const hora = document.createElement('span');
        hora.innerHTML = `<i class="fas fa-clock"></i> ${formatearHoraCita(cita.hora)}`;

        info.append(fecha, hora);

        const propietario = document.createElement('p');
        propietario.className = 'citas-proximas__propietario';
        propietario.innerHTML = `<i class="fas fa-user"></i> ${cita.propietario || '—'}`;

        item.append(header, info, propietario);
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

function mostrarDetalleCita(cita) {
    if (!cita || !modalDetalleCita) {
        return;
    }

    citaDetalleSeleccionada = cita;
    escribirDetalleCita(cita);
    abrirModalGenerico(modalDetalleCita);
}

function actualizarDetalleCitaSiCorresponde(cita) {
    if (!citaDetalleSeleccionada || !modalDetalleCita) {
        return;
    }

    const coincide = String(citaDetalleSeleccionada.id ?? '') === String(cita?.id ?? '');
    const modalVisible = modalDetalleCita.style.display === 'block';

    if (coincide && modalVisible) {
        citaDetalleSeleccionada = cita;
        escribirDetalleCita(cita);
    }
}

function prepararModalEstado(cita) {
    if (!cita || !modalEstadoCita) {
        return;
    }

    const estadoActual = String(cita.estado ?? 'Pendiente').trim().toLowerCase();

    if (estadoActual === 'atendida') {
        mostrarMensajeListadoCitas('Las citas marcadas como atendidas no se pueden modificar.', 'info');
        return;
    }

    resetCamposReprogramar();
    citaSeleccionadaParaEstado = cita;

    if (selectEstadoCita) {
        const estadoTexto = cita.estado ?? 'Pendiente';
        selectEstadoCita.value = estadoTexto;
        if (selectEstadoCita.value !== estadoTexto) {
            selectEstadoCita.value = 'Pendiente';
        }
        toggleCamposReprogramar(selectEstadoCita.value);
    }

    if (reprogramarFechaInput) {
        reprogramarFechaInput.value = cita.fecha ?? '';
    }

    if (reprogramarHoraInput) {
        reprogramarHoraInput.value = cita.hora ?? '';
    }

    if (!selectEstadoCita) {
        toggleCamposReprogramar(cita.estado);
    }

    abrirModalGenerico(modalEstadoCita);
}

async function cargarCitas(query = '') {
    if (!citasListUrl) {
        return;
    }

    try {
        const url = new URL(citasListUrl, window.location.origin);

        if (query) {
            url.searchParams.set('q', query);
        }

        const response = await fetch(url.toString(), {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            throw new Error('No se pudieron obtener las citas.');
        }

        const data = await response.json();
        const lista = Array.isArray(data?.data) ? data.data : [];

        citasCache = lista;
        renderCitas(lista);

        if (lista.length === 0 && query) {
            mostrarMensajeListadoCitas('No se encontraron citas para la búsqueda ingresada.', 'info');
        } else if (lista.length > 0) {
            limpiarMensajeListadoCitas();
        }
    } catch (error) {
        console.error(error);
        mostrarMensajeListadoCitas(error.message || 'No se pudieron cargar las citas.', 'error');
        renderCitas();
    }
}

async function actualizarEstadoCita(id, cambios = {}) {
    if (!id || !citasEstadoBaseUrl) {
        throw new Error('No se pudo identificar la cita seleccionada.');
    }

    const payload = { ...cambios };

    const response = await fetch(`${citasEstadoBaseUrl}/${id}/estado`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(payload),
    });

    const data = await response.json().catch(() => null);

    if (response.status === 422) {
        const errores = Object.values(data?.errors ?? {}).flat();
        const mensaje = errores.join(' ') || 'Verifica el estado seleccionado.';
        throw new Error(mensaje);
    }

    if (!response.ok) {
        throw new Error(data?.message || 'No se pudo actualizar el estado de la cita.');
    }

    return data?.cita ?? null;
}

async function eliminarCita(id) {
    if (!id || !citasBaseUrl) {
        throw new Error('No se pudo identificar la cita seleccionada.');
    }

    const response = await fetch(`${citasBaseUrl}/${id}`, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    });

    const data = await response.json().catch(() => null);

    if (!response.ok) {
        throw new Error(data?.message || 'No se pudo anular la cita.');
    }

    return data?.message || 'Cita anulada correctamente.';
}

const buscarCitasDebounce = debounce(valor => {
    cargarCitas(valor);
}, 350);

if (buscarCitasInput) {
    buscarCitasInput.addEventListener('input', event => {
        citasBusquedaActual = event.target.value.trim();
        buscarCitasDebounce(citasBusquedaActual);
    });
}

if (tablaCitas) {
    tablaCitas.addEventListener('click', event => {
        const whatsappLink = event.target.closest('.citas-accion__whatsapp');
        if (whatsappLink && whatsappLink.classList.contains('is-disabled')) {
            event.preventDefault();
            mostrarMensajeListadoCitas('El propietario no tiene un teléfono registrado para contactar por WhatsApp.', 'info');
            return;
        }

        const botonAnular = event.target.closest('.btnAnularCita');
        const botonDetalles = event.target.closest('.btnVerCita');
        const botonEstado = event.target.closest('.btnEstadoCita');

        if (!botonDetalles && !botonEstado && !botonAnular) {
            return;
        }

        const fila = event.target.closest('tr');
        const id = fila?.dataset.citaId;
        if (!id) {
            return;
        }

        const cita = obtenerCitaPorId(id);

        if (botonAnular) {
            if (botonAnular.disabled) {
                return;
            }

            botonAnular.disabled = true;
            botonAnular.classList.add('is-loading');

            eliminarCita(id)
                .then(() => {
                    citasCache = citasCache.filter(citaItem => String(citaItem?.id ?? '') !== String(id));
                    renderCitas(citasCache);
                    mostrarMensajeListadoCitas('Cita anulada correctamente.', 'success');
                    cargarCitasProximas();
                })
                .catch(error => {
                    console.error(error);
                    mostrarMensajeListadoCitas(error.message || 'No se pudo anular la cita.', 'error');
                    botonAnular.disabled = false;
                })
                .finally(() => {
                    botonAnular.classList.remove('is-loading');
                });

            return;
        }

        if (botonDetalles && cita) {
            mostrarDetalleCita(cita);
            return;
        }

        if (botonEstado?.disabled) {
            return;
        }

        if (botonEstado && cita) {
            prepararModalEstado(cita);
        }
    });
}

document.querySelectorAll('[data-close="detalleCita"]').forEach(elemento => {
    elemento.addEventListener('click', () => {
        cerrarModalGenerico(modalDetalleCita);
        citaDetalleSeleccionada = null;
    });
});

document.querySelectorAll('[data-close="estadoCita"]').forEach(elemento => {
    elemento.addEventListener('click', () => {
        cerrarModalGenerico(modalEstadoCita);
        resetCamposReprogramar();
        citaSeleccionadaParaEstado = null;
    });
});

if (modalDetalleCita) {
    modalDetalleCita.addEventListener('click', event => {
        if (event.target === modalDetalleCita) {
            cerrarModalGenerico(modalDetalleCita);
            citaDetalleSeleccionada = null;
        }
    });
}

if (modalEstadoCita) {
    modalEstadoCita.addEventListener('click', event => {
        if (event.target === modalEstadoCita) {
            cerrarModalGenerico(modalEstadoCita);
            resetCamposReprogramar();
            citaSeleccionadaParaEstado = null;
        }
    });
}

if (selectEstadoCita) {
    selectEstadoCita.addEventListener('change', () => {
        toggleCamposReprogramar(selectEstadoCita.value);
    });
}

if (formEstadoCita) {
    formEstadoCita.addEventListener('submit', async event => {
        event.preventDefault();

        if (!citaSeleccionadaParaEstado) {
            mostrarMensajeListadoCitas('Selecciona una cita para actualizar su estado.', 'error');
            return;
        }

        const nuevoEstado = selectEstadoCita?.value || 'Pendiente';
        const esReprogramada = String(nuevoEstado).toLowerCase() === 'reprogramada';
        const payload = { estado: nuevoEstado };

        if (esReprogramada) {
            const nuevaFecha = reprogramarFechaInput?.value || '';
            const nuevaHora = reprogramarHoraInput?.value || '';

            if (!nuevaFecha) {
                mostrarMensajeListadoCitas('Selecciona la nueva fecha para la cita reprogramada.', 'error');
                reprogramarFechaInput?.focus();
                return;
            }

            if (!nuevaHora) {
                mostrarMensajeListadoCitas('Selecciona la nueva hora para la cita reprogramada.', 'error');
                reprogramarHoraInput?.focus();
                return;
            }

            payload.fecha_cita = nuevaFecha;
            payload.hora_cita = nuevaHora;
        }

        try {
            const citaActualizada = await actualizarEstadoCita(citaSeleccionadaParaEstado.id, payload);
            cerrarModalGenerico(modalEstadoCita);
            resetCamposReprogramar();
            citaSeleccionadaParaEstado = null;

            await cargarCitas(citasBusquedaActual);

            if (citaActualizada) {
                const citaDesdeLista = obtenerCitaPorId(citaActualizada.id);
                actualizarDetalleCitaSiCorresponde(citaDesdeLista ?? citaActualizada);
            }

            mostrarMensajeListadoCitas('Estado actualizado correctamente.', 'success');
            cargarCitasProximas();
        } catch (error) {
            console.error(error);
            mostrarMensajeListadoCitas(error.message || 'No se pudo actualizar el estado de la cita.', 'error');
        }
    });
}

document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') {
        return;
    }

    if (modalEstadoCita && modalEstadoCita.style.display === 'block') {
        cerrarModalGenerico(modalEstadoCita);
        resetCamposReprogramar();
        citaSeleccionadaParaEstado = null;
    }

    if (modalDetalleCita && modalDetalleCita.style.display === 'block') {
        cerrarModalGenerico(modalDetalleCita);
        citaDetalleSeleccionada = null;
    }
});

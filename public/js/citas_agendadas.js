// JS para el módulo Citas Agendadas: administra listado, detalle, cambios de estado y filtrado de citas.
(() => {
    // Obtiene configuración compartida del dashboard o del elemento oculto.
    const configElement = document.getElementById('dashboard-config');
    let moduleConfig = window.dashboardConfig;

    // Inicializa objeto de configuración en caso de que no esté disponible.
    if (!moduleConfig || typeof moduleConfig !== 'object') {
        moduleConfig = {};
        if (configElement) {
            try {
                moduleConfig = JSON.parse(configElement.textContent || '{}');
            } catch (error) {
                moduleConfig = {};
            }
        }
        window.dashboardConfig = moduleConfig;
    }

    // Rutas y token CSRF utilizados para listar y actualizar citas.
    const citasListUrl = moduleConfig.citasListUrl || '';
    const citasEstadoBaseUrl = moduleConfig.citasEstadoBaseUrl || '';
    const citasBaseUrl = moduleConfig.citasBaseUrl || '';
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    // Referencias al DOM para listado, búsqueda y modales de detalle/estado.
    const tablaCitas = document.getElementById('tablaCitas');
    const buscarCitasInput = document.getElementById('buscarCitas');
    const citasListadoMensaje = document.getElementById('citasListadoMensaje');
    const modalDetalleCita = document.getElementById('modalDetalleCita');
    const modalEstadoCita = document.getElementById('modalEstadoCita');
    const formEstadoCita = document.getElementById('formEstadoCita');
    const selectEstadoCita = document.getElementById('selectEstadoCita');
    const reprogramarCampos = document.getElementById('reprogramarCampos');
    const reprogramarFechaInput = document.getElementById('citaReprogramadaFecha');
    const reprogramarHoraInput = document.getElementById('citaReprogramadaHora');

    // Campos donde se muestra el detalle de la cita seleccionada en el modal.
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

    // Estados locales: almacenamiento temporal de citas, selección de detalle y búsqueda.
    let citasCache = [];
    let citaDetalleSeleccionada = null;
    let citaSeleccionadaParaEstado = null;
    let citasBusquedaActual = window.citasBusquedaActual || '';
    window.citasBusquedaActual = citasBusquedaActual;

    function actualizarBusquedaActual(valor) {
        citasBusquedaActual = valor;
        window.citasBusquedaActual = valor;
    }

    // Detecta si hay algún modal visible para controlar el scroll del body.
    function hayModalVisible() {
        return Array.from(document.querySelectorAll('.modal')).some(modalEl => modalEl.style.display === 'block');
    }

    // Alterna la clase del body según la visibilidad de modales.
    function actualizarEstadoBodyModal() {
        if (hayModalVisible()) {
            document.body.classList.add('modal-open');
        } else {
            document.body.classList.remove('modal-open');
        }
    }

    // Abre un modal genérico y ajusta atributos ARIA.
    function abrirModalGenerico(modalElement) {
        if (!modalElement) {
            return;
        }

        modalElement.style.display = 'block';
        modalElement.setAttribute('aria-hidden', 'false');
        actualizarEstadoBodyModal();
    }

    // Cierra un modal genérico y restablece el estado del documento.
    function cerrarModalGenerico(modalElement) {
        if (!modalElement) {
            return;
        }

        modalElement.style.display = 'none';
        modalElement.setAttribute('aria-hidden', 'true');
        actualizarEstadoBodyModal();
    }

    // Limpia y oculta campos adicionales cuando no se reprograma una cita.
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

    // Muestra u oculta los campos de fecha y hora cuando el estado es reprogramada.
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

    // Muestra mensajes en la parte superior del listado de citas y los oculta automáticamente.
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

    window.mostrarMensajeListadoCitas = mostrarMensajeListadoCitas;

    // Oculta el mensaje general del listado y limpia estilos previos.
    function limpiarMensajeListadoCitas() {
        if (!citasListadoMensaje) {
            return;
        }

        citasListadoMensaje.hidden = true;
        citasListadoMensaje.classList.remove('is-visible', 'citas-alert--info', 'citas-alert--error', 'citas-alert--success');
        citasListadoMensaje.textContent = '';
    }

    // Devuelve la clase visual asociada a cada estado de cita.
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

    // Asigna prioridad numérica para ordenar las citas según su estado.
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

    // Ordena la lista de citas considerando estado, fecha y hora.
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

    // Pinta en el modal de detalle los datos de la cita seleccionada.
    function escribirDetalleCita(cita) {
        if (!detalleCamposCita || !cita) {
            return;
        }

        if (detalleCamposCita.id) {
            detalleCamposCita.id.textContent = cita.id ?? '—';
        }
        if (detalleCamposCita.numero_historia) {
            detalleCamposCita.numero_historia.textContent = cita.numero_historia ?? '—';
        }
        if (detalleCamposCita.mascota) {
            detalleCamposCita.mascota.textContent = cita.mascota ?? '—';
        }
        if (detalleCamposCita.propietario) {
            detalleCamposCita.propietario.textContent = cita.propietario ?? '—';
        }
        if (detalleCamposCita.propietario_telefono) {
            detalleCamposCita.propietario_telefono.textContent = cita.propietario_telefono ?? '—';
        }
        if (detalleCamposCita.fecha_legible) {
            detalleCamposCita.fecha_legible.textContent = cita.fecha_legible ?? cita.fecha ?? '—';
        }
        if (detalleCamposCita.hora) {
            detalleCamposCita.hora.textContent = cita.hora ?? '—';
        }
        if (detalleCamposCita.estado) {
            detalleCamposCita.estado.textContent = cita.estado ?? 'Pendiente';
        }
        if (detalleCamposCita.motivo) {
            detalleCamposCita.motivo.textContent = cita.motivo ?? '—';
        }
    }

    // Construye una tarjeta de cita con sus acciones asociadas.
    function crearTarjetaCita(cita = {}) {
        const card = document.createElement('article');
        card.className = 'cita-card';
        card.dataset.citaId = cita.id ?? '';

        const encabezado = document.createElement('div');
        encabezado.className = 'cita-card__top';

        const infoPrincipal = document.createElement('div');
        const tituloMascota = document.createElement('h3');
        tituloMascota.className = 'cita-card__title';
        tituloMascota.textContent = cita.mascota ?? '—';

        const propietarioTexto = document.createElement('p');
        propietarioTexto.className = 'cita-card__owner';
        propietarioTexto.innerHTML = `<i class="fas fa-user"></i> ${cita.propietario ?? '—'}`;

        infoPrincipal.appendChild(tituloMascota);
        infoPrincipal.appendChild(propietarioTexto);

        const estadoPill = document.createElement('span');
        estadoPill.className = `cita-status ${obtenerClaseEstadoCita(cita.estado)}`;
        estadoPill.textContent = cita.estado ?? 'Pendiente';

        encabezado.appendChild(infoPrincipal);
        encabezado.appendChild(estadoPill);

        const meta = document.createElement('div');
        meta.className = 'cita-card__meta';

        const crearItemMeta = (label, value, iconClass) => {
            const item = document.createElement('div');
            item.className = 'cita-card__meta-item';

            const labelEl = document.createElement('span');
            labelEl.className = 'cita-card__meta-label';
            labelEl.innerHTML = iconClass ? `<i class="${iconClass}"></i> ${label}` : label;

            const valueEl = document.createElement('span');
            valueEl.className = 'cita-card__meta-value';
            valueEl.textContent = value ?? '—';

            item.appendChild(labelEl);
            item.appendChild(valueEl);
            return item;
        };

        meta.appendChild(crearItemMeta('Fecha', cita.fecha_legible ?? cita.fecha ?? '—', 'fas fa-calendar-alt'));
        meta.appendChild(crearItemMeta('Hora', cita.hora ?? '—', 'far fa-clock'));
        meta.appendChild(crearItemMeta('Motivo', cita.motivo ?? '—', 'fas fa-notes-medical'));

        const accionesWrapper = document.createElement('div');
        accionesWrapper.className = 'citas-actions cita-card__actions';

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

        card.appendChild(encabezado);
        card.appendChild(meta);
        card.appendChild(accionesWrapper);

        return card;
    }

    // Renderiza el grid principal de citas aplicando orden y estados visuales.
    function renderCitas(lista = []) {
        if (!tablaCitas) {
            return;
        }

        const ordenadas = ordenarCitasPorPrioridad(lista);
        citasCache = ordenadas;
        tablaCitas.innerHTML = '';

        if (!ordenadas.length) {
            const vacio = document.createElement('div');
            vacio.className = 'citas-grid__empty';
            vacio.textContent = 'No hay citas registradas.';
            tablaCitas.appendChild(vacio);
            return;
        }

        const fragment = document.createDocumentFragment();
        ordenadas.forEach(cita => {
            fragment.appendChild(crearTarjetaCita(cita));
        });

        tablaCitas.appendChild(fragment);
    }

    // Busca en caché una cita por su identificador.
    function obtenerCitaPorId(id) {
        return citasCache.find(cita => String(cita?.id ?? '') === String(id));
    }

    // Si el modal de detalle está abierto para esa cita, actualiza la información mostrada.
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

    // Abre el modal de detalle con la información de la cita seleccionada.
    function mostrarDetalleCita(cita) {
        if (!cita || !modalDetalleCita) {
            return;
        }

        citaDetalleSeleccionada = cita;
        escribirDetalleCita(cita);
        abrirModalGenerico(modalDetalleCita);
    }

    // Prepara el modal que permite cambiar el estado de una cita, incluyendo campos de reprogramación.
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

    // Petición AJAX que obtiene las citas según un término de búsqueda opcional.
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

    window.cargarCitas = cargarCitas;

    // Envía al backend la actualización de estado (incluida reprogramación) de una cita.
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
    function debounce(fn, delay = 300) {
        let timeoutId;

        return (...args) => {
            window.clearTimeout(timeoutId);
            timeoutId = window.setTimeout(() => {
                fn.apply(null, args);
            }, delay);
        };
    }

    const buscarCitasDebounce = debounce(valor => {
        cargarCitas(valor);
    }, 350);

    if (buscarCitasInput) {
        buscarCitasInput.addEventListener('input', event => {
            const valor = event.target.value.trim();
            actualizarBusquedaActual(valor);
            buscarCitasDebounce(valor);
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

            const tarjeta = event.target.closest('[data-cita-id]');
            const id = tarjeta?.dataset.citaId;
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
                        if (typeof window.cargarCitasProximas === 'function') {
                            window.cargarCitasProximas();
                        }
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
                if (typeof window.cargarCitasProximas === 'function') {
                    window.cargarCitasProximas();
                }
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
})();

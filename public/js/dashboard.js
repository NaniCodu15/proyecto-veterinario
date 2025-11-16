    const links    = Array.from(document.querySelectorAll('.sidebar-menu a.nav-link'));
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

    const historiaListUrl   = dashboardConfig.historiaListUrl || '';
    const historiaStoreUrl  = dashboardConfig.historiaStoreUrl || '';
    const historiaBaseUrl   = dashboardConfig.historiaBaseUrl || '';
    const consultaStoreUrl  = dashboardConfig.consultaStoreUrl || '';
    const citasStoreUrl     = dashboardConfig.citasStoreUrl || '';
    const citasListUrl      = dashboardConfig.citasListUrl || '';
    const citasEstadoBaseUrl = dashboardConfig.citasEstadoBaseUrl || '';
    const citasBaseUrl      = dashboardConfig.citasBaseUrl || '';
    const citasUpcomingUrl  = dashboardConfig.citasUpcomingUrl || '';
    const backupGenerateUrl = dashboardConfig.backupGenerateUrl || '';
    const backupListUrl     = dashboardConfig.backupListUrl || '';
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken        = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    let historiaEditandoId = null;
    let historiaPorAnularId = null;
    let proximoNumeroHistoria = 'HC-00001';
    let citasBusquedaActual = '';
    let citasCache = [];
    let citaDetalleSeleccionada = null;
    let citaSeleccionadaParaEstado = null;
    let citasProximasIntervalId = null;

    /**
     * hayModalVisible()
     * Recorre todas las instancias con clase `.modal` y devuelve `true` si alguna mantiene `display: block`, lo que
     * permite saber cuándo debe bloquearse el scroll de fondo.
     */
    function hayModalVisible() {
        return Array.from(document.querySelectorAll('.modal')).some(modalEl => modalEl.style.display === 'block');
    }

    /**
     * actualizarEstadoBodyModal()
     * Agrega la clase `modal-open` al `<body>` cuando existe al menos un modal visible para evitar desplazamientos
     * indeseados y la elimina cuando no hay diálogos abiertos.
     */
    function actualizarEstadoBodyModal() {
        if (hayModalVisible()) {
            document.body.classList.add('modal-open');
        } else {
            document.body.classList.remove('modal-open');
        }
    }

    /**
     * abrirModalGenerico()
     * Cambia las propiedades `display` y `aria-hidden` del modal recibido para que quede visible y accesible, además
     * de sincronizar el estado general del documento.
     */
    function abrirModalGenerico(modalElement) {
        if (!modalElement) {
            return;
        }

        modalElement.style.display = 'block';
        modalElement.setAttribute('aria-hidden', 'false');
        actualizarEstadoBodyModal();
    }

    /**
     * cerrarModalGenerico()
     * Reestablece `display: none` sobre el modal y marca `aria-hidden` en `true` para retirarlo del flujo de
     * navegación y liberar el scroll si es necesario.
     */
    function cerrarModalGenerico(modalElement) {
        if (!modalElement) {
            return;
        }

        modalElement.style.display = 'none';
        modalElement.setAttribute('aria-hidden', 'true');
        actualizarEstadoBodyModal();
    }

    /**
     * debounce()
     * Envuelve funciones que dependen de eventos de entrada y retrasa su ejecución hasta que el usuario deja de
     * interactuar durante `delay` milisegundos.
     */
    function debounce(fn, delay = 300) {
        let timeoutId;

        return (...args) => {
            window.clearTimeout(timeoutId);
            timeoutId = window.setTimeout(() => {
                fn.apply(null, args);
            }, delay);
        };
    }

    /**
     * resetCamposReprogramar()
     * Oculta el bloque de campos utilizados para reagendar y elimina cualquier valor previo para evitar enviar
     * fechas/horas erróneas cuando el estado de la cita cambia.
     */
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

    /**
     * toggleCamposReprogramar()
     * Define si los campos de reprogramación deben mostrarse y marcarse como obligatorios cuando el estado deseado
     * es "reprogramada"; en cualquier otro escenario los limpia y los deja opcionales.
     */
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

    /**
     * showSection()
     * Revisa los paneles principales y utiliza el `id` de cada sección para dejar visible únicamente el contenido
     * relacionado con la clave solicitada.
     */
    function showSection(key) {
        sections.forEach(sec => {
            const activa = sec.id === `section-${key}`;
            sec.style.display = activa ? 'block' : 'none';
            sec.classList.toggle('active', activa);
        });
    }

    /**
     * clearActiveLinks()
     * Resetea los estilos de selección de todos los enlaces de la barra lateral, incluidos los indicadores de
     * submenú, antes de marcar una nueva ruta activa.
     */
    function clearActiveLinks() {
        links.forEach(link => link.classList.remove('active', 'nav-link--parent-active'));
    }

    /**
     * setActiveLink()
     * Aplica el estado visual de selección al enlace pulsado y propaga el resaltado al padre cuando corresponde,
     * manteniendo el contexto del submenú abierto.
     */
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

    /**
     * manejarNavegacion(): gestiona la visualización o el contexto de los modales; consulta nodos del DOM para
     * actualizar la interfaz; registra listeners adicionales dentro del componente; opera sobre historias clínicas
     * (creación, listado o detalle); maneja consultas médicas de cada mascota; gestiona las citas agendadas del
     * calendario; trabaja con la generación o visualización de respaldos; muestra mensajes de estado para guiar al
     * usuario.
     */
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
            cargarHistorias();
        }

        if (key === 'citas' || key === 'citas-agendadas') {
            cargarCitas(citasBusquedaActual);
        }
    }

    // Evento DOMContentLoaded sobre document: inicia la configuración una vez que el DOM está listo.
    document.addEventListener('DOMContentLoaded', () => {
        manejarNavegacion(document.querySelector('.sidebar-menu a[data-section="inicio"]'));
        cargarHistorias();
        cargarCitas();
        iniciarActualizacionCitasProximas();
    });

    links.forEach(link => {
        // Evento click sobre link: responde a clics del usuario para disparar la acción asociada.
        link.addEventListener('click', event => {
            event.preventDefault();
            manejarNavegacion(link);
        });
    });

    const modal               = document.getElementById('modalHistoria');
    const btnNueva            = document.getElementById('btnNuevaHistoria');
    const spanClose           = document.querySelector('#modalHistoria .close');
    const form                = document.getElementById('formHistoria');
    const titulo              = document.getElementById('modalTitulo');
    const numeroHistoriaInput = document.getElementById('numero_historia');
    const especieSelect       = document.getElementById('especie');
    const especieOtroGroup    = document.getElementById('grupoEspecieOtro');
    const especieOtroInput    = document.getElementById('especieOtro');
    const tablaHistorias      = document.getElementById('tablaHistorias');
    const mensajesHistoria    = Array.from(document.querySelectorAll('[data-historia-mensaje]'));
    const buscarHistoriasInput = document.getElementById('buscarHistorias');
    const btnGuardar          = form?.querySelector('.btn-guardar');
    const btnIrHistorias      = document.querySelector('.btn-ir-historias');
    const btnIrCrearHistoria  = document.getElementById('btnIrCrearHistoria');
    const confirmModal        = document.getElementById('confirmModal');
    const confirmAcceptButton = confirmModal?.querySelector('[data-confirm="accept"]');
    const confirmCancelButton = confirmModal?.querySelector('[data-confirm="cancel"]');
    const tablaCitas          = document.getElementById('tablaCitas');
    const buscarCitasInput    = document.getElementById('buscarCitas');
    const citasListadoMensaje = document.getElementById('citasListadoMensaje');
    const modalDetalleCita    = document.getElementById('modalDetalleCita');
    const modalEstadoCita     = document.getElementById('modalEstadoCita');
    const formEstadoCita      = document.getElementById('formEstadoCita');
    const selectEstadoCita    = document.getElementById('selectEstadoCita');
    const reprogramarCampos   = document.getElementById('reprogramarCampos');
    const reprogramarFechaInput = document.getElementById('citaReprogramadaFecha');
    const reprogramarHoraInput  = document.getElementById('citaReprogramadaHora');
    const btnGenerarBackup    = document.getElementById('btnGenerarBackup');
    const btnVerBackups       = document.getElementById('btnVerBackups');
    const backupMensaje       = document.querySelector('[data-backup-mensaje]');
    const backupContenedor    = document.getElementById('backupRegistros');
    const backupWrapper       = backupContenedor?.querySelector('[data-backup-wrapper]') ?? null;
    const backupTableBody     = backupContenedor?.querySelector('[data-backup-body]') ?? null;
    const detalleCamposCita   = modalDetalleCita ? {
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

    const campos = {
        nombreMascota: document.getElementById('nombreMascota'),
        edad: document.getElementById('edad'),
        raza: document.getElementById('raza'),
        sexo: document.getElementById('sexo'),
        nombrePropietario: document.getElementById('nombrePropietario'),
        telefono: document.getElementById('telefono'),
        direccion: document.getElementById('direccion'),
        dni: document.getElementById('dni'),
        peso: document.getElementById('peso'),
    };

    const formularioCita = document.getElementById('formRegistrarCita');
    const historiaSelectCita = document.getElementById('historiaSelectCitas');
    const citaCampos = {
        propietarioNombre: document.getElementById('citaPropietarioNombre'),
        propietarioDni: document.getElementById('citaPropietarioDni'),
        propietarioTelefono: document.getElementById('citaPropietarioTelefono'),
        mascotaNombre: document.getElementById('citaMascotaNombre'),
        motivo: document.getElementById('citaMotivo'),
        fecha: document.getElementById('citaFecha'),
        hora: document.getElementById('citaHora'),
    };
    const citaMensaje = document.getElementById('citaMensaje');

    const listaCitasProximas = document.getElementById('citasProximasLista');
    const modalConsultas = document.getElementById('modalConsultas');
    const modalConsultasClose = modalConsultas?.querySelector('[data-close="consultas"]');
    const listaConsultas = document.getElementById('listaConsultas');
    const formConsulta = document.getElementById('formConsulta');
    const consultaMensaje = document.getElementById('consultaMensaje');
    const consultaHistoriaId = document.getElementById('consultaHistoriaId');
    const consultaCampos = {
        fecha: document.getElementById('consultaFecha'),
        peso: document.getElementById('consultaPeso'),
        temperatura: document.getElementById('consultaTemperatura'),
        sintomas: document.getElementById('consultaSintomas'),
        diagnostico: document.getElementById('consultaDiagnostico'),
        tratamiento: document.getElementById('consultaTratamiento'),
        observaciones: document.getElementById('consultaObservaciones'),
    };

    const consultaTabs = Array.from(document.querySelectorAll('[data-tab-target]'));
    const consultaPanels = Array.from(document.querySelectorAll('[data-tab-content]'));

    /**
     * activarTabConsulta(): agrega o remueve clases CSS para reflejar estados visuales; consulta nodos del DOM para
     * actualizar la interfaz; registra listeners adicionales dentro del componente; opera sobre historias clínicas
     * (creación, listado o detalle); maneja consultas médicas de cada mascota; gestiona las citas agendadas del
     * calendario.
     */
    function activarTabConsulta(nombre = 'registro') {
        if (!consultaTabs.length || !consultaPanels.length) {
            return;
        }

        consultaTabs.forEach(tab => {
            if (!tab) {
                return;
            }

            const objetivo = tab.dataset.tabTarget;
            const activo = objetivo === nombre;
            tab.classList.toggle('is-active', activo);
            tab.setAttribute('aria-selected', activo ? 'true' : 'false');
            tab.setAttribute('tabindex', activo ? '0' : '-1');
        });

        consultaPanels.forEach(panel => {
            if (!panel) {
                return;
            }

            const objetivo = panel.dataset.tabContent;
            const activo = objetivo === nombre;
            panel.classList.toggle('is-active', activo);
            panel.hidden = !activo;
            panel.setAttribute('aria-hidden', activo ? 'false' : 'true');
        });
    }

    consultaTabs.forEach(tab => {
        // Evento click sobre tab: responde a clics del usuario para disparar la acción asociada.
        tab.addEventListener('click', () => {
            const objetivo = tab.dataset.tabTarget;
            if (objetivo) {
                activarTabConsulta(objetivo);
            }
        });
    });

    activarTabConsulta('registro');

    const detalleHistoriaCampos = {
        titulo: document.querySelector('[data-detalle-historia="titulo"]'),
        subtitulo: document.querySelector('[data-detalle-historia="subtitulo"]'),
        propietario: document.querySelector('[data-detalle-historia="propietario"]'),
        dni: document.querySelector('[data-detalle-historia="dni"]'),
        telefono: document.querySelector('[data-detalle-historia="telefono"]'),
        direccion: document.querySelector('[data-detalle-historia="direccion"]'),
        mascota: document.querySelector('[data-detalle-historia="mascota"]'),
        especie: document.querySelector('[data-detalle-historia="especie"]'),
        peso: document.querySelector('[data-detalle-historia="peso"]'),
        fecha_apertura: document.querySelector('[data-detalle-historia="fecha_apertura"]'),
    };

    let historiaSeleccionadaParaCita = null;
    let tomSelectHistoria = null;
    let historiasDisponibles = [];
    let historiasRegistradas = [];
    let terminoBusquedaHistorias = '';
    let historiaDetalleActual = null;
    let consultasDetalleActual = [];
    let respaldosCargados = false;

    /**
     * ocultarEspecieOtro(): controla reglas de obligatoriedad sobre los campos; lee o escribe valores de
     * formularios.
     */
    function ocultarEspecieOtro() {
        if (!especieOtroGroup || !especieOtroInput) {
            return;
        }

        especieOtroGroup.style.display = 'none';
        especieOtroInput.value = '';
        especieOtroInput.removeAttribute('required');
    }

    /**
     * mostrarEspecieOtro(): controla reglas de obligatoriedad sobre los campos.
     */
    function mostrarEspecieOtro() {
        if (!especieOtroGroup || !especieOtroInput) {
            return;
        }

        especieOtroGroup.style.display = 'block';
        especieOtroInput.setAttribute('required', 'required');
    }

    /**
     * prepararFormularioBase(): lee o escribe valores de formularios; opera sobre historias clínicas (creación,
     * listado o detalle).
     */
    function prepararFormularioBase() {
        if (!form) {
            return;
        }

        form.reset();
        ocultarEspecieOtro();

        if (numeroHistoriaInput) {
            numeroHistoriaInput.value = proximoNumeroHistoria;
            numeroHistoriaInput.placeholder = 'Se generará automáticamente';
        }
    }

    /**
     * abrirModal(): gestiona la visualización o el contexto de los modales.
     */
    function abrirModal() {
        if (!modal) {
            return;
        }

        modal.style.display = 'block';
        modal.setAttribute('aria-hidden', 'false');
        actualizarEstadoBodyModal();
    }

    /**
     * cerrarModal(): gestiona la visualización o el contexto de los modales.
     */
    function cerrarModal() {
        if (!modal) {
            return;
        }

        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        actualizarEstadoBodyModal();
    }

    /**
     * abrirConfirmacionPara(): gestiona la visualización o el contexto de los modales; agrega o remueve clases CSS
     * para reflejar estados visuales; temporiza acciones para crear demoras controladas; opera sobre historias
     * clínicas (creación, listado o detalle).
     */
    function abrirConfirmacionPara(id) {
        if (!id) {
            return;
        }

        if (!confirmModal) {
            if (window.confirm('¿Desea anular esta historia clínica?')) {
                eliminarHistoria(id);
            }
            return;
        }

        historiaPorAnularId = id;
        confirmModal.hidden = false;
        confirmModal.classList.add('is-visible');
        window.setTimeout(() => {
            confirmAcceptButton?.focus();
        }, 10);
    }

    /**
     * cerrarConfirmacion(): gestiona la visualización o el contexto de los modales; agrega o remueve clases CSS para
     * reflejar estados visuales; opera sobre historias clínicas (creación, listado o detalle).
     */
    function cerrarConfirmacion() {
        if (!confirmModal) {
            historiaPorAnularId = null;
            return;
        }

        confirmModal.classList.remove('is-visible');
        confirmModal.hidden = true;
        historiaPorAnularId = null;
    }

    /**
     * reiniciarFormulario(): escribe texto directamente en los elementos; opera sobre historias clínicas (creación,
     * listado o detalle).
     */
    function reiniciarFormulario() {
        historiaEditandoId = null;
        prepararFormularioBase();

        if (titulo) {
            titulo.textContent = 'Nueva Historia Clínica';
        }

        if (btnGuardar) {
            btnGuardar.textContent = 'Guardar';
        }
    }

    /**
     * abrirModalParaCrear(): gestiona la visualización o el contexto de los modales.
     */
    function abrirModalParaCrear() {
        reiniciarFormulario();
        abrirModal();
    }

    /**
     * rellenarFormulario(): gestiona la visualización o el contexto de los modales; escribe texto directamente en
     * los elementos; lee o escribe valores de formularios; opera sobre historias clínicas (creación, listado o
     * detalle).
     */
    function rellenarFormulario(historia) {
        if (!historia || !form) {
            return;
        }

        prepararFormularioBase();
        historiaEditandoId = historia.id ?? null;

        if (titulo) {
            const numero = historia.numero_historia ? ` ${historia.numero_historia}` : '';
            titulo.textContent = `Editar${numero}`.trim();
        }

        if (btnGuardar) {
            btnGuardar.textContent = 'Actualizar';
        }

        if (numeroHistoriaInput) {
            numeroHistoriaInput.value = historia.numero_historia ?? '';
        }

        if (especieSelect) {
            especieSelect.value = historia.especie ?? '';
            if (historia.especie === 'otro') {
                mostrarEspecieOtro();
                if (especieOtroInput) {
                    especieOtroInput.value = historia.especieOtro ?? '';
                }
            }
        }

        Object.entries(campos).forEach(([clave, campo]) => {
            if (!campo) {
                return;
            }

            const valor = historia[clave];
            campo.value = valor ?? '';
        });

        abrirModal();
    }

    /**
     * mostrarMensajeHistoria(): agrega o remueve clases CSS para reflejar estados visuales; escribe texto
     * directamente en los elementos; temporiza acciones para crear demoras controladas; opera sobre historias
     * clínicas (creación, listado o detalle); muestra mensajes de estado para guiar al usuario.
     */
    function mostrarMensajeHistoria(texto, tipo = 'success') {
        if (!mensajesHistoria.length) {
            return;
        }

        mensajesHistoria.forEach(mensaje => {
            mensaje.textContent = texto;
            mensaje.classList.remove('alert--success', 'alert--error');
            mensaje.classList.add(`alert--${tipo}`);
            mensaje.hidden = false;
        });

        window.clearTimeout(mostrarMensajeHistoria.timeoutId);
        mostrarMensajeHistoria.timeoutId = window.setTimeout(() => {
            mensajesHistoria.forEach(mensaje => {
                mensaje.hidden = true;
            });
        }, 4000);
    }

    /**
     * mostrarMensajeCita(): agrega o remueve clases CSS para reflejar estados visuales; escribe texto directamente
     * en los elementos; temporiza acciones para crear demoras controladas; gestiona las citas agendadas del
     * calendario; muestra mensajes de estado para guiar al usuario.
     */
    function mostrarMensajeCita(texto, tipo = 'success') {
        if (!citaMensaje) {
            return;
        }

        citaMensaje.textContent = texto;
        citaMensaje.classList.remove('cita-alert--success', 'cita-alert--error', 'is-visible');

        const clase = tipo === 'success' ? 'cita-alert--success' : 'cita-alert--error';
        citaMensaje.classList.add(clase, 'is-visible');
        citaMensaje.hidden = false;

        window.clearTimeout(mostrarMensajeCita.timeoutId);
        mostrarMensajeCita.timeoutId = window.setTimeout(() => {
            if (!citaMensaje) {
                return;
            }

            citaMensaje.classList.remove('is-visible', 'cita-alert--success', 'cita-alert--error');
            citaMensaje.hidden = true;
        }, 4000);
    }

    /**
     * mostrarMensajeConsulta(): agrega o remueve clases CSS para reflejar estados visuales; escribe texto
     * directamente en los elementos; temporiza acciones para crear demoras controladas; maneja consultas médicas de
     * cada mascota; muestra mensajes de estado para guiar al usuario.
     */
    function mostrarMensajeConsulta(texto, tipo = 'success') {
        if (!consultaMensaje) {
            return;
        }

        consultaMensaje.textContent = texto;
        consultaMensaje.classList.remove('consulta-alert--success', 'consulta-alert--error', 'is-visible');
        const clase = tipo === 'success' ? 'consulta-alert--success' : 'consulta-alert--error';
        consultaMensaje.classList.add(clase, 'is-visible');
        consultaMensaje.hidden = false;

        window.clearTimeout(mostrarMensajeConsulta.timeoutId);
        mostrarMensajeConsulta.timeoutId = window.setTimeout(() => {
            if (!consultaMensaje) {
                return;
            }

            consultaMensaje.classList.remove('is-visible', 'consulta-alert--success', 'consulta-alert--error');
            consultaMensaje.hidden = true;
        }, 4000);
    }

    /**
     * mostrarMensajeBackup(): agrega o remueve clases CSS para reflejar estados visuales; escribe texto directamente
     * en los elementos; temporiza acciones para crear demoras controladas; trabaja con la generación o visualización
     * de respaldos; muestra mensajes de estado para guiar al usuario.
     */
    function mostrarMensajeBackup(texto, tipo = 'success') {
        if (!backupMensaje) {
            return;
        }

        if (!texto) {
            backupMensaje.hidden = true;
            return;
        }

        backupMensaje.textContent = texto;
        backupMensaje.classList.remove('alert--success', 'alert--error');
        const clase = tipo === 'success' ? 'alert--success' : 'alert--error';
        backupMensaje.classList.add(clase);
        backupMensaje.hidden = false;

        window.clearTimeout(mostrarMensajeBackup.timeoutId);
        mostrarMensajeBackup.timeoutId = window.setTimeout(() => {
            if (!backupMensaje) {
                return;
            }

            backupMensaje.hidden = true;
        }, 4000);
    }

    /**
     * setButtonLoading(): agrega o remueve clases CSS para reflejar estados visuales; inyecta HTML dinámico en la
     * interfaz.
     */
    function setButtonLoading(button, isLoading, loadingText = 'Procesando...') {
        if (!button) {
            return;
        }

        if (isLoading) {
            if (!button.dataset.originalHtml) {
                button.dataset.originalHtml = button.innerHTML;
            }

            button.innerHTML = `<i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i> ${loadingText}`;
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
            button.classList.add('is-loading');
        } else {
            const original = button.dataset.originalHtml;

            if (original) {
                button.innerHTML = original;
                delete button.dataset.originalHtml;
            }

            button.disabled = false;
            button.removeAttribute('aria-busy');
            button.classList.remove('is-loading');
        }
    }

    /**
     * formatearFechaRespaldo(): función utilitaria que respalda la experiencia general del dashboard.
     */
    function formatearFechaRespaldo(valor) {
        if (!valor) {
            return '--';
        }

        const fecha = new Date(valor);

        if (Number.isNaN(fecha.getTime())) {
            return valor;
        }

        return fecha.toLocaleString('es-PE', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    /**
     * obtenerClaseEstadoRespaldo(): trabaja con la generación o visualización de respaldos.
     */
    function obtenerClaseEstadoRespaldo(estado) {
        const valor = String(estado ?? '').toLowerCase();

        if (valor === 'correcto') {
            return 'backup-log__status backup-log__status--success';
        }

        if (valor === 'fallido') {
            return 'backup-log__status backup-log__status--error';
        }

        return 'backup-log__status';
    }

    /**
     * renderBackups()
     * Construye dinámicamente las filas de la tabla de respaldos a partir de la lista recibida, incluyendo formato
     * de fecha y resaltado del estado para que el usuario identifique si la tarea fue correcta o fallida.
     */
    function renderBackups(registros = []) {
        if (!backupWrapper || !backupTableBody) {
            return;
        }

        backupTableBody.innerHTML = '';

        if (!Array.isArray(registros) || registros.length === 0) {
            backupWrapper.hidden = false;
            return;
        }

        backupWrapper.hidden = false;

        const fragment = document.createDocumentFragment();

        registros.forEach(registro => {
            const fila = document.createElement('tr');

            const columnas = [
                registro?.id_respaldo ?? registro?.id ?? '--',
                formatearFechaRespaldo(registro?.fecha_respaldo),
                registro?.nombre_archivo ?? '--',
                registro?.ruta_archivo ?? '--',
            ];

            columnas.forEach((valor, indice) => {
                const celda = document.createElement('td');
                celda.textContent = valor;

                if (indice === 3) {
                    celda.classList.add('backup-log__path');
                }

                fila.appendChild(celda);
            });

            const estadoCelda = document.createElement('td');
            estadoCelda.textContent = registro?.estado ?? '--';
            estadoCelda.className = obtenerClaseEstadoRespaldo(registro?.estado);
            fila.appendChild(estadoCelda);

            fragment.appendChild(fila);
        });

        backupTableBody.appendChild(fragment);
    }

    /**
     * cargarBackups()
     * Invoca `backupListUrl` mediante `fetch`, interpreta la respuesta JSON paginada y renderiza los registros
     * recibidos sólo una vez a menos que se fuerce la recarga. Cualquier error se comunica con mensajes visibles en
     * el tablero.
     */
    async function cargarBackups(force = false) {
        if (!backupListUrl || !backupContenedor) {
            return;
        }

        if (respaldosCargados && !force) {
            backupContenedor.hidden = false;
            return;
        }

        backupContenedor.hidden = false;

        if (backupWrapper) {
            backupWrapper.hidden = true;
        }

        try {
            const response = await fetch(backupListUrl, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('No se pudieron obtener los registros de respaldo.');
            }

            const data = await response.json();
            const registros = Array.isArray(data?.data) ? data.data : [];

            renderBackups(registros);
            respaldosCargados = true;
        } catch (error) {
            console.error(error);
            respaldosCargados = false;
            mostrarMensajeBackup(error.message || 'No se pudieron cargar los registros de respaldo.', 'error');

            if (backupWrapper) {
                backupWrapper.hidden = true;
            }
        }
    }

    /**
     * generarBackup()
     * Ejecuta la ruta de generación de respaldos enviando el token CSRF, deshabilita el botón mientras se realiza
     * el proceso y vuelve a listar los respaldos cuando el backend confirma la creación del archivo.
     */
    async function generarBackup() {
        if (!backupGenerateUrl) {
            return;
        }

        mostrarMensajeBackup('');
        setButtonLoading(btnGenerarBackup, true, 'Generando...');

        try {
            const response = await fetch(backupGenerateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({}),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data?.message || 'No se pudo generar la copia de seguridad.');
            }

            mostrarMensajeBackup(data?.message || 'Copia de seguridad generada correctamente.', 'success');
            respaldosCargados = false;

            if (backupContenedor) {
                backupContenedor.hidden = false;
                await cargarBackups(true);
            }
        } catch (error) {
            console.error(error);
            mostrarMensajeBackup(error.message || 'No se pudo generar la copia de seguridad.', 'error');
            respaldosCargados = false;
        } finally {
            setButtonLoading(btnGenerarBackup, false);
        }
    }

    /**
     * limpiarFormularioConsulta(): lee o escribe valores de formularios; opera sobre historias clínicas (creación,
     * listado o detalle); maneja consultas médicas de cada mascota.
     */
    function limpiarFormularioConsulta() {
        if (!formConsulta) {
            return;
        }

        formConsulta.reset();

        if (consultaHistoriaId && historiaDetalleActual?.id) {
            consultaHistoriaId.value = historiaDetalleActual.id;
        }

        activarTabConsulta('registro');
    }

    /**
     * crearEtiquetaConsulta(): inyecta HTML dinámico en la interfaz; maneja consultas médicas de cada mascota.
     */
    function crearEtiquetaConsulta(icono, texto) {
        const span = document.createElement('span');
        span.className = 'consulta-item__meta-tag';
        span.innerHTML = `<i class="fas ${icono}"></i> ${texto}`;
        return span;
    }

    /**
     * crearNodoConsulta(): escribe texto directamente en los elementos; maneja consultas médicas de cada mascota.
     */
    function crearNodoConsulta(consulta = {}) {
        const item = document.createElement('li');
        item.className = 'consulta-item';

        const header = document.createElement('div');
        header.className = 'consulta-item__header';

        const fecha = document.createElement('span');
        fecha.className = 'consulta-item__date';
        fecha.textContent = consulta.fecha_legible || 'Sin fecha';

        const titulo = document.createElement('h4');
        titulo.className = 'consulta-item__titulo';
        const descripcionConsulta = consulta.diagnostico || consulta.sintomas || consulta.tratamiento || consulta.observaciones;
        titulo.textContent = descripcionConsulta || 'Consulta registrada';

        header.append(fecha, titulo);

        const meta = document.createElement('div');
        meta.className = 'consulta-item__meta';

        if (consulta.peso !== undefined && consulta.peso !== null) {
            meta.appendChild(crearEtiquetaConsulta('fa-weight', `${parseFloat(consulta.peso).toFixed(2)} kg`));
        }

        if (consulta.temperatura !== undefined && consulta.temperatura !== null) {
            meta.appendChild(crearEtiquetaConsulta('fa-thermometer-half', `${parseFloat(consulta.temperatura).toFixed(1)} °C`));
        }

        const cuerpo = document.createElement('div');
        cuerpo.className = 'consulta-item__body';

        const secciones = [
            { etiqueta: 'Síntomas', valor: consulta.sintomas },
            { etiqueta: 'Diagnóstico', valor: consulta.diagnostico },
            { etiqueta: 'Tratamiento', valor: consulta.tratamiento },
            { etiqueta: 'Observaciones', valor: consulta.observaciones },
        ];

        secciones.forEach(({ etiqueta, valor }) => {
            if (!valor) {
                return;
            }

            const bloque = document.createElement('div');
            bloque.className = 'consulta-item__block';

            const titulo = document.createElement('span');
            titulo.className = 'consulta-item__block-title';
            titulo.textContent = etiqueta;

            const contenido = document.createElement('p');
            contenido.className = 'consulta-item__block-text';
            contenido.textContent = valor;

            bloque.append(titulo, contenido);
            cuerpo.appendChild(bloque);
        });

        item.append(header);

        if (meta.children.length) {
            item.appendChild(meta);
        }

        if (cuerpo.children.length) {
            item.appendChild(cuerpo);
        }

        return item;
    }

    /**
     * obtenerMarcaTiempoConsulta(): maneja consultas médicas de cada mascota.
     */
    function obtenerMarcaTiempoConsulta(consulta = {}) {
        const posiblesFechas = [
            consulta.fecha_consulta,
            consulta.fechaConsulta,
            consulta.fecha,
            consulta.created_at,
            consulta.updated_at,
        ];

        for (const valor of posiblesFechas) {
            if (!valor) {
                continue;
            }

            const fecha = new Date(valor);
            if (!Number.isNaN(fecha.getTime())) {
                return fecha.getTime();
            }
        }

        return 0;
    }

    /**
     * renderConsultas(): inyecta HTML dinámico en la interfaz; maneja consultas médicas de cada mascota.
     */
    function renderConsultas(lista = []) {
        if (!listaConsultas) {
            return;
        }

        listaConsultas.innerHTML = '';

        const listaOrdenada = [...lista].sort((a, b) => obtenerMarcaTiempoConsulta(b) - obtenerMarcaTiempoConsulta(a));
        const fragment = document.createDocumentFragment();
        listaOrdenada.forEach(consulta => {
            fragment.appendChild(crearNodoConsulta(consulta));
        });

        listaConsultas.appendChild(fragment);
    }

    /**
     * actualizarDetalleHistoria(): escribe texto directamente en los elementos; lee o escribe valores de
     * formularios; opera sobre historias clínicas (creación, listado o detalle); maneja consultas médicas de cada
     * mascota.
     */
    function actualizarDetalleHistoria(historia = {}) {
        historiaDetalleActual = historia;

        if (detalleHistoriaCampos.titulo) {
            const numero = historia.numero_historia ? `#${historia.numero_historia}` : 'Historia clínica';
            detalleHistoriaCampos.titulo.textContent = `${historia.nombreMascota || 'Mascota sin nombre'} ${numero}`;
        }

        if (detalleHistoriaCampos.subtitulo) {
            detalleHistoriaCampos.subtitulo.textContent = historia.nombrePropietario
                ? `A cargo de ${historia.nombrePropietario}`
                : 'Propietario no registrado';
        }

        if (detalleHistoriaCampos.propietario) {
            detalleHistoriaCampos.propietario.textContent = historia.nombrePropietario || '—';
        }

        if (detalleHistoriaCampos.dni) {
            detalleHistoriaCampos.dni.textContent = historia.dni ? `DNI ${historia.dni}` : 'DNI —';
        }

        if (detalleHistoriaCampos.telefono) {
            detalleHistoriaCampos.telefono.textContent = historia.telefono || '—';
        }

        if (detalleHistoriaCampos.direccion) {
            detalleHistoriaCampos.direccion.textContent = historia.direccion || 'Sin dirección registrada';
        }

        if (detalleHistoriaCampos.mascota) {
            detalleHistoriaCampos.mascota.textContent = historia.nombreMascota || '—';
        }

        if (detalleHistoriaCampos.especie) {
            const especieBase = historia.especie === 'otro' && historia.especieOtro
                ? historia.especieOtro
                : historia.especie;
            const especieFormateada = especieBase
                ? `${especieBase.charAt(0).toUpperCase()}${especieBase.slice(1)}`
                : '';
            const raza = historia.raza ? ` · ${historia.raza}` : '';
            detalleHistoriaCampos.especie.textContent = especieFormateada
                ? `${especieFormateada}${raza}`
                : raza.replace(' · ', '') || '—';
        }

        if (detalleHistoriaCampos.peso) {
            detalleHistoriaCampos.peso.textContent = historia.peso ? `${parseFloat(historia.peso).toFixed(2)} kg` : '—';
        }

        if (detalleHistoriaCampos.fecha_apertura) {
            detalleHistoriaCampos.fecha_apertura.textContent = historia.fecha_apertura
                ? `Apertura ${historia.fecha_apertura}`
                : 'Apertura —';
        }

        if (consultaHistoriaId && historia.id) {
            consultaHistoriaId.value = historia.id;
        }
    }

    /**
     * mostrarDetalleHistoria(): gestiona la visualización o el contexto de los modales; lee o escribe valores de
     * formularios; opera sobre historias clínicas (creación, listado o detalle); maneja consultas médicas de cada
     * mascota; muestra mensajes de estado para guiar al usuario.
     */
    async function mostrarDetalleHistoria(id) {
        try {
            const data = await obtenerHistoriaDetallada(id);
            const historia = data.historia ?? {};
            const consultas = Array.isArray(data.consultas) ? data.consultas : [];

            actualizarDetalleHistoria(historia);
            consultasDetalleActual = consultas;
            renderConsultas(consultasDetalleActual);
            limpiarFormularioConsulta();

            if (consultaCampos.fecha) {
                const hoy = new Date().toISOString().split('T')[0];
                consultaCampos.fecha.value = hoy;
            }

            abrirModalGenerico(modalConsultas);
        } catch (error) {
            console.error(error);
            mostrarMensajeHistoria(error.message || 'No se pudo cargar el historial clínico.', 'error');
        }
    }

    /**
     * mostrarMensajeListadoCitas(): agrega o remueve clases CSS para reflejar estados visuales; escribe texto
     * directamente en los elementos; temporiza acciones para crear demoras controladas; gestiona las citas agendadas
     * del calendario; muestra mensajes de estado para guiar al usuario.
     */
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

    /**
     * limpiarMensajeListadoCitas(): agrega o remueve clases CSS para reflejar estados visuales; escribe texto
     * directamente en los elementos; gestiona las citas agendadas del calendario; muestra mensajes de estado para
     * guiar al usuario.
     */
    function limpiarMensajeListadoCitas() {
        if (!citasListadoMensaje) {
            return;
        }

        citasListadoMensaje.hidden = true;
        citasListadoMensaje.classList.remove('is-visible', 'citas-alert--info', 'citas-alert--error', 'citas-alert--success');
        citasListadoMensaje.textContent = '';
    }

    /**
     * limpiarDatosHistoriaEnCita(): lee o escribe valores de formularios; gestiona las citas agendadas del
     * calendario.
     */
    function limpiarDatosHistoriaEnCita() {
        ['propietarioNombre', 'propietarioDni', 'propietarioTelefono', 'mascotaNombre'].forEach(clave => {
            const campo = citaCampos[clave];
            if (campo) {
                campo.value = '';
            }
        });
    }

    /**
     * obtenerClaseEstadoCita(): gestiona las citas agendadas del calendario.
     */
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

    /**
     * obtenerPrioridadEstadoCita(): función utilitaria que respalda la experiencia general del dashboard.
     */
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

    /**
     * parseFechaIso(): función utilitaria que respalda la experiencia general del dashboard.
     */
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

    /**
     * ordenarCitasPorPrioridad(): gestiona las citas agendadas del calendario.
     */
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

    /**
     * crearFilaCita(): gestiona las citas agendadas del calendario.
     */
    function crearFilaCita(cita = {}) {
        const fila = document.createElement('tr');
        fila.dataset.citaId = cita.id ?? '';

        /**
         * crearCeldaTexto(): agrega o remueve clases CSS para reflejar estados visuales; inyecta HTML dinámico en la
         * interfaz; escribe texto directamente en los elementos; gestiona las citas agendadas del calendario; muestra
         * mensajes de estado para guiar al usuario.
         */
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

    /**
     * renderCitas(): agrega o remueve clases CSS para reflejar estados visuales; inyecta HTML dinámico en la
     * interfaz; escribe texto directamente en los elementos; gestiona las citas agendadas del calendario.
     */
    function renderCitas(lista = []) {
        if (!tablaCitas) {
            return;
        }

        citasCache = Array.isArray(lista) ? lista : [];

        tablaCitas.innerHTML = '';

        if (!Array.isArray(citasCache) || citasCache.length === 0) {
            const filaVacia = document.createElement('tr');
            filaVacia.classList.add('citas-table__empty');

            const celda = document.createElement('td');
            celda.colSpan = 8;
            celda.textContent = citasBusquedaActual
                ? 'No se encontraron citas para la búsqueda ingresada.'
                : 'No hay citas registradas todavía.';

            filaVacia.appendChild(celda);
            tablaCitas.appendChild(filaVacia);
            return;
        }

        const fragment = document.createDocumentFragment();
        const listaOrdenada = ordenarCitasPorPrioridad(citasCache);

        listaOrdenada.forEach(cita => {
            fragment.appendChild(crearFilaCita(cita));
        });

        tablaCitas.appendChild(fragment);
    }

    /**
     * obtenerCitaPorId(): gestiona las citas agendadas del calendario.
     */
    function obtenerCitaPorId(id) {
        if (!id) {
            return null;
        }

        return citasCache.find(cita => String(cita?.id ?? '') === String(id)) ?? null;
    }

    /**
     * escribirDetalleCita(): escribe texto directamente en los elementos; gestiona las citas agendadas del
     * calendario.
     */
    function escribirDetalleCita(cita) {
        if (!cita) {
            return;
        }

        Object.entries(detalleCamposCita).forEach(([clave, elemento]) => {
            if (!elemento) {
                return;
            }

            let valor = cita[clave];

            if (clave === 'fecha_legible') {
                valor = cita.fecha_legible ?? cita.fecha ?? '—';
            } else if (clave === 'propietario_telefono') {
                valor = cita.propietario_telefono ?? 'Sin teléfono registrado';
            } else if (clave === 'motivo') {
                valor = cita.motivo ?? '—';
            } else if (!valor) {
                valor = '—';
            }

            elemento.textContent = valor;
        });
    }

    /**
     * obtenerClaseEstadoCitaProxima(): función utilitaria que respalda la experiencia general del dashboard.
     */
    function obtenerClaseEstadoCitaProxima(estado = '') {
        const clases = {
            pendiente: 'is-pending',
            atendida: 'is-done',
            cancelada: 'is-cancelled',
            reprogramada: 'is-rescheduled',
        };

        return clases[String(estado || '').trim().toLowerCase()] ?? 'is-pending';
    }

    /**
     * formatearFechaCorta(): función utilitaria que respalda la experiencia general del dashboard.
     */
    function formatearFechaCorta(fechaIso = '', fechaLegible = '', fechaCorta = '') {
        if (fechaCorta) {
            return fechaCorta;
        }

        if (fechaLegible) {
            const partes = fechaLegible.split('/');
            if (partes.length === 3) {
                return `${partes[0]}/${partes[1]}`;
            }
        }

        const fecha = parseFechaIso(fechaIso);
        if (!fecha) {
            return '--/--';
        }

        const dia = String(fecha.getUTCDate()).padStart(2, '0');
        const mes = String(fecha.getUTCMonth() + 1).padStart(2, '0');
        return `${dia}/${mes}`;
    }

    /**
     * formatearHoraCita(): función utilitaria que respalda la experiencia general del dashboard.
     */
    function formatearHoraCita(hora = '') {
        if (!hora) {
            return '--:--';
        }

        const partes = hora.split(':');
        if (partes.length >= 2) {
            return `${partes[0].padStart(2, '0')}:${partes[1].padStart(2, '0')}`;
        }

        return hora;
    }

    /**
     * renderCitasProximas(): inyecta HTML dinámico en la interfaz; escribe texto directamente en los elementos;
     * gestiona las citas agendadas del calendario.
     */
    function renderCitasProximas(lista = []) {
        if (!listaCitasProximas) {
            return;
        }

        listaCitasProximas.innerHTML = '';

        if (!Array.isArray(lista) || lista.length === 0) {
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

    /**
     * cargarCitasProximas()
     * Consulta periódicamente la API de próximas citas (`citasUpcomingUrl`), transforma la respuesta en tarjetas
     * legibles y actualiza la lista que se muestra en la columna lateral del dashboard.
     */
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

    /**
     * iniciarActualizacionCitasProximas(): programa actualizaciones periódicas para mantener los datos frescos;
     * gestiona las citas agendadas del calendario.
     */
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

    /**
     * mostrarDetalleCita(): gestiona la visualización o el contexto de los modales; gestiona las citas agendadas del
     * calendario.
     */
    function mostrarDetalleCita(cita) {
        if (!cita || !modalDetalleCita) {
            return;
        }

        citaDetalleSeleccionada = cita;
        escribirDetalleCita(cita);
        abrirModalGenerico(modalDetalleCita);
    }

    /**
     * actualizarDetalleCitaSiCorresponde(): gestiona la visualización o el contexto de los modales; gestiona las
     * citas agendadas del calendario.
     */
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

    /**
     * prepararModalEstado(): gestiona la visualización o el contexto de los modales; lee o escribe valores de
     * formularios; gestiona las citas agendadas del calendario; muestra mensajes de estado para guiar al usuario.
     */
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

    /**
     * cargarCitas()
     * Construye la URL de búsqueda con el parámetro `q`, consulta la API de citas y actualiza la tabla principal,
     * mostrando mensajes informativos cuando no existen coincidencias o cuando ocurre un fallo.
     */
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

    /**
     * actualizarEstadoCita()
     * Envía una petición `PATCH` con cuerpo JSON hacia `citasEstadoBaseUrl` para actualizar el estado de una cita,
     * gestionando los mensajes de validación devueltos por Laravel (422) y propagando los errores al flujo de la
     * interfaz.
     */
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

    /**
     * eliminarCita(): realiza peticiones AJAX con fetch para sincronizar datos; gestiona las citas agendadas del
     * calendario.
     */
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

    /**
     * formatearHistoriaParaOpcion(): lee o escribe valores de formularios; opera sobre historias clínicas (creación,
     * listado o detalle).
     */
    function formatearHistoriaParaOpcion(historia) {
        if (!historia || !historia.id) {
            return null;
        }

        const numero = (historia.numero_historia ?? '').toString().trim() || 'Sin código';
        const mascota = (historia.mascota ?? '').toString().trim() || 'Mascota sin nombre';
        const propietario = (historia.propietario ?? '').toString().trim() || 'Propietario sin registrar';
        const propietarioDni = (historia.propietario_dni ?? '').toString().trim();

        return {
            value: String(historia.id),
            text: `${numero} · ${mascota}`,
            numero_historia: numero,
            mascota,
            propietario,
            propietario_dni: propietarioDni,
        };
    }

    /**
     * sincronizarTomSelectHistorias(): lee o escribe valores de formularios; opera sobre historias clínicas
     * (creación, listado o detalle); gestiona las citas agendadas del calendario.
     */
    function sincronizarTomSelectHistorias() {
        if (!tomSelectHistoria) {
            return;
        }

        const valorActual = tomSelectHistoria.getValue();
        tomSelectHistoria.clearOptions();

        if (valorActual) {
            const historiaActual = historiasDisponibles.find(
                historia => String(historia?.id ?? '') === String(valorActual)
            );

            if (historiaActual) {
                const opcion = formatearHistoriaParaOpcion(historiaActual);
                if (opcion) {
                    tomSelectHistoria.addOption(opcion);
                    tomSelectHistoria.setValue(opcion.value, true);
                }
            } else {
                tomSelectHistoria.clear(true);
                historiaSeleccionadaParaCita = null;
                limpiarDatosHistoriaEnCita();
            }
        }

        if (!historiasDisponibles.length) {
            tomSelectHistoria.clear(true);
            tomSelectHistoria.disable();
            historiaSeleccionadaParaCita = null;
            limpiarDatosHistoriaEnCita();
        } else {
            tomSelectHistoria.enable();
        }

        tomSelectHistoria.setTextboxValue('');
        tomSelectHistoria.refreshOptions(false);
    }

    /**
     * poblarHistoriasParaCitas(): realiza peticiones AJAX con fetch para sincronizar datos; inyecta HTML dinámico en
     * la interfaz; escribe texto directamente en los elementos; lee o escribe valores de formularios; opera sobre
     * historias clínicas (creación, listado o detalle); gestiona las citas agendadas del calendario.
     */
    function poblarHistoriasParaCitas(lista = []) {
        if (!historiaSelectCita) {
            return;
        }

        historiasDisponibles = Array.isArray(lista)
            ? lista.filter(historia => historia && historia.id)
            : [];

        if (tomSelectHistoria) {
            sincronizarTomSelectHistorias();
            return;
        }

        const valorActual = historiaSelectCita.value;
        historiaSelectCita.innerHTML = '<option value="">Selecciona una historia clínica</option>';

        historiasDisponibles.forEach(historia => {
            const opcion = document.createElement('option');
            opcion.value = historia.id;
            const formateada = formatearHistoriaParaOpcion(historia);
            opcion.textContent = formateada?.text ?? '';
            historiaSelectCita.appendChild(opcion);
        });

        const existeValorPrevio = historiasDisponibles.some(
            historia => String(historia?.id ?? '') === String(valorActual)
        );

        if (existeValorPrevio) {
            historiaSelectCita.value = valorActual;
        } else {
            historiaSelectCita.value = '';
            historiaSeleccionadaParaCita = null;
            limpiarDatosHistoriaEnCita();
        }
    }

    window.inicializarBuscadorHistorias = function inicializarBuscadorHistorias() {
        if (!historiaSelectCita || typeof TomSelect === 'undefined') {
            return;
        }

        if (tomSelectHistoria) {
            tomSelectHistoria.destroy();
            tomSelectHistoria = null;
        }

        tomSelectHistoria = new TomSelect(historiaSelectCita, {
            valueField: 'value',
            labelField: 'text',
            searchField: ['text', 'numero_historia', 'mascota', 'propietario', 'propietario_dni'],
            allowEmptyOption: true,
            placeholder: 'Escribe al menos 2 caracteres para buscar...',
            loadThrottle: 250,
            closeAfterSelect: true,
            shouldLoad(query) {
                return query.length >= 2;
            },
            load(query, callback) {
                if (query.length < 2) {
                    callback();
                    return;
                }

                const termino = query.toLowerCase();
                const coincidencias = historiasDisponibles
                    .filter(historia => {
                        const numero = (historia.numero_historia ?? '').toString().toLowerCase();
                        const mascota = (historia.mascota ?? '').toString().toLowerCase();
                        const propietario = (historia.propietario ?? '').toString().toLowerCase();
                        const propietarioDni = (historia.propietario_dni ?? '').toString().toLowerCase();

                        return (
                            numero.includes(termino) ||
                            mascota.includes(termino) ||
                            propietario.includes(termino) ||
                            propietarioDni.includes(termino)
                        );
                    })
                    .slice(0, 25)
                    .map(formatearHistoriaParaOpcion)
                    .filter(Boolean);

                // Para integrar AJAX en el futuro, reemplazar el filtro anterior por una solicitud fetch().
                callback(coincidencias);
            },
            render: {
                option(item, escape) {
                    const numero = escape(item.numero_historia ?? 'Sin código');
                    const mascota = escape(item.mascota ?? 'Mascota sin nombre');
                    const propietario = escape(item.propietario ?? 'Propietario sin registrar');
                    const propietarioDni = escape(item.propietario_dni ?? '');
                    const propietarioDetalle = propietarioDni
                        ? `${propietario} · DNI ${propietarioDni}`
                        : propietario;
                    return `
                        <div class="ts-option__content">
                            <span class="ts-option__numero">${numero}</span>
                            <span class="ts-option__mascota">${mascota}</span>
                            <span class="ts-option__propietario">${propietarioDetalle}</span>
                        </div>
                    `;
                },
                item(item, escape) {
                    const numero = escape(item.numero_historia ?? 'Sin código');
                    const mascota = escape(item.mascota ?? 'Mascota sin nombre');
                    const propietario = escape(item.propietario ?? 'Propietario sin registrar');
                    return `
                        <div class="ts-item__content">
                            <span class="ts-item__numero">${numero}</span>
                            <span class="ts-item__mascota">${mascota}</span>
                            <span class="ts-item__propietario">${propietario}</span>
                        </div>
                    `;
                },
                no_results() {
                    if (this.inputValue.length < 2) {
                        return '<div class="ts-dropdown__message">Escribe al menos 2 caracteres para buscar.</div>';
                    }

                    return '<div class="ts-dropdown__message">No se encontraron coincidencias.</div>';
                },
            },
        });

        sincronizarTomSelectHistorias();
    };

    /**
     * obtenerHistoriaDetallada(): realiza peticiones AJAX con fetch para sincronizar datos; opera sobre historias
     * clínicas (creación, listado o detalle).
     */
    async function obtenerHistoriaDetallada(id) {
        if (!historiaBaseUrl || !id) {
            throw new Error('Seleccione una historia clínica válida.');
        }

        const response = await fetch(`${historiaBaseUrl}/${id}`, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            throw new Error('No se pudo obtener la información de la historia clínica.');
        }

        const data = await response.json();
        if (!data?.historia) {
            throw new Error('No se encontró la historia clínica seleccionada.');
        }

        return data;
    }

    /**
     * rellenarDatosHistoriaEnCita(): lee o escribe valores de formularios; opera sobre historias clínicas (creación,
     * listado o detalle); gestiona las citas agendadas del calendario.
     */
    function rellenarDatosHistoriaEnCita(historia) {
        if (!historia) {
            limpiarDatosHistoriaEnCita();
            historiaSeleccionadaParaCita = null;
            return;
        }

        historiaSeleccionadaParaCita = historia;

        if (citaCampos.propietarioNombre) {
            citaCampos.propietarioNombre.value = historia.nombrePropietario ?? '';
        }
        if (citaCampos.propietarioDni) {
            citaCampos.propietarioDni.value = historia.dni ?? '';
        }
        if (citaCampos.propietarioTelefono) {
            citaCampos.propietarioTelefono.value = historia.telefono ?? '';
        }
        if (citaCampos.mascotaNombre) {
            citaCampos.mascotaNombre.value = historia.nombreMascota ?? '';
        }
    }

    /**
     * crearTarjetaHistoria(): agrega o remueve clases CSS para reflejar estados visuales; inyecta HTML dinámico en
     * la interfaz; escribe texto directamente en los elementos; lee o escribe valores de formularios; opera sobre
     * historias clínicas (creación, listado o detalle); maneja consultas médicas de cada mascota.
     */
    function crearTarjetaHistoria(historia) {
        const card = document.createElement('article');
        card.className = 'historia-card';
        card.dataset.historiaId = historia.id ?? '';

        const header = document.createElement('div');
        header.className = 'historia-card__header';

        const badge = document.createElement('span');
        badge.className = 'historia-card__badge';
        badge.textContent = historia.numero_historia || 'Sin código';

        const fecha = document.createElement('span');
        fecha.className = 'historia-card__date';
        fecha.innerHTML = `<i class="fas fa-calendar-day"></i> ${historia.fecha_apertura || 'Sin fecha'}`;

        header.append(badge, fecha);

        const body = document.createElement('div');
        body.className = 'historia-card__body';

        const detalles = [
            { icono: 'fa-paw', etiqueta: 'Mascota', valor: historia.mascota || 'Sin nombre' },
            { icono: 'fa-user', etiqueta: 'Propietario', valor: historia.propietario || 'Sin registrar' },
            { icono: 'fa-id-card', etiqueta: 'DNI', valor: historia.propietario_dni || '—' },
        ];

        detalles.forEach(({ icono, etiqueta, valor }) => {
            const filaDetalle = document.createElement('div');
            filaDetalle.className = 'historia-card__row';

            const label = document.createElement('span');
            label.className = 'historia-card__label';
            label.innerHTML = `<i class="fas ${icono}"></i> ${etiqueta}`;

            const value = document.createElement('span');
            value.className = 'historia-card__value';
            value.textContent = valor;

            filaDetalle.append(label, value);
            body.appendChild(filaDetalle);
        });

        const acciones = document.createElement('div');
        acciones.className = 'historia-card__actions';

        const btnVerPdf = document.createElement('a');
        btnVerPdf.className = 'btn btn-info btn-sm';
        btnVerPdf.title = 'Ver historia clínica';
        btnVerPdf.setAttribute('aria-label', 'Ver historia clínica');
        btnVerPdf.innerHTML = '<i class="fas fa-eye"></i>';

        if (historia.id) {
            btnVerPdf.href = `${historiaBaseUrl}/${historia.id}/ver`;
            btnVerPdf.target = '_blank';
            btnVerPdf.rel = 'noopener';
        } else {
            btnVerPdf.href = '#';
            btnVerPdf.setAttribute('aria-disabled', 'true');
            btnVerPdf.classList.add('disabled');
        }

        const btnVerConsultas = document.createElement('button');
        btnVerConsultas.className = 'btn btn-primary btn-sm btnConsultas';
        btnVerConsultas.title = 'Ver historial clínico';
        btnVerConsultas.innerHTML = '<i class="fas fa-stream"></i> Consultas';

        const btnEditar = document.createElement('button');
        btnEditar.className = 'btn btn-warning btn-sm btnEditar';
        btnEditar.title = 'Editar historia';
        btnEditar.setAttribute('aria-label', 'Editar historia');
        btnEditar.innerHTML = '<i class="fas fa-pen"></i>';

        const btnAnular = document.createElement('button');
        btnAnular.className = 'btn btn-sm btnAnular';
        btnAnular.title = 'Anular historia';
        btnAnular.innerHTML = '<i class="fas fa-ban" aria-hidden="true"></i> Anular';

        acciones.append(btnVerPdf, btnVerConsultas, btnEditar, btnAnular);

        card.append(header, body, acciones);

        return card;
    }

    /**
     * actualizarProximoNumero(): gestiona la visualización o el contexto de los modales; lee o escribe valores de
     * formularios; opera sobre historias clínicas (creación, listado o detalle).
     */
    function actualizarProximoNumero(lista = []) {
        let maximo = 0;

        lista.forEach(historia => {
            const coincidencia = /HC-(\d+)/.exec(historia.numero_historia ?? '');
            if (!coincidencia) {
                return;
            }

            const valor = parseInt(coincidencia[1], 10);
            if (!Number.isNaN(valor)) {
                maximo = Math.max(maximo, valor);
            }
        });

        proximoNumeroHistoria = `HC-${String(maximo + 1).padStart(5, '0')}`;

        if (!historiaEditandoId && numeroHistoriaInput && modal && modal.style.display === 'block') {
            numeroHistoriaInput.value = proximoNumeroHistoria;
        }
    }

    /**
     * renderHistorias(): inyecta HTML dinámico en la interfaz; opera sobre historias clínicas (creación, listado o
     * detalle); gestiona las citas agendadas del calendario; muestra mensajes de estado para guiar al usuario.
     */
    function renderHistorias(lista = null) {
        if (Array.isArray(lista)) {
            historiasRegistradas = lista;
            poblarHistoriasParaCitas(lista);
        }

        const historiasBase = Array.isArray(historiasRegistradas) ? historiasRegistradas : [];
        actualizarProximoNumero(historiasBase);

        if (!tablaHistorias) {
            return;
        }

        const termino = terminoBusquedaHistorias.trim().toLowerCase();
        const listaFiltrada = termino
            ? historiasBase.filter(historia => {
                const numero = (historia.numero_historia ?? '').toString().toLowerCase();
                const mascota = (historia.mascota ?? '').toString().toLowerCase();
                const propietario = (historia.propietario ?? '').toString().toLowerCase();

                return (
                    numero.includes(termino) ||
                    mascota.includes(termino) ||
                    propietario.includes(termino)
                );
            })
            : historiasBase;

        tablaHistorias.innerHTML = '';

        if (!listaFiltrada.length) {
            const vacio = document.createElement('div');
            vacio.className = 'historias-registradas__empty';
            const icono = termino ? 'fa-search' : 'fa-folder-open';
            const mensaje = termino
                ? 'No se encontraron historias clínicas para la búsqueda.'
                : 'No hay historias clínicas registradas todavía.';
            vacio.innerHTML = `
                <i class="fas ${icono}"></i>
                <p>${mensaje}</p>
            `;
            tablaHistorias.appendChild(vacio);
            return;
        }

        const fragment = document.createDocumentFragment();
        listaFiltrada.forEach(historia => {
            fragment.appendChild(crearTarjetaHistoria(historia));
        });

        tablaHistorias.appendChild(fragment);
    }

    /**
     * cargarHistorias(): realiza peticiones AJAX con fetch para sincronizar datos; opera sobre historias clínicas
     * (creación, listado o detalle); gestiona las citas agendadas del calendario; muestra mensajes de estado para
     * guiar al usuario.
     */
    async function cargarHistorias() {
        if (!historiaListUrl) {
            return;
        }

        try {
            const response = await fetch(historiaListUrl, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('No se pudieron obtener las historias clínicas.');
            }

            const data = await response.json();
            renderHistorias(data.data ?? []);
        } catch (error) {
            console.error(error);
            mostrarMensajeHistoria('No se pudieron cargar las historias clínicas.', 'error');
            mostrarMensajeCita('No se pudieron cargar las historias clínicas.', 'error');
            renderHistorias([]);
        }
    }

    /**
     * navegarAHistorias(): gestiona la visualización o el contexto de los modales; agrega o remueve clases CSS para
     * reflejar estados visuales; consulta nodos del DOM para actualizar la interfaz; registra listeners adicionales
     * dentro del componente; lee o escribe valores de formularios; opera sobre historias clínicas (creación, listado
     * o detalle); maneja consultas médicas de cada mascota; gestiona las citas agendadas del calendario; trabaja con
     * la generación o visualización de respaldos; muestra mensajes de estado para guiar al usuario.
     */
    function navegarAHistorias() {
        const linkHistorias = document.querySelector('.sidebar-menu a[data-section="historias"]');
        manejarNavegacion(linkHistorias);
    }

    if (btnNueva) {
        // Evento click sobre btnNueva: responde a clics del usuario para disparar la acción asociada.
        btnNueva.addEventListener('click', () => {
            abrirModalParaCrear();
        });
    }

    if (btnGenerarBackup) {
        // Evento click sobre btnGenerarBackup: responde a clics del usuario para disparar la acción asociada.
        btnGenerarBackup.addEventListener('click', () => {
            generarBackup();
        });
    }

    if (btnVerBackups) {
        // Evento click sobre btnVerBackups: responde a clics del usuario para disparar la acción asociada.
        btnVerBackups.addEventListener('click', async () => {
            mostrarMensajeBackup('');
            setButtonLoading(btnVerBackups, true, 'Cargando...');

            try {
                await cargarBackups(true);
            } finally {
                setButtonLoading(btnVerBackups, false);
            }
        });
    }

    if (btnIrHistorias) {
        // Evento click sobre btnIrHistorias: responde a clics del usuario para disparar la acción asociada.
        btnIrHistorias.addEventListener('click', event => {
            event.preventDefault();

            navegarAHistorias();
        });
    }

    if (btnIrCrearHistoria) {
        // Evento click sobre btnIrCrearHistoria: responde a clics del usuario para disparar la acción asociada.
        btnIrCrearHistoria.addEventListener('click', event => {
            event.preventDefault();

            navegarAHistorias();
        });
    }

    if (buscarHistoriasInput) {
        // Evento input sobre buscarHistoriasInput: reacciona a la escritura en tiempo real para mantener filtros o mensajes.
        buscarHistoriasInput.addEventListener('input', event => {
            const valor = event.target && typeof event.target.value === 'string'
                ? event.target.value
                : '';
            terminoBusquedaHistorias = valor;
            renderHistorias();
        });
    }

    const buscarCitasDebounce = debounce(valor => {
        cargarCitas(valor);
    }, 350);

    if (buscarCitasInput) {
        // Evento input sobre buscarCitasInput: reacciona a la escritura en tiempo real para mantener filtros o mensajes.
        buscarCitasInput.addEventListener('input', event => {
            citasBusquedaActual = event.target.value.trim();
            buscarCitasDebounce(citasBusquedaActual);
        });
    }

    if (tablaCitas) {
        // Evento click sobre tablaCitas: responde a clics del usuario para disparar la acción asociada.
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
        // Evento click sobre elemento: responde a clics del usuario para disparar la acción asociada.
        elemento.addEventListener('click', () => {
            cerrarModalGenerico(modalDetalleCita);
            citaDetalleSeleccionada = null;
        });
    });

    document.querySelectorAll('[data-close="estadoCita"]').forEach(elemento => {
        // Evento click sobre elemento: responde a clics del usuario para disparar la acción asociada.
        elemento.addEventListener('click', () => {
            cerrarModalGenerico(modalEstadoCita);
            resetCamposReprogramar();
            citaSeleccionadaParaEstado = null;
        });
    });

    if (modalDetalleCita) {
        // Evento click sobre modalDetalleCita: responde a clics del usuario para disparar la acción asociada.
        modalDetalleCita.addEventListener('click', event => {
            if (event.target === modalDetalleCita) {
                cerrarModalGenerico(modalDetalleCita);
                citaDetalleSeleccionada = null;
            }
        });
    }

    if (modalEstadoCita) {
        // Evento click sobre modalEstadoCita: responde a clics del usuario para disparar la acción asociada.
        modalEstadoCita.addEventListener('click', event => {
            if (event.target === modalEstadoCita) {
                cerrarModalGenerico(modalEstadoCita);
                resetCamposReprogramar();
                citaSeleccionadaParaEstado = null;
            }
        });
    }

    if (selectEstadoCita) {
        // Evento change sobre selectEstadoCita: sincroniza los cambios de selects o campos dependientes.
        selectEstadoCita.addEventListener('change', () => {
            toggleCamposReprogramar(selectEstadoCita.value);
        });
    }

    if (formEstadoCita) {
        // Evento submit sobre formEstadoCita: intercepta el envío del formulario para validar y enviar por AJAX.
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

    if (historiaSelectCita) {
        // Evento change sobre historiaSelectCita: sincroniza los cambios de selects o campos dependientes.
        historiaSelectCita.addEventListener('change', async event => {
            const id = event.target.value;

            if (!id) {
                rellenarDatosHistoriaEnCita(null);
                return;
            }

            try {
                const { historia } = await obtenerHistoriaDetallada(id);
                rellenarDatosHistoriaEnCita(historia);
            } catch (error) {
                console.error(error);
                mostrarMensajeCita(error.message || 'No se pudo cargar la historia clínica seleccionada.', 'error');
                rellenarDatosHistoriaEnCita(null);
            }
        });
    }

    if (spanClose) {
        // Evento click sobre spanClose: responde a clics del usuario para disparar la acción asociada.
        spanClose.addEventListener('click', () => {
            cerrarModal();
            reiniciarFormulario();
        });
    }

    if (modalConsultasClose) {
        // Evento click sobre modalConsultasClose: responde a clics del usuario para disparar la acción asociada.
        modalConsultasClose.addEventListener('click', () => {
            cerrarModalGenerico(modalConsultas);
            limpiarFormularioConsulta();
        });
    }

    // Evento click sobre window: responde a clics del usuario para disparar la acción asociada.
    window.addEventListener('click', event => {
        if (event.target === modal) {
            cerrarModal();
            reiniciarFormulario();
        }

        if (event.target === modalConsultas) {
            cerrarModalGenerico(modalConsultas);
            limpiarFormularioConsulta();
        }
    });

    if (especieSelect) {
        // Evento change sobre especieSelect: sincroniza los cambios de selects o campos dependientes.
        especieSelect.addEventListener('change', () => {
            if (especieSelect.value === 'otro') {
                mostrarEspecieOtro();
            } else {
                ocultarEspecieOtro();
            }
        });
    }

    /**
     * cargarHistoriaParaEditar(): realiza peticiones AJAX con fetch para sincronizar datos; opera sobre historias
     * clínicas (creación, listado o detalle); muestra mensajes de estado para guiar al usuario.
     */
    async function cargarHistoriaParaEditar(id) {
        try {
            const response = await fetch(`${historiaBaseUrl}/${id}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('No se pudo obtener la historia clínica.');
            }

            const data = await response.json();
            rellenarFormulario(data.historia);
        } catch (error) {
            console.error(error);
            mostrarMensajeHistoria(error.message || 'No se pudo cargar la historia clínica.', 'error');
        }
    }

    /**
     * eliminarHistoria(): gestiona la visualización o el contexto de los modales; realiza peticiones AJAX con fetch
     * para sincronizar datos; serializa formularios y los envía vía AJAX; agrega o remueve clases CSS para reflejar
     * estados visuales; consulta nodos del DOM para actualizar la interfaz; registra listeners adicionales dentro
     * del componente; lee o escribe valores de formularios; opera sobre historias clínicas (creación, listado o
     * detalle); maneja consultas médicas de cada mascota; gestiona las citas agendadas del calendario; muestra
     * mensajes de estado para guiar al usuario.
     */
    async function eliminarHistoria(id) {
        if (!historiaBaseUrl) {
            return;
        }

        try {
            const response = await fetch(`${historiaBaseUrl}/${id}`, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (!response.ok) {
                throw new Error('No se pudo anular la historia clínica.');
            }

            mostrarMensajeHistoria('Historia clínica anulada correctamente.');
            await cargarHistorias();
        } catch (error) {
            console.error(error);
            mostrarMensajeHistoria(error.message || 'No se pudo anular la historia clínica.', 'error');
        }
    }

    if (confirmCancelButton) {
        // Evento click sobre confirmCancelButton: responde a clics del usuario para disparar la acción asociada.
        confirmCancelButton.addEventListener('click', () => {
            cerrarConfirmacion();
        });
    }

    if (confirmAcceptButton) {
        // Evento click sobre confirmAcceptButton: responde a clics del usuario para disparar la acción asociada.
        confirmAcceptButton.addEventListener('click', async () => {
            if (!historiaPorAnularId) {
                cerrarConfirmacion();
                return;
            }

            const id = historiaPorAnularId;
            cerrarConfirmacion();
            await eliminarHistoria(id);
        });
    }

    if (confirmModal) {
        // Evento click sobre confirmModal: responde a clics del usuario para disparar la acción asociada.
        confirmModal.addEventListener('click', event => {
            if (event.target === confirmModal) {
                cerrarConfirmacion();
            }
        });
    }

    // Evento keydown sobre document: controla atajos de teclado y evita comportamientos no deseados.
    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') {
            return;
        }

        if (confirmModal?.classList.contains('is-visible')) {
            cerrarConfirmacion();
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

        if (modalConsultas && modalConsultas.style.display === 'block') {
            cerrarModalGenerico(modalConsultas);
            limpiarFormularioConsulta();
        }
    });

    if (tablaHistorias) {
        // Evento click sobre tablaHistorias: responde a clics del usuario para disparar la acción asociada.
        tablaHistorias.addEventListener('click', event => {
            const botonConsultas = event.target.closest('.btnConsultas');
            const botonEditar = event.target.closest('.btnEditar');
            const botonAnular = event.target.closest('.btnAnular');

            if (botonConsultas) {
                const tarjeta = botonConsultas.closest('.historia-card');
                const id = tarjeta?.dataset.historiaId;
                if (id) {
                    mostrarDetalleHistoria(id);
                }
            }

            if (botonEditar) {
                const tarjeta = botonEditar.closest('.historia-card');
                const id = tarjeta?.dataset.historiaId;
                if (id) {
                    cargarHistoriaParaEditar(id);
                }
            }

            if (botonAnular) {
                const tarjeta = botonAnular.closest('.historia-card');
                const id = tarjeta?.dataset.historiaId;
                if (id) {
                    abrirConfirmacionPara(id);
                }
            }
        });
    }

    if (formularioCita) {
        // Evento submit sobre formularioCita: intercepta el envío del formulario para validar y enviar por AJAX.
        formularioCita.addEventListener('submit', async event => {
            event.preventDefault();

            if (!citasStoreUrl) {
                mostrarMensajeCita('No se pudo determinar la ruta para guardar la cita.', 'error');
                return;
            }

            const motivo = (citaCampos.motivo?.value || '').trim();
            const fecha = citaCampos.fecha?.value || '';
            const hora = citaCampos.hora?.value || '';

            if (!historiaSeleccionadaParaCita?.id) {
                mostrarMensajeCita('Selecciona una historia clínica antes de registrar la cita.', 'error');
                return;
            }

            if (!motivo) {
                mostrarMensajeCita('El motivo de la cita es obligatorio.', 'error');
                citaCampos.motivo?.focus();
                return;
            }

            if (!fecha) {
                mostrarMensajeCita('Selecciona la fecha de la cita.', 'error');
                citaCampos.fecha?.focus();
                return;
            }

            if (!hora) {
                mostrarMensajeCita('Selecciona la hora de la cita.', 'error');
                citaCampos.hora?.focus();
                return;
            }

            const payload = {
                id_historia: historiaSeleccionadaParaCita.id,
                fecha_cita: fecha,
                hora_cita: hora,
                motivo,
            };

            try {
                const response = await fetch(citasStoreUrl, {
                    method: 'POST',
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
                    const mensaje = errores.join(' ') || 'Verifica los datos ingresados.';
                    mostrarMensajeCita(mensaje, 'error');
                    return;
                }

                if (!response.ok) {
                    throw new Error(data?.message || 'No se pudo registrar la cita.');
                }

                mostrarMensajeCita('Cita registrada correctamente.');
                formularioCita.reset();
                limpiarDatosHistoriaEnCita();
                historiaSeleccionadaParaCita = null;
                await cargarCitas(citasBusquedaActual);
                mostrarMensajeListadoCitas('Se registró una nueva cita en la agenda.', 'success');
                cargarCitasProximas();
            } catch (error) {
                console.error(error);
                mostrarMensajeCita(error.message || 'No se pudo registrar la cita.', 'error');
            }
        });
    }

    if (formConsulta) {
        // Evento submit sobre formConsulta: intercepta el envío del formulario para validar y enviar por AJAX.
        formConsulta.addEventListener('submit', async event => {
            event.preventDefault();

            if (!consultaStoreUrl) {
                mostrarMensajeConsulta('No se pudo determinar la ruta para guardar la consulta.', 'error');
                return;
            }

            const historiaId = consultaHistoriaId?.value || historiaDetalleActual?.id;
            const fecha = consultaCampos.fecha?.value || '';

            if (!historiaId) {
                mostrarMensajeConsulta('Selecciona una historia clínica válida antes de registrar la consulta.', 'error');
                return;
            }

            if (!fecha) {
                mostrarMensajeConsulta('La fecha de la consulta es obligatoria.', 'error');
                consultaCampos.fecha?.focus();
                return;
            }

            const payload = {
                id_historia: parseInt(historiaId, 10),
                fecha_consulta: fecha,
                sintomas: consultaCampos.sintomas?.value || null,
                diagnostico: consultaCampos.diagnostico?.value || null,
                tratamiento: consultaCampos.tratamiento?.value || null,
                observaciones: consultaCampos.observaciones?.value || null,
                peso: consultaCampos.peso?.value || null,
                temperatura: consultaCampos.temperatura?.value || null,
            };

            Object.keys(payload).forEach(clave => {
                if (payload[clave] === '' || payload[clave] === null) {
                    delete payload[clave];
                }
            });

            const botonGuardarConsulta = formConsulta.querySelector('button[type="submit"]');
            if (botonGuardarConsulta) {
                botonGuardarConsulta.disabled = true;
            }

            try {
                const response = await fetch(consultaStoreUrl, {
                    method: 'POST',
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
                    const mensaje = errores.join(' ') || 'Revisa los datos de la consulta.';
                    mostrarMensajeConsulta(mensaje, 'error');
                    return;
                }

                if (!response.ok) {
                    throw new Error(data?.message || 'No se pudo guardar la consulta.');
                }

                if (data?.consulta) {
                    consultasDetalleActual = [data.consulta, ...consultasDetalleActual];
                    renderConsultas(consultasDetalleActual);
                }

                mostrarMensajeConsulta('Consulta registrada correctamente.');
                limpiarFormularioConsulta();
                if (consultaCampos.fecha) {
                    consultaCampos.fecha.value = fecha;
                }
            } catch (error) {
                console.error(error);
                mostrarMensajeConsulta(error.message || 'No se pudo guardar la consulta.', 'error');
            } finally {
                if (botonGuardarConsulta) {
                    botonGuardarConsulta.disabled = false;
                }
            }
        });
    }

    if (form) {
        // Evento submit sobre form: intercepta el envío del formulario para validar y enviar por AJAX.
        form.addEventListener('submit', async event => {
            event.preventDefault();

            if (!historiaStoreUrl) {
                return;
            }

            const formData = new FormData(form);
            const payload = {};

            formData.forEach((value, key) => {
                if (key === 'numero_historia') {
                    return;
                }

                if (typeof value === 'string') {
                    const limpio = value.trim();
                    if (limpio !== '') {
                        payload[key] = limpio;
                    }
                } else {
                    payload[key] = value;
                }
            });

            ['especieOtro', 'edad', 'peso'].forEach(campo => {
                if (payload[campo] === '' || payload[campo] === undefined) {
                    delete payload[campo];
                }
            });

            if (btnGuardar) {
                btnGuardar.disabled = true;
            }

            try {
                const url = historiaEditandoId ? `${historiaBaseUrl}/${historiaEditandoId}` : historiaStoreUrl;
                const method = historiaEditandoId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const responseData = await response.json().catch(() => null);

                if (response.status === 422) {
                    const errores = Object.values(responseData?.errors ?? {}).flat();
                    const mensaje = errores.join(' ') || 'Revisa los datos ingresados.';
                    mostrarMensajeHistoria(mensaje, 'error');
                    return;
                }

                if (!response.ok) {
                    throw new Error(responseData?.message || 'No se pudo guardar la historia clínica.');
                }

                const mensajeExito = historiaEditandoId
                    ? 'Historia clínica actualizada correctamente.'
                    : 'Historia clínica guardada correctamente.';

                mostrarMensajeHistoria(mensajeExito);
                cerrarModal();
                reiniciarFormulario();
                await cargarHistorias();
            } catch (error) {
                console.error(error);
                mostrarMensajeHistoria(error.message || 'No se pudo guardar la historia clínica.', 'error');
            } finally {
                if (btnGuardar) {
                    btnGuardar.disabled = false;
                }
            }
        });
    }


    /**
     * iniciarBuscadorHistorias(): registra listeners adicionales dentro del componente; opera sobre historias
     * clínicas (creación, listado o detalle).
     */
    const iniciarBuscadorHistorias = () => {
        if (typeof window.inicializarBuscadorHistorias === 'function') {
            window.inicializarBuscadorHistorias();
        }
    };

    if (document.readyState === 'loading') {
        // Evento DOMContentLoaded sobre document: inicia la configuración una vez que el DOM está listo.
        document.addEventListener('DOMContentLoaded', iniciarBuscadorHistorias);
    } else {
        iniciarBuscadorHistorias();
    }


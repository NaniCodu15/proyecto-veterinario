(() => {
    const configElement = document.getElementById('dashboard-config');
    let moduleConfig = window.dashboardConfig;

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

    const historiaStoreUrl = moduleConfig.historiaStoreUrl || '';
    const historiaBaseUrl = moduleConfig.historiaBaseUrl || '';
    const backupGenerateUrl = moduleConfig.backupGenerateUrl || '';
    const backupListUrl = moduleConfig.backupListUrl || '';
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    if (typeof window.proximoNumeroHistoria === 'undefined') {
        window.proximoNumeroHistoria = 'HC-00001';
    }

    const modal = document.getElementById('modalHistoria');
    const btnNueva = document.getElementById('btnNuevaHistoria');
    const spanClose = document.querySelector('#modalHistoria .close');
    const form = document.getElementById('formHistoria');
    const titulo = document.getElementById('modalTitulo');
    const numeroHistoriaInput = document.getElementById('numero_historia');
    const especieSelect = document.getElementById('especie');
    const especieOtroGroup = document.getElementById('grupoEspecieOtro');
    const especieOtroInput = document.getElementById('especieOtro');
    const mensajesHistoria = Array.from(document.querySelectorAll('[data-historia-mensaje]'));
    const btnGenerarBackup = document.getElementById('btnGenerarBackup');
    const btnVerBackups = document.getElementById('btnVerBackups');
    const backupMensaje = document.querySelector('[data-backup-mensaje]');
    const backupContenedor = document.getElementById('backupRegistros');
    const backupWrapper = backupContenedor?.querySelector('[data-backup-wrapper]') ?? null;
    const backupTableBody = backupContenedor?.querySelector('[data-backup-body]') ?? null;
    const confirmModal = document.getElementById('confirmModal');
    const confirmAcceptButton = confirmModal?.querySelector('[data-confirm="accept"]');
    const confirmCancelButton = confirmModal?.querySelector('[data-confirm="cancel"]');

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

    let historiaEditandoId = null;
    let historiaPorAnularId = null;

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

    function abrirModal() {
        if (!modal) {
            return;
        }

        modal.style.display = 'block';
        modal.setAttribute('aria-hidden', 'false');
        actualizarEstadoBodyModal();
    }

    function cerrarModal() {
        if (!modal) {
            return;
        }

        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        actualizarEstadoBodyModal();
    }

    function ocultarEspecieOtro() {
        if (!especieOtroGroup || !especieOtroInput) {
            return;
        }

        especieOtroGroup.style.display = 'none';
        especieOtroInput.value = '';
        especieOtroInput.removeAttribute('required');
    }

    function mostrarEspecieOtro() {
        if (!especieOtroGroup || !especieOtroInput) {
            return;
        }

        especieOtroGroup.style.display = 'block';
        especieOtroInput.setAttribute('required', 'required');
    }

    function prepararFormularioBase() {
        if (!form) {
            return;
        }

        form.reset();
        ocultarEspecieOtro();

        if (numeroHistoriaInput) {
            numeroHistoriaInput.value = window.proximoNumeroHistoria || 'HC-00001';
            numeroHistoriaInput.placeholder = 'Se generará automáticamente';
        }
    }

    function actualizarNumeroHistoriaEnFormulario() {
        if (!historiaEditandoId && numeroHistoriaInput && modal && modal.style.display === 'block') {
            numeroHistoriaInput.value = window.proximoNumeroHistoria || 'HC-00001';
        }
    }

    window.actualizarNumeroHistoriaEnFormulario = actualizarNumeroHistoriaEnFormulario;

    function reiniciarFormulario() {
        historiaEditandoId = null;
        prepararFormularioBase();

        if (titulo) {
            titulo.textContent = 'Nueva Historia Clínica';
        }

        const btnGuardar = form?.querySelector('.btn-guardar');
        if (btnGuardar) {
            btnGuardar.textContent = 'Guardar';
        }
    }

    function abrirModalParaCrear() {
        reiniciarFormulario();
        abrirModal();
    }

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

        const btnGuardar = form?.querySelector('.btn-guardar');
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

    window.mostrarMensajeHistoria = mostrarMensajeHistoria;

    function mostrarMensajeBackup(texto, tipo = 'success') {
        if (!backupMensaje) {
            return;
        }

        backupMensaje.textContent = texto;
        backupMensaje.classList.remove('alert--success', 'alert--error', 'alert--info');
        const clase = tipo === 'error' ? 'alert--error' : tipo === 'info' ? 'alert--info' : 'alert--success';
        backupMensaje.classList.add(clase);
        backupMensaje.hidden = texto === '';

        window.clearTimeout(mostrarMensajeBackup.timeoutId);
        if (texto) {
            mostrarMensajeBackup.timeoutId = window.setTimeout(() => {
                backupMensaje.hidden = true;
            }, 4000);
        }
    }

    function setButtonLoading(button, isLoading, loadingText = 'Procesando...') {
        if (!button) {
            return;
        }

        if (isLoading) {
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${loadingText}`;
            button.disabled = true;
        } else {
            const original = button.dataset.originalText;
            if (original) {
                button.innerHTML = original;
            }
            button.disabled = false;
        }
    }

    function formatearFecha(fecha) {
        if (!fecha) {
            return '—';
        }

        const fechaObjeto = new Date(fecha);
        return Number.isNaN(fechaObjeto.getTime())
            ? fecha
            : fechaObjeto.toLocaleString('es-ES');
    }

    function renderBackups(registros = []) {
        if (!backupWrapper || !backupTableBody) {
            return;
        }

        backupWrapper.hidden = false;
        backupTableBody.innerHTML = '';

        if (!registros.length) {
            const fila = document.createElement('tr');
            const celda = document.createElement('td');
            celda.colSpan = 5;
            celda.textContent = 'No hay registros de respaldos disponibles.';
            fila.appendChild(celda);
            backupTableBody.appendChild(fila);
            return;
        }

        const fragment = document.createDocumentFragment();

        registros.forEach(registro => {
            const fila = document.createElement('tr');

            const crearCelda = (texto, clase = '') => {
                const celda = document.createElement('td');
                if (clase) {
                    celda.className = clase;
                }
                celda.textContent = texto ?? '—';
                return celda;
            };

            const idRespaldo = registro?.id_respaldo ?? registro?.id;
            const fechaRespaldo = formatearFecha(registro?.fecha_respaldo ?? registro?.fecha);
            const nombreArchivo = registro?.nombre_archivo ?? registro?.archivo;
            const rutaArchivo = registro?.ruta_archivo ?? registro?.ruta;

            fila.appendChild(crearCelda(idRespaldo));
            fila.appendChild(crearCelda(fechaRespaldo));
            fila.appendChild(crearCelda(nombreArchivo));
            fila.appendChild(crearCelda(rutaArchivo));

            const estadoCelda = crearCelda();
            estadoCelda.textContent = registro?.estado ?? 'Pendiente';
            estadoCelda.className = `backup-log__estado backup-log__estado--${(registro?.estado || 'pendiente').toString().toLowerCase()}`;
            fila.appendChild(estadoCelda);

            fragment.appendChild(fila);
        });

        backupTableBody.appendChild(fragment);
    }

    let respaldosCargados = false;

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

    window.abrirConfirmacionPara = abrirConfirmacionPara;

    function cerrarConfirmacion() {
        if (!confirmModal) {
            historiaPorAnularId = null;
            return;
        }

        confirmModal.classList.remove('is-visible');
        confirmModal.hidden = true;
        historiaPorAnularId = null;
    }

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

    window.cargarHistoriaParaEditar = cargarHistoriaParaEditar;

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
            if (typeof window.cargarHistorias === 'function') {
                await window.cargarHistorias();
            }
        } catch (error) {
            console.error(error);
            mostrarMensajeHistoria(error.message || 'No se pudo anular la historia clínica.', 'error');
        }
    }

    if (btnNueva) {
        btnNueva.addEventListener('click', () => {
            abrirModalParaCrear();
        });
    }

    if (btnGenerarBackup) {
        btnGenerarBackup.addEventListener('click', () => {
            generarBackup();
        });
    }

    if (btnVerBackups) {
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

    if (especieSelect) {
        especieSelect.addEventListener('change', () => {
            if (especieSelect.value === 'otro') {
                mostrarEspecieOtro();
            } else {
                ocultarEspecieOtro();
            }
        });
    }

    if (spanClose) {
        spanClose.addEventListener('click', () => {
            cerrarModal();
            reiniciarFormulario();
        });
    }

    if (form) {
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

            const btnGuardar = form.querySelector('.btn-guardar');
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
                if (typeof window.cargarHistorias === 'function') {
                    await window.cargarHistorias();
                }
            } catch (error) {
                console.error(error);
                mostrarMensajeHistoria(error.message || 'No se pudo guardar la historia clínica.', 'error');
            } finally {
                const btnGuardarFinal = form.querySelector('.btn-guardar');
                if (btnGuardarFinal) {
                    btnGuardarFinal.disabled = false;
                }
            }
        });
    }

    if (confirmCancelButton) {
        confirmCancelButton.addEventListener('click', () => {
            cerrarConfirmacion();
        });
    }

    if (confirmAcceptButton) {
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
        confirmModal.addEventListener('click', event => {
            if (event.target === confirmModal) {
                cerrarConfirmacion();
            }
        });
    }

    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') {
            return;
        }

        if (confirmModal?.classList.contains('is-visible')) {
            cerrarConfirmacion();
        }
    });
})();

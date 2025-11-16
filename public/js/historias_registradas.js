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

    const historiaListUrl = moduleConfig.historiaListUrl || '';
    const historiaBaseUrl = moduleConfig.historiaBaseUrl || '';
    const consultaStoreUrl = moduleConfig.consultaStoreUrl || '';
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    const tablaHistorias = document.getElementById('tablaHistorias');
    const buscarHistoriasInput = document.getElementById('buscarHistorias');
    const modalConsultas = document.getElementById('modalConsultas');
    const listaConsultas = document.getElementById('listaConsultas');
    const formConsulta = document.getElementById('formConsulta');
    const consultaMensaje = document.getElementById('consultaMensaje');
    const consultaHistoriaId = document.getElementById('consultaHistoriaId');
    const btnIrCrearHistoria = document.getElementById('btnIrCrearHistoria');

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

    let historiasRegistradas = [];
    let terminoBusquedaHistorias = '';
    let historiaDetalleActual = null;
    let consultasDetalleActual = [];

    const mostrarMensajeHistoria = window.mostrarMensajeHistoria || (() => {});

    function emitirMensajeCita(texto, tipo = 'success') {
        if (typeof window.mostrarMensajeCita === 'function') {
            window.mostrarMensajeCita(texto, tipo);
        }
    }

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

    function mostrarMensajeConsulta(texto, tipo = 'success') {
        if (!consultaMensaje) {
            return;
        }

        consultaMensaje.textContent = texto;
        consultaMensaje.classList.remove('consulta-alert--success', 'consulta-alert--error');
        const clase = tipo === 'error' ? 'consulta-alert--error' : 'consulta-alert--success';
        consultaMensaje.classList.add(clase);
        consultaMensaje.hidden = false;

        window.clearTimeout(mostrarMensajeConsulta.timeoutId);
        mostrarMensajeConsulta.timeoutId = window.setTimeout(() => {
            consultaMensaje.hidden = true;
        }, 4000);
    }

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
        tab.addEventListener('click', () => {
            const objetivo = tab.dataset.tabTarget;
            if (objetivo) {
                activarTabConsulta(objetivo);
            }
        });
    });

    activarTabConsulta('registro');

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

    function crearEtiquetaConsulta(icono, texto) {
        const span = document.createElement('span');
        span.className = 'consulta-item__meta-tag';
        span.innerHTML = `<i class="fas ${icono}"></i> ${texto}`;
        return span;
    }

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

            const tituloBloque = document.createElement('span');
            tituloBloque.className = 'consulta-item__block-title';
            tituloBloque.textContent = etiqueta;

            const contenido = document.createElement('p');
            contenido.className = 'consulta-item__block-text';
            contenido.textContent = valor;

            bloque.append(tituloBloque, contenido);
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

    window.obtenerHistoriaDetallada = obtenerHistoriaDetallada;

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

        window.proximoNumeroHistoria = `HC-${String(maximo + 1).padStart(5, '0')}`;

        if (typeof window.actualizarNumeroHistoriaEnFormulario === 'function') {
            window.actualizarNumeroHistoriaEnFormulario();
        }
    }

    function renderHistorias(lista = null) {
        if (Array.isArray(lista)) {
            historiasRegistradas = lista;
            if (typeof window.poblarHistoriasParaCitas === 'function') {
                window.poblarHistoriasParaCitas(lista);
            }
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
                const propietarioDni = (historia.propietario_dni ?? '').toString().toLowerCase();

                return (
                    numero.includes(termino) ||
                    mascota.includes(termino) ||
                    propietario.includes(termino) ||
                    propietarioDni.includes(termino)
                );
            })
            : historiasBase;

        tablaHistorias.innerHTML = '';

        if (!listaFiltrada.length) {
            const vacio = document.createElement('div');
            vacio.className = 'historia-card historia-card--empty';
            vacio.innerHTML = '<p>No se encontraron historias clínicas.</p>';
            tablaHistorias.appendChild(vacio);
            return;
        }

        const fragment = document.createDocumentFragment();
        listaFiltrada.forEach(historia => {
            fragment.appendChild(crearTarjetaHistoria(historia));
        });

        tablaHistorias.appendChild(fragment);
    }

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
            emitirMensajeCita('No se pudieron cargar las historias clínicas.', 'error');
            renderHistorias([]);
        }
    }

    window.cargarHistorias = cargarHistorias;

    if (buscarHistoriasInput) {
        buscarHistoriasInput.addEventListener('input', event => {
            const valor = event.target && typeof event.target.value === 'string'
                ? event.target.value
                : '';
            terminoBusquedaHistorias = valor;
            renderHistorias();
        });
    }

    if (tablaHistorias) {
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
                if (id && typeof window.cargarHistoriaParaEditar === 'function') {
                    window.cargarHistoriaParaEditar(id);
                }
            }

            if (botonAnular) {
                const tarjeta = botonAnular.closest('.historia-card');
                const id = tarjeta?.dataset.historiaId;
                if (id && typeof window.abrirConfirmacionPara === 'function') {
                    window.abrirConfirmacionPara(id);
                }
            }
        });
    }

    if (btnIrCrearHistoria) {
        btnIrCrearHistoria.addEventListener('click', event => {
            event.preventDefault();
            if (typeof window.navegarAHistorias === 'function') {
                window.navegarAHistorias();
            }
        });
    }

    const cerrarModalConsultas = () => {
        cerrarModalGenerico(modalConsultas);
        limpiarFormularioConsulta();
    };

    document.querySelectorAll('[data-close="consultas"]').forEach(elemento => {
        elemento.addEventListener('click', cerrarModalConsultas);
    });

    if (modalConsultas) {
        modalConsultas.addEventListener('click', event => {
            if (event.target === modalConsultas) {
                cerrarModalConsultas();
            }
        });
    }

    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') {
            return;
        }

        if (modalConsultas && modalConsultas.style.display === 'block') {
            cerrarModalConsultas();
        }
    });

    if (formConsulta) {
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
                id_historia: historiaId,
                fecha,
                peso: consultaCampos.peso?.value || null,
                temperatura: consultaCampos.temperatura?.value || null,
                sintomas: consultaCampos.sintomas?.value || '',
                diagnostico: consultaCampos.diagnostico?.value || '',
                tratamiento: consultaCampos.tratamiento?.value || '',
                observaciones: consultaCampos.observaciones?.value || '',
            };

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
                    const mensaje = errores.join(' ') || 'Verifica los datos ingresados.';
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
})();

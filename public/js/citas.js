// JS para el módulo Registro de Citas: selecciona historias, valida datos y envía nuevas citas.
(() => {
    // Obtiene configuración global del dashboard o del elemento incrustado en la página.
    const configElement = document.getElementById('dashboard-config');
    let moduleConfig = window.dashboardConfig;

    // Asegura la disponibilidad de las rutas y evita referencias indefinidas.
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

    // Rutas y tokens para enviar la cita registrada.
    const citasStoreUrl = moduleConfig.citasStoreUrl || '';
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    // Referencias a formularios y campos del flujo de registro de cita.
    const formularioCita = document.getElementById('formRegistrarCita');
    const historiaSelectCita = document.getElementById('historiaSelectCitas');
    const historiaBusquedaCitas = document.getElementById('historiaBusquedaCitas');
    const citaMensaje = document.getElementById('citaMensaje');

    // Campos individuales del formulario para mostrar o asignar datos.
    const citaCampos = {
        propietarioNombre: document.getElementById('citaPropietarioNombre'),
        propietarioDni: document.getElementById('citaPropietarioDni'),
        propietarioTelefono: document.getElementById('citaPropietarioTelefono'),
        mascotaNombre: document.getElementById('citaMascotaNombre'),
        motivo: document.getElementById('citaMotivo'),
        fecha: document.getElementById('citaFecha'),
        hora: document.getElementById('citaHora'),
    };

    // Estados auxiliares: selección actual y catálogos.
    let historiaSeleccionadaParaCita = null;
    let historiasDisponibles = [];
    let terminoBusquedaHistoriaCita = '';

    // Muestra alertas específicas del formulario de citas y las oculta tras unos segundos.
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

            citaMensaje.hidden = true;
            citaMensaje.classList.remove('is-visible');
        }, 4000);
    }

    window.mostrarMensajeCita = mostrarMensajeCita;

    // Limpia los campos dependientes de la historia cuando no hay selección.
    function limpiarDatosHistoriaEnCita() {
        ['propietarioNombre', 'propietarioDni', 'propietarioTelefono', 'mascotaNombre'].forEach(clave => {
            const campo = citaCampos[clave];
            if (campo) {
                campo.value = '';
            }
        });
    }

    // Rellena los campos de propietario y mascota con la historia escogida.
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

    // Formatea la descripción que se muestra en las opciones del selector.
    function formatearEtiquetaHistoria(historia) {
        const numero = (historia?.numero_historia ?? '').toString().trim() || 'Sin código';
        const mascota = (historia?.mascota ?? historia?.nombreMascota ?? '').toString().trim() || 'Mascota sin nombre';
        const propietario = (historia?.propietario ?? historia?.nombrePropietario ?? '').toString().trim()
            || 'Propietario sin registrar';

        return `${numero} — ${mascota} — ${propietario}`;
    }

    // Aplica el término de búsqueda sobre las historias disponibles.
    function filtrarHistoriasDisponibles() {
        const termino = terminoBusquedaHistoriaCita.trim().toLowerCase();

        if (!termino) {
            return historiasDisponibles;
        }

        return historiasDisponibles.filter(historia => {
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
        });
    }

    // Dibuja las opciones del selector conforme a la búsqueda y conserva la selección válida.
    function renderizarSelectHistorias() {
        if (!historiaSelectCita) {
            return;
        }

        const valorActual = historiaSelectCita.value;
        const listaFiltrada = filtrarHistoriasDisponibles();

        historiaSelectCita.innerHTML = '<option value="">Selecciona una historia clínica</option>';

        listaFiltrada.forEach(historia => {
            const opcion = document.createElement('option');
            opcion.value = historia.id;
            opcion.textContent = formatearEtiquetaHistoria(historia);
            historiaSelectCita.appendChild(opcion);
        });

        const existeValorPrevio = listaFiltrada.some(
            historia => String(historia?.id ?? '') === String(valorActual)
        );

        if (existeValorPrevio) {
            historiaSelectCita.value = valorActual;
        } else {
            historiaSelectCita.value = '';
            historiaSeleccionadaParaCita = null;
            limpiarDatosHistoriaEnCita();
        }

        historiaSelectCita.disabled = !listaFiltrada.length;
    }

    // Rellena el select de historias con la data disponible y conserva selección previa.
    function poblarHistoriasParaCitas(lista = []) {
        historiasDisponibles = Array.isArray(lista)
            ? lista.filter(historia => historia && historia.id)
            : [];

        renderizarSelectHistorias();
    }

    window.poblarHistoriasParaCitas = poblarHistoriasParaCitas;

    // Búsqueda en tiempo real para actualizar las opciones del selector.
    if (historiaBusquedaCitas) {
        historiaBusquedaCitas.addEventListener('input', event => {
            terminoBusquedaHistoriaCita = typeof event.target.value === 'string'
                ? event.target.value
                : '';

            renderizarSelectHistorias();
        });
    }

    // Cambio de la historia seleccionada: obtiene detalle vía AJAX y rellena campos.
    if (historiaSelectCita) {
        historiaSelectCita.addEventListener('change', async event => {
            const id = event.target.value;

            if (!id) {
                rellenarDatosHistoriaEnCita(null);
                return;
            }

            try {
                const { historia } = await window.obtenerHistoriaDetallada(id);
                rellenarDatosHistoriaEnCita(historia);
            } catch (error) {
                console.error(error);
                mostrarMensajeCita(error.message || 'No se pudo cargar la historia clínica seleccionada.', 'error');
                rellenarDatosHistoriaEnCita(null);
            }
        });
    }

    // Evento submit del formulario de citas: valida, arma payload y lo envía.
    if (formularioCita) {
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

                if (typeof window.cargarCitas === 'function') {
                    const termino = window.citasBusquedaActual || '';
                    await window.cargarCitas(termino);
                }

                if (typeof window.mostrarMensajeListadoCitas === 'function') {
                    window.mostrarMensajeListadoCitas('Se registró una nueva cita en la agenda.', 'success');
                }

                if (typeof window.cargarCitasProximas === 'function') {
                    window.cargarCitasProximas();
                }
            } catch (error) {
                console.error(error);
                mostrarMensajeCita(error.message || 'No se pudo registrar la cita.', 'error');
            }
        });
    }
})();

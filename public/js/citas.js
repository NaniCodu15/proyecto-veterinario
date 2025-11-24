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
    const historiaSearchUrl = moduleConfig.historiaSearchUrl || '';
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    // Referencias a formularios y campos del flujo de registro de cita.
    const formularioCita = document.getElementById('formRegistrarCita');
    const historiaSelectCita = document.getElementById('historiaSelectCitas');
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

    // Estados auxiliares: selección actual
    let historiaSeleccionadaParaCita = null;

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
            citaCampos.propietarioNombre.setAttribute('readonly', 'readonly');
        }
        if (citaCampos.propietarioDni) {
            citaCampos.propietarioDni.value = historia.dni ?? '';
            citaCampos.propietarioDni.setAttribute('readonly', 'readonly');
        }
        if (citaCampos.propietarioTelefono) {
            citaCampos.propietarioTelefono.value = historia.telefono ?? '';
            citaCampos.propietarioTelefono.setAttribute('readonly', 'readonly');
        }
        if (citaCampos.mascotaNombre) {
            citaCampos.mascotaNombre.value = historia.nombreMascota ?? '';
            citaCampos.mascotaNombre.setAttribute('readonly', 'readonly');
        }
    }

    // Mantiene compatibilidad con inicializaciones previas aunque ya no se carguen listados locales.
    window.poblarHistoriasParaCitas = function poblarHistoriasParaCitas() {
        // El buscador ahora funciona 100% vía AJAX con Select2, por lo que no requiere pre-carga.
    };

    // Inicializa el componente Select2 para buscar historias clínicas por propietario o DNI.
    function inicializarBuscadorSelect2() {
        if (!historiaSelectCita || typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
            return;
        }

        const $select = $(historiaSelectCita);

        $select.select2({
            placeholder: 'Busca por propietario o DNI',
            minimumInputLength: 2,
            allowClear: true,
            width: '100%',
            ajax: {
                url: historiaSearchUrl,
                dataType: 'json',
                delay: 300,
                data(params) {
                    return { q: params.term || '' };
                },
                processResults(data) {
                    const resultados = Array.isArray(data?.results) ? data.results : [];
                    return {
                        results: resultados.map(item => ({
                            id: item.id,
                            text: item.text,
                            nombrePropietario: item.nombre_propietario || '',
                            dni: item.dni_propietario || '',
                            telefono: item.telefono_propietario || '',
                            nombreMascota: item.nombre_mascota || '',
                        })),
                    };
                },
            },
            language: {
                inputTooShort: () => 'Escribe al menos 2 caracteres para buscar.',
                noResults: () => 'No se encontraron propietarios.',
                searching: () => 'Buscando...'
            },
        });

        $select.on('select2:select', event => {
            const data = event.params?.data || {};
            rellenarDatosHistoriaEnCita({
                id: data.id,
                nombrePropietario: data.nombrePropietario,
                dni: data.dni,
                telefono: data.telefono,
                nombreMascota: data.nombreMascota,
            });
        });

        $select.on('select2:clear', () => {
            rellenarDatosHistoriaEnCita(null);
        });
    }

    // Ejecuta la inicialización del buscador según el estado de carga del DOM.
    const iniciarBuscadorHistorias = () => {
        inicializarBuscadorSelect2();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciarBuscadorHistorias);
    } else {
        iniciarBuscadorHistorias();
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
                mostrarMensajeCita('Selecciona un propietario con historia clínica antes de registrar la cita.', 'error');
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

                if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                    $(historiaSelectCita).val(null).trigger('change');
                }
            } catch (error) {
                console.error(error);
                mostrarMensajeCita(error.message || 'No se pudo registrar la cita.', 'error');
            }
        });
    }
})();

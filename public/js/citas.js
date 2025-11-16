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
    }, 4000);
}

function limpiarDatosHistoriaEnCita() {
    ['propietarioNombre', 'propietarioDni', 'propietarioTelefono', 'mascotaNombre'].forEach(clave => {
        const campo = citaCampos[clave];
        if (campo) {
            campo.value = '';
        }
    });
}

function rellenarDatosHistoriaEnCita(historia) {
    if (!historia) {
        limpiarDatosHistoriaEnCita();
        historiaSeleccionadaParaCita = null;
        return;
    }

    historiaSeleccionadaParaCita = {
        id: historia.id,
        numero_historia: historia.numero_historia,
        propietario: historia.propietario,
        propietario_dni: historia.propietario_dni,
        propietario_telefono: historia.propietario_telefono,
        mascota: historia.mascota,
    };

    if (citaCampos.propietarioNombre) {
        citaCampos.propietarioNombre.value = historia.propietario || '';
    }

    if (citaCampos.propietarioDni) {
        citaCampos.propietarioDni.value = historia.propietario_dni || '';
    }

    if (citaCampos.propietarioTelefono) {
        citaCampos.propietarioTelefono.value = historia.propietario_telefono || '';
    }

    if (citaCampos.mascotaNombre) {
        citaCampos.mascotaNombre.value = historia.mascota || '';
    }
}

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
        }
    }
}

function poblarHistoriasParaCitas(lista = []) {
    historiasDisponibles = Array.isArray(lista)
        ? lista.map(historia => ({
            id: historia.id,
            numero_historia: historia.numero_historia,
            mascota: historia.mascota,
            propietario: historia.propietario,
            propietario_dni: historia.propietario_dni,
        }))
        : [];

    if (historiaSelectCita) {
        const valorActual = historiaSelectCita.value;
        historiaSelectCita.innerHTML = '<option value="">Selecciona una historia clínica</option>';

        historiasDisponibles.forEach(historia => {
            const option = document.createElement('option');
            option.value = historia.id ?? '';
            option.textContent = `${historia.numero_historia || 'Sin código'} · ${historia.mascota || 'Sin nombre'}`;
            historiaSelectCita.appendChild(option);
        });

        if (valorActual) {
            historiaSelectCita.value = valorActual;
        } else {
            historiaSelectCita.value = '';
            historiaSeleccionadaParaCita = null;
            limpiarDatosHistoriaEnCita();
        }
    }

    sincronizarTomSelectHistorias();
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

if (historiaSelectCita) {
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
            await cargarCitas(citasBusquedaActual);
            mostrarMensajeListadoCitas('Se registró una nueva cita en la agenda.', 'success');
            cargarCitasProximas();
        } catch (error) {
            console.error(error);
            mostrarMensajeCita(error.message || 'No se pudo registrar la cita.', 'error');
        }
    });
}

const iniciarBuscadorHistorias = () => {
    if (typeof window.inicializarBuscadorHistorias === 'function') {
        window.inicializarBuscadorHistorias();
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', iniciarBuscadorHistorias);
} else {
    iniciarBuscadorHistorias();
}

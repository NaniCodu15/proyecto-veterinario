@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- BOTÓN HAMBURGUESA -->
    <button class="toggle-btn" id="toggleSidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- SIDEBAR FIJO -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logoVet.png') }}" alt="Logo" class="sidebar-logo">
        </div>

        <ul class="sidebar-menu">
            <li><a href="#" class="nav-link active" data-section="inicio"><i class="fas fa-home"></i><span>Inicio</span></a></li>
            <li><a href="#" class="nav-link" data-section="citas"><i class="fas fa-calendar-alt"></i><span>Citas</span></a></li>
            <li><a href="#" class="nav-link" data-section="historias"><i class="fas fa-notes-medical"></i><span>Historias Clínicas</span></a></li>
            <li><a href="#" class="nav-link" data-section="mascotas"><i class="fas fa-dog"></i><span>Mascotas</span></a></li>
            <li><a href="#" class="nav-link" data-section="propietarios"><i class="fas fa-user"></i><span>Propietarios</span></a></li>
            <li><a href="#" class="nav-link" data-section="consultas"><i class="fas fa-stethoscope"></i><span>Consultas</span></a></li>
            <li><a href="#" class="nav-link" data-section="vacunas"><i class="fas fa-syringe"></i><span>Vacunas</span></a></li>
            <li><a href="#" class="nav-link" data-section="tratamientos"><i class="fas fa-pills"></i><span>Tratamientos</span></a></li>
        </ul>

        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> <span>Cerrar sesión</span>
            </button>
        </form>
    </div>

    <!-- CONTENIDO PRINCIPAL (CAMBIA SEGÚN OPCIÓN) -->
    <div id="main-content" class="content">
        <!-- SECCIÓN INICIO -->
        <div id="section-inicio" class="section active">
            <h1 class="titulo">HOSPITAL VETERINARIO</h1>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar...">
            </div>

            <div class="dashboard-cards">
                <div class="stat-card">
                    <i class="fas fa-dog icon"></i>
                    <div class="stat-info">
                        <h2>{{ $totalMascotas }}</h2>
                        <p>Mascotas Registradas</p>
                    </div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-user icon"></i>
                    <div class="stat-info">
                        <h2>{{ $totalPropietarios }}</h2>
                        <p>Propietarios Registrados</p>
                    </div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-stethoscope icon"></i>
                    <div class="stat-info">
                        <h2>{{ $totalConsultas ?? 0 }}</h2>
                        <p>Consultas Realizadas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN HISTORIAS CLÍNICAS -->
        <div id="section-historias" class="section">
            <div class="historias-wrapper">
                <div class="historias-header">
                    <h1 class="titulo">Historias Clínicas</h1>

                    <!-- BOTÓN NUEVA HISTORIA -->
                    <button id="btnNuevaHistoria" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Historia Clínica
                    </button>
                </div>

                <div id="historiaMensaje" class="alert" role="status" aria-live="polite" hidden></div>

                <!-- TABLA DE HISTORIAS -->
                <div class="tabla-wrapper">
                    <table class="tabla-consultas">
                        <thead>
                            <tr>
                                <th>N° Historia</th>
                                <th>Mascota</th>
                                <th>Fecha Apertura</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaHistorias">
                            <tr class="tabla-historias__empty">
                                <td colspan="4">No hay historias clínicas registradas todavía.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL NUEVA/EDITAR HISTORIA -->
            <div id="modalHistoria" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2 id="modalTitulo">Nueva Historia Clínica</h2>
                    <form id="formHistoria">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label>ID de Historia Clínica:</label>
                                <input type="text" id="numero_historia" name="numero_historia" readonly>
                            </div>

                            <div class="form-group">
                                <label>Nombre de la Mascota:</label>
                                <input type="text" id="nombreMascota" name="nombreMascota" required>
                            </div>

                            <div class="form-group">
                                <label>Especie:</label>
                                <select id="especie" name="especie" required>
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <option value="perro">Perro</option>
                                    <option value="gato">Gato</option>
                                    <option value="otro">Otros</option>
                                </select>
                            </div>

                            <div class="form-group full-width" id="grupoEspecieOtro" style="display: none;">
                                <label>Especifique la especie:</label>
                                <input type="text" id="especieOtro" name="especieOtro">
                            </div>

                            <div class="form-group">
                                <label>Edad (años):</label>
                                <input type="number" id="edad" name="edad" min="0">
                            </div>

                            <div class="form-group">
                                <label>Raza:</label>
                                <input type="text" id="raza" name="raza" required>
                            </div>

                            <div class="form-group">
                                <label>Sexo:</label>
                                <select id="sexo" name="sexo" required>
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <option value="macho">Macho</option>
                                    <option value="hembra">Hembra</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Nombre del Propietario:</label>
                                <input type="text" id="nombrePropietario" name="nombrePropietario" required>
                            </div>

                            <div class="form-group">
                                <label>Teléfono:</label>
                                <input type="tel" id="telefono" name="telefono" required>
                            </div>

                            <div class="form-group">
                                <label>Dirección:</label>
                                <input type="text" id="direccion" name="direccion" required>
                            </div>

                            <div class="form-group">
                                <label>DNI:</label>
                                <input type="text" id="dni" name="dni" required>
                            </div>

                            <div class="form-group">
                                <label>Peso (kg):</label>
                                <input type="number" id="peso" name="peso" step="0.01" required>
                            </div>

                            <div class="form-group">
                                <label>Temperatura (°C):</label>
                                <input type="number" id="temperatura" name="temperatura" step="0.1" required>
                            </div>

                            <div class="form-group full-width">
                                <label>Síntomas:</label>
                                <textarea id="sintomas" name="sintomas" rows="3"></textarea>
                            </div>

                            <div class="form-group full-width">
                                <label>Diagnóstico:</label>
                                <textarea id="diagnostico" name="diagnostico" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success btn-guardar">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- SECCIÓN CITAS -->
        <div id="section-citas" class="section">
            <h1 class="titulo">Citas</h1>
            <p>Aquí aparecerán las citas programadas.</p>
        </div>

        <!-- SECCIÓN MASCOTAS -->
        <div id="section-mascotas" class="section">
            <h1 class="titulo">Mascotas</h1>
            <p>Listado de mascotas registradas.</p>
        </div>

        <!-- SECCIÓN PROPIETARIOS -->
        <div id="section-propietarios" class="section">
            <h1 class="titulo">Propietarios</h1>
            <p>Datos de los dueños de las mascotas.</p>
        </div>

        <!-- SECCIÓN CONSULTAS -->
        <div id="section-consultas" class="section">
            <h1 class="titulo">Consultas</h1>
            <p>Registros de consultas realizadas.</p>
        </div>

        <!-- SECCIÓN VACUNAS -->
        <div id="section-vacunas" class="section">
            <h1 class="titulo">Vacunas</h1>
            <p>Información sobre las vacunas aplicadas.</p>
        </div>

        <!-- SECCIÓN TRATAMIENTOS -->
        <div id="section-tratamientos" class="section">
            <h1 class="titulo">Tratamientos</h1>
            <p>Detalles de tratamientos asignados.</p>
        </div>
    </div>
</div>

<script>
    const sidebar  = document.getElementById('sidebar');
    const toggle   = document.getElementById('toggleSidebar');
    const links    = document.querySelectorAll('.sidebar-menu a.nav-link');
    const sections = Array.from(document.querySelectorAll('#main-content .section'));

    const historiaListUrl  = "{{ route('historia_clinicas.list') }}";
    const historiaStoreUrl = "{{ route('historia_clinicas.store') }}";
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken        = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    // Colapsar sidebar
    toggle?.addEventListener('click', () => sidebar?.classList.toggle('collapsed'));

    function showSection(key) {
        sections.forEach(sec => {
            sec.style.display = 'none';
            sec.classList.remove('active');
        });

        const el = document.getElementById('section-' + key);
        if (el) {
            el.style.display = 'block';
            el.classList.add('active');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        showSection('inicio');
        cargarHistorias();
    });

    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            links.forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            const key = this.dataset.section;
            showSection(key);

            if (key === 'historias') {
                cargarHistorias();
            }
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
    const mensajeHistorias    = document.getElementById('historiaMensaje');
    const btnGuardar          = form?.querySelector('.btn-guardar');

    function generarNumeroHistoria() {
        const timestamp = Date.now().toString(36).toUpperCase();
        const aleatorio = Math.random().toString(36).substring(2, 6).toUpperCase();
        return `PET-${timestamp}-${aleatorio}`;
    }

    function prepararFormularioHistoria() {
        if (!form) {
            return;
        }

        form.reset();

        if (numeroHistoriaInput) {
            numeroHistoriaInput.value = generarNumeroHistoria();
        }

        if (especieOtroGroup && especieOtroInput) {
            especieOtroGroup.style.display = 'none';
            especieOtroInput.value = '';
            especieOtroInput.removeAttribute('required');
        }
    }

    function mostrarMensajeHistoria(texto, tipo = 'success') {
        if (!mensajeHistorias) {
            return;
        }

        mensajeHistorias.textContent = texto;
        mensajeHistorias.classList.remove('alert--success', 'alert--error');
        mensajeHistorias.classList.add(`alert--${tipo}`);
        mensajeHistorias.hidden = false;

        window.clearTimeout(mostrarMensajeHistoria.timeoutId);
        mostrarMensajeHistoria.timeoutId = window.setTimeout(() => {
            mensajeHistorias.hidden = true;
        }, 4000);
    }

    function crearFilaHistoria(historia) {
        const fila = document.createElement('tr');
        fila.dataset.historiaId = historia.id ?? '';

        const numeroCell = document.createElement('td');
        numeroCell.textContent = historia.numero_historia || '—';

        const mascotaCell = document.createElement('td');
        mascotaCell.textContent = historia.mascota || 'Sin nombre';

        const fechaCell = document.createElement('td');
        fechaCell.textContent = historia.fecha_apertura || '—';

        const accionesCell = document.createElement('td');
        accionesCell.classList.add('acciones');

        const btnEditar = document.createElement('button');
        btnEditar.className = 'btn btn-warning btn-sm btnEditar';
        btnEditar.title = 'Editar historia';
        btnEditar.innerHTML = '<i class="fas fa-edit"></i>';

        const btnEliminar = document.createElement('button');
        btnEliminar.className = 'btn btn-danger btn-sm btnEliminar';
        btnEliminar.title = 'Eliminar historia';
        btnEliminar.innerHTML = '<i class="fas fa-trash"></i>';

        accionesCell.append(btnEditar, btnEliminar);
        fila.append(numeroCell, mascotaCell, fechaCell, accionesCell);

        return fila;
    }

    function renderHistorias(lista = []) {
        if (!tablaHistorias) {
            return;
        }

        tablaHistorias.innerHTML = '';

        if (!Array.isArray(lista) || lista.length === 0) {
            const filaVacia = document.createElement('tr');
            filaVacia.classList.add('tabla-historias__empty');

            const celda = document.createElement('td');
            celda.colSpan = 4;
            celda.textContent = 'No hay historias clínicas registradas todavía.';

            filaVacia.appendChild(celda);
            tablaHistorias.appendChild(filaVacia);
            return;
        }

        const fragment = document.createDocumentFragment();
        lista.forEach(historia => {
            fragment.appendChild(crearFilaHistoria(historia));
        });

        tablaHistorias.appendChild(fragment);
    }

    async function cargarHistorias() {
        if (!historiaListUrl || !tablaHistorias) {
            return;
        }

        try {
            const response = await fetch(historiaListUrl, {
                headers: { 'Accept': 'application/json' },
            });

            if (!response.ok) {
                throw new Error('No se pudieron obtener las historias clínicas.');
            }

            const data = await response.json();
            renderHistorias(data.data ?? []);
        } catch (error) {
            console.error(error);
            mostrarMensajeHistoria('No se pudieron cargar las historias clínicas.', 'error');
            renderHistorias();
        }
    }

    if (btnNueva) {
        btnNueva.addEventListener('click', () => {
            titulo.textContent = 'Nueva Historia Clínica';
            prepararFormularioHistoria();
            if (modal) {
                modal.style.display = 'block';
            }
        });
    }

    if (spanClose) {
        spanClose.addEventListener('click', () => {
            if (modal) {
                modal.style.display = 'none';
            }
        });
    }

    window.addEventListener('click', event => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    if (especieSelect) {
        especieSelect.addEventListener('change', () => {
            if (!especieOtroGroup || !especieOtroInput) {
                return;
            }

            if (especieSelect.value === 'otro') {
                especieOtroGroup.style.display = 'block';
                especieOtroInput.setAttribute('required', 'required');
            } else {
                especieOtroGroup.style.display = 'none';
                especieOtroInput.removeAttribute('required');
                especieOtroInput.value = '';
            }
        });
    }

    if (form) {
        form.addEventListener('submit', async event => {
            event.preventDefault();

            if (!historiaStoreUrl) {
                return;
            }

            const formData = new FormData(form);

            if (!formData.get('numero_historia')) {
                formData.set('numero_historia', generarNumeroHistoria());
            }

            const payload = {};
            formData.forEach((value, key) => {
                if (typeof value === 'string') {
                    payload[key] = value.trim();
                } else {
                    payload[key] = value;
                }
            });

            if (!payload.especieOtro) {
                delete payload.especieOtro;
            }

            if (!payload.edad) {
                delete payload.edad;
            }

            if (!payload.sintomas) {
                delete payload.sintomas;
            }

            if (!payload.diagnostico) {
                delete payload.diagnostico;
            }

            if (btnGuardar) {
                btnGuardar.disabled = true;
            }

            try {
                const response = await fetch(historiaStoreUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
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

                if (responseData?.historia) {
                    if (tablaHistorias) {
                        const filaVacia = tablaHistorias.querySelector('.tabla-historias__empty');
                        if (filaVacia) {
                            filaVacia.remove();
                        }
                        tablaHistorias.prepend(crearFilaHistoria(responseData.historia));
                    }

                    mostrarMensajeHistoria('Historia clínica guardada correctamente.');
                    if (modal) {
                        modal.style.display = 'none';
                    }
                    prepararFormularioHistoria();
                } else {
                    mostrarMensajeHistoria('Historia clínica guardada correctamente.');
                    cargarHistorias();
                }
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
</script>
@endsection

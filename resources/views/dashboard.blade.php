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
            <div class="historias-header">
                <div>
                    <h1 class="titulo">Historias Clínicas</h1>
                    <p class="historias-subtitulo">Administra el historial médico de cada mascota, registra nuevos eventos y mantén el seguimiento actualizado.</p>
                </div>
                <button id="btnNuevaHistoria" class="btn btn-primary btn-animate">
                    <i class="fas fa-notes-medical"></i>
                    <span>Registrar historia</span>
                </button>
            </div>

            <div class="historias-overview">
                <div class="overview-card total">
                    <span class="overview-label">Total registradas</span>
                    <strong class="overview-value">{{ $totalHistorias }}</strong>
                    <span class="overview-help">Historias clínicas activas</span>
                </div>
                <div class="overview-card success">
                    <span class="overview-label">Mascotas con historia</span>
                    <strong class="overview-value">{{ $mascotasConHistoria }}</strong>
                    <span class="overview-help">En seguimiento</span>
                </div>
                <div class="overview-card warning">
                    <span class="overview-label">Mascotas sin historia</span>
                    <strong class="overview-value">{{ $mascotasSinHistoria }}</strong>
                    <span class="overview-help">Pendientes de evaluación</span>
                </div>
            </div>

            <div class="historias-toolbar">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" id="busquedaHistorias" placeholder="Buscar por mascota, especie o código">
                </div>
                <div class="toolbar-tags">
                    <span class="tag tag-soft">Actualiza diagnósticos y tratamientos en cada visita</span>
                </div>
            </div>

            <div id="historiasAlert" class="historias-alert" role="alert" style="display: none;"></div>

            <div id="listaHistorias" class="historias-lista" data-url="{{ route('historia_clinicas.list') }}">
                <div class="historias-loader">
                    <span class="loader"></span>
                    <p>Cargando historias clínicas...</p>
                </div>
            </div>

            <!-- MODAL NUEVA/EDITAR HISTORIA -->
            <div id="modalHistoria" class="modal">
                <div class="modal-content modal-historia">
                    <button type="button" class="close" aria-label="Cerrar">&times;</button>
                    <h2 id="modalTitulo">Nueva Historia Clínica</h2>
                    <form id="formHistoria" data-store="{{ route('historia_clinicas.store') }}" data-update="{{ url('historia_clinicas') }}">
                        <input type="hidden" id="historiaId">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="id_mascota">Mascota</label>
                                <select id="id_mascota" name="id_mascota" required>
                                    <option value="" disabled selected>Selecciona una mascota</option>
                                    @foreach ($mascotas as $mascota)
                                        <option value="{{ $mascota->id_mascota }}">{{ $mascota->nombre }} — {{ ucfirst($mascota->especie ?? 'N/D') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fecha_apertura">Fecha de apertura</label>
                                <input type="date" id="fecha_apertura" name="fecha_apertura" required>
                            </div>
                            <div class="form-group">
                                <label for="peso">Peso (kg)</label>
                                <input type="number" step="0.01" id="peso" name="peso" min="0" placeholder="Ej. 12.3">
                            </div>
                            <div class="form-group">
                                <label for="temperatura">Temperatura (°C)</label>
                                <input type="number" step="0.1" id="temperatura" name="temperatura" min="0" placeholder="Ej. 38.5">
                            </div>
                            <div class="form-group">
                                <label for="frecuencia_cardiaca">Frecuencia cardiaca</label>
                                <input type="text" id="frecuencia_cardiaca" name="frecuencia_cardiaca" placeholder="Ej. 90 lpm">
                            </div>
                            <div class="form-group">
                                <label for="sintomas">Síntomas</label>
                                <textarea id="sintomas" name="sintomas" rows="3" placeholder="Describe los signos clínicos"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="diagnostico">Diagnóstico</label>
                                <textarea id="diagnostico" name="diagnostico" rows="3" placeholder="Resultado de la evaluación"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="tratamientos">Tratamientos</label>
                                <textarea id="tratamientos" name="tratamientos" rows="3" placeholder="Medicamentos o terapias indicadas"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="vacunas">Vacunas</label>
                                <textarea id="vacunas" name="vacunas" rows="2" placeholder="Vacunas aplicadas"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="notas">Notas adicionales</label>
                                <textarea id="notas" name="notas" rows="2" placeholder="Recomendaciones futuras"></textarea>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn btn-light" id="btnCancelarModal">Cancelar</button>
                            <button type="submit" class="btn btn-success" id="btnGuardarHistoria">Guardar cambios</button>
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
    let historiasInitialized = false;

    // Colapsar sidebar
    toggle.addEventListener('click', () => sidebar.classList.toggle('collapsed'));

    // --- helpers para mostrar/ocultar secciones sin depender del CSS ---
    function showSection(key) {
        sections.forEach(sec => { sec.style.display = 'none'; sec.classList.remove('active'); });
        const el = document.getElementById('section-' + key);
        if (el) { el.style.display = 'block'; el.classList.add('active'); }
    }

    function initHistoriasClinicas() {
        const modal = document.getElementById('modalHistoria');
        const btnNueva = document.getElementById('btnNuevaHistoria');
        const closeBtn = document.querySelector('#modalHistoria .close');
        const cancelBtn = document.getElementById('btnCancelarModal');
        const form = document.getElementById('formHistoria');
        const titulo = document.getElementById('modalTitulo');
        const alertBox = document.getElementById('historiasAlert');
        const listaHistorias = document.getElementById('listaHistorias');
        const searchInput = document.getElementById('busquedaHistorias');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        if (!modal || !form || !listaHistorias) {
            return;
        }

        if (historiasInitialized) {
            if (typeof window.refreshHistoriasClinicas === 'function') {
                window.refreshHistoriasClinicas();
            }
            return;
        }

        let editId = null;
        const loaderTemplate = `
            <div class="historias-loader">
                <span class="loader"></span>
                <p>Cargando historias clínicas...</p>
            </div>`;

        const baseUrl = listaHistorias.dataset.url;

        const closeModal = () => {
            modal.classList.remove('visible');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 200);
        };

        const openModal = (title = 'Nueva Historia Clínica') => {
            titulo.textContent = title;
            modal.style.display = 'block';
            requestAnimationFrame(() => modal.classList.add('visible'));
        };

        const setDefaultDate = () => {
            const fechaInput = document.getElementById('fecha_apertura');
            if (fechaInput && !fechaInput.value) {
                const today = new Date().toISOString().split('T')[0];
                fechaInput.value = today;
            }
        };

        const resetForm = () => {
            form.reset();
            editId = null;
            document.getElementById('historiaId').value = '';
            const mascotaSelect = document.getElementById('id_mascota');
            if (mascotaSelect) {
                mascotaSelect.selectedIndex = 0;
            }
            document.getElementById('btnGuardarHistoria').textContent = 'Guardar cambios';
            setDefaultDate();
        };

        const showAlert = (type, message) => {
            if (!alertBox) return;
            alertBox.textContent = message;
            alertBox.className = `historias-alert ${type}`;
            alertBox.style.display = 'block';
            setTimeout(() => {
                alertBox.style.opacity = '1';
            }, 10);
        };

        const clearAlert = () => {
            if (!alertBox) return;
            alertBox.style.opacity = '0';
            setTimeout(() => {
                alertBox.style.display = 'none';
                alertBox.textContent = '';
                alertBox.className = 'historias-alert';
            }, 200);
        };

        const fetchHistorias = async (url = null) => {
            if (!baseUrl) return;
            const targetUrl = new URL(url || baseUrl, window.location.origin);
            if (searchInput && searchInput.value.trim()) {
                targetUrl.searchParams.set('search', searchInput.value.trim());
            }
            listaHistorias.innerHTML = loaderTemplate;
            try {
                const response = await fetch(targetUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) {
                    throw new Error('No se pudo cargar la información.');
                }
                const html = await response.text();
                listaHistorias.innerHTML = html;
            } catch (error) {
                listaHistorias.innerHTML = `
                    <div class="historias-empty">
                        <h3>Error al cargar</h3>
                        <p>${error.message}</p>
                    </div>`;
            }
        };

        const populateForm = (data) => {
            document.getElementById('historiaId').value = data.id_historia;
            document.getElementById('id_mascota').value = data.id_mascota;
            document.getElementById('fecha_apertura').value = data.fecha_apertura ?? '';
            document.getElementById('peso').value = data.peso ?? '';
            document.getElementById('temperatura').value = data.temperatura ?? '';
            document.getElementById('frecuencia_cardiaca').value = data.frecuencia_cardiaca ?? '';
            document.getElementById('sintomas').value = data.sintomas ?? '';
            document.getElementById('diagnostico').value = data.diagnostico ?? '';
            document.getElementById('tratamientos').value = data.tratamientos ?? '';
            document.getElementById('vacunas').value = data.vacunas ?? '';
            document.getElementById('notas').value = data.notas ?? '';
            document.getElementById('btnGuardarHistoria').textContent = 'Actualizar historia';
        };

        const serializeForm = () => {
            const formData = new FormData(form);
            return Object.fromEntries(formData.entries());
        };

        const handleSubmit = async (event) => {
            event.preventDefault();
            clearAlert();

            const payload = serializeForm();
            const isEdit = Boolean(editId);
            const url = isEdit ? `${form.dataset.update}/${editId}` : form.dataset.store;
            const method = isEdit ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => null);
                    const errorMessage = errorData?.message || 'No se pudo guardar la historia clínica.';
                    if (errorData?.errors) {
                        const firstError = Object.values(errorData.errors)[0][0];
                        showAlert('error', firstError);
                    } else {
                        showAlert('error', errorMessage);
                    }
                    return;
                }

                const result = await response.json();
                showAlert('success', result.message || 'Operación realizada correctamente.');
                closeModal();
                await fetchHistorias();
            } catch (error) {
                showAlert('error', error.message || 'Ocurrió un error inesperado.');
            }
        };

        const handleEdit = async (id) => {
            clearAlert();
            try {
                const response = await fetch(`${form.dataset.update}/${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('No se pudo obtener la historia clínica.');
                const data = await response.json();
                editId = id;
                populateForm(data);
                openModal('Editar Historia Clínica');
            } catch (error) {
                showAlert('error', error.message);
            }
        };

        const handleDelete = async (id) => {
            clearAlert();
            const confirmDelete = confirm('¿Deseas eliminar esta historia clínica? Esta acción no se puede deshacer.');
            if (!confirmDelete) {
                return;
            }

            try {
                const response = await fetch(`${form.dataset.update}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) throw new Error('No se pudo eliminar la historia clínica.');
                const result = await response.json();
                showAlert('success', result.message || 'Historia clínica eliminada.');
                await fetchHistorias();
            } catch (error) {
                showAlert('error', error.message);
            }
        };

        if (btnNueva) {
            btnNueva.addEventListener('click', () => {
                resetForm();
                openModal();
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }

        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        form.addEventListener('submit', handleSubmit);

        listaHistorias.addEventListener('click', (event) => {
            const paginationLink = event.target.closest('.page-btn:not(.disabled)[href]');
            if (paginationLink) {
                event.preventDefault();
                fetchHistorias(paginationLink.getAttribute('href'));
                return;
            }

            const editBtn = event.target.closest('.btnEditar');
            if (editBtn) {
                event.preventDefault();
                handleEdit(editBtn.dataset.id);
                return;
            }

            const deleteBtn = event.target.closest('.btnEliminar');
            if (deleteBtn) {
                event.preventDefault();
                handleDelete(deleteBtn.dataset.id);
            }
        });

        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => fetchHistorias(), 350);
            });
        }

        resetForm();
        fetchHistorias();
        historiasInitialized = true;
        window.refreshHistoriasClinicas = fetchHistorias;
    }

    document.addEventListener('DOMContentLoaded', () => {
        showSection('inicio');
        initHistoriasClinicas();
    });

    links.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            links.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            const key = this.dataset.section;
            showSection(key);
            if (key === 'historias') {
                if (historiasInitialized && typeof window.refreshHistoriasClinicas === 'function') {
                    window.refreshHistoriasClinicas();
                } else {
                    initHistoriasClinicas();
                }
            }
        });
    });
</script>
@endsection

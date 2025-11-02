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
            <h1 class="titulo">Historias Clínicas</h1>

            <div class="historias-wrapper">
                <div id="alertaHistoria" class="historia-alert is-hidden" role="status" aria-live="polite"></div>

                <div class="historias-header">
                    <div class="historias-texto">
                        <h2 class="historias-titulo">Registro rápido de pacientes</h2>
                        <p class="historias-descripcion">Conserva la información esencial de cada visita con un solo formulario.</p>
                    </div>
                    <button id="btnNuevaHistoria" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Nueva Historia Clínica
                    </button>
                </div>

                <div class="historia-card">
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
                                <tr data-sintomas="Vacunación anual" data-diagnostico="Control preventivo">
                                    <td>HC-2025-001</td>
                                    <td>
                                        <div class="historia-mascota">
                                            <span class="mascota-nombre">Firulais</span>
                                            <span class="historia-meta">Peso: 5.4 kg · Temp: 38.5 °C</span>
                                        </div>
                                    </td>
                                    <td>24/10/2025</td>
                                    <td class="tabla-acciones">
                                        <button class="btn btn-icon btn-warning btn-sm btnEditar" type="button" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-icon btn-danger btn-sm btnEliminar" type="button" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p id="emptyHistorias" class="empty-state hidden">
                        No hay historias clínicas registradas todavía. ¡Añade la primera para comenzar!
                    </p>
                </div>
            </div>

            <div id="modalHistoria" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitulo">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 id="modalTitulo">Nueva Historia Clínica</h2>
                        <button type="button" class="close-modal" aria-label="Cerrar formulario">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                    <form id="formHistoria" autocomplete="off">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="mascota">Mascota</label>
                                <input type="text" id="mascota" placeholder="Nombre de la mascota" required>
                            </div>

                            <div class="form-group">
                                <label for="peso">Peso (kg)</label>
                                <input type="number" id="peso" min="0" step="0.1" placeholder="Ej. 5.4" required>
                            </div>

                            <div class="form-group">
                                <label for="temperatura">Temperatura (°C)</label>
                                <input type="number" id="temperatura" step="0.1" placeholder="Ej. 38.5" required>
                            </div>

                            <div class="form-group form-group-full">
                                <label for="sintomas">Síntomas</label>
                                <textarea id="sintomas" rows="3" placeholder="Describe los síntomas observados"></textarea>
                            </div>

                            <div class="form-group form-group-full">
                                <label for="diagnostico">Diagnóstico</label>
                                <textarea id="diagnostico" rows="3" placeholder="Registra el diagnóstico inicial"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" id="cancelarHistoria">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
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

    // Colapsar sidebar
    toggle.addEventListener('click', () => sidebar.classList.toggle('collapsed'));

    // --- helpers para mostrar/ocultar secciones sin depender del CSS ---
    function showSection(key) {
        // oculta todas
        sections.forEach(sec => { sec.style.display = 'none'; sec.classList.remove('active'); });
        // muestra solo la pedida
        const el = document.getElementById('section-' + key);
        if (el) { el.style.display = 'block'; el.classList.add('active'); }
    }

    // Estado inicial: solo Inicio
    document.addEventListener('DOMContentLoaded', () => {
        showSection('inicio');
        renderEmptyState();
    });

    // Navegación por sidebar
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            // activo visual
            links.forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            // mostrar sección
            const key = this.dataset.section;
            showSection(key);
        });
    });

    // ===== MODAL HISTORIAS CLÍNICAS =====
    const modal            = document.getElementById('modalHistoria');
    const btnNueva         = document.getElementById('btnNuevaHistoria');
    const closeButtons     = document.querySelectorAll('#modalHistoria .close-modal');
    const btnCancelar      = document.getElementById('cancelarHistoria');
    const form             = document.getElementById('formHistoria');
    const titulo           = document.getElementById('modalTitulo');
    const tablaHistorias   = document.getElementById('tablaHistorias');
    const emptyState       = document.getElementById('emptyHistorias');
    const alertaHistoria   = document.getElementById('alertaHistoria');

    function obtenerCorrelativoInicial() {
        if (!tablaHistorias) return 0;
        const valores = Array.from(tablaHistorias.querySelectorAll('tr td:first-child'))
            .map(celda => celda.textContent.trim().split('-').pop())
            .map(numero => parseInt(numero, 10))
            .filter(numero => !Number.isNaN(numero));

        return valores.length ? Math.max(...valores) : 0;
    }

    let correlativoHistoria = obtenerCorrelativoInicial();

    function renderEmptyState() {
        if (!emptyState || !tablaHistorias) return;
        const hayFilas = tablaHistorias.querySelectorAll('tr').length > 0;
        emptyState.classList.toggle('hidden', hayFilas);
    }

    let alertaTimeout;

    function showAlert(message, type = 'success') {
        if (!alertaHistoria) return;
        alertaHistoria.textContent = message;
        alertaHistoria.classList.remove('is-hidden', 'is-error', 'is-success');
        alertaHistoria.classList.add(type === 'error' ? 'is-error' : 'is-success');

        if (alertaTimeout) clearTimeout(alertaTimeout);
        alertaTimeout = setTimeout(() => {
            alertaHistoria.classList.add('is-hidden');
        }, 3200);
    }

    function generarNumeroHistoria() {
        const year = new Date().getFullYear();
        correlativoHistoria += 1;
        return `HC-${year}-${String(correlativoHistoria).padStart(3, '0')}`;
    }

    function formatoDecimal(valor) {
        if (valor === '' || Number.isNaN(Number(valor))) {
            return '—';
        }
        return Number(valor).toFixed(1);
    }

    function crearFilaHistoria(data) {
        if (!tablaHistorias) return;
        const fila = document.createElement('tr');
        fila.dataset.sintomas = data.sintomas;
        fila.dataset.diagnostico = data.diagnostico;
        const pesoTexto = data.peso === '—' ? '—' : `${data.peso} kg`;
        const temperaturaTexto = data.temperatura === '—' ? '—' : `${data.temperatura} °C`;
        fila.innerHTML = `
            <td>${data.numero}</td>
            <td>
                <div class="historia-mascota">
                    <span class="mascota-nombre">${data.mascota}</span>
                    <span class="historia-meta">Peso: ${pesoTexto} · Temp: ${temperaturaTexto}</span>
                </div>
            </td>
            <td>${data.fecha}</td>
            <td class="tabla-acciones">
                <button class="btn btn-icon btn-warning btn-sm btnEditar" type="button" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-icon btn-danger btn-sm btnEliminar" type="button" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        fila.classList.add('is-new');
        tablaHistorias.prepend(fila);

        setTimeout(() => {
            fila.classList.remove('is-new');
        }, 1600);

        renderEmptyState();
    }

    function openModal() {
        if (!modal || !form) return;
        form.reset();
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('show'));

        setTimeout(() => {
            document.getElementById('mascota')?.focus();
        }, 150);
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    }

    if (btnNueva) {
        btnNueva.addEventListener('click', () => {
            titulo.textContent = 'Nueva Historia Clínica';
            openModal();
        });
    }

    closeButtons.forEach(button => button.addEventListener('click', closeModal));
    if (btnCancelar) btnCancelar.addEventListener('click', closeModal);
    window.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && modal?.classList.contains('show')) closeModal(); });

    if (form) {
        form.addEventListener('submit', e => {
            e.preventDefault();

            const mascota      = document.getElementById('mascota').value.trim();
            const peso          = document.getElementById('peso').value.trim();
            const temperatura   = document.getElementById('temperatura').value.trim();
            const sintomas      = document.getElementById('sintomas').value.trim();
            const diagnostico   = document.getElementById('diagnostico').value.trim();

            if (!mascota) {
                showAlert('Ingresa el nombre de la mascota para continuar.', 'error');
                return;
            }

            const historia = {
                numero: generarNumeroHistoria(),
                mascota,
                peso: formatoDecimal(peso),
                temperatura: formatoDecimal(temperatura),
                sintomas: sintomas || 'Sin síntomas registrados',
                diagnostico: diagnostico || 'Sin diagnóstico registrado',
                fecha: new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })
            };

            crearFilaHistoria(historia);
            form.reset();
            closeModal();
            showAlert('Historia clínica guardada correctamente.');
        });
    }

</script>
@endsection

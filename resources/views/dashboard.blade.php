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
                            <tr>
                                <td>HC-2025-001</td>
                                <td>Firulais</td>
                                <td>24/10/2025</td>
                                <td class="acciones">
                                    <button class="btn btn-warning btn-sm btnEditar"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm btnEliminar"><i class="fas fa-trash"></i></button>
                                </td>
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
                                <input type="text" id="idHistoria" readonly>
                            </div>

                            <div class="form-group">
                                <label>Nombre de la Mascota:</label>
                                <input type="text" id="nombreMascota" required>
                            </div>

                            <div class="form-group">
                                <label>Especie:</label>
                                <select id="especie" required>
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <option value="perro">Perro</option>
                                    <option value="gato">Gato</option>
                                    <option value="otro">Otros</option>
                                </select>
                            </div>

                            <div class="form-group full-width" id="grupoEspecieOtro" style="display: none;">
                                <label>Especifique la especie:</label>
                                <input type="text" id="especieOtro">
                            </div>

                            <div class="form-group">
                                <label>Edad (años):</label>
                                <input type="number" id="edad" min="0" required>
                            </div>

                            <div class="form-group">
                                <label>Raza:</label>
                                <input type="text" id="raza" required>
                            </div>

                            <div class="form-group">
                                <label>Sexo:</label>
                                <select id="sexo" required>
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <option value="macho">Macho</option>
                                    <option value="hembra">Hembra</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Nombre del Propietario:</label>
                                <input type="text" id="nombrePropietario" required>
                            </div>

                            <div class="form-group">
                                <label>Teléfono:</label>
                                <input type="tel" id="telefono" required>
                            </div>

                            <div class="form-group">
                                <label>Dirección:</label>
                                <input type="text" id="direccion" required>
                            </div>

                            <div class="form-group">
                                <label>DNI:</label>
                                <input type="text" id="dni" required>
                            </div>

                            <div class="form-group">
                                <label>Peso (kg):</label>
                                <input type="number" id="peso" required>
                            </div>

                            <div class="form-group">
                                <label>Temperatura (°C):</label>
                                <input type="number" id="temperatura" required>
                            </div>

                            <div class="form-group full-width">
                                <label>Síntomas:</label>
                                <textarea id="sintomas" rows="3"></textarea>
                            </div>

                            <div class="form-group full-width">
                                <label>Diagnóstico:</label>
                                <textarea id="diagnostico" rows="3"></textarea>
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
    const modal             = document.getElementById('modalHistoria');
    const btnNueva          = document.getElementById('btnNuevaHistoria');
    const spanClose         = document.querySelector('#modalHistoria .close');
    const form              = document.getElementById('formHistoria');
    const titulo            = document.getElementById('modalTitulo');
    const historiaIdInput   = document.getElementById('idHistoria');
    const especieSelect     = document.getElementById('especie');
    const especieOtroGroup  = document.getElementById('grupoEspecieOtro');
    const especieOtroInput  = document.getElementById('especieOtro');
    const tablaHistorias    = document.getElementById('tablaHistorias');
    const mensajeHistorias  = document.getElementById('historiaMensaje');

    function generarIdHistoria() {
        const timestamp = Date.now().toString(36).toUpperCase();
        const aleatorio = Math.random().toString(36).substring(2, 6).toUpperCase();
        return `PET-${timestamp}-${aleatorio}`;
    }

    function prepararFormularioHistoria() {
        if (!form) return;
        form.reset();
        if (historiaIdInput) {
            historiaIdInput.value = generarIdHistoria();
        }
        if (especieOtroGroup && especieOtroInput) {
            especieOtroGroup.style.display = 'none';
            especieOtroInput.removeAttribute('required');
        }
    }

    function obtenerFechaActualFormateada() {
        const ahora = new Date();
        const opciones = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return ahora.toLocaleDateString('es-ES', opciones);
    }

    function crearFilaHistoria({ id, mascota, fecha }) {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${id}</td>
            <td>${mascota}</td>
            <td>${fecha}</td>
            <td class="acciones">
                <button class="btn btn-warning btn-sm btnEditar" title="Editar historia"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger btn-sm btnEliminar" title="Eliminar historia"><i class="fas fa-trash"></i></button>
            </td>
        `;
        return fila;
    }

    function mostrarMensajeHistoria(texto, tipo = 'success') {
        if (!mensajeHistorias) return;
        mensajeHistorias.textContent = texto;
        mensajeHistorias.classList.remove('alert--success', 'alert--error');
        mensajeHistorias.classList.add(`alert--${tipo}`);
        mensajeHistorias.hidden = false;

        window.clearTimeout(mostrarMensajeHistoria.timeoutId);
        mostrarMensajeHistoria.timeoutId = window.setTimeout(() => {
            mensajeHistorias.hidden = true;
        }, 4000);
    }

    if (btnNueva) {
        btnNueva.addEventListener('click', () => {
            titulo.textContent = "Nueva Historia Clínica";
            prepararFormularioHistoria();
            modal.style.display = 'block';
        });
    }
    if (spanClose) spanClose.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });

    if (especieSelect) {
        especieSelect.addEventListener('change', () => {
            if (!especieOtroGroup || !especieOtroInput) return;
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
        form.addEventListener('submit', e => {
            e.preventDefault();

            const datosHistoria = {
                id: historiaIdInput ? (historiaIdInput.value || generarIdHistoria()) : generarIdHistoria(),
                mascota: document.getElementById('nombreMascota')?.value.trim() || 'Sin nombre',
                fecha: obtenerFechaActualFormateada(),
            };

            if (tablaHistorias) {
                const nuevaFila = crearFilaHistoria(datosHistoria);
                tablaHistorias.prepend(nuevaFila);
            }

            mostrarMensajeHistoria('Historia clínica guardada correctamente.');
            modal.style.display = 'none';
            prepararFormularioHistoria();
        });
    }
</script>
@endsection

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

            <!-- BOTÓN NUEVA HISTORIA -->
            <button id="btnNuevaHistoria" class="btn btn-primary" style="margin-bottom:15px;">
                <i class="fas fa-plus"></i> Nueva Historia Clínica
            </button>

            <!-- TABLA DE HISTORIAS -->
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
                        <td>
                            <button class="btn btn-warning btn-sm btnEditar"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm btnEliminar"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- MODAL NUEVA/EDITAR HISTORIA -->
            <div id="modalHistoria" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2 id="modalTitulo">Nueva Historia Clínica</h2>
                    <form id="formHistoria">
                        <div class="form-group">
                            <label>Mascota:</label>
                            <input type="text" id="mascota" required>
                        </div>

                        <div class="form-group">
                            <label>Peso (kg):</label>
                            <input type="number" id="peso" required>
                        </div>

                        <div class="form-group">
                            <label>Temperatura (°C):</label>
                            <input type="number" id="temperatura" required>
                        </div>

                        <div class="form-group">
                            <label>Síntomas:</label>
                            <textarea id="sintomas" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Diagnóstico:</label>
                            <textarea id="diagnostico" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Guardar</button>
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
    const modal     = document.getElementById('modalHistoria');
    const btnNueva  = document.getElementById('btnNuevaHistoria');
    const spanClose = document.querySelector('#modalHistoria .close');
    const form      = document.getElementById('formHistoria');
    const titulo    = document.getElementById('modalTitulo');

    if (btnNueva) {
        btnNueva.addEventListener('click', () => {
            titulo.textContent = "Nueva Historia Clínica";
            form.reset();
            modal.style.display = 'block';
        });
    }
    if (spanClose) spanClose.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });

    if (form) {
        form.addEventListener('submit', e => {
            e.preventDefault();
            alert("Historia guardada correctamente (aquí se conectará con backend).");
            modal.style.display = 'none';
        });
    }
</script>
@endsection

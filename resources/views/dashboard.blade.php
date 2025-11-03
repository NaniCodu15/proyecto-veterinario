@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- SIDEBAR FIJO -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="sidebar-logo-wrap">
                    <img src="{{ asset('images/logoVet.png') }}" alt="Logo" class="sidebar-logo">
                </div>
                <div class="sidebar-brand-text">
                    <span class="sidebar-brand-label">Hospital</span>
                    <h1 class="sidebar-brand-name">Hospital Veterinario</h1>
                    <span class="sidebar-brand-tagline">Cuidado integral para cada paciente</span>
                </div>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="#" class="nav-link active" data-section="inicio">
                    <span class="nav-icon"><i class="fas fa-home"></i></span>
                    <span class="nav-text">Inicio</span>
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="citas">
                    <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span class="nav-text">Citas</span>
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="historias">
                    <span class="nav-icon"><i class="fas fa-notes-medical"></i></span>
                    <span class="nav-text">Historias Clínicas</span>
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="mascotas">
                    <span class="nav-icon"><i class="fas fa-dog"></i></span>
                    <span class="nav-text">Mascotas</span>
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="propietarios">
                    <span class="nav-icon"><i class="fas fa-user"></i></span>
                    <span class="nav-text">Propietarios</span>
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="consultas">
                    <span class="nav-icon"><i class="fas fa-stethoscope"></i></span>
                    <span class="nav-text">Consultas</span>
                </a>
            </li>
        </ul>

        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="btn-logout">
                <span class="nav-icon nav-icon--logout"><i class="fas fa-sign-out-alt"></i></span>
                <span class="nav-text">Cerrar sesión</span>
            </button>
        </form>
    </div>

    <!-- CONTENIDO PRINCIPAL (CAMBIA SEGÚN OPCIÓN) -->
    <div id="main-content" class="content">
        <!-- SECCIÓN INICIO -->
        <div id="section-inicio" class="section active">
            <div class="welcome-card">
                <div class="welcome-card__body">
                    <span class="welcome-card__badge">Hospital Veterinario</span>
                    <h1 class="welcome-card__title">Un panel cálido para la gestión integral veterinaria</h1>
                    <p class="welcome-card__subtitle">
                        Organiza pacientes, citas y seguimientos en un entorno profesional, limpio y pensado para transmitir confianza al equipo y a las familias.
                    </p>
                    <div class="welcome-card__actions">
                        <a href="#" class="btn btn-primary btn-ir-historias" data-section="historias">
                            <i class="fas fa-notes-medical"></i>
                            Gestionar historias clínicas
                        </a>
                        <button type="button" class="btn btn-outline" id="btnAccesoRapido">
                            <i class="fas fa-plus-circle"></i>
                            Registrar nueva historia
                        </button>
                    </div>
                    <div class="welcome-card__meta">
                        <span><i class="fas fa-heartbeat"></i> Seguimiento preventivo y cálido</span>
                        <span><i class="fas fa-headset"></i> Equipo coordinado y disponible</span>
                    </div>
                </div>
                <div class="welcome-card__illustration">
                    <div class="welcome-card__halo"></div>
                    <img src="{{ asset('images/logoVet.png') }}" alt="Hospital veterinario" class="welcome-card__image">
                </div>
            </div>

            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar en el panel...">
            </div>

            <div class="dashboard-cards">
                <div class="stat-card stat-card--primary">
                    <div class="stat-card__header">
                        <span>Pacientes activos</span>
                        <span class="stat-card__trend stat-card__trend--up"><i class="fas fa-arrow-up"></i> 8%</span>
                    </div>
                    <div class="stat-card__body">
                        <div class="stat-card__icon"><i class="fas fa-paw"></i></div>
                        <div class="stat-info">
                            <h2>{{ $totalMascotas }}</h2>
                            <p>Mascotas registradas</p>
                        </div>
                    </div>
                    <div class="stat-card__footer">
                        <div class="stat-card__sparkline"><span style="width: 72%;"></span></div>
                        <span>Promedio mensual</span>
                    </div>
                </div>

                <div class="stat-card stat-card--sky">
                    <div class="stat-card__header">
                        <span>Propietarios fidelizados</span>
                        <span class="stat-card__trend stat-card__trend--calm"><i class="fas fa-user-check"></i> +5 nuevos</span>
                    </div>
                    <div class="stat-card__body">
                        <div class="stat-card__icon"><i class="fas fa-users"></i></div>
                        <div class="stat-info">
                            <h2>{{ $totalPropietarios }}</h2>
                            <p>Propietarios activos</p>
                        </div>
                    </div>
                    <div class="stat-card__footer">
                        <div class="stat-card__sparkline"><span style="width: 65%;"></span></div>
                        <span>Seguimiento comunitario</span>
                    </div>
                </div>

                <div class="stat-card stat-card--mint">
                    <div class="stat-card__header">
                        <span>Consultas resueltas</span>
                        <span class="stat-card__trend stat-card__trend--up"><i class="fas fa-check-circle"></i> +3 hoy</span>
                    </div>
                    <div class="stat-card__body">
                        <div class="stat-card__icon"><i class="fas fa-stethoscope"></i></div>
                        <div class="stat-info">
                            <h2>{{ $totalConsultas ?? 0 }}</h2>
                            <p>Consultas históricas</p>
                        </div>
                    </div>
                    <div class="stat-card__footer">
                        <div class="stat-card__sparkline"><span style="width: 58%;"></span></div>
                        <span>Casos exitosos</span>
                    </div>
                </div>

                <div class="stat-card stat-card--accent">
                    <div class="stat-card__header">
                        <span>Citas del día</span>
                        <span class="stat-card__trend stat-card__trend--alert"><i class="fas fa-bell"></i> 2 pendientes</span>
                    </div>
                    <div class="stat-card__body">
                        <div class="stat-card__icon"><i class="fas fa-calendar-day"></i></div>
                        <div class="stat-info">
                            <h2>8</h2>
                            <p>Reservas programadas</p>
                        </div>
                    </div>
                    <div class="stat-card__footer">
                        <div class="stat-card__sparkline"><span style="width: 48%;"></span></div>
                        <span>Actualizado hace 2 h</span>
                    </div>
                </div>
            </div>

            <div class="insight-grid">
                <section class="panel panel--schedule">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">Citas del día</h2>
                            <p class="panel__subtitle">Coordina la agenda del equipo de atención primaria.</p>
                        </div>
                        <span class="panel__tag"><i class="fas fa-clock"></i> Hoy</span>
                    </div>
                    <ul class="schedule-list">
                        <li>
                            <div class="schedule-list__time">08:30</div>
                            <div class="schedule-list__info">
                                <p class="schedule-list__pet">Luna · Control preventivo</p>
                                <span class="schedule-list__owner">Propietario: Ana Pérez</span>
                            </div>
                            <span class="schedule-list__status schedule-list__status--check"><i class="fas fa-check"></i>Confirmada</span>
                        </li>
                        <li>
                            <div class="schedule-list__time">10:15</div>
                            <div class="schedule-list__info">
                                <p class="schedule-list__pet">Max · Vacunación anual</p>
                                <span class="schedule-list__owner">Propietario: Carlos Gómez</span>
                            </div>
                            <span class="schedule-list__status schedule-list__status--pending"><i class="fas fa-hourglass-half"></i>En sala</span>
                        </li>
                        <li>
                            <div class="schedule-list__time">12:40</div>
                            <div class="schedule-list__info">
                                <p class="schedule-list__pet">Nala · Revisión postoperatoria</p>
                                <span class="schedule-list__owner">Propietario: Laura Rivas</span>
                            </div>
                            <span class="schedule-list__status schedule-list__status--alert"><i class="fas fa-triangle-exclamation"></i>Prioritario</span>
                        </li>
                    </ul>
                </section>

                <section class="panel panel--alerts">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">Alertas y seguimientos</h2>
                            <p class="panel__subtitle">Mantén al equipo informado sobre pendientes críticos.</p>
                        </div>
                        <span class="panel__tag"><i class="fas fa-heart"></i> Cuidados</span>
                    </div>
                    <div class="alert-summary">
                        <i class="fas fa-heartbeat"></i>
                        3 pacientes requieren monitoreo cercano
                    </div>
                    <ul class="alert-list">
                        <li>
                            <div class="alert-list__info">
                                <p class="alert-list__title">Control post cirugía</p>
                                <p class="alert-list__note">Kira · Revisar suturas y analgésicos</p>
                            </div>
                            <span class="alert-list__pill">Antes de 14:00</span>
                        </li>
                        <li>
                            <div class="alert-list__info">
                                <p class="alert-list__title">Recordatorio de laboratorio</p>
                                <p class="alert-list__note">Toby · Resultados hemograma</p>
                            </div>
                            <span class="alert-list__pill">Recibir hoy</span>
                        </li>
                        <li>
                            <div class="alert-list__info">
                                <p class="alert-list__title">Vacuna pendiente</p>
                                <p class="alert-list__note">Luna · Refuerzo anual</p>
                            </div>
                            <span class="alert-list__pill">Programar cita</span>
                        </li>
                    </ul>
                </section>

                <section class="panel panel--care panel--wide">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">Indicadores de bienestar</h2>
                            <p class="panel__subtitle">Comparte con tu equipo una visión clara del estado de cada paciente.</p>
                        </div>
                        <span class="panel__tag"><i class="fas fa-leaf"></i> Bienestar</span>
                    </div>
                    <div class="care-progress">
                        <div class="care-progress__item">
                            <div class="care-progress__label">
                                Adherencia a tratamientos
                                <span>92%</span>
                            </div>
                            <div class="care-progress__track">
                                <span class="care-progress__bar" style="width: 92%;"></span>
                            </div>
                        </div>
                        <div class="care-progress__item">
                            <div class="care-progress__label">
                                Pacientes en seguimiento activo
                                <span>18</span>
                            </div>
                            <div class="care-progress__track">
                                <span class="care-progress__bar" style="width: 70%;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="care-notes">
                        <div class="care-note"><i class="fas fa-paw"></i> Nuevos planes preventivos listos</div>
                        <div class="care-note"><i class="fas fa-sun"></i> Agenda balanceada mañana y tarde</div>
                        <div class="care-note"><i class="fas fa-comment-dots"></i> Actualiza notas de bienestar semanal</div>
                    </div>
                </section>
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
                        <div class="form-section">
                            <h3 class="form-section__title"><span>🐶</span>Datos de la mascota</h3>
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
                                    <label>Peso (kg):</label>
                                    <input type="number" id="peso" name="peso" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="form-section__title"><span>👤</span>Datos del propietario</h3>
                            <div class="form-grid">
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
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="form-section__title"><span>🩺</span>Consulta</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Temperatura (°C):</label>
                                    <input type="number" id="temperatura" name="temperatura" step="0.1">
                                </div>

                                <div class="form-group full-width">
                                    <label>Síntomas:</label>
                                    <textarea id="sintomas" name="sintomas" rows="3"></textarea>
                                </div>

                                <div class="form-group full-width">
                                    <label>Diagnóstico:</label>
                                    <textarea id="diagnostico" name="diagnostico" rows="3"></textarea>
                                </div>

                                <div class="form-group full-width">
                                    <label>Vacunas:</label>
                                    <textarea id="vacunas" name="vacunas" rows="3"></textarea>
                                </div>

                                <div class="form-group full-width">
                                    <label>Tratamientos:</label>
                                    <textarea id="tratamientos" name="tratamientos" rows="3"></textarea>
                                </div>
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

    </div>
</div>

<div id="confirmModal" class="confirm-modal" role="alertdialog" aria-modal="true" aria-labelledby="confirmModalMessage" hidden>
    <div class="confirm-modal__dialog">
        <p id="confirmModalMessage" class="confirm-modal__message">¿Desea eliminar esta historia clínica?</p>
        <div class="confirm-modal__actions">
            <button type="button" class="btn btn-confirm-cancel" data-confirm="cancel">Cancelar</button>
            <button type="button" class="btn btn-confirm-accept" data-confirm="accept">Aceptar</button>
        </div>
    </div>
</div>

<script>
    const links    = document.querySelectorAll('.sidebar-menu a.nav-link');
    const sections = Array.from(document.querySelectorAll('#main-content .section'));

    const historiaListUrl  = "{{ route('historia_clinicas.list') }}";
    const historiaStoreUrl = "{{ route('historia_clinicas.store') }}";
    const historiaBaseUrl  = "{{ url('historia_clinicas') }}";
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken        = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    let historiaEditandoId = null;
    let historiaPorEliminarId = null;
    let proximoNumeroHistoria = 'HC-00001';

    function showSection(key) {
        sections.forEach(sec => {
            const activa = sec.id === `section-${key}`;
            sec.style.display = activa ? 'block' : 'none';
            sec.classList.toggle('active', activa);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        showSection('inicio');
        cargarHistorias();
    });

    links.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();

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
    const btnAccesoRapido     = document.getElementById('btnAccesoRapido');
    const btnIrHistorias      = document.querySelector('.btn-ir-historias');
    const confirmModal        = document.getElementById('confirmModal');
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
        temperatura: document.getElementById('temperatura'),
        sintomas: document.getElementById('sintomas'),
        diagnostico: document.getElementById('diagnostico'),
        vacunas: document.getElementById('vacunas'),
        tratamientos: document.getElementById('tratamientos'),
    };

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
            numeroHistoriaInput.value = proximoNumeroHistoria;
            numeroHistoriaInput.placeholder = 'Se generará automáticamente';
        }
    }

    function abrirModal() {
        if (!modal) {
            return;
        }

        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }

    function cerrarModal() {
        if (!modal) {
            return;
        }

        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    function abrirConfirmacionPara(id) {
        if (!id) {
            return;
        }

        if (!confirmModal) {
            if (window.confirm('¿Desea eliminar esta historia clínica?')) {
                eliminarHistoria(id);
            }
            return;
        }

        historiaPorEliminarId = id;
        confirmModal.hidden = false;
        confirmModal.classList.add('is-visible');
        window.setTimeout(() => {
            confirmAcceptButton?.focus();
        }, 10);
    }

    function cerrarConfirmacion() {
        if (!confirmModal) {
            historiaPorEliminarId = null;
            return;
        }

        confirmModal.classList.remove('is-visible');
        confirmModal.hidden = true;
        historiaPorEliminarId = null;
    }

    function reiniciarFormulario() {
        historiaEditandoId = null;
        prepararFormularioBase();

        if (titulo) {
            titulo.textContent = 'Nueva Historia Clínica';
        }

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

        proximoNumeroHistoria = `HC-${String(maximo + 1).padStart(5, '0')}`;

        if (!historiaEditandoId && numeroHistoriaInput && modal && modal.style.display === 'block') {
            numeroHistoriaInput.value = proximoNumeroHistoria;
        }
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
            actualizarProximoNumero([]);
            return;
        }

        const fragment = document.createDocumentFragment();
        lista.forEach(historia => {
            fragment.appendChild(crearFilaHistoria(historia));
        });

        tablaHistorias.appendChild(fragment);
        actualizarProximoNumero(lista);
    }

    async function cargarHistorias() {
        if (!historiaListUrl || !tablaHistorias) {
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
            renderHistorias();
        }
    }

    if (btnNueva) {
        btnNueva.addEventListener('click', () => {
            abrirModalParaCrear();
        });
    }

    if (btnAccesoRapido) {
        btnAccesoRapido.addEventListener('click', () => {
            abrirModalParaCrear();
        });
    }

    if (btnIrHistorias) {
        btnIrHistorias.addEventListener('click', event => {
            event.preventDefault();

            const linkHistorias = document.querySelector('.sidebar-menu a[data-section="historias"]');
            if (linkHistorias) {
                links.forEach(l => l.classList.remove('active'));
                linkHistorias.classList.add('active');
            }

            showSection('historias');
            cargarHistorias();
        });
    }

    if (spanClose) {
        spanClose.addEventListener('click', () => {
            cerrarModal();
            reiniciarFormulario();
        });
    }

    window.addEventListener('click', event => {
        if (event.target === modal) {
            cerrarModal();
            reiniciarFormulario();
        }
    });

    if (especieSelect) {
        especieSelect.addEventListener('change', () => {
            if (especieSelect.value === 'otro') {
                mostrarEspecieOtro();
            } else {
                ocultarEspecieOtro();
            }
        });
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
                throw new Error('No se pudo eliminar la historia clínica.');
            }

            mostrarMensajeHistoria('Historia clínica eliminada correctamente.');
            await cargarHistorias();
        } catch (error) {
            console.error(error);
            mostrarMensajeHistoria(error.message || 'No se pudo eliminar la historia clínica.', 'error');
        }
    }

    if (confirmCancelButton) {
        confirmCancelButton.addEventListener('click', () => {
            cerrarConfirmacion();
        });
    }

    if (confirmAcceptButton) {
        confirmAcceptButton.addEventListener('click', async () => {
            if (!historiaPorEliminarId) {
                cerrarConfirmacion();
                return;
            }

            const id = historiaPorEliminarId;
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
        if (event.key === 'Escape' && confirmModal?.classList.contains('is-visible')) {
            cerrarConfirmacion();
        }
    });

    if (tablaHistorias) {
        tablaHistorias.addEventListener('click', event => {
            const botonEditar = event.target.closest('.btnEditar');
            const botonEliminar = event.target.closest('.btnEliminar');

            if (botonEditar) {
                const fila = botonEditar.closest('tr');
                const id = fila?.dataset.historiaId;
                if (id) {
                    cargarHistoriaParaEditar(id);
                }
            }

            if (botonEliminar) {
                const fila = botonEliminar.closest('tr');
                const id = fila?.dataset.historiaId;
                if (id) {
                    abrirConfirmacionPara(id);
                }
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

            ['especieOtro', 'edad', 'peso', 'temperatura', 'sintomas', 'diagnostico', 'vacunas', 'tratamientos'].forEach(campo => {
                if (payload[campo] === '' || payload[campo] === undefined) {
                    delete payload[campo];
                }
            });

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
                await cargarHistorias();
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

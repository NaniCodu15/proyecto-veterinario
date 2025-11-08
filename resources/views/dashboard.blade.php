@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- SIDEBAR FIJO -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logoVet.png') }}" alt="Logo" class="sidebar-logo">
        </div>

        <ul class="sidebar-menu">
            <li><a href="#" class="nav-link active" data-section="inicio"><i class="fas fa-home"></i><span>Inicio</span></a></li>
            <li class="sidebar-item sidebar-item--has-submenu">
                <a href="#" class="nav-link" data-section="historias"><i class="fas fa-notes-medical"></i><span>Historias Clínicas</span></a>
                <ul class="sidebar-submenu" id="sidebarHistoriasSubmenu">
                    <li>
                        <a href="#" class="nav-link nav-link--sublayer" data-section="historias" data-parent="historias" data-action="ver-listado-historias">
                            <i class="fas fa-list-ul"></i>
                            <span>Listado general</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu__empty">No hay historias clínicas registradas.</li>
                </ul>
            </li>
            <li class="sidebar-item sidebar-item--has-submenu">
                <a href="#" class="nav-link" data-section="citas"><i class="fas fa-calendar-alt"></i><span>Citas</span></a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="#" class="nav-link nav-link--sublayer" data-section="citas-agendadas" data-parent="citas">
                            <i class="fas fa-calendar-check"></i>
                            <span>Citas Agendadas</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li><a href="#" class="nav-link" data-section="mascotas"><i class="fas fa-dog"></i><span>Mascotas</span></a></li>
            <li><a href="#" class="nav-link" data-section="propietarios"><i class="fas fa-user"></i><span>Propietarios</span></a></li>
            <li><a href="#" class="nav-link" data-section="consultas"><i class="fas fa-stethoscope"></i><span>Consultas</span></a></li>
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
                            <span class="schedule-list__status schedule-list__status--check"><i class="fas fa-check"></i>Atendida</span>
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
                    <div class="historias-header__content">
                        <h1 class="titulo">Historias Clínicas</h1>
                        <p class="historias-header__description">Crea nuevas historias clínicas y mantén el seguimiento de cada paciente.</p>
                    </div>

                    <!-- BOTÓN NUEVA HISTORIA -->
                    <button id="btnNuevaHistoria" class="btn btn-primary" data-action="abrir-modal-historia">
                        <i class="fas fa-plus"></i> Nueva Historia Clínica
                    </button>
                </div>

                <div class="historias-tip">
                    <i class="fas fa-lightbulb"></i>
                    <span>Selecciona una historia desde el submenú lateral para abrirla y editarla al instante.</span>
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
            <div class="citas-header">
                <h1 class="titulo">Citas</h1>
                <p>Coordina las citas con información actualizada de cada paciente y su familia en pocos pasos.</p>
            </div>

            <div class="citas-grid citas-grid--single">
                <section class="citas-card" id="registrarCitaCard">
                    <div class="citas-card__header">
                        <div>
                            <h2>Registrar Cita</h2>
                            <p>Selecciona una historia clínica existente para completar automáticamente los datos.</p>
                        </div>
                        <div class="citas-card__icon" aria-hidden="true"><i class="fas fa-calendar-plus"></i></div>
                    </div>

                    <div id="citaMensaje" class="cita-alert" role="alert" hidden></div>

                    <form id="formRegistrarCita" class="cita-form" novalidate>
                        <div class="cita-form__group">
                            <label for="historiaSelectCitas">Historia clínica</label>
                            <select id="historiaSelectCitas" name="historia" required>
                                <option value="">Selecciona una historia clínica</option>
                            </select>
                        </div>

                        <div class="cita-form__grid" aria-live="polite">
                            <div class="cita-form__group">
                                <label for="citaPropietarioNombre">Nombre del propietario</label>
                                <input type="text" id="citaPropietarioNombre" readonly>
                            </div>

                            <div class="cita-form__group">
                                <label for="citaPropietarioDni">DNI del propietario</label>
                                <input type="text" id="citaPropietarioDni" readonly>
                            </div>

                            <div class="cita-form__group">
                                <label for="citaPropietarioTelefono">Teléfono del propietario</label>
                                <input type="text" id="citaPropietarioTelefono" readonly>
                            </div>

                            <div class="cita-form__group">
                                <label for="citaMascotaNombre">Nombre de la mascota</label>
                                <input type="text" id="citaMascotaNombre" readonly>
                            </div>
                        </div>

                        <div class="cita-form__group">
                            <label for="citaMotivo">Motivo de la cita</label>
                            <textarea id="citaMotivo" name="motivo" placeholder="Describe brevemente el motivo de la visita" required></textarea>
                        </div>

                        <div class="cita-form__group">
                            <label for="citaFecha">Fecha de la cita</label>
                            <input type="date" id="citaFecha" name="fecha_cita" required>
                        </div>

                        <div class="cita-form__group">
                            <label for="citaHora">Hora de la cita</label>
                            <input type="time" id="citaHora" name="hora_cita" required>
                        </div>

                        <div class="cita-form__actions">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i>
                                Guardar cita
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>

        <div id="section-citas-agendadas" class="section">
            <div class="citas-header">
                <h1 class="titulo">Citas Agendadas</h1>
                <p>Visualiza y gestiona cada cita registrada en el sistema.</p>
            </div>

            <section class="citas-card citas-card--list" id="listadoCitasCard">
                <div class="citas-card__header">
                    <div>
                        <h2>Agenda de citas</h2>
                        <p>Controla el estado y seguimiento de cada cita programada.</p>
                    </div>
                    <div class="citas-card__icon" aria-hidden="true"><i class="fas fa-clipboard-list"></i></div>
                </div>

                <div id="citasListadoMensaje" class="citas-alert" role="status" aria-live="polite" hidden></div>

                <div class="citas-toolbar">
                    <label for="buscarCitas" class="citas-search">
                        <i class="fas fa-search"></i>
                        <input type="search" id="buscarCitas" placeholder="Buscar por mascota o propietario">
                    </label>
                </div>

                <div class="citas-table-wrapper">
                    <table class="citas-table">
                        <thead>
                            <tr>
                                <th>🆔 ID de la cita</th>
                                <th>🐾 Nombre de la mascota</th>
                                <th>🧍‍♀️ Nombre del propietario</th>
                                <th>📅 Fecha de la cita</th>
                                <th>⏰ Hora de la cita</th>
                                <th>💬 Motivo</th>
                                <th>🔖 Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaCitas">
                            <tr class="citas-table__empty">
                                <td colspan="8">No hay citas registradas todavía.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
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

<div id="modalDetalleCita" class="modal modal--cita" aria-hidden="true">
    <div class="modal-content modal-content--cita">
        <span class="close" data-close="detalleCita">&times;</span>
        <h2>Detalle de la cita</h2>
        <div class="cita-detalle">
            <div class="cita-detalle__row"><span class="cita-detalle__label">ID</span><span class="cita-detalle__value" data-detalle="id">—</span></div>
            <div class="cita-detalle__row"><span class="cita-detalle__label">Historia clínica</span><span class="cita-detalle__value" data-detalle="numero_historia">—</span></div>
            <div class="cita-detalle__row"><span class="cita-detalle__label">Mascota</span><span class="cita-detalle__value" data-detalle="mascota">—</span></div>
            <div class="cita-detalle__row"><span class="cita-detalle__label">Propietario</span><span class="cita-detalle__value" data-detalle="propietario">—</span></div>
            <div class="cita-detalle__row"><span class="cita-detalle__label">Teléfono</span><span class="cita-detalle__value" data-detalle="propietario_telefono">—</span></div>
            <div class="cita-detalle__row"><span class="cita-detalle__label">Fecha</span><span class="cita-detalle__value" data-detalle="fecha_legible">—</span></div>
            <div class="cita-detalle__row"><span class="cita-detalle__label">Hora</span><span class="cita-detalle__value" data-detalle="hora">—</span></div>
            <div class="cita-detalle__row"><span class="cita-detalle__label">Estado</span><span class="cita-detalle__value" data-detalle="estado">—</span></div>
            <div class="cita-detalle__row cita-detalle__row--full"><span class="cita-detalle__label">Motivo</span><span class="cita-detalle__value" data-detalle="motivo">—</span></div>
        </div>
    </div>
</div>

<div id="modalEstadoCita" class="modal modal--cita" aria-hidden="true">
    <div class="modal-content modal-content--cita">
        <span class="close" data-close="estadoCita">&times;</span>
        <h2>Actualizar estado de la cita</h2>
        <p class="cita-estado__subtitle">Selecciona el estado que refleje el seguimiento actual de la cita.</p>
        <form id="formEstadoCita" class="cita-estado-form">
            <div class="form-group">
                <label for="selectEstadoCita">Estado</label>
                <select id="selectEstadoCita" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Atendida">Atendida</option>
                    <option value="Reprogramada">Reprogramada</option>
                    <option value="Cancelada">Cancelada</option>
                </select>
            </div>

            <div id="reprogramarCampos" class="reprogramar-campos" hidden>
                <div class="form-group">
                    <label for="citaReprogramadaFecha">Nueva fecha</label>
                    <input type="date" id="citaReprogramadaFecha">
                </div>
                <div class="form-group">
                    <label for="citaReprogramadaHora">Nueva hora</label>
                    <input type="time" id="citaReprogramadaHora">
                </div>
            </div>
            <div class="cita-estado-actions">
                <button type="button" class="btn btn-outline" data-close="estadoCita">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    const sidebarMenu = document.querySelector('.sidebar-menu');
    const sections    = Array.from(document.querySelectorAll('#main-content .section'));

    const historiaListUrl   = "{{ route('historia_clinicas.list') }}";
    const historiaStoreUrl  = "{{ route('historia_clinicas.store') }}";
    const historiaBaseUrl   = "{{ url('historia_clinicas') }}";
    const citasStoreUrl     = "{{ route('citas.store') }}";
    const citasListUrl      = "{{ route('citas.list') }}";
    const citasEstadoBaseUrl = "{{ url('citas') }}";
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken        = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    let historiaEditandoId = null;
    let historiaPorEliminarId = null;
    let proximoNumeroHistoria = 'HC-00001';
    let citasBusquedaActual = '';
    let citasCache = [];
    let citaDetalleSeleccionada = null;
    let citaSeleccionadaParaEstado = null;

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

    function debounce(fn, delay = 300) {
        let timeoutId;

        return (...args) => {
            window.clearTimeout(timeoutId);
            timeoutId = window.setTimeout(() => {
                fn.apply(null, args);
            }, delay);
        };
    }

    function resetCamposReprogramar() {
        if (reprogramarCampos) {
            reprogramarCampos.hidden = true;
        }

        if (reprogramarFechaInput) {
            reprogramarFechaInput.value = '';
            reprogramarFechaInput.removeAttribute('required');
        }

        if (reprogramarHoraInput) {
            reprogramarHoraInput.value = '';
            reprogramarHoraInput.removeAttribute('required');
        }
    }

    function toggleCamposReprogramar(estado) {
        const esReprogramada = String(estado || '').toLowerCase() === 'reprogramada';

        if (reprogramarCampos) {
            reprogramarCampos.hidden = !esReprogramada;
        }

        if (reprogramarFechaInput) {
            if (esReprogramada) {
                reprogramarFechaInput.setAttribute('required', 'required');
            } else {
                reprogramarFechaInput.removeAttribute('required');
            }
        }

        if (reprogramarHoraInput) {
            if (esReprogramada) {
                reprogramarHoraInput.setAttribute('required', 'required');
            } else {
                reprogramarHoraInput.removeAttribute('required');
            }
        }
    }

    function showSection(key) {
        sections.forEach(sec => {
            const activa = sec.id === `section-${key}`;
            sec.style.display = activa ? 'block' : 'none';
            sec.classList.toggle('active', activa);
        });
    }

    function clearActiveLinks() {
        document.querySelectorAll('.sidebar-menu a.nav-link').forEach(link => {
            link.classList.remove('active', 'nav-link--parent-active');
        });
    }

    function setActiveLink(link) {
        if (!link) {
            return;
        }

        clearActiveLinks();
        link.classList.add('active');

        const parentSection = link.dataset.parent;
        if (parentSection) {
            const parentLink = document.querySelector(`.sidebar-menu a.nav-link[data-section="${parentSection}"]`);
            parentLink?.classList.add('nav-link--parent-active');
        } else if (link.closest('.sidebar-item--has-submenu')) {
            link.classList.add('nav-link--parent-active');
        }
    }

    function manejarNavegacion(link, opciones = {}) {
        if (!link) {
            return;
        }

        const key = link.dataset.section;
        if (!key) {
            return;
        }

        setActiveLink(link);
        showSection(key);

        const { recargarHistorias = true } = opciones;

        if (key === 'historias' && recargarHistorias) {
            cargarHistorias();
        }

        if (key === 'citas' || key === 'citas-agendadas') {
            cargarCitas(citasBusquedaActual);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        manejarNavegacion(document.querySelector('.sidebar-menu a[data-section="inicio"]'));
        cargarHistorias();
        cargarCitas();
    });

    if (sidebarMenu) {
        sidebarMenu.addEventListener('click', event => {
            const link = event.target.closest('a.nav-link');
            if (!link || !sidebarMenu.contains(link)) {
                return;
            }

            event.preventDefault();

            const accion = link.dataset.action;
            const opciones = {};

            if (link.dataset.section === 'historias' && accion === 'abrir-historia') {
                opciones.recargarHistorias = false;
            }

            manejarNavegacion(link, opciones);

            if (accion === 'ver-listado-historias') {
                window.requestAnimationFrame(() => {
                    desplazarAListadoHistorias();
                });
            } else if (accion === 'abrir-historia' && link.dataset.historiaId) {
                const historiaId = link.dataset.historiaId;
                window.requestAnimationFrame(() => {
                    resaltarHistoriaEnTabla(historiaId);
                });
                cargarHistoriaParaEditar(historiaId);
            }
        });
    }

    const modal               = document.getElementById('modalHistoria');
    const botonesNuevaHistoria = Array.from(document.querySelectorAll('[data-action="abrir-modal-historia"]'));
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
    const submenuHistorias    = document.getElementById('sidebarHistoriasSubmenu');
    const confirmModal        = document.getElementById('confirmModal');
    const confirmAcceptButton = confirmModal?.querySelector('[data-confirm="accept"]');
    const confirmCancelButton = confirmModal?.querySelector('[data-confirm="cancel"]');
    const tablaCitas          = document.getElementById('tablaCitas');
    const buscarCitasInput    = document.getElementById('buscarCitas');
    const citasListadoMensaje = document.getElementById('citasListadoMensaje');
    const modalDetalleCita    = document.getElementById('modalDetalleCita');
    const modalEstadoCita     = document.getElementById('modalEstadoCita');
    const formEstadoCita      = document.getElementById('formEstadoCita');
    const selectEstadoCita    = document.getElementById('selectEstadoCita');
    const reprogramarCampos   = document.getElementById('reprogramarCampos');
    const reprogramarFechaInput = document.getElementById('citaReprogramadaFecha');
    const reprogramarHoraInput  = document.getElementById('citaReprogramadaHora');
    const detalleCamposCita   = modalDetalleCita ? {
        id: modalDetalleCita.querySelector('[data-detalle="id"]'),
        numero_historia: modalDetalleCita.querySelector('[data-detalle="numero_historia"]'),
        mascota: modalDetalleCita.querySelector('[data-detalle="mascota"]'),
        propietario: modalDetalleCita.querySelector('[data-detalle="propietario"]'),
        propietario_telefono: modalDetalleCita.querySelector('[data-detalle="propietario_telefono"]'),
        fecha_legible: modalDetalleCita.querySelector('[data-detalle="fecha_legible"]'),
        hora: modalDetalleCita.querySelector('[data-detalle="hora"]'),
        estado: modalDetalleCita.querySelector('[data-detalle="estado"]'),
        motivo: modalDetalleCita.querySelector('[data-detalle="motivo"]'),
    } : {};

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

    let historiaSeleccionadaParaCita = null;
    let tomSelectHistoria = null;
    let historiasDisponibles = [];

    function desplazarAListadoHistorias() {
        if (!tablaHistorias) {
            return;
        }

        const contenedorTabla = tablaHistorias.closest('.tabla-wrapper');
        if (contenedorTabla) {
            contenedorTabla.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function resaltarHistoriaEnTabla(id) {
        if (!tablaHistorias || !id) {
            return;
        }

        const filas = tablaHistorias.querySelectorAll('tr');
        filas.forEach(fila => fila.classList.remove('tabla-consultas__row--highlight'));

        const filaObjetivo = tablaHistorias.querySelector(`tr[data-historia-id="${id}"]`);
        if (!filaObjetivo) {
            return;
        }

        filaObjetivo.classList.add('tabla-consultas__row--highlight');
        filaObjetivo.scrollIntoView({ behavior: 'smooth', block: 'center' });

        window.setTimeout(() => {
            filaObjetivo.classList.remove('tabla-consultas__row--highlight');
        }, 2000);
    }

    function crearElementoSubmenuHistoria(historia) {
        if (!historia || !submenuHistorias) {
            return null;
        }

        const numero = (historia.numero_historia ?? '').toString().trim() || 'Sin código';
        const mascota = (historia.mascota ?? '').toString().trim() || 'Mascota sin nombre';

        const item = document.createElement('li');
        const enlace = document.createElement('a');
        enlace.href = '#';
        enlace.className = 'nav-link nav-link--sublayer';
        enlace.dataset.section = 'historias';
        enlace.dataset.parent = 'historias';
        enlace.dataset.action = 'abrir-historia';
        enlace.dataset.historiaId = historia.id ?? '';
        enlace.title = `Abrir ${numero} · ${mascota}`;

        const icono = document.createElement('i');
        icono.className = 'fas fa-file-medical';

        const texto = document.createElement('span');
        texto.textContent = `${numero} · ${mascota}`;

        enlace.append(icono, texto);
        item.appendChild(enlace);

        return item;
    }

    function actualizarSubmenuHistorias(lista = []) {
        if (!submenuHistorias) {
            return;
        }

        submenuHistorias.innerHTML = '';

        const itemListado = document.createElement('li');
        const enlaceListado = document.createElement('a');
        enlaceListado.href = '#';
        enlaceListado.className = 'nav-link nav-link--sublayer';
        enlaceListado.dataset.section = 'historias';
        enlaceListado.dataset.parent = 'historias';
        enlaceListado.dataset.action = 'ver-listado-historias';

        const iconoListado = document.createElement('i');
        iconoListado.className = 'fas fa-list-ul';

        const textoListado = document.createElement('span');
        textoListado.textContent = 'Listado general';

        enlaceListado.append(iconoListado, textoListado);
        itemListado.appendChild(enlaceListado);
        submenuHistorias.appendChild(itemListado);

        if (!Array.isArray(lista) || lista.length === 0) {
            const itemVacio = document.createElement('li');
            itemVacio.className = 'sidebar-submenu__empty';
            itemVacio.textContent = 'No hay historias clínicas registradas.';
            submenuHistorias.appendChild(itemVacio);
            return;
        }

        const fragment = document.createDocumentFragment();
        lista.forEach(historia => {
            const elemento = crearElementoSubmenuHistoria(historia);
            if (elemento) {
                fragment.appendChild(elemento);
            }
        });

        submenuHistorias.appendChild(fragment);
    }

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
        modal.setAttribute('aria-hidden', 'false');
        actualizarEstadoBodyModal();
    }

    function cerrarModal() {
        if (!modal) {
            return;
        }

        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        actualizarEstadoBodyModal();
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

            citaMensaje.classList.remove('is-visible', 'cita-alert--success', 'cita-alert--error');
            citaMensaje.hidden = true;
        }, 4000);
    }

    function mostrarMensajeListadoCitas(texto, tipo = 'info') {
        if (!citasListadoMensaje) {
            return;
        }

        citasListadoMensaje.textContent = texto;
        citasListadoMensaje.classList.remove('citas-alert--info', 'citas-alert--error', 'citas-alert--success', 'is-visible');

        const clase = tipo === 'error'
            ? 'citas-alert--error'
            : tipo === 'success'
                ? 'citas-alert--success'
                : 'citas-alert--info';

        citasListadoMensaje.classList.add(clase, 'is-visible');
        citasListadoMensaje.hidden = false;

        window.clearTimeout(mostrarMensajeListadoCitas.timeoutId);
        mostrarMensajeListadoCitas.timeoutId = window.setTimeout(() => {
            limpiarMensajeListadoCitas();
        }, 5000);
    }

    function limpiarMensajeListadoCitas() {
        if (!citasListadoMensaje) {
            return;
        }

        citasListadoMensaje.hidden = true;
        citasListadoMensaje.classList.remove('is-visible', 'citas-alert--info', 'citas-alert--error', 'citas-alert--success');
        citasListadoMensaje.textContent = '';
    }

    function limpiarDatosHistoriaEnCita() {
        ['propietarioNombre', 'propietarioDni', 'propietarioTelefono', 'mascotaNombre'].forEach(clave => {
            const campo = citaCampos[clave];
            if (campo) {
                campo.value = '';
            }
        });
    }

    function obtenerClaseEstadoCita(estado = '') {
        const normalizado = String(estado || '').trim().toLowerCase();

        switch (normalizado) {
            case 'atendida':
                return 'cita-status--success';
            case 'reprogramada':
                return 'cita-status--warning';
            case 'cancelada':
                return 'cita-status--danger';
            case 'pendiente':
            default:
                return 'cita-status--pending';
        }
    }

    function crearFilaCita(cita = {}) {
        const fila = document.createElement('tr');
        fila.dataset.citaId = cita.id ?? '';

        const crearCeldaTexto = (valor, clase = '') => {
            const celda = document.createElement('td');
            if (clase) {
                celda.classList.add(clase);
            }
            celda.textContent = valor ?? '—';
            return celda;
        };

        fila.appendChild(crearCeldaTexto(cita.id ?? '—'));
        fila.appendChild(crearCeldaTexto(cita.mascota ?? '—'));
        fila.appendChild(crearCeldaTexto(cita.propietario ?? '—'));
        fila.appendChild(crearCeldaTexto(cita.fecha_legible ?? cita.fecha ?? '—'));
        fila.appendChild(crearCeldaTexto(cita.hora ?? '—'));

        const motivoCell = crearCeldaTexto(cita.motivo ?? '—', 'citas-table__motivo');
        if (cita.motivo) {
            motivoCell.title = cita.motivo;
        }
        fila.appendChild(motivoCell);

        const estadoCell = document.createElement('td');
        const estadoPill = document.createElement('span');
        estadoPill.className = `cita-status ${obtenerClaseEstadoCita(cita.estado)}`;
        estadoPill.textContent = cita.estado ?? 'Pendiente';
        estadoCell.appendChild(estadoPill);
        fila.appendChild(estadoCell);

        const accionesCell = document.createElement('td');
        accionesCell.classList.add('citas-table__acciones');

        const accionesWrapper = document.createElement('div');
        accionesWrapper.className = 'citas-actions';

        const whatsappLink = document.createElement('a');
        whatsappLink.className = 'citas-accion__whatsapp';
        whatsappLink.innerHTML = '<i class="fab fa-whatsapp"></i>';
        whatsappLink.setAttribute('aria-label', 'Contactar por WhatsApp');

        if (cita.propietario_whatsapp) {
            const mensajeWhatsapp = `Hola ${cita.propietario ?? ''}, te contactamos de la veterinaria respecto a la cita de ${cita.mascota ?? 'tu mascota'}.`;
            whatsappLink.href = `https://wa.me/${cita.propietario_whatsapp}?text=${encodeURIComponent(mensajeWhatsapp)}`;
            whatsappLink.target = '_blank';
            whatsappLink.rel = 'noopener noreferrer';
            whatsappLink.title = 'Contactar por WhatsApp';
        } else {
            whatsappLink.href = '#';
            whatsappLink.classList.add('is-disabled');
            whatsappLink.setAttribute('aria-disabled', 'true');
            whatsappLink.title = 'Teléfono no disponible';
        }

        const btnDetalles = document.createElement('button');
        btnDetalles.type = 'button';
        btnDetalles.className = 'btn btn-outline btn-sm btnVerCita';
        btnDetalles.innerHTML = '<i class="fas fa-eye"></i> Ver detalles';

        const btnEstado = document.createElement('button');
        btnEstado.type = 'button';
        btnEstado.className = 'btn btn-warning btn-sm btnEstadoCita';
        btnEstado.innerHTML = '<i class="fas fa-exchange-alt"></i> Cambiar estado';

        if (String(cita.estado || '').trim().toLowerCase() === 'atendida') {
            btnEstado.disabled = true;
            btnEstado.classList.add('is-disabled');
            btnEstado.setAttribute('aria-disabled', 'true');
            btnEstado.title = 'Las citas atendidas no pueden modificarse.';
        }

        accionesWrapper.appendChild(whatsappLink);
        accionesWrapper.appendChild(btnDetalles);
        accionesWrapper.appendChild(btnEstado);
        accionesCell.appendChild(accionesWrapper);
        fila.appendChild(accionesCell);

        return fila;
    }

    function renderCitas(lista = []) {
        if (!tablaCitas) {
            return;
        }

        citasCache = Array.isArray(lista) ? lista : [];

        tablaCitas.innerHTML = '';

        if (!Array.isArray(citasCache) || citasCache.length === 0) {
            const filaVacia = document.createElement('tr');
            filaVacia.classList.add('citas-table__empty');

            const celda = document.createElement('td');
            celda.colSpan = 8;
            celda.textContent = citasBusquedaActual
                ? 'No se encontraron citas para la búsqueda ingresada.'
                : 'No hay citas registradas todavía.';

            filaVacia.appendChild(celda);
            tablaCitas.appendChild(filaVacia);
            return;
        }

        const fragment = document.createDocumentFragment();
        citasCache.forEach(cita => {
            fragment.appendChild(crearFilaCita(cita));
        });

        tablaCitas.appendChild(fragment);
    }

    function obtenerCitaPorId(id) {
        if (!id) {
            return null;
        }

        return citasCache.find(cita => String(cita?.id ?? '') === String(id)) ?? null;
    }

    function escribirDetalleCita(cita) {
        if (!cita) {
            return;
        }

        Object.entries(detalleCamposCita).forEach(([clave, elemento]) => {
            if (!elemento) {
                return;
            }

            let valor = cita[clave];

            if (clave === 'fecha_legible') {
                valor = cita.fecha_legible ?? cita.fecha ?? '—';
            } else if (clave === 'propietario_telefono') {
                valor = cita.propietario_telefono ?? 'Sin teléfono registrado';
            } else if (clave === 'motivo') {
                valor = cita.motivo ?? '—';
            } else if (!valor) {
                valor = '—';
            }

            elemento.textContent = valor;
        });
    }

    function mostrarDetalleCita(cita) {
        if (!cita || !modalDetalleCita) {
            return;
        }

        citaDetalleSeleccionada = cita;
        escribirDetalleCita(cita);
        abrirModalGenerico(modalDetalleCita);
    }

    function actualizarDetalleCitaSiCorresponde(cita) {
        if (!citaDetalleSeleccionada || !modalDetalleCita) {
            return;
        }

        const coincide = String(citaDetalleSeleccionada.id ?? '') === String(cita?.id ?? '');
        const modalVisible = modalDetalleCita.style.display === 'block';

        if (coincide && modalVisible) {
            citaDetalleSeleccionada = cita;
            escribirDetalleCita(cita);
        }
    }

    function prepararModalEstado(cita) {
        if (!cita || !modalEstadoCita) {
            return;
        }

        const estadoActual = String(cita.estado ?? 'Pendiente').trim().toLowerCase();

        if (estadoActual === 'atendida') {
            mostrarMensajeListadoCitas('Las citas marcadas como atendidas no se pueden modificar.', 'info');
            return;
        }

        resetCamposReprogramar();
        citaSeleccionadaParaEstado = cita;

        if (selectEstadoCita) {
            const estadoTexto = cita.estado ?? 'Pendiente';
            selectEstadoCita.value = estadoTexto;
            if (selectEstadoCita.value !== estadoTexto) {
                selectEstadoCita.value = 'Pendiente';
            }
            toggleCamposReprogramar(selectEstadoCita.value);
        }

        if (reprogramarFechaInput) {
            reprogramarFechaInput.value = cita.fecha ?? '';
        }

        if (reprogramarHoraInput) {
            reprogramarHoraInput.value = cita.hora ?? '';
        }

        if (!selectEstadoCita) {
            toggleCamposReprogramar(cita.estado);
        }

        abrirModalGenerico(modalEstadoCita);
    }

    async function cargarCitas(query = '') {
        if (!citasListUrl) {
            return;
        }

        try {
            const url = new URL(citasListUrl, window.location.origin);

            if (query) {
                url.searchParams.set('q', query);
            }

            const response = await fetch(url.toString(), {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('No se pudieron obtener las citas.');
            }

            const data = await response.json();
            const lista = Array.isArray(data?.data) ? data.data : [];

            renderCitas(lista);

            if (lista.length === 0 && query) {
                mostrarMensajeListadoCitas('No se encontraron citas para la búsqueda ingresada.', 'info');
            } else if (lista.length > 0) {
                limpiarMensajeListadoCitas();
            }
        } catch (error) {
            console.error(error);
            mostrarMensajeListadoCitas(error.message || 'No se pudieron cargar las citas.', 'error');
            renderCitas();
        }
    }

    async function actualizarEstadoCita(id, cambios = {}) {
        if (!id || !citasEstadoBaseUrl) {
            throw new Error('No se pudo identificar la cita seleccionada.');
        }

        const payload = { ...cambios };

        const response = await fetch(`${citasEstadoBaseUrl}/${id}/estado`, {
            method: 'PATCH',
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
            const mensaje = errores.join(' ') || 'Verifica el estado seleccionado.';
            throw new Error(mensaje);
        }

        if (!response.ok) {
            throw new Error(data?.message || 'No se pudo actualizar el estado de la cita.');
        }

        return data?.cita ?? null;
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
            } else {
                tomSelectHistoria.clear(true);
                historiaSeleccionadaParaCita = null;
                limpiarDatosHistoriaEnCita();
            }
        }

        if (!historiasDisponibles.length) {
            tomSelectHistoria.clear(true);
            tomSelectHistoria.disable();
            historiaSeleccionadaParaCita = null;
            limpiarDatosHistoriaEnCita();
        } else {
            tomSelectHistoria.enable();
        }

        tomSelectHistoria.setTextboxValue('');
        tomSelectHistoria.refreshOptions(false);
    }

    function poblarHistoriasParaCitas(lista = []) {
        if (!historiaSelectCita) {
            return;
        }

        historiasDisponibles = Array.isArray(lista)
            ? lista.filter(historia => historia && historia.id)
            : [];

        if (tomSelectHistoria) {
            sincronizarTomSelectHistorias();
            return;
        }

        const valorActual = historiaSelectCita.value;
        historiaSelectCita.innerHTML = '<option value="">Selecciona una historia clínica</option>';

        historiasDisponibles.forEach(historia => {
            const opcion = document.createElement('option');
            opcion.value = historia.id;
            const formateada = formatearHistoriaParaOpcion(historia);
            opcion.textContent = formateada?.text ?? '';
            historiaSelectCita.appendChild(opcion);
        });

        const existeValorPrevio = historiasDisponibles.some(
            historia => String(historia?.id ?? '') === String(valorActual)
        );

        if (existeValorPrevio) {
            historiaSelectCita.value = valorActual;
        } else {
            historiaSelectCita.value = '';
            historiaSeleccionadaParaCita = null;
            limpiarDatosHistoriaEnCita();
        }
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

                // Para integrar AJAX en el futuro, reemplazar el filtro anterior por una solicitud fetch().
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

        return data.historia;
    }

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
        poblarHistoriasParaCitas(Array.isArray(lista) ? lista : []);
        actualizarSubmenuHistorias(Array.isArray(lista) ? lista : []);

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
            mostrarMensajeCita('No se pudieron cargar las historias clínicas.', 'error');
            renderHistorias();
        }
    }

    if (botonesNuevaHistoria.length) {
        botonesNuevaHistoria.forEach(boton => {
            boton.addEventListener('click', () => {
                abrirModalParaCrear();
            });
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
            manejarNavegacion(linkHistorias);
        });
    }

    const buscarCitasDebounce = debounce(valor => {
        cargarCitas(valor);
    }, 350);

    if (buscarCitasInput) {
        buscarCitasInput.addEventListener('input', event => {
            citasBusquedaActual = event.target.value.trim();
            buscarCitasDebounce(citasBusquedaActual);
        });
    }

    if (tablaCitas) {
        tablaCitas.addEventListener('click', event => {
            const whatsappLink = event.target.closest('.citas-accion__whatsapp');
            if (whatsappLink && whatsappLink.classList.contains('is-disabled')) {
                event.preventDefault();
                mostrarMensajeListadoCitas('El propietario no tiene un teléfono registrado para contactar por WhatsApp.', 'info');
                return;
            }

            const botonDetalles = event.target.closest('.btnVerCita');
            const botonEstado = event.target.closest('.btnEstadoCita');

            if (!botonDetalles && !botonEstado) {
                return;
            }

            const fila = event.target.closest('tr');
            const id = fila?.dataset.citaId;
            if (!id) {
                return;
            }

            const cita = obtenerCitaPorId(id);

            if (botonDetalles && cita) {
                mostrarDetalleCita(cita);
                return;
            }

            if (botonEstado?.disabled) {
                return;
            }

            if (botonEstado && cita) {
                prepararModalEstado(cita);
            }
        });
    }

    document.querySelectorAll('[data-close="detalleCita"]').forEach(elemento => {
        elemento.addEventListener('click', () => {
            cerrarModalGenerico(modalDetalleCita);
            citaDetalleSeleccionada = null;
        });
    });

    document.querySelectorAll('[data-close="estadoCita"]').forEach(elemento => {
        elemento.addEventListener('click', () => {
            cerrarModalGenerico(modalEstadoCita);
            resetCamposReprogramar();
            citaSeleccionadaParaEstado = null;
        });
    });

    if (modalDetalleCita) {
        modalDetalleCita.addEventListener('click', event => {
            if (event.target === modalDetalleCita) {
                cerrarModalGenerico(modalDetalleCita);
                citaDetalleSeleccionada = null;
            }
        });
    }

    if (modalEstadoCita) {
        modalEstadoCita.addEventListener('click', event => {
            if (event.target === modalEstadoCita) {
                cerrarModalGenerico(modalEstadoCita);
                resetCamposReprogramar();
                citaSeleccionadaParaEstado = null;
            }
        });
    }

    if (selectEstadoCita) {
        selectEstadoCita.addEventListener('change', () => {
            toggleCamposReprogramar(selectEstadoCita.value);
        });
    }

    if (formEstadoCita) {
        formEstadoCita.addEventListener('submit', async event => {
            event.preventDefault();

            if (!citaSeleccionadaParaEstado) {
                mostrarMensajeListadoCitas('Selecciona una cita para actualizar su estado.', 'error');
                return;
            }

            const nuevoEstado = selectEstadoCita?.value || 'Pendiente';
            const esReprogramada = String(nuevoEstado).toLowerCase() === 'reprogramada';
            const payload = { estado: nuevoEstado };

            if (esReprogramada) {
                const nuevaFecha = reprogramarFechaInput?.value || '';
                const nuevaHora = reprogramarHoraInput?.value || '';

                if (!nuevaFecha) {
                    mostrarMensajeListadoCitas('Selecciona la nueva fecha para la cita reprogramada.', 'error');
                    reprogramarFechaInput?.focus();
                    return;
                }

                if (!nuevaHora) {
                    mostrarMensajeListadoCitas('Selecciona la nueva hora para la cita reprogramada.', 'error');
                    reprogramarHoraInput?.focus();
                    return;
                }

                payload.fecha_cita = nuevaFecha;
                payload.hora_cita = nuevaHora;
            }

            try {
                const citaActualizada = await actualizarEstadoCita(citaSeleccionadaParaEstado.id, payload);
                cerrarModalGenerico(modalEstadoCita);
                resetCamposReprogramar();
                citaSeleccionadaParaEstado = null;

                await cargarCitas(citasBusquedaActual);

                if (citaActualizada) {
                    const citaDesdeLista = obtenerCitaPorId(citaActualizada.id);
                    actualizarDetalleCitaSiCorresponde(citaDesdeLista ?? citaActualizada);
                }

                mostrarMensajeListadoCitas('Estado actualizado correctamente.', 'success');
            } catch (error) {
                console.error(error);
                mostrarMensajeListadoCitas(error.message || 'No se pudo actualizar el estado de la cita.', 'error');
            }
        });
    }

    if (historiaSelectCita) {
        historiaSelectCita.addEventListener('change', async event => {
            const id = event.target.value;

            if (!id) {
                rellenarDatosHistoriaEnCita(null);
                return;
            }

            try {
                const historia = await obtenerHistoriaDetallada(id);
                rellenarDatosHistoriaEnCita(historia);
            } catch (error) {
                console.error(error);
                mostrarMensajeCita(error.message || 'No se pudo cargar la historia clínica seleccionada.', 'error');
                rellenarDatosHistoriaEnCita(null);
            }
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
        if (event.key !== 'Escape') {
            return;
        }

        if (confirmModal?.classList.contains('is-visible')) {
            cerrarConfirmacion();
        }

        if (modalEstadoCita && modalEstadoCita.style.display === 'block') {
            cerrarModalGenerico(modalEstadoCita);
            resetCamposReprogramar();
            citaSeleccionadaParaEstado = null;
        }

        if (modalDetalleCita && modalDetalleCita.style.display === 'block') {
            cerrarModalGenerico(modalDetalleCita);
            citaDetalleSeleccionada = null;
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
            } catch (error) {
                console.error(error);
                mostrarMensajeCita(error.message || 'No se pudo registrar la cita.', 'error');
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

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.0/dist/css/tom-select.default.min.css">
    <style>
        .cita-form__group .ts-wrapper {
            width: 100%;
        }

        .cita-form__group .ts-wrapper.single .ts-control {
            border-radius: var(--radius-md);
            border: 1px solid rgba(122, 168, 255, 0.3);
            padding: 12px 14px;
            font-size: 0.95rem;
            background: rgba(255, 255, 255, 0.95);
            color: var(--text-dark);
            box-shadow: inset 0 1px 2px rgba(122, 168, 255, 0.1);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            min-height: 52px;
        }

        .cita-form__group .ts-wrapper.single.focus .ts-control,
        .cita-form__group .ts-wrapper.single .ts-control:hover {
            outline: none;
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 4px rgba(156, 194, 255, 0.25);
        }

        .cita-form__group .ts-dropdown {
            border-radius: var(--radius-md);
            border: 1px solid rgba(122, 168, 255, 0.25);
            box-shadow: 0 18px 48px rgba(126, 142, 177, 0.18);
        }

        .ts-option__content {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 4px 2px;
        }

        .ts-option__numero,
        .ts-item__numero {
            font-weight: 600;
            color: #3f5b96;
            letter-spacing: 0.02em;
        }

        .ts-option__mascota,
        .ts-item__mascota {
            font-size: 0.9rem;
            color: #4d5f87;
        }

        .ts-option__propietario,
        .ts-item__propietario {
            font-size: 0.8rem;
            color: #6c7a91;
        }

        .ts-item__content {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
            padding: 6px 10px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(232, 240, 255, 0.9) 0%, rgba(246, 250, 255, 0.95) 100%);
            color: #344563;
            font-weight: 600;
            min-width: 0;
        }

        .ts-dropdown__message {
            padding: 12px;
            font-size: 0.85rem;
            color: #6c7a91;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.0/dist/js/tom-select.complete.min.js"></script>
    <script>
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
    </script>
@endpush
@endsection

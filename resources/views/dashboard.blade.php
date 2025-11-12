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
            <li class="sidebar-item sidebar-item--has-submenu">
                <a href="#" class="nav-link" data-section="historias"><i class="fas fa-notes-medical"></i><span>Historias Clínicas</span></a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="#" class="nav-link nav-link--sublayer" data-section="historias-registradas" data-parent="historias">
                            <i class="fas fa-folder-open"></i>
                            <span>Historias Registradas</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        @php
            $user = auth()->user();
            $userName = $user?->name ?? 'Usuario';
            $userEmail = $user?->email ?? 'usuario@correo.com';
            $avatarUrl = $user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=7aa8ff&color=ffffff';
        @endphp

        <div class="user-card">
            <div class="user-card__info">
                <div class="user-card__avatar">
                    <img src="{{ $avatarUrl }}" alt="Avatar de {{ $userName }}">
                </div>
                <div class="user-card__details">
                    <span class="user-card__name">{{ $userName }}</span>
                    <span class="user-card__email">{{ $userEmail }}</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="user-card__logout">
                @csrf
                <button type="submit" aria-label="Cerrar sesión">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- CONTENIDO PRINCIPAL (CAMBIA SEGÚN OPCIÓN) -->
    <div id="main-content" class="content">
        <!-- SECCIÓN INICIO -->
        <div id="section-inicio" class="section active">
            <div class="home-hero">
                <div class="home-hero__content">
                    <span class="home-hero__badge">CLÍNICA CENTRAL</span>
                    <h1 class="home-hero__title">Seguimiento integral con una mirada moderna</h1>
                    <p class="home-hero__subtitle">
                        Centraliza la información clínica, anticipa citas críticas y brinda una experiencia confiable a cada familia.
                    </p>
                    <div class="home-hero__actions">
                        <a href="#" class="btn btn-primary btn-ir-historias" data-section="historias">
                            <i class="fas fa-clinic-medical"></i>
                            Abrir módulo clínico
                        </a>
                        <button type="button" class="btn btn-ghost" id="btnAccesoRapido">
                            <i class="fas fa-calendar-plus"></i>
                            Ver agenda
                        </button>
                    </div>
                </div>
                <div class="home-hero__visual">
                    <div class="home-hero__bubble home-hero__bubble--one"></div>
                    <div class="home-hero__bubble home-hero__bubble--two"></div>
                    <img src="{{ asset('images/logoVet.png') }}" alt="Dra. Alfaro" class="home-hero__image">
                </div>
            </div>

            <div class="metrics-grid">
                <article class="metric-card metric-card--patients">
                    <header class="metric-card__header">
                        <span class="metric-card__label">Pacientes activos</span>
                        <span class="metric-card__icon"><i class="fas fa-paw"></i></span>
                    </header>
                    <div class="metric-card__body">
                        <h2 class="metric-card__value">{{ $totalMascotas }}</h2>
                        <p class="metric-card__description">Mascotas con seguimiento vigente</p>
                    </div>
                    <footer class="metric-card__footer">
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                    </footer>
                </article>

                <article class="metric-card metric-card--owners">
                    <header class="metric-card__header">
                        <span class="metric-card__label">Propietarios fidelizados</span>
                        <span class="metric-card__icon"><i class="fas fa-users"></i></span>
                    </header>
                    <div class="metric-card__body">
                        <h2 class="metric-card__value">{{ $totalPropietarios }}</h2>
                        <p class="metric-card__description">Familias activas en el último trimestre</p>
                    </div>
                    <footer class="metric-card__footer">
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                    </footer>
                </article>

                <article class="metric-card metric-card--records">
                    <header class="metric-card__header">
                        <span class="metric-card__label">Historias clínicas</span>
                        <span class="metric-card__icon"><i class="fas fa-file-medical"></i></span>
                    </header>
                    <div class="metric-card__body">
                        <h2 class="metric-card__value">{{ $totalHistorias }}</h2>
                        <p class="metric-card__description">Registros completos y auditados</p>
                    </div>
                    <footer class="metric-card__footer">
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                    </footer>
                </article>

                <article class="metric-card metric-card--consults">
                    <header class="metric-card__header">
                        <span class="metric-card__label">Consultas resueltas</span>
                        <span class="metric-card__icon"><i class="fas fa-stethoscope"></i></span>
                    </header>
                    <div class="metric-card__body">
                        <h2 class="metric-card__value">{{ $totalConsultas ?? 0 }}</h2>
                        <p class="metric-card__description">Atenciones finalizadas con seguimiento</p>
                    </div>
                    <footer class="metric-card__footer">
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                        <span class="metric-card__dot"></span>
                    </footer>
                </article>
            </div>

            <div class="overview-grid">
                <section class="panel panel--appointments">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">Citas próximas</h2>
                            <p class="panel__subtitle">Coordina la agenda del equipo de atención primaria.</p>
                        </div>
                        <span class="panel__chip"><i class="fas fa-clock"></i> Hoy</span>
                    </div>
                    <ul class="appointment-list">
                        @forelse ($upcomingAppointments as $appointment)
                            @php
                                $status = $appointment['estado'] ?? 'Pendiente';
                                $statusClasses = [
                                    'Pendiente' => 'is-pending',
                                    'Atendida' => 'is-done',
                                    'Cancelada' => 'is-cancelled',
                                    'Reprogramada' => 'is-rescheduled',
                                ];
                                $statusClass = $statusClasses[$status] ?? 'is-pending';
                            @endphp
                            <li class="appointment-list__item">
                                <div class="appointment-list__time">
                                    <span class="appointment-list__hour">{{ $appointment['hora'] ?? '--:--' }}</span>
                                    <span class="appointment-list__date">{{ $appointment['fecha'] ?? '--/--' }}</span>
                                </div>
                                <div class="appointment-list__details">
                                    <p class="appointment-list__pet">{{ $appointment['mascota'] }} <span>· {{ $appointment['motivo'] }}</span></p>
                                    <span class="appointment-list__owner">Propietario: {{ $appointment['propietario'] }}</span>
                                </div>
                                <span class="appointment-list__status {{ $statusClass }}">{{ $status }}</span>
                            </li>
                        @empty
                            <li class="appointment-list__item appointment-list__item--empty">
                                <div>
                                    <p>No hay citas próximas registradas.</p>
                                    <span>Agenda una nueva cita para mantener una atención oportuna.</span>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </section>

                <section class="panel panel--insights">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">Experiencia de bienestar</h2>
                            <p class="panel__subtitle">Refuerza la comunicación y el seguimiento con cada familia.</p>
                        </div>
                    </div>
                    <div class="insight-cards">
                        <article class="insight-card">
                            <span class="insight-card__icon"><i class="fas fa-heart"></i></span>
                            <div>
                                <h3>Planes preventivos activos</h3>
                                <p>Programas personalizados que mantienen saludables a los pacientes más delicados.</p>
                            </div>
                        </article>
                        <article class="insight-card">
                            <span class="insight-card__icon"><i class="fas fa-comments"></i></span>
                            <div>
                                <h3>Seguimiento con familias</h3>
                                <p>Recordatorios amables y reportes breves para construir confianza día a día.</p>
                            </div>
                        </article>
                        <article class="insight-card">
                            <span class="insight-card__icon"><i class="fas fa-sun"></i></span>
                            <div>
                                <h3>Agenda balanceada</h3>
                                <p>Distribuye atenciones entre turnos para sostener un ritmo de trabajo saludable.</p>
                            </div>
                        </article>
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
                                <span class="care-progress__bar" style="width: 72%;"></span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <!-- SECCIÓN HISTORIAS CLÍNICAS -->
        <div id="section-historias" class="section">
            <div class="historias-create">
                <div class="historias-create__content">
                    <span class="historias-create__badge"><i class="fas fa-star"></i> Registro clínico</span>
                    <h1 class="titulo historias-create__title">Historias Clínicas</h1>
                    <p class="historias-create__text">
                        Genera nuevas historias clínicas para cada paciente y mantén un seguimiento cálido y organizado de su bienestar.
                    </p>
                    <div class="historias-create__actions">
                        <button id="btnNuevaHistoria" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Crear nueva historia
                        </button>
                    </div>
                    <div class="alert historias-create__alert" role="status" aria-live="polite" data-historia-mensaje hidden></div>
                </div>
                <div class="historias-create__panel">
                    <h2 class="historias-create__panel-title">Una gestión moderna y humana</h2>
                    <ul class="historias-create__benefits">
                        <li><i class="fas fa-heartbeat"></i><span>Seguimiento integral de cada visita y control preventivo.</span></li>
                        <li><i class="fas fa-user-friends"></i><span>Datos del propietario siempre a mano para comunicar novedades.</span></li>
                        <li><i class="fas fa-shield-alt"></i><span>Historial clínico seguro, centralizado y fácil de actualizar.</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="section-historias-registradas" class="section">
            <div class="historias-registradas">
                <div class="historias-registradas__header">
                    <div>
                        <h1 class="titulo">Historias Registradas</h1>
                        <p>Consulta, edita y coordina la información clínica de tus pacientes en una vista cuidada y cómoda.</p>
                    </div>
                    <button type="button" class="btn btn-outline" id="btnIrCrearHistoria">
                        <i class="fas fa-plus-circle"></i>
                        Crear nueva historia
                    </button>
                </div>

                <div class="alert historias-registradas__alert" role="status" aria-live="polite" data-historia-mensaje hidden></div>

                <div class="historias-registradas__toolbar">
                    <div class="historias-registradas__search">
                        <i class="fas fa-search historias-registradas__search-icon" aria-hidden="true"></i>
                        <input
                            type="search"
                            id="buscarHistorias"
                            class="historias-registradas__search-input"
                            placeholder="Buscar por número, propietario o mascota"
                            aria-label="Buscar historias clínicas"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="historias-registradas__grid" id="tablaHistorias">
                    <div class="historias-registradas__empty">
                        <i class="fas fa-folder-open"></i>
                        <p>No hay historias clínicas registradas todavía.</p>
                    </div>
                </div>
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
                                <label>Peso inicial (kg):</label>
                                <input type="number" id="peso" name="peso" step="0.01" min="0" required>
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
                                <input type="text" id="telefono" name="telefono" required>
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

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success btn-guardar">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL DETALLE DE HISTORIA Y CONSULTAS -->
        <div id="modalConsultas" class="modal modal--historia" aria-hidden="true">
            <div class="modal-content modal-content--historia">
                <span class="close" data-close="consultas">&times;</span>
                <div class="historia-detalle">
                    <div class="historia-detalle__header">
                        <div>
                            <span class="historia-detalle__badge"><i class="fas fa-notes-medical"></i> Historia clínica</span>
                            <h2 class="historia-detalle__title" data-detalle-historia="titulo">Historia clínica</h2>
                            <p class="historia-detalle__subtitle" data-detalle-historia="subtitulo">—</p>
                        </div>
                    </div>

                    <div class="historia-detalle__info-grid">
                        <div class="historia-detalle__info-item">
                            <span>Propietario</span>
                            <strong data-detalle-historia="propietario">—</strong>
                            <small data-detalle-historia="dni">DNI —</small>
                        </div>
                        <div class="historia-detalle__info-item">
                            <span>Contacto</span>
                            <strong data-detalle-historia="telefono">—</strong>
                            <small data-detalle-historia="direccion">—</small>
                        </div>
                        <div class="historia-detalle__info-item">
                            <span>Mascota</span>
                            <strong data-detalle-historia="mascota">—</strong>
                            <small data-detalle-historia="especie">—</small>
                        </div>
                        <div class="historia-detalle__info-item">
                            <span>Peso inicial</span>
                            <strong data-detalle-historia="peso">—</strong>
                            <small data-detalle-historia="fecha_apertura">Apertura —</small>
                        </div>
                    </div>

                    <div class="historia-detalle__body">
                        <div class="historia-detalle__tabs" role="tablist" aria-label="Secciones de la historia clínica">
                            <button type="button" class="historia-detalle__tab is-active" id="tabRegistroConsultas" data-tab-target="registro" role="tab" aria-controls="panelRegistroConsultas" aria-selected="true" tabindex="0">
                                Registrar consulta
                            </button>
                            <button type="button" class="historia-detalle__tab" id="tabListadoConsultas" data-tab-target="consultas" role="tab" aria-controls="panelListadoConsultas" aria-selected="false" tabindex="-1">
                                Consultas registradas
                            </button>
                        </div>

                        <section id="panelRegistroConsultas" class="historia-detalle__form historia-detalle__panel is-active" data-tab-content="registro" role="tabpanel" aria-labelledby="tabRegistroConsultas">
                            <div class="historia-detalle__section-header">
                                <h3>Registrar nueva consulta</h3>
                                <p>Documenta la evolución del paciente en cada visita.</p>
                            </div>
                            <div id="consultaMensaje" class="consulta-alert" role="status" aria-live="polite" hidden></div>
                            <form id="formConsulta" class="consulta-form" novalidate>
                                <input type="hidden" id="consultaHistoriaId" name="id_historia">
                                <div class="consulta-form__grid">
                                    <div class="form-group">
                                        <label for="consultaFecha">Fecha de la consulta</label>
                                        <input type="date" id="consultaFecha" name="fecha_consulta" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="consultaPeso">Peso (kg)</label>
                                        <input type="number" id="consultaPeso" name="peso" step="0.01" min="0">
                                    </div>
                                    <div class="form-group">
                                        <label for="consultaTemperatura">Temperatura (°C)</label>
                                        <input type="number" id="consultaTemperatura" name="temperatura" step="0.1">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="consultaSintomas">Síntomas</label>
                                    <textarea id="consultaSintomas" name="sintomas" rows="2" placeholder="Describe signos clínicos observados"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="consultaDiagnostico">Diagnóstico</label>
                                    <textarea id="consultaDiagnostico" name="diagnostico" rows="2" placeholder="Resumen del diagnóstico"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="consultaTratamiento">Tratamiento</label>
                                    <textarea id="consultaTratamiento" name="tratamiento" rows="2" placeholder="Medicaciones o procedimientos indicados"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="consultaObservaciones">Observaciones</label>
                                    <textarea id="consultaObservaciones" name="observaciones" rows="2" placeholder="Notas adicionales sobre la atención"></textarea>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i>
                                        Guardar consulta
                                    </button>
                                </div>
                            </form>
                        </section>
                        <section id="panelListadoConsultas" class="historia-detalle__panel historia-detalle__panel--consultas" data-tab-content="consultas" role="tabpanel" aria-labelledby="tabListadoConsultas" hidden aria-hidden="true">
                            <div class="historia-detalle__section-header">
                                <h3>Consultas registradas</h3>
                                <p>Seguimiento cronológico de la atención brindada.</p>
                            </div>
                            <div class="historia-detalle__timeline">
                                <ul id="listaConsultas" class="historia-detalle__timeline-list"></ul>
                            </div>
                        </section>
                    </div>
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
    const links    = Array.from(document.querySelectorAll('.sidebar-menu a.nav-link'));
    const sections = Array.from(document.querySelectorAll('#main-content .section'));

    const historiaListUrl   = "{{ route('historia_clinicas.list') }}";
    const historiaStoreUrl  = "{{ route('historia_clinicas.store') }}";
    const historiaBaseUrl   = "{{ url('historia_clinicas') }}";
    const consultaStoreUrl  = "{{ route('consultas.store') }}";
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
        links.forEach(link => link.classList.remove('active', 'nav-link--parent-active'));
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

    function manejarNavegacion(link) {
        if (!link) {
            return;
        }

        const key = link.dataset.section;
        if (!key) {
            return;
        }

        setActiveLink(link);
        showSection(key);

        if (key === 'historias' || key === 'historias-registradas') {
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

    links.forEach(link => {
        link.addEventListener('click', event => {
            event.preventDefault();
            manejarNavegacion(link);
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
    const mensajesHistoria    = Array.from(document.querySelectorAll('[data-historia-mensaje]'));
    const buscarHistoriasInput = document.getElementById('buscarHistorias');
    const btnGuardar          = form?.querySelector('.btn-guardar');
    const btnAccesoRapido     = document.getElementById('btnAccesoRapido');
    const btnIrHistorias      = document.querySelector('.btn-ir-historias');
    const btnIrCrearHistoria  = document.getElementById('btnIrCrearHistoria');
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

    const modalConsultas = document.getElementById('modalConsultas');
    const modalConsultasClose = modalConsultas?.querySelector('[data-close="consultas"]');
    const listaConsultas = document.getElementById('listaConsultas');
    const formConsulta = document.getElementById('formConsulta');
    const consultaMensaje = document.getElementById('consultaMensaje');
    const consultaHistoriaId = document.getElementById('consultaHistoriaId');
    const consultaCampos = {
        fecha: document.getElementById('consultaFecha'),
        peso: document.getElementById('consultaPeso'),
        temperatura: document.getElementById('consultaTemperatura'),
        sintomas: document.getElementById('consultaSintomas'),
        diagnostico: document.getElementById('consultaDiagnostico'),
        tratamiento: document.getElementById('consultaTratamiento'),
        observaciones: document.getElementById('consultaObservaciones'),
    };

    const consultaTabs = Array.from(document.querySelectorAll('[data-tab-target]'));
    const consultaPanels = Array.from(document.querySelectorAll('[data-tab-content]'));

    function activarTabConsulta(nombre = 'registro') {
        if (!consultaTabs.length || !consultaPanels.length) {
            return;
        }

        consultaTabs.forEach(tab => {
            if (!tab) {
                return;
            }

            const objetivo = tab.dataset.tabTarget;
            const activo = objetivo === nombre;
            tab.classList.toggle('is-active', activo);
            tab.setAttribute('aria-selected', activo ? 'true' : 'false');
            tab.setAttribute('tabindex', activo ? '0' : '-1');
        });

        consultaPanels.forEach(panel => {
            if (!panel) {
                return;
            }

            const objetivo = panel.dataset.tabContent;
            const activo = objetivo === nombre;
            panel.classList.toggle('is-active', activo);
            panel.hidden = !activo;
            panel.setAttribute('aria-hidden', activo ? 'false' : 'true');
        });
    }

    consultaTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const objetivo = tab.dataset.tabTarget;
            if (objetivo) {
                activarTabConsulta(objetivo);
            }
        });
    });

    activarTabConsulta('registro');

    const detalleHistoriaCampos = {
        titulo: document.querySelector('[data-detalle-historia="titulo"]'),
        subtitulo: document.querySelector('[data-detalle-historia="subtitulo"]'),
        propietario: document.querySelector('[data-detalle-historia="propietario"]'),
        dni: document.querySelector('[data-detalle-historia="dni"]'),
        telefono: document.querySelector('[data-detalle-historia="telefono"]'),
        direccion: document.querySelector('[data-detalle-historia="direccion"]'),
        mascota: document.querySelector('[data-detalle-historia="mascota"]'),
        especie: document.querySelector('[data-detalle-historia="especie"]'),
        peso: document.querySelector('[data-detalle-historia="peso"]'),
        fecha_apertura: document.querySelector('[data-detalle-historia="fecha_apertura"]'),
    };

    let historiaSeleccionadaParaCita = null;
    let tomSelectHistoria = null;
    let historiasDisponibles = [];
    let historiasRegistradas = [];
    let terminoBusquedaHistorias = '';
    let historiaDetalleActual = null;
    let consultasDetalleActual = [];

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
        if (!mensajesHistoria.length) {
            return;
        }

        mensajesHistoria.forEach(mensaje => {
            mensaje.textContent = texto;
            mensaje.classList.remove('alert--success', 'alert--error');
            mensaje.classList.add(`alert--${tipo}`);
            mensaje.hidden = false;
        });

        window.clearTimeout(mostrarMensajeHistoria.timeoutId);
        mostrarMensajeHistoria.timeoutId = window.setTimeout(() => {
            mensajesHistoria.forEach(mensaje => {
                mensaje.hidden = true;
            });
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

    function mostrarMensajeConsulta(texto, tipo = 'success') {
        if (!consultaMensaje) {
            return;
        }

        consultaMensaje.textContent = texto;
        consultaMensaje.classList.remove('consulta-alert--success', 'consulta-alert--error', 'is-visible');
        const clase = tipo === 'success' ? 'consulta-alert--success' : 'consulta-alert--error';
        consultaMensaje.classList.add(clase, 'is-visible');
        consultaMensaje.hidden = false;

        window.clearTimeout(mostrarMensajeConsulta.timeoutId);
        mostrarMensajeConsulta.timeoutId = window.setTimeout(() => {
            if (!consultaMensaje) {
                return;
            }

            consultaMensaje.classList.remove('is-visible', 'consulta-alert--success', 'consulta-alert--error');
            consultaMensaje.hidden = true;
        }, 4000);
    }

    function limpiarFormularioConsulta() {
        if (!formConsulta) {
            return;
        }

        formConsulta.reset();

        if (consultaHistoriaId && historiaDetalleActual?.id) {
            consultaHistoriaId.value = historiaDetalleActual.id;
        }

        activarTabConsulta('registro');
    }

    function crearEtiquetaConsulta(icono, texto) {
        const span = document.createElement('span');
        span.className = 'consulta-item__meta-tag';
        span.innerHTML = `<i class="fas ${icono}"></i> ${texto}`;
        return span;
    }

    function crearNodoConsulta(consulta = {}) {
        const item = document.createElement('li');
        item.className = 'consulta-item';

        const header = document.createElement('div');
        header.className = 'consulta-item__header';

        const fecha = document.createElement('span');
        fecha.className = 'consulta-item__date';
        fecha.textContent = consulta.fecha_legible || 'Sin fecha';

        const titulo = document.createElement('h4');
        titulo.className = 'consulta-item__titulo';
        const descripcionConsulta = consulta.diagnostico || consulta.sintomas || consulta.tratamiento || consulta.observaciones;
        titulo.textContent = descripcionConsulta || 'Consulta registrada';

        header.append(fecha, titulo);

        const meta = document.createElement('div');
        meta.className = 'consulta-item__meta';

        if (consulta.peso !== undefined && consulta.peso !== null) {
            meta.appendChild(crearEtiquetaConsulta('fa-weight', `${parseFloat(consulta.peso).toFixed(2)} kg`));
        }

        if (consulta.temperatura !== undefined && consulta.temperatura !== null) {
            meta.appendChild(crearEtiquetaConsulta('fa-thermometer-half', `${parseFloat(consulta.temperatura).toFixed(1)} °C`));
        }

        const cuerpo = document.createElement('div');
        cuerpo.className = 'consulta-item__body';

        const secciones = [
            { etiqueta: 'Síntomas', valor: consulta.sintomas },
            { etiqueta: 'Diagnóstico', valor: consulta.diagnostico },
            { etiqueta: 'Tratamiento', valor: consulta.tratamiento },
            { etiqueta: 'Observaciones', valor: consulta.observaciones },
        ];

        secciones.forEach(({ etiqueta, valor }) => {
            if (!valor) {
                return;
            }

            const bloque = document.createElement('div');
            bloque.className = 'consulta-item__block';

            const titulo = document.createElement('span');
            titulo.className = 'consulta-item__block-title';
            titulo.textContent = etiqueta;

            const contenido = document.createElement('p');
            contenido.className = 'consulta-item__block-text';
            contenido.textContent = valor;

            bloque.append(titulo, contenido);
            cuerpo.appendChild(bloque);
        });

        item.append(header);

        if (meta.children.length) {
            item.appendChild(meta);
        }

        if (cuerpo.children.length) {
            item.appendChild(cuerpo);
        }

        return item;
    }

    function obtenerMarcaTiempoConsulta(consulta = {}) {
        const posiblesFechas = [
            consulta.fecha_consulta,
            consulta.fechaConsulta,
            consulta.fecha,
            consulta.created_at,
            consulta.updated_at,
        ];

        for (const valor of posiblesFechas) {
            if (!valor) {
                continue;
            }

            const fecha = new Date(valor);
            if (!Number.isNaN(fecha.getTime())) {
                return fecha.getTime();
            }
        }

        return 0;
    }

    function renderConsultas(lista = []) {
        if (!listaConsultas) {
            return;
        }

        listaConsultas.innerHTML = '';

        const listaOrdenada = [...lista].sort((a, b) => obtenerMarcaTiempoConsulta(b) - obtenerMarcaTiempoConsulta(a));
        const fragment = document.createDocumentFragment();
        listaOrdenada.forEach(consulta => {
            fragment.appendChild(crearNodoConsulta(consulta));
        });

        listaConsultas.appendChild(fragment);
    }

    function actualizarDetalleHistoria(historia = {}) {
        historiaDetalleActual = historia;

        if (detalleHistoriaCampos.titulo) {
            const numero = historia.numero_historia ? `#${historia.numero_historia}` : 'Historia clínica';
            detalleHistoriaCampos.titulo.textContent = `${historia.nombreMascota || 'Mascota sin nombre'} ${numero}`;
        }

        if (detalleHistoriaCampos.subtitulo) {
            detalleHistoriaCampos.subtitulo.textContent = historia.nombrePropietario
                ? `A cargo de ${historia.nombrePropietario}`
                : 'Propietario no registrado';
        }

        if (detalleHistoriaCampos.propietario) {
            detalleHistoriaCampos.propietario.textContent = historia.nombrePropietario || '—';
        }

        if (detalleHistoriaCampos.dni) {
            detalleHistoriaCampos.dni.textContent = historia.dni ? `DNI ${historia.dni}` : 'DNI —';
        }

        if (detalleHistoriaCampos.telefono) {
            detalleHistoriaCampos.telefono.textContent = historia.telefono || '—';
        }

        if (detalleHistoriaCampos.direccion) {
            detalleHistoriaCampos.direccion.textContent = historia.direccion || 'Sin dirección registrada';
        }

        if (detalleHistoriaCampos.mascota) {
            detalleHistoriaCampos.mascota.textContent = historia.nombreMascota || '—';
        }

        if (detalleHistoriaCampos.especie) {
            const especieBase = historia.especie === 'otro' && historia.especieOtro
                ? historia.especieOtro
                : historia.especie;
            const especieFormateada = especieBase
                ? `${especieBase.charAt(0).toUpperCase()}${especieBase.slice(1)}`
                : '';
            const raza = historia.raza ? ` · ${historia.raza}` : '';
            detalleHistoriaCampos.especie.textContent = especieFormateada
                ? `${especieFormateada}${raza}`
                : raza.replace(' · ', '') || '—';
        }

        if (detalleHistoriaCampos.peso) {
            detalleHistoriaCampos.peso.textContent = historia.peso ? `${parseFloat(historia.peso).toFixed(2)} kg` : '—';
        }

        if (detalleHistoriaCampos.fecha_apertura) {
            detalleHistoriaCampos.fecha_apertura.textContent = historia.fecha_apertura
                ? `Apertura ${historia.fecha_apertura}`
                : 'Apertura —';
        }

        if (consultaHistoriaId && historia.id) {
            consultaHistoriaId.value = historia.id;
        }
    }

    async function mostrarDetalleHistoria(id) {
        try {
            const data = await obtenerHistoriaDetallada(id);
            const historia = data.historia ?? {};
            const consultas = Array.isArray(data.consultas) ? data.consultas : [];

            actualizarDetalleHistoria(historia);
            consultasDetalleActual = consultas;
            renderConsultas(consultasDetalleActual);
            limpiarFormularioConsulta();

            if (consultaCampos.fecha) {
                const hoy = new Date().toISOString().split('T')[0];
                consultaCampos.fecha.value = hoy;
            }

            abrirModalGenerico(modalConsultas);
        } catch (error) {
            console.error(error);
            mostrarMensajeHistoria(error.message || 'No se pudo cargar el historial clínico.', 'error');
        }
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

        return data;
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

    function crearTarjetaHistoria(historia) {
        const card = document.createElement('article');
        card.className = 'historia-card';
        card.dataset.historiaId = historia.id ?? '';

        const header = document.createElement('div');
        header.className = 'historia-card__header';

        const badge = document.createElement('span');
        badge.className = 'historia-card__badge';
        badge.textContent = historia.numero_historia || 'Sin código';

        const fecha = document.createElement('span');
        fecha.className = 'historia-card__date';
        fecha.innerHTML = `<i class="fas fa-calendar-day"></i> ${historia.fecha_apertura || 'Sin fecha'}`;

        header.append(badge, fecha);

        const body = document.createElement('div');
        body.className = 'historia-card__body';

        const detalles = [
            { icono: 'fa-paw', etiqueta: 'Mascota', valor: historia.mascota || 'Sin nombre' },
            { icono: 'fa-user', etiqueta: 'Propietario', valor: historia.propietario || 'Sin registrar' },
            { icono: 'fa-id-card', etiqueta: 'DNI', valor: historia.propietario_dni || '—' },
        ];

        detalles.forEach(({ icono, etiqueta, valor }) => {
            const filaDetalle = document.createElement('div');
            filaDetalle.className = 'historia-card__row';

            const label = document.createElement('span');
            label.className = 'historia-card__label';
            label.innerHTML = `<i class="fas ${icono}"></i> ${etiqueta}`;

            const value = document.createElement('span');
            value.className = 'historia-card__value';
            value.textContent = valor;

            filaDetalle.append(label, value);
            body.appendChild(filaDetalle);
        });

        const acciones = document.createElement('div');
        acciones.className = 'historia-card__actions';

        const btnVerConsultas = document.createElement('button');
        btnVerConsultas.className = 'btn btn-primary btn-sm btnConsultas';
        btnVerConsultas.title = 'Ver historial clínico';
        btnVerConsultas.innerHTML = '<i class="fas fa-stream"></i> Consultas';

        const btnEditar = document.createElement('button');
        btnEditar.className = 'btn btn-warning btn-sm btnEditar';
        btnEditar.title = 'Editar historia';
        btnEditar.innerHTML = '<i class="fas fa-edit"></i> Editar';

        const btnEliminar = document.createElement('button');
        btnEliminar.className = 'btn btn-danger btn-sm btnEliminar';
        btnEliminar.title = 'Eliminar historia';
        btnEliminar.innerHTML = '<i class="fas fa-trash"></i> Eliminar';

        acciones.append(btnVerConsultas, btnEditar, btnEliminar);

        card.append(header, body, acciones);

        return card;
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

    function renderHistorias(lista = null) {
        if (Array.isArray(lista)) {
            historiasRegistradas = lista;
            poblarHistoriasParaCitas(lista);
        }

        const historiasBase = Array.isArray(historiasRegistradas) ? historiasRegistradas : [];
        actualizarProximoNumero(historiasBase);

        if (!tablaHistorias) {
            return;
        }

        const termino = terminoBusquedaHistorias.trim().toLowerCase();
        const listaFiltrada = termino
            ? historiasBase.filter(historia => {
                const numero = (historia.numero_historia ?? '').toString().toLowerCase();
                const mascota = (historia.mascota ?? '').toString().toLowerCase();
                const propietario = (historia.propietario ?? '').toString().toLowerCase();

                return (
                    numero.includes(termino) ||
                    mascota.includes(termino) ||
                    propietario.includes(termino)
                );
            })
            : historiasBase;

        tablaHistorias.innerHTML = '';

        if (!listaFiltrada.length) {
            const vacio = document.createElement('div');
            vacio.className = 'historias-registradas__empty';
            const icono = termino ? 'fa-search' : 'fa-folder-open';
            const mensaje = termino
                ? 'No se encontraron historias clínicas para la búsqueda.'
                : 'No hay historias clínicas registradas todavía.';
            vacio.innerHTML = `
                <i class="fas ${icono}"></i>
                <p>${mensaje}</p>
            `;
            tablaHistorias.appendChild(vacio);
            return;
        }

        const fragment = document.createDocumentFragment();
        listaFiltrada.forEach(historia => {
            fragment.appendChild(crearTarjetaHistoria(historia));
        });

        tablaHistorias.appendChild(fragment);
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
            renderHistorias([]);
        }
    }

    function navegarAHistorias() {
        const linkHistorias = document.querySelector('.sidebar-menu a[data-section="historias"]');
        manejarNavegacion(linkHistorias);
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

            navegarAHistorias();
        });
    }

    if (btnIrCrearHistoria) {
        btnIrCrearHistoria.addEventListener('click', event => {
            event.preventDefault();

            navegarAHistorias();
        });
    }

    if (buscarHistoriasInput) {
        buscarHistoriasInput.addEventListener('input', event => {
            const valor = event.target && typeof event.target.value === 'string'
                ? event.target.value
                : '';
            terminoBusquedaHistorias = valor;
            renderHistorias();
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
                const { historia } = await obtenerHistoriaDetallada(id);
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

    if (modalConsultasClose) {
        modalConsultasClose.addEventListener('click', () => {
            cerrarModalGenerico(modalConsultas);
            limpiarFormularioConsulta();
        });
    }

    window.addEventListener('click', event => {
        if (event.target === modal) {
            cerrarModal();
            reiniciarFormulario();
        }

        if (event.target === modalConsultas) {
            cerrarModalGenerico(modalConsultas);
            limpiarFormularioConsulta();
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

        if (modalConsultas && modalConsultas.style.display === 'block') {
            cerrarModalGenerico(modalConsultas);
            limpiarFormularioConsulta();
        }
    });

    if (tablaHistorias) {
        tablaHistorias.addEventListener('click', event => {
            const botonConsultas = event.target.closest('.btnConsultas');
            const botonEditar = event.target.closest('.btnEditar');
            const botonEliminar = event.target.closest('.btnEliminar');

            if (botonConsultas) {
                const tarjeta = botonConsultas.closest('.historia-card');
                const id = tarjeta?.dataset.historiaId;
                if (id) {
                    mostrarDetalleHistoria(id);
                }
            }

            if (botonEditar) {
                const tarjeta = botonEditar.closest('.historia-card');
                const id = tarjeta?.dataset.historiaId;
                if (id) {
                    cargarHistoriaParaEditar(id);
                }
            }

            if (botonEliminar) {
                const tarjeta = botonEliminar.closest('.historia-card');
                const id = tarjeta?.dataset.historiaId;
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

    if (formConsulta) {
        formConsulta.addEventListener('submit', async event => {
            event.preventDefault();

            if (!consultaStoreUrl) {
                mostrarMensajeConsulta('No se pudo determinar la ruta para guardar la consulta.', 'error');
                return;
            }

            const historiaId = consultaHistoriaId?.value || historiaDetalleActual?.id;
            const fecha = consultaCampos.fecha?.value || '';

            if (!historiaId) {
                mostrarMensajeConsulta('Selecciona una historia clínica válida antes de registrar la consulta.', 'error');
                return;
            }

            if (!fecha) {
                mostrarMensajeConsulta('La fecha de la consulta es obligatoria.', 'error');
                consultaCampos.fecha?.focus();
                return;
            }

            const payload = {
                id_historia: parseInt(historiaId, 10),
                fecha_consulta: fecha,
                sintomas: consultaCampos.sintomas?.value || null,
                diagnostico: consultaCampos.diagnostico?.value || null,
                tratamiento: consultaCampos.tratamiento?.value || null,
                observaciones: consultaCampos.observaciones?.value || null,
                peso: consultaCampos.peso?.value || null,
                temperatura: consultaCampos.temperatura?.value || null,
            };

            Object.keys(payload).forEach(clave => {
                if (payload[clave] === '' || payload[clave] === null) {
                    delete payload[clave];
                }
            });

            const botonGuardarConsulta = formConsulta.querySelector('button[type="submit"]');
            if (botonGuardarConsulta) {
                botonGuardarConsulta.disabled = true;
            }

            try {
                const response = await fetch(consultaStoreUrl, {
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
                    const mensaje = errores.join(' ') || 'Revisa los datos de la consulta.';
                    mostrarMensajeConsulta(mensaje, 'error');
                    return;
                }

                if (!response.ok) {
                    throw new Error(data?.message || 'No se pudo guardar la consulta.');
                }

                if (data?.consulta) {
                    consultasDetalleActual = [data.consulta, ...consultasDetalleActual];
                    renderConsultas(consultasDetalleActual);
                }

                mostrarMensajeConsulta('Consulta registrada correctamente.');
                limpiarFormularioConsulta();
                if (consultaCampos.fecha) {
                    consultaCampos.fecha.value = fecha;
                }
            } catch (error) {
                console.error(error);
                mostrarMensajeConsulta(error.message || 'No se pudo guardar la consulta.', 'error');
            } finally {
                if (botonGuardarConsulta) {
                    botonGuardarConsulta.disabled = false;
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

            ['especieOtro', 'edad', 'peso'].forEach(campo => {
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

        .historias-registradas__toolbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin: 24px 0 20px;
        }

        .historias-registradas__search {
            position: relative;
            flex: 1 1 280px;
            max-width: 360px;
        }

        .historias-registradas__search-input {
            width: 100%;
            border-radius: var(--radius-md);
            border: 1px solid rgba(122, 168, 255, 0.3);
            padding: 12px 16px 12px 44px;
            font-size: 0.95rem;
            background: rgba(255, 255, 255, 0.95);
            color: var(--text-dark);
            box-shadow: inset 0 1px 2px rgba(122, 168, 255, 0.12);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .historias-registradas__search-input::placeholder {
            color: rgba(63, 91, 150, 0.55);
        }

        .historias-registradas__search-input:focus {
            outline: none;
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 4px rgba(156, 194, 255, 0.22);
        }

        .historias-registradas__search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(63, 91, 150, 0.55);
            font-size: 0.95rem;
        }

        .modal--historia .modal-content--historia {
            max-width: 1100px;
            background: linear-gradient(135deg, rgba(244, 248, 255, 0.98), rgba(255, 255, 255, 0.98));
            border-radius: 28px;
            border: 1px solid rgba(134, 165, 255, 0.2);
            padding: 32px 36px;
            box-shadow: 0 40px 80px rgba(132, 160, 255, 0.18);
        }

        .historia-detalle {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .historia-detalle__header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
        }

        .historia-detalle__badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--primary-dark);
            background: rgba(156, 194, 255, 0.18);
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 600;
        }

        .historia-detalle__title {
            font-size: 1.75rem;
            color: var(--text-dark);
            margin: 10px 0 6px;
        }

        .historia-detalle__subtitle {
            color: rgba(43, 57, 144, 0.7);
            font-size: 0.95rem;
            margin: 0;
        }

        .historia-detalle__info-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            background: rgba(237, 244, 255, 0.65);
            border-radius: 22px;
            padding: 18px 22px;
        }

        .historia-detalle__info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
            color: var(--text-dark);
        }

        .historia-detalle__info-item > span {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(43, 57, 144, 0.55);
        }

        .historia-detalle__info-item > strong {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .historia-detalle__info-item > small {
            font-size: 0.85rem;
            color: rgba(43, 57, 144, 0.6);
        }

        .historia-detalle__body {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .historia-detalle__tabs {
            display: inline-flex;
            gap: 12px;
            background: rgba(255, 255, 255, 0.68);
            border-radius: 999px;
            padding: 6px;
            border: 1px solid rgba(156, 194, 255, 0.35);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
            align-self: flex-start;
        }

        .historia-detalle__tab {
            border: none;
            background: transparent;
            color: rgba(43, 57, 144, 0.6);
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 999px;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }

        .historia-detalle__tab:hover {
            color: var(--text-dark);
        }

        .historia-detalle__tab:focus-visible {
            outline: 2px solid rgba(122, 168, 255, 0.6);
            outline-offset: 2px;
        }

        .historia-detalle__tab.is-active {
            background: linear-gradient(135deg, rgba(122, 168, 255, 0.28), rgba(178, 214, 255, 0.45));
            color: var(--text-dark);
            box-shadow: 0 6px 18px rgba(122, 168, 255, 0.25);
        }

        .historia-detalle__panel {
            display: none;
        }

        .historia-detalle__panel.is-active {
            display: block;
        }

        @media (max-width: 768px) {
            .historia-detalle__tabs {
                width: 100%;
                justify-content: space-between;
            }

            .historia-detalle__tab {
                flex: 1;
            }
        }

        .historia-detalle__section-header {
            margin-bottom: 18px;
        }

        .historia-detalle__section-header h3 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--text-dark);
        }

        .historia-detalle__section-header p {
            margin: 6px 0 0;
            color: rgba(43, 57, 144, 0.65);
            font-size: 0.95rem;
        }

        .historia-detalle__timeline {
            background: rgba(255, 255, 255, 0.78);
            border-radius: 24px;
            padding: 24px;
            border: 1px solid rgba(156, 194, 255, 0.25);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
            display: flex;
            flex-direction: column;
            height: 520px;
        }

        .historia-detalle__timeline-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 18px;
            flex: 1;
            overflow-y: auto;
            padding-right: 8px;
            scrollbar-width: thin;
        }

        .historia-detalle__timeline-list::-webkit-scrollbar {
            width: 8px;
        }

        .historia-detalle__timeline-list::-webkit-scrollbar-thumb {
            background: rgba(156, 194, 255, 0.6);
            border-radius: 12px;
        }

        .historia-detalle__timeline-list::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 992px) {
            .historia-detalle__timeline {
                height: 420px;
            }

            .historia-detalle__timeline-list {
                padding-right: 4px;
            }
        }

        .consulta-item {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(241, 247, 255, 0.95));
            border-radius: 20px;
            padding: 20px 22px;
            border: 1px solid rgba(156, 194, 255, 0.22);
            box-shadow: 0 14px 30px rgba(140, 173, 255, 0.12);
        }

        .consulta-item__header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            margin-bottom: 12px;
        }

        .consulta-item__date {
            font-size: 0.9rem;
            font-weight: 600;
            color: rgba(43, 57, 144, 0.7);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .consulta-item__titulo {
            font-size: 1.15rem;
            margin: 0;
            color: var(--text-dark);
        }

        .consulta-item__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }

        .consulta-item__meta-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(156, 194, 255, 0.22);
            color: var(--primary-dark);
            font-weight: 600;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .consulta-item__body {
            display: grid;
            gap: 14px;
        }

        .consulta-item__block {
            background: rgba(237, 244, 255, 0.6);
            border-radius: 16px;
            padding: 12px 14px;
        }

        .consulta-item__block-title {
            display: block;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(43, 57, 144, 0.55);
            margin-bottom: 6px;
        }

        .consulta-item__block-text {
            margin: 0;
            color: var(--text-dark);
            line-height: 1.5;
        }

        .historia-detalle__form {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 24px;
            padding: 24px 26px;
            border: 1px solid rgba(156, 194, 255, 0.22);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
        }

        .consulta-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .consulta-form__grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .consulta-form .form-group label {
            font-weight: 600;
            color: var(--text-dark);
        }

        .consulta-form .form-group input,
        .consulta-form .form-group textarea {
            width: 100%;
            border: 1px solid rgba(122, 168, 255, 0.3);
            border-radius: var(--radius-md);
            padding: 12px 14px;
            font-size: 0.95rem;
            background: rgba(255, 255, 255, 0.92);
            color: var(--text-dark);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .consulta-form .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .consulta-form .form-group input:focus,
        .consulta-form .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 4px rgba(156, 194, 255, 0.2);
        }

        .consulta-alert {
            border-radius: var(--radius-md);
            padding: 14px 16px;
            font-weight: 600;
            display: none;
        }

        .consulta-alert.is-visible {
            display: block;
        }

        .consulta-alert--success {
            background: rgba(138, 199, 174, 0.18);
            color: rgba(23, 120, 88, 0.95);
            border: 1px solid rgba(138, 199, 174, 0.45);
        }

        .consulta-alert--error {
            background: rgba(255, 179, 179, 0.18);
            color: rgba(186, 30, 30, 0.95);
            border: 1px solid rgba(255, 179, 179, 0.45);
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

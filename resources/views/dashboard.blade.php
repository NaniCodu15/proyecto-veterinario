@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- SIDEBAR FIJO -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logoVet.png') }}" alt="Logo" class="sidebar-logo">
        </div>

        {{-- Menú principal con accesos rápidos a cada módulo del dashboard --}}
        <ul class="sidebar-menu">
            <li><a href="#" class="nav-link active" data-section="inicio"><i class="fas fa-home"></i><span>Inicio</span></a></li>
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
        </ul>

        {{-- Tarjeta con la identidad del usuario autenticado y el botón de cierre de sesión --}}
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
            {{-- Hero introductorio con CTA hacia la gestión clínica --}}
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
                    </div>
                </div>
                <div class="home-hero__visual">
                    <div class="home-hero__bubble home-hero__bubble--one"></div>
                    <div class="home-hero__bubble home-hero__bubble--two"></div>
                    <img src="{{ asset('images/logoVet.png') }}" alt="Dra. Alfaro" class="home-hero__image">
                </div>
            </div>

            {{-- Tarjetas de métricas clave para ofrecer visibilidad inmediata del estado de la clínica --}}
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
                    {{-- Listado dinámico de citas próximas; cada item muestra horario, mascota y propietario --}}
                    <ul class="appointment-list" id="citasProximasLista">
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

            </div>
        </div>

        {{-- Sección interactiva para registrar nuevas historias y consultas --}}
        @include('historias_clinicas')

        {{-- Tabla/tablas con el histórico de historias ya creadas --}}
        @include('historias_registradas')

        <!-- MODAL NUEVA/EDITAR HISTORIA -->
        <div id="modalHistoria" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="modalTitulo">Nueva Historia Clínica</h2>
                {{-- Formulario dividido por secciones para capturar datos de mascota y propietario --}}
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
                                <label>Peso:</label>
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
                        {{-- Tabs accesibles que permiten alternar entre registro y listado de consultas --}}
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
                            {{-- Formulario de consulta con campos clínicos y botón de guardado --}}
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

        {{-- Vista especializada para crear citas desde el dashboard --}}
        @include('citas')

        {{-- Sección con el detalle de todas las citas en estado activo --}}
        @include('citas_agendadas')

    </div>
</div>

{{-- Modal genérico para confirmar acciones destructivas --}}
<div id="confirmModal" class="confirm-modal" role="alertdialog" aria-modal="true" aria-labelledby="confirmModalMessage" hidden>
    <div class="confirm-modal__dialog">
        <p id="confirmModalMessage" class="confirm-modal__message">¿Desea anular esta historia clínica?</p>
        <div class="confirm-modal__actions">
            <button type="button" class="btn btn-confirm-cancel" data-confirm="cancel">Cancelar</button>
            <button type="button" class="btn btn-confirm-accept" data-confirm="accept">Sí, anular</button>
        </div>
    </div>
</div>

{{-- Modal de solo lectura con el detalle completo de una cita --}}
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

{{-- Modal para actualizar el estado de la cita y, si aplica, reprogramar la agenda --}}
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


@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.0/dist/css/tom-select.default.min.css">
    <link rel="stylesheet" href="{{ asset('css/historias_clinicas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/historias_registradas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/citas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/citas_agendadas.css') }}">
@endpush

@push('scripts')
    <script id="dashboard-config" type="application/json">
        {!! json_encode([
            'historiaListUrl' => route('historia_clinicas.list'),
            'historiaStoreUrl' => route('historia_clinicas.store'),
            'historiaBaseUrl' => url('historia_clinicas'),
            'consultaStoreUrl' => route('consultas.store'),
            'citasStoreUrl' => route('citas.store'),
            'citasListUrl' => route('citas.list'),
            'citasEstadoBaseUrl' => url('citas'),
            'citasBaseUrl' => url('citas'),
            'citasUpcomingUrl' => route('citas.upcoming'),
            'backupGenerateUrl' => route('backups.generate'),
            'backupListUrl' => route('backups.index'),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.0/dist/js/tom-select.complete.min.js"></script>
    {{-- Lógica del dashboard: controla tabs, formularios AJAX y modales --}}
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush

@endsection

@php
    $isAdmin = auth()->user()?->hasRole('admin');
    $isAssistant = auth()->user()?->hasRole('asistente');
@endphp
{{-- Plantilla principal del panel de control --}}
@extends('layouts.app')

@section('content')

@push('scripts')
    {{-- Librería para campos seleccionables mejorados --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.0/dist/js/tom-select.complete.min.js"></script>
@endpush
{{-- Contenedor general de la vista del dashboard --}}
<div class="dashboard-container">
    {{-- Barra lateral fija con navegación principal --}}
    <div class="sidebar" id="sidebar">
        {{-- Encabezado de la barra lateral con logotipo --}}
        <div class="sidebar-header">
            <img src="{{ asset('images/logoVet.png') }}" alt="Logo" class="sidebar-logo">
        </div>

        {{-- Menú de navegación de módulos --}}
        <ul class="sidebar-menu">
            {{-- Acceso directo a la sección de inicio --}}
            <li><a href="#" class="nav-link active" data-section="inicio"><i class="fas fa-home"></i><span>Inicio</span></a></li>
            {{-- Navegación hacia el módulo de historias clínicas --}}
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

            @if ($isAssistant)
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
            @elseif($isAdmin)
                <li class="sidebar-item sidebar-item--has-submenu">
                    <a href="#" class="nav-link" data-section="citas-agendadas"><i class="fas fa-calendar-alt"></i><span>Citas</span></a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="#" class="nav-link nav-link--sublayer" data-section="citas-agendadas" data-parent="citas-agendadas">
                                <i class="fas fa-calendar-check"></i>
                                <span>Citas Agendadas</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>

        @php
            $user = auth()->user();
            $userName = $user?->name ?? 'Usuario';
            $userEmail = $user?->email ?? 'usuario@correo.com';
            $avatarUrl = $user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=7aa8ff&color=ffffff';
        @endphp

        {{-- Tarjeta con datos del usuario autenticado y cierre de sesión --}}
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

    {{-- Contenido principal que cambia según la sección seleccionada --}}
    <div id="main-content" class="content">
        {{-- Sección de bienvenida e indicadores principales --}}
        <div id="section-inicio" class="section active">
            <div class="home-hero">
                <div class="home-hero__content">
                    <span class="home-hero__badge">HOSPITAL VETERINARIO</span>
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

            {{-- Cuadrícula de métricas principales del sistema --}}
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

            {{-- Panel con listado de próximas citas --}}
            <div class="overview-grid">
                <section class="panel panel--appointments">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">Citas próximas</h2>
                            <p class="panel__subtitle">Coordina la agenda del equipo de atención primaria.</p>
                        </div>
                        <span class="panel__chip"><i class="fas fa-clock"></i> Hoy</span>
                    </div>
                    <ul class="appointment-list" id="citasProximasLista">
                        @forelse ($upcomingAppointments as $appointment)
                            {{-- Definición de clases de estado según el estado de la cita --}}
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
                            {{-- Fila de cita próxima --}}
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
                            {{-- Mensaje cuando no hay citas próximas registradas --}}
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

        {{-- Inclusión de la sección de creación y gestión de historias clínicas --}}
        @if ($isAssistant || $isAdmin)
            @include('layouts.historias_clinicas')

            {{-- Inclusión de la sección de historias ya registradas --}}
            @include('layouts.historias_registradas')
        @endif



        {{-- Inclusión del formulario de registro de citas --}}
        @if ($isAssistant)
            @include('layouts.citas')
        @endif

        {{-- Inclusión del listado de citas agendadas --}}
        @include('layouts.citas_agendadas')

    </div>
</div>

{{-- Modal de confirmación para acciones sensibles (solo administradores pueden anular) --}}
@if ($isAdmin)
    <div id="confirmModal" class="confirm-modal" role="alertdialog" aria-modal="true" aria-labelledby="confirmModalMessage" hidden>
        <div class="confirm-modal__dialog">
            <p id="confirmModalMessage" class="confirm-modal__message">¿Desea anular esta historia clínica?</p>
            <div class="confirm-modal__actions">
                <button type="button" class="btn btn-confirm-cancel" data-confirm="cancel">Cancelar</button>
                <button type="button" class="btn btn-confirm-accept" data-confirm="accept">Sí, anular</button>
            </div>
        </div>
    </div>
@endif


{{-- Configuración JSON para rutas utilizadas por los scripts del dashboard --}}
<div id="dashboard-config" hidden>
    {!! json_encode([
        'historiaListUrl' => route('historia_clinicas.list'),
        'historiaStoreUrl' => route('historia_clinicas.store'),
        'historiaBaseUrl' => url('historia_clinicas'),
        'historias' => $historias ?? [],
        'consultaStoreUrl' => route('consultas.store'),
        'citasStoreUrl' => route('citas.store'),
        'citasListUrl' => route('citas.list'),
        'citasEstadoBaseUrl' => url('citas'),
        'citasBaseUrl' => url('citas'),
        'citasUpcomingUrl' => route('citas.upcoming'),
        'backupGenerateUrl' => $isAdmin ? route('backups.generate') : null,
        'backupListUrl' => $isAdmin ? route('backups.index') : null,
            'permissions' => [
                'is_admin' => $isAdmin,
                'is_assistant' => $isAssistant,
                'can_create_historia' => $isAssistant || $isAdmin,
                'can_edit_historia' => $isAssistant || $isAdmin,
                'can_delete_historia' => $isAdmin,
                'can_manage_backups' => $isAdmin,
                'can_manage_consultas' => $isAssistant || $isAdmin,
                'can_manage_citas' => $isAssistant || $isAdmin,
                'can_delete_citas' => $isAdmin,
            ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</div>


@push('styles')
    {{-- Hojas de estilo externas e internas para las secciones del dashboard --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.0/dist/css/tom-select.default.min.css">
    <link rel="stylesheet" href="{{ asset('css/historias_clinicas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/historias_registradas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/citas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/citas_agendadas.css') }}">
@endpush

@push('scripts')
    {{-- Script principal del dashboard que gestiona las interacciones --}}
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush

@endsection

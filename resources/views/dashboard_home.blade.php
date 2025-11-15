@extends('dashboard')

@section('title', 'Panel de control')

@section('content')
    @php
        $upcomingAppointments = collect($upcomingAppointments ?? []);
        $mascotas = ($mascotas ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator ? $mascotas : collect($mascotas ?? []);
    @endphp

    <!-- Hero principal -->
    <section class="home-hero">
        <div class="home-hero__content">
            <span class="home-hero__badge">Clínica Central</span>
            <h2 class="home-hero__title">Seguimiento integral con una mirada moderna</h2>
            <p class="home-hero__subtitle">
                Centraliza la información clínica, anticipa citas críticas y brinda una experiencia confiable a cada familia.
            </p>
            <div class="home-hero__actions">
                <a href="{{ route('historias.clinicas') }}" class="btn btn-primary">
                    <i class="fas fa-clinic-medical"></i>
                    Abrir módulo clínico
                </a>
                <a href="{{ route('citas') }}" class="btn btn-secondary">
                    <i class="fas fa-calendar"></i>
                    Gestionar citas
                </a>
            </div>
        </div>
        <div class="home-hero__visual">
            <img src="{{ asset('images/logoVet.png') }}" alt="Ilustración dashboard" class="home-hero__image">
        </div>
    </section>

    <!-- Métricas rápidas -->
    <section class="metrics-grid">
        <article class="metric-card metric-card--patients">
            <header class="metric-card__header">
                <span class="metric-card__label">Pacientes activos</span>
                <span class="metric-card__icon"><i class="fas fa-paw"></i></span>
            </header>
            <div class="metric-card__body">
                <h3 class="metric-card__value">{{ number_format($totalMascotas ?? 0) }}</h3>
                <p class="metric-card__description">Mascotas con seguimiento vigente</p>
            </div>
        </article>
        <article class="metric-card metric-card--owners">
            <header class="metric-card__header">
                <span class="metric-card__label">Propietarios fidelizados</span>
                <span class="metric-card__icon"><i class="fas fa-users"></i></span>
            </header>
            <div class="metric-card__body">
                <h3 class="metric-card__value">{{ number_format($totalPropietarios ?? 0) }}</h3>
                <p class="metric-card__description">Familias activas en el último trimestre</p>
            </div>
        </article>
        <article class="metric-card metric-card--records">
            <header class="metric-card__header">
                <span class="metric-card__label">Historias clínicas</span>
                <span class="metric-card__icon"><i class="fas fa-file-medical"></i></span>
            </header>
            <div class="metric-card__body">
                <h3 class="metric-card__value">{{ number_format($totalHistorias ?? 0) }}</h3>
                <p class="metric-card__description">Registros completos y auditados</p>
            </div>
        </article>
        <article class="metric-card metric-card--consults">
            <header class="metric-card__header">
                <span class="metric-card__label">Consultas resueltas</span>
                <span class="metric-card__icon"><i class="fas fa-stethoscope"></i></span>
            </header>
            <div class="metric-card__body">
                <h3 class="metric-card__value">{{ number_format($totalConsultas ?? 0) }}</h3>
                <p class="metric-card__description">Atenciones finalizadas con seguimiento</p>
            </div>
        </article>
    </section>

    <!-- Agenda rápida -->
    <section class="overview-grid">
        <article class="panel panel--appointments">
            <div class="panel__header">
                <div>
                    <h3 class="panel__title">Citas próximas</h3>
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
        </article>
    </section>

    <!-- Registro de pacientes -->
    <section class="records-panel">
        <header class="records-panel__header">
            <div>
                <p class="records-panel__eyebrow">Pacientes activos</p>
                <h3 class="records-panel__title">Listado de mascotas registradas</h3>
            </div>
            <a href="{{ route('historias.registradas') }}" class="btn btn-outline">
                Revisar historias
            </a>
        </header>

        <div class="records-table__wrapper">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>Mascota</th>
                        <th>Propietario</th>
                        <th>Especie</th>
                        <th>Última consulta</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mascotas as $mascota)
                        @php
                            $owner = $mascota->propietario;
                            $historia = $mascota->historiaClinica;
                            $lastConsulta = optional($historia?->consultas?->sortByDesc('fecha_consulta')->first())->fecha_consulta;
                        @endphp
                        <tr>
                            <td>
                                <span class="records-table__label">{{ $mascota->nombre }}</span>
                                <small class="records-table__muted">Código: {{ $historia->numero_historia ?? '—' }}</small>
                            </td>
                            <td>{{ trim(($owner->nombres ?? '') . ' ' . ($owner->apellidos ?? '')) ?: 'Sin propietario' }}</td>
                            <td>{{ ucfirst($mascota->especie ?? 'No definida') }}</td>
                            <td>{{ $lastConsulta ? \Carbon\Carbon::parse($lastConsulta)->format('d/m/Y') : 'Sin registro' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="records-table__empty">Aún no existen mascotas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($mascotas instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="records-panel__pagination">
                {{ $mascotas->links() }}
            </div>
        @endif
    </section>
@endsection

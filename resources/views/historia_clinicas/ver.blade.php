@extends('layouts.app')

@push('styles')
<style>
    body.dashboard-layout {
        background: #f3f4f6;
    }

    .historia-view {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        padding: 32px;
        gap: 24px;
    }

    .historia-view__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .historia-view__titles {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .historia-view__badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: #2563eb;
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .historia-view__title {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .historia-view__meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        padding: 24px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 45px -20px rgba(37, 99, 235, 0.35);
    }

    .historia-view__meta-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .historia-view__meta-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
    }

    .historia-view__meta-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
    }

    .historia-view__iframe-wrapper {
        flex: 1;
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 50px -20px rgba(59, 130, 246, 0.35);
    }

    .historia-view__iframe {
        width: 100%;
        height: calc(100vh - 260px);
        border: none;
    }

    .historia-view__download {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 999px;
        background: #111827;
        color: #fff;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.2s ease-in-out, transform 0.2s ease-in-out;
    }

    .historia-view__download:hover,
    .historia-view__download:focus-visible {
        background: #1f2937;
        transform: translateY(-1px);
    }

    .historia-view__download i {
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .historia-view {
            padding: 24px 16px;
        }

        .historia-view__title {
            font-size: 1.6rem;
        }

        .historia-view__iframe {
            height: calc(100vh - 220px);
        }
    }
</style>
@endpush

@section('content')
    <main class="historia-view">
        <header class="historia-view__header">
            <div class="historia-view__titles">
                <span class="historia-view__badge"><i class="fas fa-notes-medical"></i> Historia clínica</span>
                <h1 class="historia-view__title">{{ optional($historia->mascota)->nombre ?? 'Paciente' }} · {{ $codigo }}</h1>
            </div>
            <a class="historia-view__download" href="{{ $downloadUrl }}">
                <i class="fas fa-download"></i> Descargar PDF
            </a>
        </header>

        <section class="historia-view__meta">
            <div class="historia-view__meta-item">
                <span class="historia-view__meta-label">Propietario</span>
                <span class="historia-view__meta-value">{{ trim((optional(optional($historia->mascota)->propietario)->nombres ?? '') . ' ' . (optional(optional($historia->mascota)->propietario)->apellidos ?? '')) ?: '—' }}</span>
            </div>
            <div class="historia-view__meta-item">
                <span class="historia-view__meta-label">Mascota</span>
                <span class="historia-view__meta-value">{{ optional($historia->mascota)->nombre ?? '—' }} ({{ optional($historia->mascota)->especie ?? '—' }})</span>
            </div>
            <div class="historia-view__meta-item">
                <span class="historia-view__meta-label">Código HC</span>
                <span class="historia-view__meta-value">{{ $codigo }}</span>
            </div>
            <div class="historia-view__meta-item">
                <span class="historia-view__meta-label">Fecha de apertura</span>
                <span class="historia-view__meta-value">{{ optional($historia->fecha_apertura)->format('d/m/Y') ?? '—' }}</span>
            </div>
        </section>

        <div class="historia-view__iframe-wrapper">
            <iframe class="historia-view__iframe" src="{{ $pdfUrl }}" title="Historia clínica {{ $codigo }}" loading="lazy"></iframe>
        </div>
    </main>
@endsection

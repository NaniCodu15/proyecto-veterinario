<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historia clínica {{ $codigo }}</title>
    <style>
        @page {
            margin: 32px 36px;
        }

        body {
            font-family: 'Nunito', Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .pdf-wrapper {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .pdf-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 12px;
            border-bottom: 3px solid #2563eb;
        }

        .pdf-header__brand {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
        }

        .pdf-header__logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }

        .pdf-header__title {
            margin: 0;
            font-size: 20px;
            letter-spacing: 0.08em;
            font-weight: 800;
            color: #111827;
        }

        .pdf-header__main-title {
            flex: 1;
            margin: 0;
            text-align: center;
            font-size: 20px;
            letter-spacing: 0.08em;
            font-weight: 800;
            color: #111827;
            text-transform: uppercase;
        }

        .pdf-header__subtitle {
            margin: 2px 0 0;
            color: #4b5563;
            font-size: 11px;
        }

        .pdf-header__code {
            text-align: right;
            font-weight: 700;
            color: #2563eb;
            flex: 1;
        }

        .pdf-section {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 18px;
        }

        .pdf-section + .pdf-section {
            margin-top: 4px;
        }

        .pdf-section__title {
            margin: 0 0 12px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #2563eb;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px 16px;
        }

        .info-grid__item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .info-grid__label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #6b7280;
            font-weight: 700;
        }

        .info-grid__value {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
        }

        .consultas-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .consulta-card {
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 14px 16px;
            background: rgba(37, 99, 235, 0.04);
        }

        .consulta-card__header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 8px;
        }

        .consulta-card__title {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            color: #1d4ed8;
        }

        .consulta-card__date {
            font-size: 11px;
            color: #4b5563;
            font-weight: 600;
        }

        .consulta-card__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px 16px;
        }

        .consulta-card__block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .consulta-card__label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1f2937;
            letter-spacing: 0.1em;
        }

        .consulta-card__text {
            font-size: 12px;
            line-height: 1.4;
            color: #1f2937;
            margin: 0;
        }

        .tratamientos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }

        .tratamientos-table th,
        .tratamientos-table td {
            border: 1px solid #cbd5f5;
            padding: 6px;
            text-align: left;
        }

        .tratamientos-table th {
            background: rgba(37, 99, 235, 0.12);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 10px;
            color: #1d4ed8;
        }

        .observaciones {
            margin-top: 8px;
            padding: 10px 12px;
            background: #fff;
            border: 1px dashed #93c5fd;
            border-radius: 8px;
            font-size: 11px;
            color: #1f2937;
        }

        .sin-consultas {
            padding: 24px;
            text-align: center;
            border: 1px dashed #d1d5db;
            border-radius: 12px;
            color: #6b7280;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="pdf-wrapper">
        <header class="pdf-header">
            <div class="pdf-header__brand">
                @if ($logoPath && file_exists($logoPath))
                    <img class="pdf-header__logo" src="{{ $logoPath }}" alt="Logo">
                @endif
                <div>
                    <h1 class="pdf-header__title">HISTORIA CLÍNICA</h1>
                    <p class="pdf-header__subtitle">Emitido el {{ $fecha_emision }}</p>
                </div>
            </div>
            <h1 class="pdf-header__main-title">HOSPITAL VETERINARIO</h1>
            <div class="pdf-header__code">
                <div>Código HC</div>
                <div style="font-size: 16px;">{{ $codigo }}</div>
            </div>
        </header>

        <section class="pdf-section">
            <h2 class="pdf-section__title">Datos de la mascota</h2>
            <div class="info-grid">
                <div class="info-grid__item">
                    <span class="info-grid__label">Nombre</span>
                    <span class="info-grid__value">{{ $mascota['nombre'] }}</span>
                </div>
                <div class="info-grid__item">
                    <span class="info-grid__label">Especie / Raza</span>
                    <span class="info-grid__value">{{ $mascota['especie'] }} · {{ $mascota['raza'] }}</span>
                </div>
                <div class="info-grid__item">
                    <span class="info-grid__label">Sexo</span>
                    <span class="info-grid__value">{{ $mascota['sexo'] }}</span>
                </div>
                <div class="info-grid__item">
                    <span class="info-grid__label">Edad</span>
                    <span class="info-grid__value">{{ $mascota['edad'] }}</span>
                </div>
                <div class="info-grid__item">
                    <span class="info-grid__label">Peso actual</span>
                    <span class="info-grid__value">{{ $mascota['peso'] }}</span>
                </div>
                <div class="info-grid__item">
                    <span class="info-grid__label">Fecha de apertura</span>
                    <span class="info-grid__value">{{ $fecha_apertura }}</span>
                </div>
            </div>
        </section>

        <section class="pdf-section">
            <h2 class="pdf-section__title">Datos del propietario</h2>
            <div class="info-grid">
                <div class="info-grid__item">
                    <span class="info-grid__label">Nombre</span>
                    <span class="info-grid__value">{{ $propietario['nombre'] }}</span>
                </div>
                <div class="info-grid__item">
                    <span class="info-grid__label">Documento</span>
                    <span class="info-grid__value">{{ $propietario['dni'] }}</span>
                </div>
                <div class="info-grid__item">
                    <span class="info-grid__label">Teléfono</span>
                    <span class="info-grid__value">{{ $propietario['telefono'] }}</span>
                </div>
                <div class="info-grid__item">
                    <span class="info-grid__label">Dirección</span>
                    <span class="info-grid__value">{{ $propietario['direccion'] }}</span>
                </div>
            </div>
        </section>

        <section class="pdf-section">
            <h2 class="pdf-section__title">Consultas, diagnósticos y tratamientos</h2>
            @if($consultas->isEmpty())
                <div class="sin-consultas">No se registran consultas para esta historia clínica.</div>
            @else
                <div class="consultas-list">
                    @foreach($consultas as $index => $consulta)
                        <article class="consulta-card">
                            <div class="consulta-card__header">
                                <h3 class="consulta-card__title">Consulta {{ $index + 1 }}</h3>
                                <span class="consulta-card__date">
                                    {{ $consulta['fecha'] ?? '—' }}
                                    @if(!empty($consulta['hora']))
                                        · {{ $consulta['hora'] }}
                                    @endif
                                </span>
                            </div>
                            <div class="consulta-card__grid">
                                <div class="consulta-card__block">
                                    <span class="consulta-card__label">Síntomas</span>
                                    <p class="consulta-card__text">{{ $consulta['sintomas'] ?? '—' }}</p>
                                </div>
                                <div class="consulta-card__block">
                                    <span class="consulta-card__label">Diagnóstico</span>
                                    <p class="consulta-card__text">{{ $consulta['diagnostico'] ?? '—' }}</p>
                                </div>
                                <div class="consulta-card__block">
                                    <span class="consulta-card__label">Tratamiento</span>
                                    <p class="consulta-card__text">{{ $consulta['tratamiento'] ?? '—' }}</p>
                                </div>
                                <div class="consulta-card__block">
                                    <span class="consulta-card__label">Peso / Temperatura</span>
                                    <p class="consulta-card__text">
                                        {{ $consulta['peso'] ? $consulta['peso'] . ' kg' : '—' }}
                                        @if($consulta['temperatura'])
                                            · {{ $consulta['temperatura'] }} °C
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($consulta['tratamientos_detallados']->isNotEmpty())
                                <table class="tratamientos-table">
                                    <thead>
                                    <tr>
                                        <th>Medicamento</th>
                                        <th>Dosis</th>
                                        <th>Duración</th>
                                        <th>Indicaciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($consulta['tratamientos_detallados'] as $tratamiento)
                                        <tr>
                                            <td>{{ $tratamiento['medicamento'] ?? '—' }}</td>
                                            <td>{{ $tratamiento['dosis'] ?? '—' }}</td>
                                            <td>{{ $tratamiento['duracion'] ?? '—' }}</td>
                                            <td>{{ $tratamiento['indicaciones'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif

                            @if(!empty($consulta['observaciones']))
                                <div class="observaciones">
                                    <strong>Observaciones:</strong>
                                    <div>{{ $consulta['observaciones'] }}</div>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</body>
</html>

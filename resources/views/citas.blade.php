@extends('dashboard')

@php($css = 'citas.css')
@php($js = 'citas.js')

@section('title', 'Gestión de citas')

@section('content')
    <!-- Cabecera del módulo de citas -->
    <section class="module-header">
        <div>
            <p class="module-header__eyebrow">Agenda inteligente</p>
            <h2 class="module-header__title">Programación de citas</h2>
            <p class="module-header__subtitle">Coordina nuevos compromisos, valida disponibilidad y evita cruces.</p>
        </div>
        <button class="btn btn-primary" id="btnAbrirModalCita">
            <i class="fas fa-calendar-plus"></i>
            Registrar cita
        </button>
    </section>

    <!-- Formulario de registro -->
    <section class="module-card">
        <header class="module-card__header">
            <h3 class="module-card__title">Datos de la cita</h3>
            <span class="module-card__chip">Validación automática</span>
        </header>
        <form class="cita-form" id="formCitas">
            <div class="cita-form__grid">
                <label>
                    <span>Mascota</span>
                    <input type="text" name="mascota" placeholder="Nombre de la mascota" required>
                </label>
                <label>
                    <span>Propietario</span>
                    <input type="text" name="propietario" placeholder="Nombre del propietario" required>
                </label>
                <label>
                    <span>Motivo</span>
                    <input type="text" name="motivo" placeholder="Consulta general" required>
                </label>
                <label>
                    <span>Fecha</span>
                    <input type="date" name="fecha" required>
                </label>
                <label>
                    <span>Hora</span>
                    <input type="time" name="hora" required>
                </label>
                <label>
                    <span>Notas</span>
                    <textarea name="notas" rows="3" placeholder="Indicaciones adicionales"></textarea>
                </label>
            </div>
            <div class="cita-form__footer">
                <button type="reset" class="btn btn-outline">Limpiar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </section>

    <!-- Tabla de disponibilidad -->
    <section class="module-card">
        <header class="module-card__header">
            <div>
                <h3 class="module-card__title">Disponibilidad semanal</h3>
                <p class="module-card__subtitle">Controla la ocupación diaria de la agenda.</p>
            </div>
            <div class="module-card__actions">
                <button class="btn btn-secondary" id="btnRefrescarAgenda">
                    <i class="fas fa-sync"></i>
                    Actualizar
                </button>
            </div>
        </header>
        <div class="agenda-grid" id="agendaGrid">
            <p class="agenda-grid__empty">Aún no se han generado bloques de agenda.</p>
        </div>
    </section>
@endsection

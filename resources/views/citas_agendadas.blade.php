@extends('dashboard')

@php($css = 'citas_agendadas.css')
@php($js = 'citas_agendadas.js')

@section('title', 'Citas agendadas')

@section('content')
    <!-- Encabezado -->
    <section class="module-header">
        <div>
            <p class="module-header__eyebrow">Seguimiento diario</p>
            <h2 class="module-header__title">Citas agendadas</h2>
            <p class="module-header__subtitle">Monitorea el estado de cada cita y gestiona los cambios en segundos.</p>
        </div>
        <div class="module-header__filters">
            <input type="search" placeholder="Buscar cita" id="busquedaCitas">
            <select id="estadoCita">
                <option value="all">Todos los estados</option>
                <option value="pending">Pendiente</option>
                <option value="done">Atendida</option>
                <option value="cancelled">Cancelada</option>
            </select>
        </div>
    </section>

    <!-- Tablero de citas -->
    <section class="citas-board">
        <div class="citas-column" data-status="pending">
            <header>
                <h3>Pendientes</h3>
                <span class="citas-column__count" id="countPending">0</span>
            </header>
            <div class="citas-column__body" id="columnPending">
                <p class="citas-column__empty">Sin registros pendientes.</p>
            </div>
        </div>
        <div class="citas-column" data-status="done">
            <header>
                <h3>Atendidas</h3>
                <span class="citas-column__count" id="countDone">0</span>
            </header>
            <div class="citas-column__body" id="columnDone">
                <p class="citas-column__empty">Sin registros atendidos.</p>
            </div>
        </div>
        <div class="citas-column" data-status="cancelled">
            <header>
                <h3>Canceladas</h3>
                <span class="citas-column__count" id="countCancelled">0</span>
            </header>
            <div class="citas-column__body" id="columnCancelled">
                <p class="citas-column__empty">Sin registros cancelados.</p>
            </div>
        </div>
    </section>
@endsection

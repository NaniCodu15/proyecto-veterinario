@extends('dashboard')

@php($css = 'historias_registradas.css')
@php($js = 'historias_registradas.js')

@section('title', 'Historias registradas')

@section('content')
    <!-- Resumen del repositorio -->
    <section class="module-header">
        <div>
            <p class="module-header__eyebrow">Repositorio de pacientes</p>
            <h2 class="module-header__title">Historias registradas</h2>
            <p class="module-header__subtitle">Explora los expedientes creados y mantén un seguimiento detallado.</p>
        </div>
        <div class="module-header__filters">
            <label>
                <span class="sr-only">Ordenar por</span>
                <select id="ordenHistorias">
                    <option value="recent">Más recientes</option>
                    <option value="older">Más antiguas</option>
                    <option value="alphabetic">Alfabético</option>
                </select>
            </label>
            <label>
                <span class="sr-only">Estado</span>
                <select id="estadoHistorias">
                    <option value="all">Todos</option>
                    <option value="active">Activas</option>
                    <option value="archived">Archivadas</option>
                </select>
            </label>
        </div>
    </section>

    <!-- Listado tipo cards -->
    <section class="historias-grid" id="historiasGrid">
        <article class="historia-card historia-card--empty">
            <p>Aún no se han cargado historias.</p>
            <span>Utiliza los filtros o registra una nueva historia para visualizarla aquí.</span>
        </article>
    </section>
@endsection

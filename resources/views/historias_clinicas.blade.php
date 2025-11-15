@extends('dashboard')

@php($css = 'historias_clinicas.css')
@php($js = 'historias_clinicas.js')

@section('title', 'Historias clínicas')

@section('content')
    <!-- Encabezado del módulo -->
    <section class="module-header">
        <div>
            <p class="module-header__eyebrow">Módulo clínico</p>
            <h2 class="module-header__title">Gestión integral de historias</h2>
            <p class="module-header__subtitle">
                Registra antecedentes, evoluciones, consultas y alertas asociadas a cada paciente en tiempo real.
            </p>
        </div>
        <button class="btn btn-primary" id="btnNuevaHistoria">
            <i class="fas fa-plus"></i>
            Crear historia
        </button>
    </section>

    <!-- Formulario compacto para historias -->
    <section class="module-card">
        <header class="module-card__header">
            <h3 class="module-card__title">Nueva historia clínica</h3>
            <span class="module-card__chip">Registro guiado</span>
        </header>
        <form class="historia-form" id="formHistoriaClinica">
            <div class="historia-form__group">
                <label for="numero_historia">Número</label>
                <input type="text" id="numero_historia" name="numero_historia" placeholder="HC-0001" required>
            </div>
            <div class="historia-form__group">
                <label for="paciente">Paciente</label>
                <input type="text" id="paciente" name="paciente" placeholder="Nombre de la mascota" required>
            </div>
            <div class="historia-form__group">
                <label for="propietario">Propietario</label>
                <input type="text" id="propietario" name="propietario" placeholder="Dueño principal" required>
            </div>
            <div class="historia-form__group">
                <label for="notas">Notas iniciales</label>
                <textarea id="notas" name="notas" rows="3" placeholder="Antecedentes, alergias o indicaciones previas"></textarea>
            </div>
            <div class="historia-form__footer">
                <button type="reset" class="btn btn-outline">Limpiar</button>
                <button type="submit" class="btn btn-primary">Guardar historia</button>
            </div>
        </form>
    </section>

    <!-- Panel de historias recientes -->
    <section class="module-card">
        <header class="module-card__header">
            <div>
                <h3 class="module-card__title">Historias recientes</h3>
                <p class="module-card__subtitle">Organiza y consulta rápidamente los registros creados.</p>
            </div>
            <div class="module-card__actions">
                <input type="search" placeholder="Buscar por mascota o propietario" id="busquedaHistorias">
                <button class="btn btn-secondary" id="btnFiltrarHistorias">
                    <i class="fas fa-filter"></i>
                    Filtros
                </button>
            </div>
        </header>
        <div class="historia-table__wrapper">
            <table class="historia-table" aria-live="polite">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Mascota</th>
                        <th>Propietario</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="historiaTableBody">
                    <tr>
                        <td colspan="5" class="historia-table__empty">No hay historias registradas aún.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection

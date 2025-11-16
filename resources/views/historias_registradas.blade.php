        <div id="section-historias-registradas" class="section">
            <div class="historias-registradas">
                <div class="historias-registradas__header">
                    <div class="historias-registradas__header-content">
                        <span class="historias-registradas__eyebrow">Panel de historias</span>
                        <h1 class="historias-registradas__title titulo">Historias Registradas</h1>
                        <p class="historias-registradas__subtitle">Consulta, edita y coordina la información clínica de tus pacientes en una vista cuidada y cómoda.</p>
                        <div class="historias-registradas__search" role="search">
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
                    <button type="button" class="historias-registradas__create-btn" id="btnIrCrearHistoria">
                        <span class="historias-registradas__create-icon" aria-hidden="true"><i class="fas fa-plus"></i></span>
                        <span class="historias-registradas__create-label">Crear nueva historia</span>
                    </button>
                </div>

                <div class="alert historias-registradas__alert" role="status" aria-live="polite" data-historia-mensaje hidden></div>

                <div class="historias-registradas__grid" id="tablaHistorias">
                    <div class="historias-registradas__empty">
                        <i class="fas fa-folder-open"></i>
                        <p>No hay historias clínicas registradas todavía.</p>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script src="{{ asset('js/historias_registradas.js') }}"></script>
@endpush

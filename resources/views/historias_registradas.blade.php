        <div id="section-historias-registradas" class="section">
            {{-- Mosaico con filtro y listado responsive de historias ya existentes --}}
            <div class="historias-registradas">
                <div class="historias-registradas__header">
                    <div class="historias-registradas__header-content">
                        <span class="historias-registradas__eyebrow">Panel de historias</span>
                        <h1 class="historias-registradas__title titulo">Historias Registradas</h1>
                        <p class="historias-registradas__subtitle">Consulta, edita y coordina la información clínica de tus pacientes en una vista cuidada y cómoda.</p>
                        {{-- Campo de búsqueda para filtrar historias en vivo --}}
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

                {{-- Contenedor para mostrar feedback después de crear o actualizar historias --}}
                <div class="alert historias-registradas__alert" role="status" aria-live="polite" data-historia-mensaje hidden></div>

                {{-- Grid reactivo donde se renderizan las tarjetas de cada historia --}}
                <div class="historias-registradas__grid" id="tablaHistorias">
                    <div class="historias-registradas__empty">
                        <i class="fas fa-folder-open"></i>
                        <p>No hay historias clínicas registradas todavía.</p>
                    </div>
                </div>
            </div>
        </div>

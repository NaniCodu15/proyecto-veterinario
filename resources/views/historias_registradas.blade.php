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

        <!-- MODAL DETALLE DE HISTORIA Y CONSULTAS -->
        <div id="modalConsultas" class="modal modal--historia" aria-hidden="true">
            <div class="modal-content modal-content--historia">
                <span class="close" data-close="consultas">&times;</span>
                <div class="historia-detalle">
                    <div class="historia-detalle__header">
                        <div>
                            <span class="historia-detalle__badge"><i class="fas fa-notes-medical"></i> Historia clínica</span>
                            <h2 class="historia-detalle__title" data-detalle-historia="titulo">Historia clínica</h2>
                            <p class="historia-detalle__subtitle" data-detalle-historia="subtitulo">—</p>
                        </div>
                    </div>

                    <div class="historia-detalle__info-grid">
                        <div class="historia-detalle__info-item">
                            <span>Propietario</span>
                            <strong data-detalle-historia="propietario">—</strong>
                            <small data-detalle-historia="dni">DNI —</small>
                        </div>
                        <div class="historia-detalle__info-item">
                            <span>Contacto</span>
                            <strong data-detalle-historia="telefono">—</strong>
                            <small data-detalle-historia="direccion">—</small>
                        </div>
                        <div class="historia-detalle__info-item">
                            <span>Mascota</span>
                            <strong data-detalle-historia="mascota">—</strong>
                            <small data-detalle-historia="especie">—</small>
                        </div>
                        <div class="historia-detalle__info-item">
                            <span>Peso inicial</span>
                            <strong data-detalle-historia="peso">—</strong>
                            <small data-detalle-historia="fecha_apertura">Apertura —</small>
                        </div>
                    </div>

                    <div class="historia-detalle__body">
                        <div class="historia-detalle__tabs" role="tablist" aria-label="Secciones de la historia clínica">
                            <button type="button" class="historia-detalle__tab is-active" id="tabRegistroConsultas" data-tab-target="registro" role="tab" aria-controls="panelRegistroConsultas" aria-selected="true" tabindex="0">
                                Registrar consulta
                            </button>
                            <button type="button" class="historia-detalle__tab" id="tabListadoConsultas" data-tab-target="consultas" role="tab" aria-controls="panelListadoConsultas" aria-selected="false" tabindex="-1">
                                Consultas registradas
                            </button>
                        </div>

                        <section id="panelRegistroConsultas" class="historia-detalle__form historia-detalle__panel is-active" data-tab-content="registro" role="tabpanel" aria-labelledby="tabRegistroConsultas">
                            <div class="historia-detalle__section-header">
                                <h3>Registrar nueva consulta</h3>
                                <p>Documenta la evolución del paciente en cada visita.</p>
                            </div>
                            <div id="consultaMensaje" class="consulta-alert" role="status" aria-live="polite" hidden></div>
                            <form id="formConsulta" class="consulta-form" novalidate>
                                <input type="hidden" id="consultaHistoriaId" name="id_historia">
                                <div class="consulta-form__grid">
                                    <div class="form-group">
                                        <label for="consultaFecha">Fecha de la consulta</label>
                                        <input type="date" id="consultaFecha" name="fecha_consulta" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="consultaPeso">Peso (kg)</label>
                                        <input type="number" id="consultaPeso" name="peso" step="0.01" min="0">
                                    </div>
                                    <div class="form-group">
                                        <label for="consultaTemperatura">Temperatura (°C)</label>
                                        <input type="number" id="consultaTemperatura" name="temperatura" step="0.1">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="consultaSintomas">Síntomas</label>
                                    <textarea id="consultaSintomas" name="sintomas" rows="2" placeholder="Describe signos clínicos observados"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="consultaDiagnostico">Diagnóstico</label>
                                    <textarea id="consultaDiagnostico" name="diagnostico" rows="2" placeholder="Resumen del diagnóstico"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="consultaTratamiento">Tratamiento</label>
                                    <textarea id="consultaTratamiento" name="tratamiento" rows="2" placeholder="Medicaciones o procedimientos indicados"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="consultaObservaciones">Observaciones</label>
                                    <textarea id="consultaObservaciones" name="observaciones" rows="2" placeholder="Notas adicionales sobre la atención"></textarea>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i>
                                        Guardar consulta
                                    </button>
                                </div>
                            </form>
                        </section>
                        <section id="panelListadoConsultas" class="historia-detalle__panel historia-detalle__panel--consultas" data-tab-content="consultas" role="tabpanel" aria-labelledby="tabListadoConsultas" hidden aria-hidden="true">
                            <div class="historia-detalle__section-header">
                                <h3>Consultas registradas</h3>
                                <p>Seguimiento cronológico de la atención brindada.</p>
                            </div>
                            <div class="historia-detalle__timeline">
                                <ul id="listaConsultas" class="historia-detalle__timeline-list"></ul>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>

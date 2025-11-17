        {{-- Sección con el listado y gestión de citas agendadas --}}
        <div id="section-citas-agendadas" class="section">
            {{-- Tarjeta de agenda de citas --}}
            <section class="citas-card citas-card--list" id="listadoCitasCard">
                <div class="citas-card__header">
                    <div>
                        <h2>Agenda de citas</h2>
                        <p>Controla el estado y seguimiento de cada cita programada.</p>
                    </div>
                    <div class="citas-card__icon" aria-hidden="true"><i class="fas fa-clipboard-list"></i></div>
                </div>

                {{-- Alerta para mostrar mensajes del listado de citas --}}
                <div id="citasListadoMensaje" class="citas-alert" role="status" aria-live="polite" hidden></div>

                <div class="citas-toolbar">
                    {{-- Buscador de citas por mascota o propietario --}}
                    <label for="buscarCitas" class="citas-search">
                        <i class="fas fa-search"></i>
                        <input type="search" id="buscarCitas" placeholder="Buscar por mascota o propietario">
                    </label>
                </div>

                <div class="citas-table-wrapper">
                    <div class="citas-table-container">
                        {{-- Tabla con citas registradas --}}
                        <table class="citas-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mascota</th>
                                    <th>Propietario</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Motivo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaCitas">
                                <tr class="citas-table__empty">
                                    <td colspan="8">No hay citas registradas todavía.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
    </div>
</section>

@push('scripts')
    {{-- Script para administración de citas agendadas --}}
    <script src="{{ asset('js/citas_agendadas.js') }}"></script>
@endpush
        </div>

        {{-- Modal para mostrar detalle completo de la cita --}}
        <div id="modalDetalleCita" class="modal modal--cita" aria-hidden="true">
            <div class="modal-content modal-content--cita">
                <span class="close" data-close="detalleCita">&times;</span>
                <h2>Detalle de la cita</h2>
                <div class="cita-detalle">
                    <div class="cita-detalle__row"><span class="cita-detalle__label">ID</span><span class="cita-detalle__value" data-detalle="id">—</span></div>
                    <div class="cita-detalle__row"><span class="cita-detalle__label">Historia clínica</span><span class="cita-detalle__value" data-detalle="numero_historia">—</span></div>
                    <div class="cita-detalle__row"><span class="cita-detalle__label">Mascota</span><span class="cita-detalle__value" data-detalle="mascota">—</span></div>
                    <div class="cita-detalle__row"><span class="cita-detalle__label">Propietario</span><span class="cita-detalle__value" data-detalle="propietario">—</span></div>
                    <div class="cita-detalle__row"><span class="cita-detalle__label">Teléfono</span><span class="cita-detalle__value" data-detalle="propietario_telefono">—</span></div>
                    <div class="cita-detalle__row"><span class="cita-detalle__label">Fecha</span><span class="cita-detalle__value" data-detalle="fecha_legible">—</span></div>
                    <div class="cita-detalle__row"><span class="cita-detalle__label">Hora</span><span class="cita-detalle__value" data-detalle="hora">—</span></div>
                    <div class="cita-detalle__row"><span class="cita-detalle__label">Estado</span><span class="cita-detalle__value" data-detalle="estado">—</span></div>
                    <div class="cita-detalle__row cita-detalle__row--full"><span class="cita-detalle__label">Motivo</span><span class="cita-detalle__value" data-detalle="motivo">—</span></div>
                </div>
            </div>
        </div>

        {{-- Modal para actualizar el estado de una cita --}}
        <div id="modalEstadoCita" class="modal modal--cita" aria-hidden="true">
            <div class="modal-content modal-content--cita">
                <span class="close" data-close="estadoCita">&times;</span>
                <h2>Actualizar estado de la cita</h2>
                <p class="cita-estado__subtitle">Selecciona el estado que refleje el seguimiento actual de la cita.</p>
                <form id="formEstadoCita" class="cita-estado-form">
                    <div class="form-group">
                        {{-- Selector de estado de seguimiento --}}
                        <label for="selectEstadoCita">Estado</label>
                        <select id="selectEstadoCita" required>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Atendida">Atendida</option>
                            <option value="Reprogramada">Reprogramada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>

                    <div id="reprogramarCampos" class="reprogramar-campos" hidden>
                        <div class="form-group">
                            {{-- Nueva fecha cuando se reprograma la cita --}}
                            <label for="citaReprogramadaFecha">Nueva fecha</label>
                            <input type="date" id="citaReprogramadaFecha">
                        </div>
                        <div class="form-group">
                            {{-- Nueva hora para citas reprogramadas --}}
                            <label for="citaReprogramadaHora">Nueva hora</label>
                            <input type="time" id="citaReprogramadaHora">
                        </div>
                    </div>
                    <div class="cita-estado-actions">
                        {{-- Acciones para cerrar o guardar el cambio de estado --}}
                        <button type="button" class="btn btn-outline" data-close="estadoCita">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>

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

                <div class="citas-grid-wrapper">
                    {{-- Listado de citas en formato línea --}}
                    <div class="citas-grid" id="tablaCitas">
                        <div class="citas-grid__empty">No hay citas registradas todavía.</div>
                    </div>
                </div>
</section>

@push('scripts')
    {{-- Script para administración de citas agendadas --}}
    <script src="{{ asset('js/citas_agendadas.js') }}"></script>
@endpush
        </div>

        {{-- Modal para editar cita --}}
        <div id="modalEditarCita" class="modal modal--cita" aria-hidden="true">
            <div class="modal-content modal-content--cita">
                <span class="close" data-close="editarCita">&times;</span>
                <h2>Editar Cita</h2>
                <div id="editarCitaMensaje" class="cita-alert" role="alert" hidden></div>
                <form id="formEditarCita" class="cita-estado-form">
                    <div class="form-group">
                        <label for="editarCitaHistoria">Historia Clínica</label>
                        <input type="text" id="editarCitaHistoria" readonly>
                    </div>

                    <div class="form-group">
                        <label for="editarCitaFecha">Fecha de la cita</label>
                        <input type="date" id="editarCitaFecha" name="fecha_cita" required>
                    </div>

                    <div class="form-group">
                        <label for="editarCitaHora">Hora de la cita</label>
                        <input type="time" id="editarCitaHora" name="hora_cita" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="editarCitaMotivo">Motivo</label>
                        <textarea id="editarCitaMotivo" name="motivo" rows="4" required></textarea>
                    </div>

                    <div class="cita-estado-actions">
                        <button type="button" class="btn btn-outline" data-close="editarCita">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
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

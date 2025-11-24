        {{-- Sección para registrar nuevas citas --}}
        <div id="section-citas" class="section">
            <div class="citas-grid citas-grid--single">
                {{-- Tarjeta principal del formulario de cita --}}
                <section class="citas-card" id="registrarCitaCard">
                    <div class="citas-card__header">
                        <div>
                            <h2>Registrar Cita</h2>
                            <p>Selecciona una historia clínica existente para completar automáticamente los datos.</p>
                        </div>
                        <div class="citas-card__icon" aria-hidden="true"><i class="fas fa-calendar-circle-plus"></i></div>
                    </div>

                    {{-- Mensaje de retroalimentación para acciones de cita --}}
                    <div id="citaMensaje" class="cita-alert" role="alert" hidden></div>

                    {{-- Formulario de registro de cita con datos precargados --}}
                    <form id="formRegistrarCita" class="cita-form" novalidate>
                        <div class="cita-form__group">
                            {{-- Selección de la historia clínica relacionada --}}
                            <label for="historiaSelectCitas">Historia clínica</label>
                            <select id="historiaSelectCitas" name="historia" required>
                                <option value="">Selecciona una historia clínica</option>
                            </select>
                        </div>

                        <div class="cita-form__grid" aria-live="polite">
                            <div class="cita-form__group">
                                {{-- Información del propietario: nombre --}}
                                <label for="citaPropietarioNombre">Nombre del propietario</label>
                                <input type="text" id="citaPropietarioNombre" readonly>
                            </div>

                            <div class="cita-form__group">
                                {{-- Información del propietario: documento de identidad --}}
                                <label for="citaPropietarioDni">DNI del propietario</label>
                                <input type="text" id="citaPropietarioDni" readonly>
                            </div>

                            <div class="cita-form__group">
                                {{-- Información del propietario: teléfono --}}
                                <label for="citaPropietarioTelefono">Teléfono del propietario</label>
                                <input type="text" id="citaPropietarioTelefono" readonly>
                            </div>

                            <div class="cita-form__group">
                                {{-- Nombre de la mascota asociada a la cita --}}
                                <label for="citaMascotaNombre">Nombre de la mascota</label>
                                <input type="text" id="citaMascotaNombre" readonly>
    </div>
</div>

@push('scripts')
    {{-- Script para la lógica de creación y validación de citas --}}
    <script src="{{ asset('js/citas.js') }}"></script>
@endpush

                        <div class="cita-form__group">
                            {{-- Motivo principal de la visita --}}
                            <label for="citaMotivo">Motivo de la cita</label>
                            <textarea id="citaMotivo" name="motivo" placeholder="Describe brevemente el motivo de la visita" required></textarea>
                        </div>

                        <div class="cita-form__group">
                            {{-- Fecha programada para la atención --}}
                            <label for="citaFecha">Fecha de la cita</label>
                            <input type="date" id="citaFecha" name="fecha_cita" required>
                        </div>

                        <div class="cita-form__group">
                            {{-- Hora programada de la cita --}}
                            <label for="citaHora">Hora de la cita</label>
                            <input type="time" id="citaHora" name="hora_cita" required>
                        </div>

                        <div class="cita-form__actions">
                            {{-- Botón para confirmar y guardar la cita --}}
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i>
                                Guardar cita
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>

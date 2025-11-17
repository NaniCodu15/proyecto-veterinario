        {{-- Sección para creación de historias clínicas --}}
        <div id="section-historias" class="section">
            <div class="historias-create">
                <div class="historias-create__content">
                    <span class="historias-create__badge"><i class="fas fa-star"></i> Registro clínico</span>
                    <h1 class="titulo historias-create__title">Historias Clínicas</h1>
                    <p class="historias-create__text">
                        Genera nuevas historias clínicas para cada paciente y mantén un seguimiento cálido y organizado de su bienestar.
                    </p>
                    <div class="historias-create__actions">
                        <button id="btnNuevaHistoria" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Crear nueva historia
                        </button>
                    </div>
                    {{-- Alerta informativa de resultados al crear historia --}}
                    <div class="alert historias-create__alert" role="status" aria-live="polite" data-historia-mensaje hidden></div>
                </div>
                <div class="historias-create__panel">
                    <h2 class="historias-create__panel-title">Una gestión moderna y humana</h2>
                    <ul class="historias-create__benefits">
                        <li><i class="fas fa-heartbeat"></i><span>Seguimiento integral de cada visita y control preventivo.</span></li>
                        <li><i class="fas fa-user-friends"></i><span>Datos del propietario siempre a mano para comunicar novedades.</span></li>
                        <li><i class="fas fa-shield-alt"></i><span>Historial clínico seguro, centralizado y fácil de actualizar.</span></li>
                    </ul>
                </div>
            </div>

            <div class="backup-panel" id="panelBackups">
                {{-- Información de respaldo de datos --}}
                <div class="backup-panel__content">
                    <span class="backup-panel__badge"><i class="fas fa-shield-heart"></i> Seguridad de datos</span>
                    <h2 class="backup-panel__title">Copia de seguridad del sistema</h2>
                    <p class="backup-panel__text">
                        Genera un respaldo completo de la información clínica y consulta el historial de copias de seguridad
                        realizadas.
                    </p>
                </div>

                {{-- Acciones para generar y revisar backups --}}
                <div class="backup-panel__actions">
                    <button type="button" class="btn btn-primary backup-panel__button" id="btnGenerarBackup">
                        <i class="fas fa-database"></i>
                        Generar copia de seguridad
                    </button>
                    <button type="button" class="btn btn-outline backup-panel__button" id="btnVerBackups">
                        <i class="fas fa-list"></i>
                        Ver registros de copias de seguridad
                    </button>
                </div>

                <div class="alert backup-panel__alert" role="status" data-backup-mensaje hidden></div>

                {{-- Listado de registros de respaldos --}}
                <div id="backupRegistros" class="backup-log" hidden>
                    <div class="tabla-wrapper backup-log__wrapper" data-backup-wrapper hidden>
                        <table class="backup-log__table">
                            <thead>
                                <tr>
                                    <th scope="col">ID respaldo</th>
                                    <th scope="col">Fecha de respaldo</th>
                                    <th scope="col">Nombre del archivo</th>
                                    <th scope="col">Ruta del archivo</th>
                                    <th scope="col">Estado</th>
                                </tr>
                            </thead>
                            <tbody data-backup-body></tbody>
                        </table>
        </div>
    </div>
</div>

@push('scripts')
    {{-- Script dedicado al manejo de historias clínicas --}}
    <script src="{{ asset('js/historias_clinicas.js') }}"></script>
@endpush
        </div>

        {{-- Modal para crear o editar historias clínicas --}}
        <div id="modalHistoria" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="modalTitulo">Nueva Historia Clínica</h2>
                {{-- Formulario principal de historia clínica --}}
                <form id="formHistoria">
                    <div class="form-section">
                        <h3 class="form-section__title"><span>🐶</span>Datos de la mascota</h3>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                {{-- Identificador único de historia --}}
                                <label>ID de Historia Clínica:</label>
                                <input type="text" id="numero_historia" name="numero_historia" readonly>
                            </div>

                            <div class="form-group">
                                {{-- Nombre de la mascota asociada --}}
                                <label>Nombre de la Mascota:</label>
                                <input type="text" id="nombreMascota" name="nombreMascota" required>
                            </div>

                            <div class="form-group">
                                {{-- Selección de especie --}}
                                <label>Especie:</label>
                                <select id="especie" name="especie" required>
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <option value="perro">Perro</option>
                                    <option value="gato">Gato</option>
                                    <option value="otro">Otros</option>
                                </select>
                            </div>

                            <div class="form-group full-width" id="grupoEspecieOtro" style="display: none;">
                                {{-- Campo adicional para especie personalizada --}}
                                <label>Especifique la especie:</label>
                                <input type="text" id="especieOtro" name="especieOtro">
                            </div>

                            <div class="form-group">
                                {{-- Edad de la mascota en años --}}
                                <label>Edad (años):</label>
                                <input type="number" id="edad" name="edad" min="0">
                            </div>

                            <div class="form-group">
                                {{-- Raza específica del paciente --}}
                                <label>Raza:</label>
                                <input type="text" id="raza" name="raza" required>
                            </div>

                            <div class="form-group">
                                {{-- Género del paciente --}}
                                <label>Sexo:</label>
                                <select id="sexo" name="sexo" required>
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <option value="macho">Macho</option>
                                    <option value="hembra">Hembra</option>
                                </select>
                            </div>

                            <div class="form-group">
                                {{-- Peso corporal registrado --}}
                                <label>Peso:</label>
                                <input type="number" id="peso" name="peso" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section__title"><span>👤</span>Datos del propietario</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                {{-- Nombre completo del responsable --}}
                                <label>Nombre del Propietario:</label>
                                <input type="text" id="nombrePropietario" name="nombrePropietario" required>
                            </div>

                            <div class="form-group">
                                {{-- Número telefónico de contacto --}}
                                <label>Teléfono:</label>
                                <input type="text" id="telefono" name="telefono" required>
                            </div>

                            <div class="form-group">
                                {{-- Dirección del propietario --}}
                                <label>Dirección:</label>
                                <input type="text" id="direccion" name="direccion" required>
                            </div>

                            <div class="form-group">
                                {{-- Documento de identidad --}}
                                <label>DNI:</label>
                                <input type="text" id="dni" name="dni" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        {{-- Botón de guardado del formulario de historia clínica --}}
                        <button type="submit" class="btn btn-success btn-guardar">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

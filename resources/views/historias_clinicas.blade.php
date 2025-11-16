        <!-- SECCIÓN HISTORIAS CLÍNICAS -->
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
                <div class="backup-panel__content">
                    <span class="backup-panel__badge"><i class="fas fa-shield-heart"></i> Seguridad de datos</span>
                    <h2 class="backup-panel__title">Copia de seguridad del sistema</h2>
                    <p class="backup-panel__text">
                        Genera un respaldo completo de la información clínica y consulta el historial de copias de seguridad
                        realizadas.
                    </p>
                </div>

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
</div>

@push('scripts')
    <script src="{{ asset('js/historias_clinicas.js') }}"></script>
@endpush

        <div id="section-citas-agendadas" class="section">
            <section class="citas-card citas-card--list" id="listadoCitasCard">
                <div class="citas-card__header">
                    <div>
                        <h2>Agenda de citas</h2>
                        <p>Controla el estado y seguimiento de cada cita programada.</p>
                    </div>
                    <div class="citas-card__icon" aria-hidden="true"><i class="fas fa-clipboard-list"></i></div>
                </div>

                <div id="citasListadoMensaje" class="citas-alert" role="status" aria-live="polite" hidden></div>

                <div class="citas-toolbar">
                    <label for="buscarCitas" class="citas-search">
                        <i class="fas fa-search"></i>
                        <input type="search" id="buscarCitas" placeholder="Buscar por mascota o propietario">
                    </label>
                </div>

                <div class="citas-table-wrapper">
                    <div class="citas-table-container">
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
        </div>

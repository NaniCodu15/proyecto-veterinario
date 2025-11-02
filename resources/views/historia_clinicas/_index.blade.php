<div class="historias-table-card">
    @if ($historias->count())
        <table class="tabla-consultas tabla-historias">
            <thead>
                <tr>
                    <th>N° Historia</th>
                    <th>Mascota</th>
                    <th>Propietario</th>
                    <th>Fecha apertura</th>
                    <th>Resumen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historias as $historia)
                    <tr>
                        <td>
                            <span class="badge badge-historia">{{ $historia->numero_historia }}</span>
                        </td>
                        <td>
                            <div class="historia-mascota">
                                <strong>{{ $historia->mascota->nombre ?? 'Sin nombre' }}</strong>
                                <small class="historia-meta">{{ $historia->mascota->especie ?? 'N/D' }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="historia-propietario">
                                {{ optional($historia->mascota->propietario)->nombre ?? 'Sin propietario asignado' }}
                            </div>
                        </td>
                        <td>
                            <span class="historia-meta">
                                {{ optional($historia->fecha_apertura ? \Illuminate\Support\Carbon::parse($historia->fecha_apertura) : null)?->translatedFormat('d \de F Y') ?? 'Sin fecha' }}
                            </span>
                        </td>
                        <td>
                            <div class="historia-resumen">
                                @if ($historia->diagnostico)
                                    <p><strong>Diagnóstico:</strong> {{ \Illuminate\Support\Str::limit($historia->diagnostico, 80) }}</p>
                                @else
                                    <p class="historia-meta">Sin diagnóstico registrado</p>
                                @endif
                                <div class="historia-pills">
                                    @if ($historia->peso)
                                        <span class="pill">Peso: {{ $historia->peso }} kg</span>
                                    @endif
                                    @if ($historia->temperatura)
                                        <span class="pill">Temp: {{ $historia->temperatura }} °C</span>
                                    @endif
                                    @if ($historia->frecuencia_cardiaca)
                                        <span class="pill">FC: {{ $historia->frecuencia_cardiaca }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="historia-actions">
                                <button type="button" class="btn btn-sm btn-warning btnEditar" data-id="{{ $historia->id_historia }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btnEliminar" data-id="{{ $historia->id_historia }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="paginacion-historias">
            <div class="paginacion-info">
                Mostrando {{ $historias->firstItem() }} - {{ $historias->lastItem() }} de {{ $historias->total() }} historias
            </div>
            <div class="paginacion-controles">
                @if ($historias->onFirstPage())
                    <span class="page-btn disabled">Anterior</span>
                @else
                    <a href="{{ $historias->previousPageUrl() }}" class="page-btn">Anterior</a>
                @endif

                <span class="page-indicator">Página {{ $historias->currentPage() }} de {{ $historias->lastPage() }}</span>

                @if ($historias->hasMorePages())
                    <a href="{{ $historias->nextPageUrl() }}" class="page-btn">Siguiente</a>
                @else
                    <span class="page-btn disabled">Siguiente</span>
                @endif
            </div>
        </div>
    @else
        <div class="historias-empty">
            <img src="{{ asset('images/empty-state.svg') }}" alt="Sin historias" class="empty-illustration" onerror="this.style.display='none'">
            <h3>No hay historias clínicas registradas</h3>
            <p>Cuando registres una historia clínica podrás verla aquí y hacer seguimiento de su evolución.</p>
        </div>
    @endif
</div>

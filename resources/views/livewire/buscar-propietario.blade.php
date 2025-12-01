<div class="form-group full-width">
    <label for="buscarPropietario">Buscar propietario (nombre o DNI):</label>
    <div class="input-with-button">
        <input
            id="buscarPropietario"
            type="search"
            wire:model.debounce.300ms="busqueda"
            placeholder="Escribe el nombre o DNI del propietario"
            autocomplete="off"
        >
        <button type="button" class="btn btn-outline" wire:click="limpiarSeleccion">Nuevo propietario</button>
    </div>

    @if (!empty($resultados))
        <ul class="search-results">
            @foreach ($resultados as $propietario)
                <li>
                    <button type="button" wire:click="seleccionar({{ $propietario['id'] }})">
                        <span class="resultado-nombre">{{ $propietario['nombre_propietario'] ?: 'Sin nombre' }}</span>
                        <span class="resultado-dni">DNI: {{ $propietario['dni_propietario'] ?: '—' }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
</div>

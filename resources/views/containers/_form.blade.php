<div class="row">

    <div class="col-12">

        <h5 class="fw-semibold mb-3">
            Información del contenedor
        </h5>

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Número de contenedor *
        </label>

        <input type="text" name="container_number" class="form-control @error('container_number') is-invalid @enderror"
            value="{{ old('container_number', $container->container_number ?? '') }}"
            required>

        @error('container_number')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Tipo *
        </label>

        <select name="container_type" class="form-select" required>

            @foreach ([
        'DRY' => 'Seco',
        'REEFER' => 'Refrigerado',
        'OPEN_TOP' => 'Open Top',
        'FLAT_RACK' => 'Flat Rack',
        'TANK' => 'Tanque',
        'OTHER' => 'Otro',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('container_type', $container->container_type ?? 'DRY') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Tamaño *
        </label>

        <select name="container_size" class="form-select" required>

            @foreach ([
        '20FT' => '20 pies',
        '40FT' => '40 pies',
        '40HC' => '40 HC',
        '45FT' => '45 pies',
        'OTHER' => 'Otro',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('container_size', $container->container_size ?? '40FT') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Estado de carga *
        </label>

        <select name="load_status" class="form-select" required>

            @foreach ([
        'EMPTY' => 'Vacío',
        'FULL' => 'Lleno',
        'UNKNOWN' => 'No definido',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('load_status', $container->load_status ?? 'UNKNOWN') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Estado operativo *
        </label>

        <select name="operational_status" class="form-select" required>

            @foreach ([
        'AVAILABLE' => 'Disponible',
        'ASSIGNED' => 'Asignado',
        'IN_TRANSIT' => 'En tránsito',
        'AT_CLIENT' => 'En cliente',
        'AT_PORT' => 'En puerto',
        'AT_DEPOT' => 'En depósito',
        'MAINTENANCE' => 'Mantenimiento',
        'OUT_OF_SERVICE' => 'Fuera de servicio',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('operational_status', $container->operational_status ?? 'AVAILABLE') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Ubicación actual
        </label>

        <select name="current_location_id" class="form-select">

            <option value="">
                Sin ubicación definida
            </option>

            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(old('current_location_id', $container->current_location_id ?? '') == $location->id)>

                    {{ $location->name }}
                    - {{ $location->type_label }}

                </option>
            @endforeach

        </select>

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Número de sello
        </label>

        <input type="text" name="seal_number" class="form-control"
            value="{{ old('seal_number', $container->seal_number ?? '') }}">

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Naviera
        </label>

        <input type="text" name="shipping_line" class="form-control"
            value="{{ old('shipping_line', $container->shipping_line ?? '') }}">

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Última inspección
        </label>

        <input type="date" name="last_inspection_date" class="form-control"
            value="{{ old(
                'last_inspection_date',
                isset($container?->last_inspection_date) ? $container->last_inspection_date->format('Y-m-d') : '',
            ) }}">

    </div>

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Pesos
        </h5>

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Tara (kg)
        </label>

        <input type="number" step="0.01" min="0" name="tare_weight_kg" class="form-control"
            value="{{ old('tare_weight_kg', $container->tare_weight_kg ?? '') }}">

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Peso bruto máximo (kg)
        </label>

        <input type="number" step="0.01" min="0" name="max_gross_weight_kg"
            class="form-control @error('max_gross_weight_kg') is-invalid @enderror"
            value="{{ old('max_gross_weight_kg', $container->max_gross_weight_kg ?? '') }}">

        @error('max_gross_weight_kg')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

    <div class="col-12 mb-3">

        <label class="form-label">
            Observaciones
        </label>

        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $container->notes ?? '') }}</textarea>

    </div>

    <div class="col-12 mb-4">

        <div class="form-check form-switch">

            <input type="checkbox" name="is_active" value="1" id="container_active" class="form-check-input"
                @checked(old('is_active', isset($container) ? $container->is_active : true))>

            <label class="form-check-label" for="container_active">

                Contenedor activo
            </label>

        </div>

    </div>

</div>

<div class="d-flex justify-content-end gap-2">

    <a href="{{ route('containers.index') }}" class="btn btn-light">

        Cancelar
    </a>

    <button type="submit" class="btn btn-primary">

        <i class="ti ti-device-floppy me-1"></i>

        {{ isset($container) ? 'Actualizar contenedor' : 'Guardar contenedor' }}

    </button>

</div>

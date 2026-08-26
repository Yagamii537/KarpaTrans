<div class="row">

    {{-- ========================================================= --}}
    {{-- IDENTIFICACIÓN --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <h5 class="fw-semibold mb-3">
            Identificación del vehículo
        </h5>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Placa *
        </label>

        <input type="text" name="plate" class="form-control @error('plate') is-invalid @enderror"
            value="{{ old('plate', $vehicle->plate ?? '') }}" required>

        @error('plate')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Código interno
        </label>

        <input type="text" name="internal_code" class="form-control"
            value="{{ old('internal_code', $vehicle->internal_code ?? '') }}">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Tipo de vehículo *
        </label>

        <select name="vehicle_type" class="form-select" required>

            @foreach ([
        'TRACTOCAMION' => 'Tractocamión',
        'CAMION' => 'Camión',
        'CAMIONETA' => 'Camioneta',
        'OTRO' => 'Otro',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('vehicle_type', $vehicle->vehicle_type ?? 'TRACTOCAMION') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Estado operativo *
        </label>

        <select name="operational_status" class="form-select" required>

            @foreach ([
        'AVAILABLE' => 'Disponible',
        'ASSIGNED' => 'Asignado',
        'MAINTENANCE' => 'Mantenimiento',
        'OUT_OF_SERVICE' => 'Fuera de servicio',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('operational_status', $vehicle->operational_status ?? 'AVAILABLE') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Marca *
        </label>

        <input type="text" name="brand" class="form-control" value="{{ old('brand', $vehicle->brand ?? '') }}"
            required>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Modelo *
        </label>

        <input type="text" name="model" class="form-control" value="{{ old('model', $vehicle->model ?? '') }}"
            required>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Año
        </label>

        <input type="number" name="year" min="1950" max="{{ date('Y') + 1 }}" class="form-control"
            value="{{ old('year', $vehicle->year ?? '') }}">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Color
        </label>

        <input type="text" name="color" class="form-control" value="{{ old('color', $vehicle->color ?? '') }}">

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Número de chasis / VIN
        </label>

        <input type="text" name="chassis_number" class="form-control"
            value="{{ old('chassis_number', $vehicle->chassis_number ?? '') }}">

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Número de motor
        </label>

        <input type="text" name="engine_number" class="form-control"
            value="{{ old('engine_number', $vehicle->engine_number ?? '') }}">

    </div>


    {{-- ========================================================= --}}
    {{-- DATOS TÉCNICOS --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-2">
            Pesos, capacidades y dimensiones
        </h5>

        <p class="text-muted mb-3">

            Estos datos se utilizarán posteriormente
            para validar si el vehículo es apto para
            una operación.

        </p>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Tara / peso vacío (kg)
        </label>

        <input type="number" step="0.01" min="0" name="tare_weight_kg"
            class="form-control @error('tare_weight_kg') is-invalid @enderror"
            value="{{ old('tare_weight_kg', $vehicle->tare_weight_kg ?? '') }}">

        @error('tare_weight_kg')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Peso bruto (kg)
        </label>

        <input type="number" step="0.01" min="0" name="gross_weight_kg"
            class="form-control @error('gross_weight_kg') is-invalid @enderror"
            value="{{ old('gross_weight_kg', $vehicle->gross_weight_kg ?? ($vehicle->max_weight_kg ?? '')) }}">

        @error('gross_weight_kg')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Capacidad máxima de carga (kg)
        </label>

        <input type="number" step="0.01" min="0" name="max_load_capacity_kg" class="form-control"
            value="{{ old('max_load_capacity_kg', $vehicle->max_load_capacity_kg ?? '') }}">

        <small class="text-muted">
            Se comparará con el peso estimado de la OT.
        </small>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Largo (m)
        </label>

        <input type="number" step="0.01" min="0" name="length_m" class="form-control"
            value="{{ old('length_m', $vehicle->length_m ?? '') }}">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Ancho (m)
        </label>

        <input type="number" step="0.01" min="0" name="width_m" class="form-control"
            value="{{ old('width_m', $vehicle->width_m ?? '') }}">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Alto (m)
        </label>

        <input type="number" step="0.01" min="0" name="height_m" class="form-control"
            value="{{ old('height_m', $vehicle->height_m ?? '') }}">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Número de ejes
        </label>

        <input type="number" min="1" max="20" name="axles" class="form-control"
            value="{{ old('axles', $vehicle->axles ?? '') }}">

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Volumen (m³)
        </label>

        <input type="number" step="0.01" min="0" name="volume_m3" class="form-control"
            value="{{ old('volume_m3', $vehicle->volume_m3 ?? '') }}">

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Capacidad de combustible
        </label>

        <input type="number" step="0.01" min="0" name="fuel_capacity" class="form-control"
            value="{{ old('fuel_capacity', $vehicle->fuel_capacity ?? '') }}">

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Odómetro actual
        </label>

        <input type="number" step="0.01" min="0" name="current_odometer" class="form-control"
            value="{{ old('current_odometer', $vehicle->current_odometer ?? '') }}">

    </div>


    {{-- ========================================================= --}}
    {{-- PROPIEDAD --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Propiedad del vehículo
        </h5>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Tipo de propiedad *
        </label>

        <select name="ownership_type" class="form-select" required>

            @foreach ([
        'PROPIO' => 'Propio',
        'ALQUILADO' => 'Alquilado',
        'TERCERO' => 'Tercero',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('ownership_type', $vehicle->ownership_type ?? 'PROPIO') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Propietario
        </label>

        <input type="text" name="owner_name" class="form-control"
            value="{{ old('owner_name', $vehicle->owner_name ?? '') }}">

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Identificación propietario
        </label>

        <input type="text" name="owner_identification" class="form-control"
            value="{{ old('owner_identification', $vehicle->owner_identification ?? '') }}">

    </div>


    {{-- ========================================================= --}}
    {{-- DOCUMENTOS --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Documentación
        </h5>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Vencimiento matrícula
        </label>

        <input type="date" name="registration_expiration_date" class="form-control"
            value="{{ old(
                'registration_expiration_date',
                isset($vehicle?->registration_expiration_date) ? $vehicle->registration_expiration_date->format('Y-m-d') : '',
            ) }}">

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Vencimiento revisión técnica
        </label>

        <input type="date" name="technical_review_expiration_date" class="form-control"
            value="{{ old(
                'technical_review_expiration_date',
                isset($vehicle?->technical_review_expiration_date)
                    ? $vehicle->technical_review_expiration_date->format('Y-m-d')
                    : '',
            ) }}">

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Vencimiento seguro
        </label>

        <input type="date" name="insurance_expiration_date" class="form-control"
            value="{{ old(
                'insurance_expiration_date',
                isset($vehicle?->insurance_expiration_date) ? $vehicle->insurance_expiration_date->format('Y-m-d') : '',
            ) }}">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Foto
        </label>

        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="form-control">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Matrícula
        </label>

        <input type="file" name="registration_document" accept=".pdf,.jpg,.jpeg,.png" class="form-control">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Seguro
        </label>

        <input type="file" name="insurance_document" accept=".pdf,.jpg,.jpeg,.png" class="form-control">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Revisión técnica
        </label>

        <input type="file" name="technical_review_document" accept=".pdf,.jpg,.jpeg,.png" class="form-control">

    </div>


    {{-- ========================================================= --}}
    {{-- FINAL --}}
    {{-- ========================================================= --}}

    <div class="col-12 mb-3">

        <label class="form-label">
            Observaciones
        </label>

        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $vehicle->notes ?? '') }}</textarea>

    </div>


    <div class="col-12 mb-4">

        <div class="form-check form-switch">

            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                @checked(old('is_active', isset($vehicle) ? $vehicle->is_active : true))>

            <label for="is_active" class="form-check-label">

                Vehículo activo

            </label>

        </div>

    </div>

</div>


<div class="d-flex justify-content-end gap-2">

    <a href="{{ route('vehicles.index') }}" class="btn btn-light">

        Cancelar

    </a>

    <button type="submit" class="btn btn-primary">

        <i class="ti ti-device-floppy me-1"></i>

        {{ isset($vehicle) ? 'Actualizar vehículo' : 'Guardar vehículo' }}

    </button>

</div>

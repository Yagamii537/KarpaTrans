<div class="row">

    <div class="col-12">
        <h5 class="fw-semibold mb-3">Información general</h5>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Placa *</label>
        <input type="text" name="plate" class="form-control @error('plate') is-invalid @enderror"
            value="{{ old('plate', $vehicle->plate ?? '') }}" required>

        @error('plate')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Código interno</label>
        <input type="text" name="internal_code" class="form-control"
            value="{{ old('internal_code', $vehicle->internal_code ?? '') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Tipo *</label>
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
        <label class="form-label">Estado operativo *</label>
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
        <label class="form-label">Marca *</label>
        <input type="text" name="brand" class="form-control" value="{{ old('brand', $vehicle->brand ?? '') }}"
            required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Modelo *</label>
        <input type="text" name="model" class="form-control" value="{{ old('model', $vehicle->model ?? '') }}"
            required>
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Año</label>
        <input type="number" name="year" min="1950" max="{{ date('Y') + 1 }}" class="form-control"
            value="{{ old('year', $vehicle->year ?? '') }}">
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Color</label>
        <input type="text" name="color" class="form-control" value="{{ old('color', $vehicle->color ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Número de chasis/VIN</label>
        <input type="text" name="chassis_number" class="form-control"
            value="{{ old('chassis_number', $vehicle->chassis_number ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Número de motor</label>
        <input type="text" name="engine_number" class="form-control"
            value="{{ old('engine_number', $vehicle->engine_number ?? '') }}">
    </div>

    <div class="col-12">
        <hr>
        <h5 class="fw-semibold mb-3">Propiedad y capacidad</h5>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Propiedad</label>
        <select name="ownership_type" class="form-select">
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
        <label class="form-label">Propietario</label>
        <input type="text" name="owner_name" class="form-control"
            value="{{ old('owner_name', $vehicle->owner_name ?? '') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Identificación propietario</label>
        <input type="text" name="owner_identification" class="form-control"
            value="{{ old('owner_identification', $vehicle->owner_identification ?? '') }}">
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Combustible (gal)</label>
        <input type="number" step="0.01" name="fuel_capacity" class="form-control"
            value="{{ old('fuel_capacity', $vehicle->fuel_capacity ?? '') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Kilometraje actual</label>
        <input type="number" step="0.01" name="current_odometer" class="form-control"
            value="{{ old('current_odometer', $vehicle->current_odometer ?? '') }}">
    </div>

    <div class="col-12">
        <hr>

        <h5 class="fw-semibold mb-3">
            Pesos y medidas
        </h5>

        <p class="text-muted">
            Información técnica utilizada para validar la compatibilidad
            del vehículo con cargas y operaciones.
        </p>
    </div>

    <div class="col-md-4 mb-3">

        <label for="tare_weight_kg" class="form-label">

            Tara / peso del vehículo (kg)
        </label>

        <input type="number" step="0.01" min="0" name="tare_weight_kg" id="tare_weight_kg"
            class="form-control @error('tare_weight_kg') is-invalid @enderror"
            value="{{ old('tare_weight_kg', $vehicle->tare_weight_kg ?? '') }}">

        @error('tare_weight_kg')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

    <div class="col-md-4 mb-3">

        <label for="max_weight_kg" class="form-label">

            Peso máximo permitido (kg)
        </label>

        <input type="number" step="0.01" min="0" name="max_weight_kg" id="max_weight_kg"
            class="form-control @error('max_weight_kg') is-invalid @enderror"
            value="{{ old('max_weight_kg', $vehicle->max_weight_kg ?? '') }}">

        @error('max_weight_kg')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Capacidad útil estimada
        </label>

        <input type="text" id="payload_display" class="form-control" readonly value="">
    </div>

    <div class="col-md-4 mb-3">

        <label for="length_m" class="form-label">

            Largo (m)
        </label>

        <input type="number" step="0.01" min="0" name="length_m" id="length_m" class="form-control"
            value="{{ old('length_m', $vehicle->length_m ?? '') }}">

    </div>

    <div class="col-md-4 mb-3">

        <label for="width_m" class="form-label">

            Ancho (m)
        </label>

        <input type="number" step="0.01" min="0" name="width_m" id="width_m" class="form-control"
            value="{{ old('width_m', $vehicle->width_m ?? '') }}">

    </div>

    <div class="col-md-4 mb-3">

        <label for="height_m" class="form-label">

            Alto (m)
        </label>

        <input type="number" step="0.01" min="0" name="height_m" id="height_m" class="form-control"
            value="{{ old('height_m', $vehicle->height_m ?? '') }}">

    </div>

    <div class="col-12">
        <hr>
        <h5 class="fw-semibold mb-3">Documentos</h5>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Vencimiento matrícula</label>
        <input type="date" name="registration_expiration_date" class="form-control"
            value="{{ old(
                'registration_expiration_date',
                isset($vehicle?->registration_expiration_date) ? $vehicle->registration_expiration_date->format('Y-m-d') : '',
            ) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Vencimiento revisión</label>
        <input type="date" name="technical_review_expiration_date" class="form-control"
            value="{{ old(
                'technical_review_expiration_date',
                isset($vehicle?->technical_review_expiration_date)
                    ? $vehicle->technical_review_expiration_date->format('Y-m-d')
                    : '',
            ) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Vencimiento seguro</label>
        <input type="date" name="insurance_expiration_date" class="form-control"
            value="{{ old(
                'insurance_expiration_date',
                isset($vehicle?->insurance_expiration_date) ? $vehicle->insurance_expiration_date->format('Y-m-d') : '',
            ) }}">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Fotografía</label>
        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="form-control">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Matrícula</label>
        <input type="file" name="registration_document" accept=".pdf,.jpg,.jpeg,.png" class="form-control">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Seguro</label>
        <input type="file" name="insurance_document" accept=".pdf,.jpg,.jpeg,.png" class="form-control">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Revisión técnica</label>
        <input type="file" name="technical_review_document" accept=".pdf,.jpg,.jpeg,.png" class="form-control">
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">Observaciones</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $vehicle->notes ?? '') }}</textarea>
    </div>

    <div class="col-12 mb-4">
        <div class="form-check form-switch">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="vehicle_active"
                @checked(old('is_active', isset($vehicle) ? $vehicle->is_active : true))>

            <label class="form-check-label" for="vehicle_active">
                Vehículo activo
            </label>
        </div>
    </div>

</div>

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('vehicles.index') }}" class="btn btn-light">
        Cancelar
    </a>

    <button class="btn btn-primary" type="submit">
        <i class="ti ti-device-floppy me-1"></i>
        {{ isset($vehicle) ? 'Actualizar vehículo' : 'Guardar vehículo' }}
    </button>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const tare = document.getElementById('tare_weight_kg');
        const maxWeight = document.getElementById('max_weight_kg');
        const display = document.getElementById('payload_display');

        function calculatePayload() {

            const tareValue = parseFloat(tare?.value || 0);
            const maxValue = parseFloat(maxWeight?.value || 0);

            if (tareValue > 0 && maxValue > 0 && maxValue >= tareValue) {

                const payload = maxValue - tareValue;

                display.value = payload.toFixed(2) + ' kg';

            } else {

                display.value = '';
            }
        }

        tare?.addEventListener('input', calculatePayload);
        maxWeight?.addEventListener('input', calculatePayload);

        calculatePayload();
    });
</script>

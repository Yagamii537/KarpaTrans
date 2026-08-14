<div class="row">



    <div class="col-md-3 mb-3">
        <label class="form-label">Código *</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $chassis->code ?? '') }}" required>
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Placa</label>
        <input type="text" name="plate" class="form-control" value="{{ old('plate', $chassis->plate ?? '') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Tipo *</label>
        <select name="chassis_type" class="form-select" required>
            @foreach ([
        'PORTACONTENEDOR' => 'Portacontenedor',
        'EXTENSIBLE' => 'Extensible',
        'PLATAFORMA' => 'Plataforma',
        'CAMA_BAJA' => 'Cama baja',
        'OTRO' => 'Otro',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('chassis_type', $chassis->chassis_type ?? 'PORTACONTENEDOR') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Marca</label>
        <input type="text" name="brand" class="form-control" value="{{ old('brand', $chassis->brand ?? '') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Modelo</label>
        <input type="text" name="model" class="form-control" value="{{ old('model', $chassis->model ?? '') }}">
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Año</label>
        <input type="number" name="year" class="form-control" value="{{ old('year', $chassis->year ?? '') }}">
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Ejes</label>
        <input type="number" name="axles" class="form-control" value="{{ old('axles', $chassis->axles ?? '') }}">
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Capacidad (t)</label>
        <input type="number" step="0.01" name="maximum_capacity_tons" class="form-control"
            value="{{ old('maximum_capacity_tons', $chassis->maximum_capacity_tons ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Número de serie</label>
        <input type="text" name="serial_number" class="form-control"
            value="{{ old('serial_number', $chassis->serial_number ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Estado operativo</label>
        <select name="operational_status" class="form-select">
            @foreach ([
        'AVAILABLE' => 'Disponible',
        'ASSIGNED' => 'Asignado',
        'MAINTENANCE' => 'Mantenimiento',
        'OUT_OF_SERVICE' => 'Fuera de servicio',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('operational_status', $chassis->operational_status ?? 'AVAILABLE') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 mb-3">
        <label class="form-label d-block">Compatibilidad</label>

        <div class="d-flex flex-wrap gap-4">
            <div class="form-check">
                <input type="checkbox" name="supports_20ft" value="1" class="form-check-input" id="supports_20ft"
                    @checked(old('supports_20ft', isset($chassis) ? $chassis->supports_20ft : true))>

                <label class="form-check-label" for="supports_20ft">
                    Contenedor 20 pies
                </label>
            </div>

            <div class="form-check">
                <input type="checkbox" name="supports_40ft" value="1" class="form-check-input" id="supports_40ft"
                    @checked(old('supports_40ft', isset($chassis) ? $chassis->supports_40ft : true))>

                <label class="form-check-label" for="supports_40ft">
                    Contenedor 40 pies
                </label>
            </div>

            <div class="form-check">
                <input type="checkbox" name="supports_reefer" value="1" class="form-check-input"
                    id="supports_reefer" @checked(old('supports_reefer', $chassis->supports_reefer ?? false))>

                <label class="form-check-label" for="supports_reefer">
                    Contenedor refrigerado
                </label>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Vencimiento matrícula</label>
        <input type="date" name="registration_expiration_date" class="form-control"
            value="{{ old(
                'registration_expiration_date',
                isset($chassis?->registration_expiration_date) ? $chassis->registration_expiration_date->format('Y-m-d') : '',
            ) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Vencimiento revisión</label>
        <input type="date" name="technical_review_expiration_date" class="form-control"
            value="{{ old(
                'technical_review_expiration_date',
                isset($chassis?->technical_review_expiration_date)
                    ? $chassis->technical_review_expiration_date->format('Y-m-d')
                    : '',
            ) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Fotografía</label>
        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="form-control">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Matrícula</label>
        <input type="file" name="registration_document" accept=".pdf,.jpg,.jpeg,.png" class="form-control">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Revisión técnica</label>
        <input type="file" name="technical_review_document" accept=".pdf,.jpg,.jpeg,.png" class="form-control">
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">Observaciones</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $chassis->notes ?? '') }}</textarea>
    </div>

    <div class="col-12 mb-4">
        <div class="form-check form-switch">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="chassis_active"
                @checked(old('is_active', isset($chassis) ? $chassis->is_active : true))>

            <label class="form-check-label" for="chassis_active">
                Chasis activo
            </label>
        </div>
    </div>

</div>

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('chassis.index') }}" class="btn btn-light">
        Cancelar
    </a>

    <button class="btn btn-primary">
        <i class="ti ti-device-floppy me-1"></i>
        {{ isset($chassis) ? 'Actualizar chasis' : 'Guardar chasis' }}
    </button>
</div>

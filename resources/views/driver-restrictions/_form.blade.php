<div class="row">

    <div class="col-md-4 mb-3">
        <label class="form-label">
            Conductor *
        </label>

        <select name="driver_id" class="form-select" required>

            <option value="">
                Seleccione conductor
            </option>

            @foreach ($drivers as $driver)
                <option value="{{ $driver->id }}" @selected(old('driver_id', $driverRestriction->driver_id ?? '') == $driver->id)>

                    {{ $driver->full_name }}
                    - {{ $driver->identification }}

                </option>
            @endforeach

        </select>
    </div>

    <div class="col-12">
        <h5 class="fw-semibold mt-2 mb-3">
            Aplica a
        </h5>

        <p class="text-muted">
            Puede definir una restricción general o específica.
        </p>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">
            Cliente
        </label>

        <select name="client_id" class="form-select">

            <option value="">
                Cualquier cliente
            </option>

            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected(old('client_id', $driverRestriction->client_id ?? '') == $client->id)>

                    {{ $client->business_name }}

                </option>
            @endforeach

        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">
            Subcliente
        </label>

        <select name="subclient_id" class="form-select">

            <option value="">
                Cualquier subcliente
            </option>

            @foreach ($subclients as $subclient)
                <option value="{{ $subclient->id }}" @selected(old('subclient_id', $driverRestriction->subclient_id ?? '') == $subclient->id)>

                    {{ $subclient->business_name }}

                </option>
            @endforeach

        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">
            Planta
        </label>

        <select name="plant_id" class="form-select">

            <option value="">
                Cualquier planta
            </option>

            @foreach ($plants as $plant)
                <option value="{{ $plant->id }}" @selected(old('plant_id', $driverRestriction->plant_id ?? '') == $plant->id)>

                    {{ $plant->name }}

                </option>
            @endforeach

        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">
            Ubicación
        </label>

        <select name="location_id" class="form-select">

            <option value="">
                Cualquier ubicación
            </option>

            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(old('location_id', $driverRestriction->location_id ?? '') == $location->id)>

                    {{ $location->name }}

                </option>
            @endforeach

        </select>
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">
            Motivo *
        </label>

        <textarea name="reason" rows="3" class="form-control" required>{{ old('reason', $driverRestriction->reason ?? '') }}</textarea>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">
            Fecha inicio *
        </label>

        <input type="date" name="start_date" class="form-control"
            value="{{ old(
                'start_date',
                isset($driverRestriction?->start_date) ? $driverRestriction->start_date->format('Y-m-d') : now()->format('Y-m-d'),
            ) }}"
            required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">
            Tipo
        </label>

        <select name="restriction_type" class="form-select" id="restriction_type">

            <option value="INDEFINITE" @selected(old('restriction_type', $driverRestriction->restriction_type ?? 'INDEFINITE') === 'INDEFINITE')>

                Indefinida
            </option>

            <option value="TEMPORARY" @selected(old('restriction_type', $driverRestriction->restriction_type ?? '') === 'TEMPORARY')>

                Temporal
            </option>

        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">
            Fecha fin
        </label>

        <input type="date" name="end_date" class="form-control"
            value="{{ old('end_date', isset($driverRestriction?->end_date) ? $driverRestriction->end_date->format('Y-m-d') : '') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">
            Acción
        </label>

        <select name="action_type" class="form-select">

            <option value="BLOCK" @selected(old('action_type', $driverRestriction->action_type ?? 'BLOCK') === 'BLOCK')>

                Bloquear asignación
            </option>

            <option value="WARNING" @selected(old('action_type', $driverRestriction->action_type ?? '') === 'WARNING')>

                Mostrar advertencia
            </option>

        </select>
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">
            Observaciones
        </label>

        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $driverRestriction->notes ?? '') }}</textarea>
    </div>

    <div class="col-12 mb-4">
        <div class="form-check form-switch">

            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="restriction_active"
                @checked(old('is_active', isset($driverRestriction) ? $driverRestriction->is_active : true))>

            <label class="form-check-label" for="restriction_active">

                Restricción activa
            </label>

        </div>
    </div>

</div>

<div class="d-flex justify-content-end gap-2">

    <a href="{{ route('driver-restrictions.index') }}" class="btn btn-light">
        Cancelar
    </a>

    <button type="submit" class="btn btn-primary">

        <i class="ti ti-device-floppy me-1"></i>

        {{ isset($driverRestriction) ? 'Actualizar restricción' : 'Guardar restricción' }}

    </button>

</div>

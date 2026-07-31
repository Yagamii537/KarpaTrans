<div class="row">

    <div class="col-12">
        <h5 class="fw-semibold mb-3">
            Información fiscal
        </h5>
    </div>

    <div class="col-md-8 mb-3">
        <label class="form-label" for="business_name">
            Razón social <span class="text-danger">*</span>
        </label>

        <input type="text"
               id="business_name"
               name="business_name"
               class="form-control @error('business_name') is-invalid @enderror"
               value="{{ old('business_name', $client->business_name ?? '') }}"
               required>

        @error('business_name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label" for="trade_name">
            Nombre comercial
        </label>

        <input type="text"
               id="trade_name"
               name="trade_name"
               class="form-control @error('trade_name') is-invalid @enderror"
               value="{{ old('trade_name', $client->trade_name ?? '') }}">

        @error('trade_name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label" for="identification_type">
            Tipo de identificación
        </label>

        <select id="identification_type"
                name="identification_type"
                class="form-select @error('identification_type') is-invalid @enderror"
                required>

            @foreach (['RUC', 'CEDULA', 'PASAPORTE'] as $type)
                <option value="{{ $type }}"
                    @selected(old(
                        'identification_type',
                        $client->identification_type ?? 'RUC'
                    ) === $type)>

                    {{ $type === 'CEDULA' ? 'Cédula' : ucfirst(strtolower($type)) }}
                </option>
            @endforeach
        </select>

        @error('identification_type')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label" for="identification">
            Identificación <span class="text-danger">*</span>
        </label>

        <input type="text"
               id="identification"
               name="identification"
               class="form-control @error('identification') is-invalid @enderror"
               value="{{ old('identification', $client->identification ?? '') }}"
               required>

        @error('identification')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label" for="contact_name">
            Persona de contacto
        </label>

        <input type="text"
               id="contact_name"
               name="contact_name"
               class="form-control @error('contact_name') is-invalid @enderror"
               value="{{ old('contact_name', $client->contact_name ?? '') }}">

        @error('contact_name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-12">
        <hr>
        <h5 class="fw-semibold mb-3">
            Información de contacto
        </h5>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label" for="email">
            Correo electrónico
        </label>

        <input type="email"
               id="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $client->email ?? '') }}">

        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label" for="phone">
            Teléfono principal
        </label>

        <input type="text"
               id="phone"
               name="phone"
               class="form-control"
               value="{{ old('phone', $client->phone ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label" for="secondary_phone">
            Teléfono adicional
        </label>

        <input type="text"
               id="secondary_phone"
               name="secondary_phone"
               class="form-control"
               value="{{ old(
                   'secondary_phone',
                   $client->secondary_phone ?? ''
               ) }}">
    </div>

    <div class="col-12 mb-3">
        <label class="form-label" for="address">
            Dirección
        </label>

        <textarea id="address"
                  name="address"
                  rows="2"
                  class="form-control">{{ old('address', $client->address ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <hr>

        <h5 class="fw-semibold mb-1">
            Reglas de operación
        </h5>

        <p class="text-muted">
            Estos valores se usarán posteriormente para calcular automáticamente
            las horas de servicio y stand-by.
        </p>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label" for="free_loading_hours">
            Horas libres de carga
        </label>

        <input type="number"
               id="free_loading_hours"
               name="free_loading_hours"
               min="0"
               max="240"
               class="form-control @error('free_loading_hours') is-invalid @enderror"
               value="{{ old(
                   'free_loading_hours',
                   $client->free_loading_hours ?? 10
               ) }}"
               required>

        @error('free_loading_hours')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label" for="free_unloading_hours">
            Horas libres de descarga
        </label>

        <input type="number"
               id="free_unloading_hours"
               name="free_unloading_hours"
               min="0"
               max="240"
               class="form-control @error('free_unloading_hours') is-invalid @enderror"
               value="{{ old(
                   'free_unloading_hours',
                   $client->free_unloading_hours ?? 10
               ) }}"
               required>

        @error('free_unloading_hours')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label" for="service_time_start">
            Inicio del conteo
        </label>

        <select id="service_time_start"
                name="service_time_start"
                class="form-select"
                required>

            <option value="requested_time"
                @selected(old(
                    'service_time_start',
                    $client->service_time_start ?? 'requested_time'
                ) === 'requested_time')>

                Desde la hora solicitada
            </option>

            <option value="arrival_time"
                @selected(old(
                    'service_time_start',
                    $client->service_time_start ?? ''
                ) === 'arrival_time')>

                Desde la hora de llegada
            </option>
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label" for="standby_fraction_minutes">
            Fracción para stand-by
        </label>

        <div class="input-group">
            <input type="number"
                   id="standby_fraction_minutes"
                   name="standby_fraction_minutes"
                   min="0"
                   max="59"
                   class="form-control"
                   value="{{ old(
                       'standby_fraction_minutes',
                       $client->standby_fraction_minutes ?? 30
                   ) }}"
                   required>

            <span class="input-group-text">
                minutos
            </span>
        </div>
    </div>

    <div class="col-12 mb-3">
        <label class="form-label" for="notes">
            Observaciones
        </label>

        <textarea id="notes"
                  name="notes"
                  rows="3"
                  class="form-control">{{ old('notes', $client->notes ?? '') }}</textarea>
    </div>

    <div class="col-12 mb-4">
        <div class="form-check form-switch">
            <input type="checkbox"
                   class="form-check-input"
                   id="is_active"
                   name="is_active"
                   value="1"
                   @checked(old(
                       'is_active',
                       isset($client) ? $client->is_active : true
                   ))>

            <label class="form-check-label" for="is_active">
                Cliente activo
            </label>
        </div>
    </div>

</div>

<div class="d-flex justify-content-end gap-2">

    <a href="{{ route('clients.index') }}"
       class="btn btn-light">
        Cancelar
    </a>

    <button type="submit"
            class="btn btn-primary">

        <i class="ti ti-device-floppy me-1"></i>

        {{ isset($client) ? 'Actualizar cliente' : 'Guardar cliente' }}
    </button>

</div>

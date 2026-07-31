<div class="row">

    <div class="col-12">
        <h5 class="fw-semibold mb-3">
            Información general
        </h5>
    </div>

    <div class="col-md-5 mb-3">
        <label for="name" class="form-label">
            Nombre <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="name"
               id="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $location->name ?? '') }}"
               required>

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="code" class="form-label">
            Código
        </label>

        <input type="text"
               name="code"
               id="code"
               class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $location->code ?? '') }}">

        @error('code')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="type" class="form-label">
            Tipo <span class="text-danger">*</span>
        </label>

        <select name="type"
                id="type"
                class="form-select @error('type') is-invalid @enderror"
                required>

            <option value="">
                Seleccione
            </option>

            @foreach ([
                'PORT' => 'Puerto',
                'DEPOT' => 'Depósito',
                'YARD' => 'Patio',
                'WAREHOUSE' => 'Bodega',
                'EXTERNAL_PLANT' => 'Planta externa',
                'WORKSHOP' => 'Taller',
                'CUSTOMER_LOCATION' => 'Punto del cliente',
                'OTHER' => 'Otro',
            ] as $value => $label)

                <option value="{{ $value }}"
                    @selected(
                        old(
                            'type',
                            $location->type ?? ''
                        ) === $value
                    )>

                    {{ $label }}
                </option>

            @endforeach
        </select>

        @error('type')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="city" class="form-label">
            Ciudad
        </label>

        <input type="text"
               name="city"
               id="city"
               class="form-control"
               value="{{ old('city', $location->city ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="province" class="form-label">
            Provincia
        </label>

        <input type="text"
               name="province"
               id="province"
               class="form-control"
               value="{{ old(
                   'province',
                   $location->province ?? ''
               ) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="reference" class="form-label">
            Referencia
        </label>

        <input type="text"
               name="reference"
               id="reference"
               class="form-control"
               value="{{ old(
                   'reference',
                   $location->reference ?? ''
               ) }}">
    </div>

    <div class="col-12 mb-3">
        <label for="address" class="form-label">
            Dirección <span class="text-danger">*</span>
        </label>

        <textarea name="address"
                  id="address"
                  rows="2"
                  class="form-control @error('address') is-invalid @enderror"
                  required>{{ old('address', $location->address ?? '') }}</textarea>

        @error('address')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-12">
        <hr>

        <h5 class="fw-semibold mb-3">
            Contacto
        </h5>
    </div>

    <div class="col-md-4 mb-3">
        <label for="contact_name" class="form-label">
            Persona de contacto
        </label>

        <input type="text"
               name="contact_name"
               id="contact_name"
               class="form-control"
               value="{{ old(
                   'contact_name',
                   $location->contact_name ?? ''
               ) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="phone" class="form-label">
            Teléfono
        </label>

        <input type="text"
               name="phone"
               id="phone"
               class="form-control"
               value="{{ old('phone', $location->phone ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="email" class="form-label">
            Correo electrónico
        </label>

        <input type="email"
               name="email"
               id="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $location->email ?? '') }}">

        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-12">
        <hr>

        <h5 class="fw-semibold mb-3">
            Coordenadas y horario
        </h5>
    </div>

    <div class="col-md-3 mb-3">
        <label for="latitude" class="form-label">
            Latitud
        </label>

        <input type="number"
               step="0.0000001"
               name="latitude"
               id="latitude"
               class="form-control"
               value="{{ old(
                   'latitude',
                   $location->latitude ?? ''
               ) }}">
    </div>

    <div class="col-md-3 mb-3">
        <label for="longitude" class="form-label">
            Longitud
        </label>

        <input type="number"
               step="0.0000001"
               name="longitude"
               id="longitude"
               class="form-control"
               value="{{ old(
                   'longitude',
                   $location->longitude ?? ''
               ) }}">
    </div>

    <div class="col-md-3 mb-3">
        <label for="opening_time" class="form-label">
            Hora de apertura
        </label>

        <input type="time"
               name="opening_time"
               id="opening_time"
               class="form-control"
               value="{{ old(
                   'opening_time',
                   $location->opening_time ?? ''
               ) }}">
    </div>

    <div class="col-md-3 mb-3">
        <label for="closing_time" class="form-label">
            Hora de cierre
        </label>

        <input type="time"
               name="closing_time"
               id="closing_time"
               class="form-control"
               value="{{ old(
                   'closing_time',
                   $location->closing_time ?? ''
               ) }}">
    </div>

    <div class="col-12">
        <hr>

        <h5 class="fw-semibold mb-3">
            Configuración logística
        </h5>
    </div>

    <div class="col-md-4 mb-3">
        <div class="form-check form-switch">

            <input type="checkbox"
                   name="receives_empty_containers"
                   id="receives_empty_containers"
                   value="1"
                   class="form-check-input"
                   @checked(
                       old(
                           'receives_empty_containers',
                           $location->receives_empty_containers ?? false
                       )
                   )>

            <label for="receives_empty_containers"
                   class="form-check-label">

                Recibe contenedores vacíos
            </label>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="form-check form-switch">

            <input type="checkbox"
                   name="receives_full_containers"
                   id="receives_full_containers"
                   value="1"
                   class="form-check-input"
                   @checked(
                       old(
                           'receives_full_containers',
                           $location->receives_full_containers ?? false
                       )
                   )>

            <label for="receives_full_containers"
                   class="form-check-label">

                Recibe contenedores llenos
            </label>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="form-check form-switch">

            <input type="checkbox"
                   name="requires_appointment"
                   id="requires_appointment"
                   value="1"
                   class="form-check-input"
                   @checked(
                       old(
                           'requires_appointment',
                           $location->requires_appointment ?? false
                       )
                   )>

            <label for="requires_appointment"
                   class="form-check-label">

                Requiere turno o cita
            </label>
        </div>
    </div>

    <div class="col-12 mb-3">
        <label for="notes" class="form-label">
            Observaciones
        </label>

        <textarea name="notes"
                  id="notes"
                  rows="3"
                  class="form-control">{{ old('notes', $location->notes ?? '') }}</textarea>
    </div>

    <div class="col-12 mb-4">
        <div class="form-check form-switch">

            <input type="checkbox"
                   name="is_active"
                   id="is_active"
                   value="1"
                   class="form-check-input"
                   @checked(
                       old(
                           'is_active',
                           isset($location)
                               ? $location->is_active
                               : true
                       )
                   )>

            <label for="is_active"
                   class="form-check-label">

                Ubicación activa
            </label>
        </div>
    </div>

</div>

<div class="d-flex justify-content-end gap-2">

    <a href="{{ route('locations.index') }}"
       class="btn btn-light">

        Cancelar
    </a>

    <button type="submit"
            class="btn btn-primary">

        <i class="ti ti-device-floppy me-1"></i>

        {{ isset($location)
            ? 'Actualizar ubicación'
            : 'Guardar ubicación' }}
    </button>

</div>

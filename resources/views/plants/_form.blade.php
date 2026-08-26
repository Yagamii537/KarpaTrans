<div class="row">

    <div class="col-12">

        <h5 class="fw-semibold mb-3">
            Información de la planta
        </h5>

        <p class="text-muted">

            La planta pertenece a un cliente y podrá
            utilizarse como planta principal, origen
            o destino en las órdenes de trabajo.

        </p>

    </div>


    <div class="col-md-6 mb-3">

        <label for="client_id" class="form-label">

            Cliente
            <span class="text-danger">*</span>

        </label>


        <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror" required>

            <option value="">
                Seleccione un cliente
            </option>


            @foreach ($clients as $availableClient)
                <option value="{{ $availableClient->id }}" @selected(old('client_id', $plant->client_id ?? request('client_id')) == $availableClient->id)>

                    {{ $availableClient->business_name }}

                </option>
            @endforeach

        </select>


        @error('client_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-4 mb-3">

        <label for="name" class="form-label">

            Nombre de la planta
            <span class="text-danger">*</span>

        </label>


        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $plant->name ?? '') }}" required>


        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-2 mb-3">

        <label for="code" class="form-label">

            Código

        </label>


        <input type="text" name="code" id="code" class="form-control"
            value="{{ old('code', $plant->code ?? '') }}" placeholder="Ej.: PL-01">

    </div>


    <div class="col-md-4 mb-3">

        <label for="city" class="form-label">

            Ciudad

        </label>


        <input type="text" name="city" id="city" class="form-control"
            value="{{ old('city', $plant->city ?? '') }}">

    </div>


    <div class="col-md-8 mb-3">

        <label for="address" class="form-label">

            Dirección
            <span class="text-danger">*</span>

        </label>


        <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror"
            value="{{ old('address', $plant->address ?? '') }}" required>


        @error('address')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-12 mb-3">

        <label for="reference" class="form-label">

            Referencia de ubicación

        </label>


        <textarea name="reference" id="reference" rows="2" class="form-control"
            placeholder="Ej.: entrada por la puerta norte, junto a...">{{ old('reference', $plant->reference ?? '') }}</textarea>

    </div>


    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Contacto de la planta
        </h5>

    </div>


    <div class="col-md-4 mb-3">

        <label for="contact_name" class="form-label">

            Persona de contacto

        </label>


        <input type="text" name="contact_name" id="contact_name" class="form-control"
            value="{{ old('contact_name', $plant->contact_name ?? '') }}">

    </div>


    <div class="col-md-4 mb-3">

        <label for="phone" class="form-label">

            Teléfono

        </label>


        <input type="text" name="phone" id="phone" class="form-control"
            value="{{ old('phone', $plant->phone ?? '') }}">

    </div>


    <div class="col-md-4 mb-3">

        <label for="email" class="form-label">

            Correo electrónico

        </label>


        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $plant->email ?? '') }}">


        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Ubicación geográfica
        </h5>

    </div>


    <div class="col-md-6 mb-3">

        <label for="latitude" class="form-label">

            Latitud

        </label>


        <input type="number" step="0.0000001" min="-90" max="90" name="latitude" id="latitude"
            class="form-control @error('latitude') is-invalid @enderror"
            value="{{ old('latitude', $plant->latitude ?? '') }}" placeholder="-0.180653">


        @error('latitude')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-6 mb-3">

        <label for="longitude" class="form-label">

            Longitud

        </label>


        <input type="number" step="0.0000001" min="-180" max="180" name="longitude" id="longitude"
            class="form-control @error('longitude') is-invalid @enderror"
            value="{{ old('longitude', $plant->longitude ?? '') }}" placeholder="-78.467834">


        @error('longitude')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-12 mb-3">

        <label for="notes" class="form-label">

            Observaciones

        </label>


        <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $plant->notes ?? '') }}</textarea>

    </div>


    <div class="col-12 mb-4">

        <div class="form-check form-switch">

            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                @checked(old('is_active', isset($plant) ? $plant->is_active : true))>


            <label for="is_active" class="form-check-label">

                Planta activa

            </label>

        </div>

    </div>

</div>


<div class="d-flex justify-content-end gap-2">

    <a href="{{ route('plants.index') }}" class="btn btn-light">

        Cancelar

    </a>


    <button type="submit" class="btn btn-primary">

        <i class="ti ti-device-floppy me-1"></i>

        {{ isset($plant) ? 'Actualizar planta' : 'Guardar planta' }}

    </button>

</div>

<div class="row">

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Nombre del tipo de carga *
        </label>

        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $cargoType->name ?? '') }}" required>

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Código
        </label>

        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', $cargoType->code ?? '') }}" placeholder="Ej.: REEF">

        @error('code')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Estado
        </label>

        <div class="form-check form-switch mt-2">

            <input type="checkbox" name="is_active" value="1" id="cargo_type_active" class="form-check-input"
                @checked(old('is_active', isset($cargoType) ? $cargoType->is_active : true))>

            <label class="form-check-label" for="cargo_type_active">

                Tipo de carga activo

            </label>

        </div>

    </div>


    <div class="col-12 mb-3">

        <label class="form-label">
            Descripción
        </label>

        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
            placeholder="Descripción del tipo de carga">{{ old('description', $cargoType->description ?? '') }}</textarea>

        @error('description')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-2">
            Clientes permitidos
        </h5>

        <p class="text-muted mb-3">
            Seleccione los clientes que pueden manejar este tipo de carga.
        </p>

    </div>


    @forelse ($clients as $client)
        <div class="col-md-4 mb-3">

            <div class="form-check">

                <input type="checkbox" name="clients[]" value="{{ $client->id }}" id="client_{{ $client->id }}"
                    class="form-check-input" @checked(in_array($client->id, old('clients', isset($cargoType) ? $cargoType->clients->pluck('id')->toArray() : [])))>

                <label class="form-check-label" for="client_{{ $client->id }}">

                    {{ $client->business_name }}

                    @if ($client->identification)
                        <small class="text-muted d-block">

                            {{ $client->identification }}

                        </small>
                    @endif

                </label>

            </div>

        </div>

    @empty

        <div class="col-12">

            <div class="alert alert-warning">

                <i class="ti ti-alert-circle me-1"></i>

                No existen clientes activos registrados.

            </div>

        </div>
    @endforelse

</div>


<div class="d-flex justify-content-end gap-2 mt-4">

    <a href="{{ route('cargo-types.index') }}" class="btn btn-light">

        Cancelar

    </a>

    <button type="submit" class="btn btn-primary">

        <i class="ti ti-device-floppy me-1"></i>

        {{ isset($cargoType) ? 'Actualizar tipo de carga' : 'Guardar tipo de carga' }}

    </button>

</div>

<div class="row">

    {{-- ========================================================= --}}
    {{-- INFORMACIÓN GENERAL --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <h5 class="fw-semibold mb-3">
            Información del tipo de carga
        </h5>

    </div>


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
            placeholder="Descripción o características de esta carga">{{ old('description', $cargoType->description ?? '') }}</textarea>

        @error('description')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- ========================================================= --}}
    {{-- CLIENTES Y SUBCLIENTES --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-2">
            Clientes y subclientes permitidos
        </h5>

        <p class="text-muted mb-4">

            Seleccione los clientes que pueden
            manejar este tipo de carga.

            Después puede limitar su uso
            a subclientes específicos.

        </p>

    </div>


    @error('clients')
        <div class="col-12">

            <div class="alert alert-danger">
                {{ $message }}
            </div>

        </div>
    @enderror


    @error('subclients')
        <div class="col-12">

            <div class="alert alert-danger">
                {{ $message }}
            </div>

        </div>
    @enderror


    @php

        $selectedClients = collect(old('clients', isset($cargoType) ? $cargoType->clients->pluck('id')->toArray() : []))
            ->map(fn($id) => (int) $id)
            ->toArray();

        $selectedSubclients = collect(
            old('subclients', isset($cargoType) ? $cargoType->subclients->pluck('id')->toArray() : []),
        )
            ->map(fn($id) => (int) $id)
            ->toArray();

    @endphp


    @forelse ($clients as $client)

        <div class="col-12 mb-3">

            <div class="card border mb-0">

                <div class="card-body">

                    {{-- CLIENTE --}}

                    <div class="form-check">

                        <input type="checkbox" name="clients[]" value="{{ $client->id }}"
                            id="client_{{ $client->id }}" class="form-check-input cargo-client"
                            data-client="{{ $client->id }}" @checked(in_array($client->id, $selectedClients))>

                        <label class="form-check-label fw-semibold" for="client_{{ $client->id }}">

                            {{ $client->business_name }}

                        </label>

                    </div>


                    @if ($client->identification)
                        <small class="text-muted ms-4">

                            {{ $client->identification }}

                        </small>
                    @endif


                    {{-- SUBCLIENTES --}}

                    @if ($client->subclients->isNotEmpty())
                        <div class="ms-4 mt-3">

                            <div class="small fw-semibold text-muted mb-2">

                                Subclientes permitidos

                            </div>


                            <div class="row">

                                @foreach ($client->subclients as $subclient)
                                    <div class="col-md-4 mb-2">

                                        <div class="form-check">

                                            <input type="checkbox" name="subclients[]" value="{{ $subclient->id }}"
                                                id="subclient_{{ $subclient->id }}"
                                                class="form-check-input cargo-subclient"
                                                data-client="{{ $client->id }}" @checked(in_array($subclient->id, $selectedSubclients))>

                                            <label class="form-check-label" for="subclient_{{ $subclient->id }}">

                                                {{ $subclient->business_name }}

                                            </label>

                                        </div>

                                    </div>
                                @endforeach

                            </div>


                            <small class="text-muted">

                                Si no selecciona ningún
                                subcliente, la carga seguirá
                                disponible para el cliente principal,
                                pero no aparecerá cuando una OT
                                seleccione un subcliente.

                            </small>

                        </div>
                    @else
                        <div class="ms-4 mt-2">

                            <small class="text-muted">

                                Este cliente no tiene
                                subclientes activos.

                            </small>

                        </div>
                    @endif

                </div>

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


<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const clients =
                document.querySelectorAll(
                    '.cargo-client'
                );

            const subclients =
                document.querySelectorAll(
                    '.cargo-subclient'
                );


            /*
             * Habilitar subclientes
             * únicamente cuando el cliente
             * está seleccionado.
             */

            function refreshSubclients() {
                subclients.forEach(
                    function(subclient) {

                        const clientId =
                            subclient.dataset.client;

                        const parentClient =
                            document.querySelector(
                                '.cargo-client[data-client="' +
                                clientId +
                                '"]'
                            );


                        const enabled =
                            parentClient &&
                            parentClient.checked;


                        subclient.disabled = !enabled;


                        if (!enabled) {

                            subclient.checked =
                                false;
                        }
                    }
                );
            }


            /*
             * Si seleccionamos un subcliente,
             * el cliente debe quedar seleccionado.
             */

            subclients.forEach(
                function(subclient) {

                    subclient.addEventListener(
                        'change',
                        function() {

                            if (!this.checked) {
                                return;
                            }


                            const parentClient =
                                document.querySelector(
                                    '.cargo-client[data-client="' +
                                    this.dataset.client +
                                    '"]'
                                );


                            if (parentClient) {

                                parentClient.checked =
                                    true;
                            }


                            refreshSubclients();
                        }
                    );
                }
            );


            clients.forEach(
                function(client) {

                    client.addEventListener(
                        'change',
                        refreshSubclients
                    );
                }
            );


            refreshSubclients();
        }
    );
</script>

<div class="row">

    {{-- ========================================================= --}}
    {{-- DATOS GENERALES --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <h5 class="fw-semibold mb-3">
            Información del subcliente
        </h5>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Cliente principal *
        </label>

        <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror" required>

            <option value="">
                Seleccione cliente
            </option>

            @foreach ($clients as $client)
                <option value="{{ $client->id }}" data-free-loading="{{ $client->free_loading_hours }}"
                    data-free-unloading="{{ $client->free_unloading_hours }}"
                    data-time-start="{{ $client->service_time_start }}"
                    data-fraction="{{ $client->standby_fraction_minutes }}" @selected(old('client_id', $subclient->client_id ?? '') == $client->id)>

                    {{ $client->business_name }}

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

        <label class="form-label">
            Razón social / nombre *
        </label>

        <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror"
            value="{{ old('business_name', $subclient->business_name ?? '') }}" required>

        @error('business_name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Nombre comercial
        </label>

        <input type="text" name="trade_name" class="form-control"
            value="{{ old('trade_name', $subclient->trade_name ?? '') }}">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Tipo identificación
        </label>

        <select name="identification_type" class="form-select">

            <option value="">
                Sin identificación
            </option>

            @foreach (['RUC', 'CEDULA', 'PASAPORTE', 'OTRO'] as $type)
                <option value="{{ $type }}" @selected(old('identification_type', $subclient->identification_type ?? '') === $type)>

                    {{ $type }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Identificación
        </label>

        <input type="text" name="identification" class="form-control"
            value="{{ old('identification', $subclient->identification ?? '') }}">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Contacto
        </label>

        <input type="text" name="contact_name" class="form-control"
            value="{{ old('contact_name', $subclient->contact_name ?? '') }}">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Teléfono
        </label>

        <input type="text" name="phone" class="form-control" value="{{ old('phone', $subclient->phone ?? '') }}">

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Correo
        </label>

        <input type="email" name="email" class="form-control" value="{{ old('email', $subclient->email ?? '') }}">

    </div>


    <div class="col-md-8 mb-3">

        <label class="form-label">
            Dirección
        </label>

        <input type="text" name="address" class="form-control"
            value="{{ old('address', $subclient->address ?? '') }}">

    </div>


    {{-- ========================================================= --}}
    {{-- REGLAS OPERATIVAS --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-2">
            Reglas de operación
        </h5>

        <p class="text-muted mb-3">
            El subcliente puede utilizar las reglas
            del cliente principal o configurar reglas propias.
        </p>

    </div>


    <div class="col-12 mb-4">

        <div class="form-check form-switch">

            <input type="checkbox" name="inherits_operational_rules" value="1" id="inherits_operational_rules"
                class="form-check-input" @checked(old('inherits_operational_rules', isset($subclient) ? $subclient->inherits_operational_rules : true))>

            <label class="form-check-label fw-semibold" for="inherits_operational_rules">

                Usar reglas operativas del cliente principal

            </label>

        </div>

        <small class="text-muted">

            Si esta opción está activa,
            los cambios futuros en las reglas del cliente
            se aplicarán también al subcliente.

        </small>

    </div>


    {{-- RESUMEN REGLAS DEL CLIENTE --}}

    <div class="col-12 mb-4" id="client_rules_summary">

        <div class="alert alert-light border">

            <div class="fw-semibold mb-2">
                Reglas del cliente principal
            </div>

            <div class="row">

                <div class="col-md-3">

                    <small class="text-muted d-block">
                        Horas libres carga
                    </small>

                    <strong id="summary_loading">
                        -
                    </strong>

                </div>

                <div class="col-md-3">

                    <small class="text-muted d-block">
                        Horas libres descarga
                    </small>

                    <strong id="summary_unloading">
                        -
                    </strong>

                </div>

                <div class="col-md-3">

                    <small class="text-muted d-block">
                        Inicio conteo
                    </small>

                    <strong id="summary_start">
                        -
                    </strong>

                </div>

                <div class="col-md-3">

                    <small class="text-muted d-block">
                        Fracción Stand-by
                    </small>

                    <strong id="summary_fraction">
                        -
                    </strong>

                </div>

            </div>

        </div>

    </div>


    {{-- CONFIGURACIÓN PROPIA --}}

    <div class="col-12" id="custom_rules_section">

        <div class="row">

            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Horas libres de carga *
                </label>

                <input type="number" name="free_loading_hours" min="0" max="999"
                    id="free_loading_hours" class="form-control @error('free_loading_hours') is-invalid @enderror"
                    value="{{ old('free_loading_hours', $subclient->free_loading_hours ?? '') }}">

                @error('free_loading_hours')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Horas libres de descarga *
                </label>

                <input type="number" name="free_unloading_hours" min="0" max="999"
                    id="free_unloading_hours" class="form-control @error('free_unloading_hours') is-invalid @enderror"
                    value="{{ old('free_unloading_hours', $subclient->free_unloading_hours ?? '') }}">

                @error('free_unloading_hours')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Inicio del conteo *
                </label>

                <select name="service_time_start" id="service_time_start"
                    class="form-select @error('service_time_start') is-invalid @enderror">

                    <option value="">
                        Seleccione
                    </option>

                    <option value="requested_time" @selected(old('service_time_start', $subclient->service_time_start ?? '') === 'requested_time')>

                        Hora solicitada por el cliente

                    </option>

                    <option value="arrival_time" @selected(old('service_time_start', $subclient->service_time_start ?? '') === 'arrival_time')>

                        Hora real de llegada

                    </option>

                </select>

                @error('service_time_start')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Fracción Stand-by (min) *
                </label>

                <input type="number" name="standby_fraction_minutes" min="1" max="1440"
                    id="standby_fraction_minutes"
                    class="form-control @error('standby_fraction_minutes') is-invalid @enderror"
                    value="{{ old('standby_fraction_minutes', $subclient->standby_fraction_minutes ?? '') }}">

                @error('standby_fraction_minutes')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- TIPOS DE CARGA --}}
    {{-- ========================================================= --}}

    {{-- ========================================================= --}}
    {{-- TIPOS DE CARGA --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-2">
            Tipos de carga
        </h5>

        <p class="text-muted mb-3">

            Solo se muestran los tipos de carga
            configurados previamente para el cliente principal.

        </p>

    </div>


    <div class="col-12">

        <div id="cargo_types_loading" class="alert alert-light border" style="display:none;">

            <span class="spinner-border spinner-border-sm me-2"></span>

            Consultando tipos de carga...

        </div>


        <div id="cargo_types_empty" class="alert alert-warning" style="display:none;">

            <i class="ti ti-alert-circle me-1"></i>

            Este cliente no tiene tipos de carga configurados.

            <a href="{{ route('cargo-types.index') }}" class="alert-link">

                Configurar tipos de carga

            </a>

        </div>


        <div class="row" id="cargo_types_container">

        </div>

    </div>


    @forelse ($cargoTypes as $cargoType)
        <div class="col-md-4 mb-2">

            <div class="form-check">

                <input type="checkbox" name="cargo_types[]" value="{{ $cargoType->id }}"
                    id="cargo_{{ $cargoType->id }}" class="form-check-input" @checked(in_array(
                            $cargoType->id,
                            old('cargo_types', isset($subclient) ? $subclient->cargoTypes->pluck('id')->toArray() : [])))>

                <label class="form-check-label" for="cargo_{{ $cargoType->id }}">

                    {{ $cargoType->name }}

                </label>

            </div>

        </div>

    @empty

        <div class="col-12">

            <div class="alert alert-warning">

                <i class="ti ti-alert-circle me-1"></i>

                No existen tipos de carga activos.

            </div>

        </div>
    @endforelse


    {{-- ========================================================= --}}
    {{-- OBSERVACIONES --}}
    {{-- ========================================================= --}}

    <div class="col-12 mt-3 mb-3">

        <label class="form-label">
            Observaciones
        </label>

        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $subclient->notes ?? '') }}</textarea>

    </div>


    {{-- ESTADO --}}

    <div class="col-12 mb-4">

        <div class="form-check form-switch">

            <input type="checkbox" name="is_active" value="1" id="subclient_active" class="form-check-input"
                @checked(old('is_active', isset($subclient) ? $subclient->is_active : true))>

            <label class="form-check-label" for="subclient_active">

                Subcliente activo

            </label>

        </div>

    </div>

</div>


<div class="d-flex justify-content-end gap-2">

    <a href="{{ route('subclients.index') }}" class="btn btn-light">

        Cancelar

    </a>

    <button type="submit" class="btn btn-primary">

        <i class="ti ti-device-floppy me-1"></i>

        {{ isset($subclient) ? 'Actualizar subcliente' : 'Guardar subcliente' }}

    </button>

</div>


<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            /*
            |--------------------------------------------------------------------------
            | ELEMENTOS
            |--------------------------------------------------------------------------
            */

            const clientSelect =
                document.getElementById(
                    'client_id'
                );

            const inheritCheckbox =
                document.getElementById(
                    'inherits_operational_rules'
                );

            const customRules =
                document.getElementById(
                    'custom_rules_section'
                );

            const loadingInput =
                document.getElementById(
                    'free_loading_hours'
                );

            const unloadingInput =
                document.getElementById(
                    'free_unloading_hours'
                );

            const startSelect =
                document.getElementById(
                    'service_time_start'
                );

            const fractionInput =
                document.getElementById(
                    'standby_fraction_minutes'
                );


            const summaryLoading =
                document.getElementById(
                    'summary_loading'
                );

            const summaryUnloading =
                document.getElementById(
                    'summary_unloading'
                );

            const summaryStart =
                document.getElementById(
                    'summary_start'
                );

            const summaryFraction =
                document.getElementById(
                    'summary_fraction'
                );


            /*
            |--------------------------------------------------------------------------
            | TIPOS DE CARGA
            |--------------------------------------------------------------------------
            */

            const cargoContainer =
                document.getElementById(
                    'cargo_types_container'
                );

            const cargoLoading =
                document.getElementById(
                    'cargo_types_loading'
                );

            const cargoEmpty =
                document.getElementById(
                    'cargo_types_empty'
                );


            /*
             * Cargas seleccionadas en edición
             * o cuando validation falla.
             */
            const selectedCargoTypes =
                @json(old(
                        'cargo_types',
                        isset($subclient) ? $subclient->cargoTypes->pluck('id')->map(fn($id) => (int) $id)->toArray() : []));


            /*
            |--------------------------------------------------------------------------
            | REGLAS DEL CLIENTE
            |--------------------------------------------------------------------------
            */

            function getSelectedClient() {
                if (
                    !clientSelect ||
                    !clientSelect.value
                ) {
                    return null;
                }

                return clientSelect.options[
                    clientSelect.selectedIndex
                ];
            }


            function updateClientSummary() {
                const option =
                    getSelectedClient();

                if (!option) {

                    summaryLoading.textContent =
                        '-';

                    summaryUnloading.textContent =
                        '-';

                    summaryStart.textContent =
                        '-';

                    summaryFraction.textContent =
                        '-';

                    return;
                }


                summaryLoading.textContent =
                    (
                        option.dataset.freeLoading ||
                        '0'
                    ) +
                    ' horas';


                summaryUnloading.textContent =
                    (
                        option.dataset.freeUnloading ||
                        '0'
                    ) +
                    ' horas';


                if (
                    option.dataset.timeStart ===
                    'arrival_time'
                ) {

                    summaryStart.textContent =
                        'Hora real de llegada';

                } else {

                    summaryStart.textContent =
                        'Hora solicitada';
                }


                summaryFraction.textContent =
                    (
                        option.dataset.fraction ||
                        '30'
                    ) +
                    ' min';
            }


            function toggleRules() {
                if (!inheritCheckbox) {
                    return;
                }

                const inherits =
                    inheritCheckbox.checked;

                if (inherits) {

                    customRules.style.display =
                        'none';

                    loadingInput.required =
                        false;

                    unloadingInput.required =
                        false;

                    startSelect.required =
                        false;

                    fractionInput.required =
                        false;

                } else {

                    customRules.style.display =
                        '';

                    loadingInput.required =
                        true;

                    unloadingInput.required =
                        true;

                    startSelect.required =
                        true;

                    fractionInput.required =
                        true;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | CARGAR TIPOS DE CARGA DEL CLIENTE
            |--------------------------------------------------------------------------
            */

            async function loadCargoTypes() {
                cargoContainer.innerHTML =
                    '';

                cargoEmpty.style.display =
                    'none';


                if (
                    !clientSelect.value
                ) {

                    cargoEmpty.style.display =
                        '';

                    cargoEmpty.innerHTML =
                        '<i class="ti ti-alert-circle me-1"></i>' +
                        'Seleccione primero un cliente principal.';

                    return;
                }


                cargoLoading.style.display =
                    '';


                try {

                    const url =
                        new URL(
                            '{{ route('cargo-types.available') }}',
                            window.location.origin
                        );


                    url.searchParams.set(
                        'client_id',
                        clientSelect.value
                    );


                    const response =
                        await fetch(
                            url.toString(), {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );


                    if (!response.ok) {

                        throw new Error(
                            'No fue posible consultar los tipos de carga.'
                        );
                    }


                    const cargoTypes =
                        await response.json();


                    if (
                        cargoTypes.length === 0
                    ) {

                        cargoEmpty.style.display =
                            '';

                        cargoEmpty.innerHTML =
                            '<i class="ti ti-alert-circle me-1"></i>' +
                            'Este cliente no tiene tipos de carga configurados.';

                        return;
                    }


                    cargoTypes.forEach(
                        function(cargo) {

                            const column =
                                document.createElement(
                                    'div'
                                );

                            column.className =
                                'col-md-4 mb-2';


                            const checked =
                                selectedCargoTypes.includes(
                                    Number(cargo.id)
                                ) ?
                                'checked' :
                                '';


                            column.innerHTML = `
                            <div class="form-check">

                                <input
                                    type="checkbox"
                                    name="cargo_types[]"
                                    value="${cargo.id}"
                                    id="cargo_${cargo.id}"
                                    class="form-check-input"
                                    ${checked}
                                >

                                <label
                                    class="form-check-label"
                                    for="cargo_${cargo.id}"
                                >
                                    ${cargo.name}
                                </label>

                            </div>
                        `;


                            cargoContainer.appendChild(
                                column
                            );
                        }
                    );

                } catch (error) {

                    console.error(error);

                    cargoEmpty.style.display =
                        '';

                    cargoEmpty.innerHTML =
                        '<i class="ti ti-alert-circle me-1"></i>' +
                        'No fue posible cargar los tipos de carga.';

                } finally {

                    cargoLoading.style.display =
                        'none';
                }
            }


            /*
            |--------------------------------------------------------------------------
            | EVENTOS
            |--------------------------------------------------------------------------
            */

            clientSelect.addEventListener(
                'change',
                function() {

                    updateClientSummary();

                    /*
                     * Cuando cambia cliente,
                     * las cargas marcadas anteriormente
                     * ya no son válidas.
                     */
                    selectedCargoTypes.length =
                        0;

                    loadCargoTypes();
                }
            );


            inheritCheckbox.addEventListener(
                'change',
                toggleRules
            );


            /*
            |--------------------------------------------------------------------------
            | INICIO
            |--------------------------------------------------------------------------
            */

            updateClientSummary();

            toggleRules();

            loadCargoTypes();
        }
    );
</script>

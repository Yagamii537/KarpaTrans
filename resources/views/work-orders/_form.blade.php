<div class="row">

    {{-- ========================================================= --}}
    {{-- CLIENTE --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <h5 class="fw-semibold mb-3">
            Cliente y requerimiento
        </h5>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Cliente *
        </label>

        <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror" required>

            <option value="">
                Seleccione cliente
            </option>


            @foreach ($clients as $client)
                <option value="{{ $client->id }}" data-free-loading="{{ $client->free_loading_hours ?? 0 }}"
                    data-free-unloading="{{ $client->free_unloading_hours ?? 0 }}"
                    data-count-start="{{ $client->service_time_start ?? 'requested_time' }}"
                    data-fraction="{{ $client->standby_fraction_minutes ?? 30 }}" @selected(old('client_id', $workOrder->client_id ?? '') == $client->id)>

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
            Subcliente
        </label>

        <select name="subclient_id" id="subclient_id" class="form-select">

            <option value="">
                Sin subcliente
            </option>


            @foreach ($subclients as $subclient)
                <option value="{{ $subclient->id }}" data-client="{{ $subclient->client_id }}"
                    data-inherits="{{ $subclient->inherits_operational_rules ? '1' : '0' }}"
                    data-free-loading="{{ $subclient->free_loading_hours ?? 0 }}"
                    data-free-unloading="{{ $subclient->free_unloading_hours ?? 0 }}"
                    data-count-start="{{ $subclient->service_time_start ?? 'requested_time' }}"
                    data-fraction="{{ $subclient->standby_fraction_minutes ?? 30 }}" @selected(old('subclient_id', $workOrder->subclient_id ?? '') == $subclient->id)>

                    {{ $subclient->business_name }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Tipo de carga
        </label>

        <select name="cargo_type_id" id="cargo_type_id"
            class="form-select @error('cargo_type_id') is-invalid @enderror">

            <option value="">
                Seleccione primero un cliente
            </option>

        </select>

        @error('cargo_type_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror


        <small class="text-muted" id="cargo_type_help">

            Las cargas dependen del cliente
            y subcliente seleccionados.

        </small>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Booking
        </label>

        <input type="text" name="booking_number" class="form-control"
            value="{{ old('booking_number', $workOrder->booking_number ?? '') }}">

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            N.º orden del cliente
        </label>

        <input type="text" name="customer_order_number" class="form-control"
            value="{{ old('customer_order_number', $workOrder->customer_order_number ?? '') }}">

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Referencia del cliente
        </label>

        <input type="text" name="customer_reference" class="form-control"
            value="{{ old('customer_reference', $workOrder->customer_reference ?? '') }}">

    </div>


    {{-- ========================================================= --}}
    {{-- OPERACIÓN --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Operación
        </h5>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Tipo de operación *
        </label>

        <select name="operation_type" id="operation_type" class="form-select" required>

            @foreach ([
        'EXPORT' => 'Exportación',
        'IMPORT' => 'Importación',
        'TRANSFER' => 'Transferencia',
        'OTHER' => 'Otra',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('operation_type', $workOrder->operation_type ?? 'EXPORT') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Modalidad del servicio *
        </label>

        <select name="service_modality" id="service_modality" class="form-select" required>

            @foreach ([
        'IMMEDIATE' => 'Inmediata',
        'POSITIONING' => 'Posición',
        'PICKUP' => 'Retiro',
        'POSITIONING_PICKUP' => 'Posición + Retiro',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('service_modality', $workOrder->service_modality ?? 'IMMEDIATE') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>


        <small class="text-muted">

            Define cómo se ejecutará físicamente
            el servicio.

        </small>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Planta principal
        </label>

        <select name="plant_id" id="plant_id" class="form-select">

            <option value="">
                Sin planta
            </option>


            @foreach ($plants as $plant)
                <option value="{{ $plant->id }}" data-client="{{ $plant->client_id }}"
                    @selected(old('plant_id', $workOrder->plant_id ?? '') == $plant->id)>

                    {{ $plant->name }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Cantidad solicitada *
        </label>

        <input type="number" name="requested_trips" min="1" max="500" class="form-control"
            value="{{ old('requested_trips', $workOrder->requested_trips ?? 1) }}" required>

        <small class="text-muted">

            Cantidad de servicios/contenedores
            solicitados.

        </small>

    </div>


    {{-- ========================================================= --}}
    {{-- ORIGEN DESTINO --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Origen y destino
        </h5>

    </div>


    <div class="col-md-2 mb-3">

        <label class="form-label">
            Tipo origen
        </label>

        <select name="origin_type" id="origin_type" class="form-select">

            <option value="LOCATION" @selected(old('origin_type', $workOrder->origin_type ?? 'LOCATION') === 'LOCATION')>

                Ubicación

            </option>

            <option value="PLANT" @selected(old('origin_type', $workOrder->origin_type ?? '') === 'PLANT')>

                Planta

            </option>

        </select>

    </div>


    <div class="col-md-4 mb-3" id="origin_location_group">

        <label class="form-label">
            Ubicación origen
        </label>

        <select name="origin_location_id" id="origin_location_id" class="form-select">

            <option value="">
                Seleccione
            </option>


            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(old('origin_location_id', $workOrder->origin_location_id ?? '') == $location->id)>

                    {{ $location->name }}
                    - {{ $location->type_label }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-4 mb-3" id="origin_plant_group">

        <label class="form-label">
            Planta origen
        </label>

        <select name="origin_plant_id" id="origin_plant_id" class="form-select">

            <option value="">
                Seleccione
            </option>


            @foreach ($plants as $plant)
                <option value="{{ $plant->id }}" data-client="{{ $plant->client_id }}"
                    @selected(old('origin_plant_id', $workOrder->origin_plant_id ?? '') == $plant->id)>

                    {{ $plant->name }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-2 mb-3">

        <label class="form-label">
            Tipo destino
        </label>

        <select name="destination_type" id="destination_type" class="form-select">

            <option value="LOCATION" @selected(old('destination_type', $workOrder->destination_type ?? 'LOCATION') === 'LOCATION')>

                Ubicación

            </option>

            <option value="PLANT" @selected(old('destination_type', $workOrder->destination_type ?? '') === 'PLANT')>

                Planta

            </option>

        </select>

    </div>


    <div class="col-md-4 mb-3" id="destination_location_group">

        <label class="form-label">
            Ubicación destino
        </label>

        <select name="destination_location_id" id="destination_location_id" class="form-select">

            <option value="">
                Seleccione
            </option>


            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(old('destination_location_id', $workOrder->destination_location_id ?? '') == $location->id)>

                    {{ $location->name }}
                    - {{ $location->type_label }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-4 mb-3" id="destination_plant_group">

        <label class="form-label">
            Planta destino
        </label>

        <select name="destination_plant_id" id="destination_plant_id" class="form-select">

            <option value="">
                Seleccione
            </option>


            @foreach ($plants as $plant)
                <option value="{{ $plant->id }}" data-client="{{ $plant->client_id }}"
                    @selected(old('destination_plant_id', $workOrder->destination_plant_id ?? '') == $plant->id)>

                    {{ $plant->name }}

                </option>
            @endforeach

        </select>

    </div>


    {{-- ========================================================= --}}
    {{-- PLANIFICACIÓN --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Planificación
        </h5>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Fecha solicitada *
        </label>

        <input type="date" name="requested_date" class="form-control"
            value="{{ old(
                'requested_date',
                isset($workOrder?->requested_date) ? $workOrder->requested_date->format('Y-m-d') : now()->format('Y-m-d'),
            ) }}"
            required>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Hora solicitada
        </label>

        <input type="time" name="requested_time" class="form-control"
            value="{{ old('requested_time', $workOrder->requested_time ?? '') }}">

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Turno / cita
        </label>

        <input type="datetime-local" name="appointment_at" class="form-control"
            value="{{ old(
                'appointment_at',
                isset($workOrder?->appointment_at) ? $workOrder->appointment_at->format('Y-m-d\TH:i') : '',
            ) }}">

    </div>


    {{-- ========================================================= --}}
    {{-- STAND-BY --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-2">
            Parametrización de Stand-by
        </h5>

        <p class="text-muted mb-3">

            La regla se obtiene automáticamente
            del cliente o subcliente y queda
            guardada en esta orden.

        </p>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Proceso *
        </label>

        <select name="standby_process_type" id="standby_process_type" class="form-select" required>

            <option value="LOAD" @selected(old('standby_process_type', $workOrder->standby_process_type ?? 'LOAD') === 'LOAD')>

                Carga

            </option>


            <option value="UNLOAD" @selected(old('standby_process_type', $workOrder->standby_process_type ?? '') === 'UNLOAD')>

                Descarga

            </option>

        </select>

    </div>


    <div class="col-md-8 mb-3">

        <div class="alert alert-light border mb-0">

            <div class="row">

                <div class="col-md-4">

                    <small class="text-muted d-block">
                        Horas libres
                    </small>

                    <strong id="rule_free_hours">
                        -
                    </strong>

                </div>


                <div class="col-md-4">

                    <small class="text-muted d-block">
                        Inicio conteo
                    </small>

                    <strong id="rule_count_start">
                        -
                    </strong>

                </div>


                <div class="col-md-4">

                    <small class="text-muted d-block">
                        Fracción
                    </small>

                    <strong id="rule_fraction">
                        -
                    </strong>

                </div>

            </div>


            <div class="mt-2">

                <small class="text-muted">

                    Fuente:
                    <strong id="rule_source">
                        -
                    </strong>

                </small>

            </div>

        </div>

    </div>


    {{-- EXCEPCIÓN --}}

    <div class="col-12 mb-3">

        <div class="form-check form-switch">

            <input type="checkbox" name="standby_rule_overridden" value="1" id="standby_rule_overridden"
                class="form-check-input" @checked(old('standby_rule_overridden', $workOrder->standby_rule_overridden ?? false))>

            <label class="form-check-label fw-semibold" for="standby_rule_overridden">

                Aplicar excepción manual de Stand-by

            </label>

        </div>

        <small class="text-muted">

            Utilícelo cuando la operación tenga
            condiciones especiales autorizadas.

        </small>

    </div>


    <div class="col-12" id="standby_override_section" style="display:none;">

        <div class="row">

            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Horas libres *
                </label>

                <input type="number" name="standby_override_free_hours" id="standby_override_free_hours"
                    min="0" max="999" class="form-control"
                    value="{{ old(
                        'standby_override_free_hours',
                        $workOrder->standby_rule_overridden ?? false ? $workOrder->standby_free_hours : '',
                    ) }}">

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Inicio del conteo *
                </label>

                <select name="standby_override_count_start_type" id="standby_override_count_start_type"
                    class="form-select">

                    <option value="REQUESTED_TIME" @selected(old('standby_override_count_start_type', $workOrder->standby_count_start_type ?? '') === 'REQUESTED_TIME')>

                        Hora solicitada

                    </option>


                    <option value="ARRIVAL_TIME" @selected(old('standby_override_count_start_type', $workOrder->standby_count_start_type ?? '') === 'ARRIVAL_TIME')>

                        Hora real de llegada

                    </option>

                </select>

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Fracción (min) *
                </label>

                <input type="number" name="standby_override_fraction_minutes" id="standby_override_fraction_minutes"
                    min="1" max="1440" class="form-control"
                    value="{{ old(
                        'standby_override_fraction_minutes',
                        $workOrder->standby_rule_overridden ?? false ? $workOrder->standby_fraction_minutes : '',
                    ) }}">

            </div>


            <div class="col-md-12 mb-3">

                <label class="form-label">
                    Motivo de la excepción *
                </label>

                <textarea name="standby_override_reason" id="standby_override_reason" rows="2" class="form-control"
                    placeholder="Explique por qué se modificó la regla estándar">{{ old('standby_override_reason', $workOrder->standby_override_reason ?? '') }}</textarea>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- CONTENEDOR --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Carga y contenedor
        </h5>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Tipo contenedor
        </label>

        <select name="requested_container_type" class="form-select">

            <option value="">
                No definido
            </option>

            @foreach ([
        'DRY' => 'Seco',
        'REEFER' => 'Refrigerado',
        'OPEN_TOP' => 'Open Top',
        'FLAT_RACK' => 'Flat Rack',
        'TANK' => 'Tanque',
        'OTHER' => 'Otro',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('requested_container_type', $workOrder->requested_container_type ?? '') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Tamaño
        </label>

        <select name="requested_container_size" class="form-select">

            <option value="">
                No definido
            </option>

            @foreach ([
        '20FT' => '20 pies',
        '40FT' => '40 pies',
        '40HC' => '40 HC',
        '45FT' => '45 pies',
        'OTHER' => 'Otro',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('requested_container_size', $workOrder->requested_container_size ?? '') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Peso estimado (kg)
        </label>

        <input type="number" name="estimated_weight_kg" step="0.01" min="0" class="form-control"
            value="{{ old('estimated_weight_kg', $workOrder->estimated_weight_kg ?? '') }}">

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Estado
        </label>

        <select name="status" class="form-select">

            @foreach ([
        'PENDING' => 'Pendiente',
        'PLANNED' => 'Planificada',
        'IN_PROGRESS' => 'En ejecución',
        'COMPLETED' => 'Completada',
        'CANCELLED' => 'Cancelada',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $workOrder->status ?? 'PENDING') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-12 mb-3">

        <label class="form-label">
            Descripción de carga
        </label>

        <textarea name="cargo_description" rows="2" class="form-control">{{ old('cargo_description', $workOrder->cargo_description ?? '') }}</textarea>

    </div>


    <div class="col-12 mb-4">

        <label class="form-label">
            Observaciones
        </label>

        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $workOrder->notes ?? '') }}</textarea>

    </div>

</div>


<div class="d-flex justify-content-end gap-2">

    <a href="{{ route('work-orders.index') }}" class="btn btn-light">

        Cancelar

    </a>

    <button type="submit" class="btn btn-primary">

        <i class="ti ti-device-floppy me-1"></i>

        {{ isset($workOrder) ? 'Actualizar orden' : 'Guardar orden' }}

    </button>

</div>


<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const clientSelect =
                document.getElementById('client_id');

            const subclientSelect =
                document.getElementById('subclient_id');

            const cargoTypeSelect =
                document.getElementById('cargo_type_id');

            const cargoTypeHelp =
                document.getElementById('cargo_type_help');

            const operationType =
                document.getElementById('operation_type');

            const standbyProcess =
                document.getElementById('standby_process_type');

            const overrideCheck =
                document.getElementById('standby_rule_overridden');

            const overrideSection =
                document.getElementById('standby_override_section');


            const ruleFreeHours =
                document.getElementById('rule_free_hours');

            const ruleCountStart =
                document.getElementById('rule_count_start');

            const ruleFraction =
                document.getElementById('rule_fraction');

            const ruleSource =
                document.getElementById('rule_source');


            const plantSelect =
                document.getElementById('plant_id');

            const originPlant =
                document.getElementById('origin_plant_id');

            const destinationPlant =
                document.getElementById('destination_plant_id');

            const originType =
                document.getElementById('origin_type');

            const destinationType =
                document.getElementById('destination_type');

            const originLocationGroup =
                document.getElementById('origin_location_group');

            const originPlantGroup =
                document.getElementById('origin_plant_group');

            const destinationLocationGroup =
                document.getElementById('destination_location_group');

            const destinationPlantGroup =
                document.getElementById('destination_plant_group');


            const initialCargo =
                @json(old('cargo_type_id', $workOrder->cargo_type_id ?? null));


            /*
            |--------------------------------------------------------------------------
            | CLIENTE
            |--------------------------------------------------------------------------
            */

            function filterByClient(select) {
                if (!select) {
                    return;
                }

                const clientId =
                    clientSelect.value;


                Array.from(
                    select.options
                ).forEach(
                    function(option) {

                        if (!option.value) {
                            option.hidden = false;
                            return;
                        }

                        option.hidden =
                            option.dataset.client !==
                            clientId;
                    }
                );
            }


            function filterClientData() {
                filterByClient(
                    subclientSelect
                );

                filterByClient(
                    plantSelect
                );

                filterByClient(
                    originPlant
                );

                filterByClient(
                    destinationPlant
                );
            }


            /*
            |--------------------------------------------------------------------------
            | STAND-BY
            |--------------------------------------------------------------------------
            */

            function getClientRule() {
                if (!clientSelect.value) {
                    return null;
                }

                const option =
                    clientSelect.options[
                        clientSelect.selectedIndex
                    ];

                return {

                    loading: Number(
                        option.dataset.freeLoading ||
                        0
                    ),

                    unloading: Number(
                        option.dataset.freeUnloading ||
                        0
                    ),

                    countStart: option.dataset.countStart ||
                        'requested_time',

                    fraction: Number(
                        option.dataset.fraction ||
                        30
                    ),

                    source: 'Cliente'
                };
            }


            function getEffectiveRule() {
                const clientRule =
                    getClientRule();


                if (!clientRule) {
                    return null;
                }


                if (!subclientSelect.value) {
                    return clientRule;
                }


                const option =
                    subclientSelect.options[
                        subclientSelect.selectedIndex
                    ];


                if (
                    option.dataset.inherits ===
                    '1'
                ) {

                    return clientRule;
                }


                return {

                    loading: Number(
                        option.dataset.freeLoading ||
                        0
                    ),

                    unloading: Number(
                        option.dataset.freeUnloading ||
                        0
                    ),

                    countStart: option.dataset.countStart ||
                        'requested_time',

                    fraction: Number(
                        option.dataset.fraction ||
                        30
                    ),

                    source: 'Subcliente'
                };
            }


            function refreshStandbyRule() {
                const rule =
                    getEffectiveRule();


                if (!rule) {

                    ruleFreeHours.textContent =
                        '-';

                    ruleCountStart.textContent =
                        '-';

                    ruleFraction.textContent =
                        '-';

                    ruleSource.textContent =
                        '-';

                    return;
                }


                const freeHours =
                    standbyProcess.value ===
                    'UNLOAD' ?
                    rule.unloading :
                    rule.loading;


                ruleFreeHours.textContent =
                    freeHours + ' h';


                ruleCountStart.textContent =
                    rule.countStart ===
                    'arrival_time' ?
                    'Hora real de llegada' :
                    'Hora solicitada';


                ruleFraction.textContent =
                    rule.fraction + ' min';


                ruleSource.textContent =
                    rule.source;
            }


            /*
             * Exportación:
             * normalmente carga.
             *
             * Importación:
             * normalmente descarga.
             */
            function suggestStandbyProcess() {
                if (
                    operationType.value ===
                    'EXPORT'
                ) {

                    standbyProcess.value =
                        'LOAD';

                } else if (
                    operationType.value ===
                    'IMPORT'
                ) {

                    standbyProcess.value =
                        'UNLOAD';
                }


                refreshStandbyRule();
            }


            function toggleOverride() {
                overrideSection.style.display =
                    overrideCheck.checked ?
                    '' :
                    'none';
            }


            /*
            |--------------------------------------------------------------------------
            | CARGAS
            |--------------------------------------------------------------------------
            */

            async function loadCargoTypes(
                preserve = false
            ) {

                if (!clientSelect.value) {

                    cargoTypeSelect.innerHTML =
                        '<option value="">Seleccione primero un cliente</option>';

                    return;
                }


                cargoTypeSelect.disabled =
                    true;

                cargoTypeSelect.innerHTML =
                    '<option value="">Cargando...</option>';


                try {

                    const url =
                        new URL(
                            @json(route('cargo-types.available'))
                        );


                    url.searchParams.set(
                        'client_id',
                        clientSelect.value
                    );


                    if (
                        subclientSelect.value
                    ) {

                        url.searchParams.set(
                            'subclient_id',
                            subclientSelect.value
                        );
                    }


                    const response =
                        await fetch(
                            url.toString(), {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );


                    if (!response.ok) {
                        throw new Error();
                    }


                    const cargos =
                        await response.json();


                    cargoTypeSelect.innerHTML =
                        '<option value="">Seleccione tipo de carga</option>';


                    cargos.forEach(
                        function(cargo) {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                cargo.id;

                            option.textContent =
                                cargo.name;


                            if (
                                preserve &&
                                String(cargo.id) ===
                                String(
                                    initialCargo
                                )
                            ) {

                                option.selected =
                                    true;
                            }


                            cargoTypeSelect.appendChild(
                                option
                            );
                        }
                    );


                    cargoTypeHelp.textContent =
                        subclientSelect.value ?
                        'Cargas habilitadas para cliente y subcliente.' :
                        'Cargas habilitadas para el cliente.';


                } catch (error) {

                    cargoTypeSelect.innerHTML =
                        '<option value="">Error al cargar</option>';

                } finally {

                    cargoTypeSelect.disabled =
                        false;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | ORIGEN / DESTINO
            |--------------------------------------------------------------------------
            */

            function toggleOrigin() {
                const plant =
                    originType.value ===
                    'PLANT';


                originPlantGroup.style.display =
                    plant ? '' : 'none';


                originLocationGroup.style.display =
                    plant ? 'none' : '';
            }


            function toggleDestination() {
                const plant =
                    destinationType.value ===
                    'PLANT';


                destinationPlantGroup.style.display =
                    plant ? '' : 'none';


                destinationLocationGroup.style.display =
                    plant ? 'none' : '';
            }


            /*
            |--------------------------------------------------------------------------
            | EVENTOS
            |--------------------------------------------------------------------------
            */

            clientSelect.addEventListener(
                'change',
                function() {

                    subclientSelect.value =
                        '';

                    filterClientData();

                    loadCargoTypes(false);

                    refreshStandbyRule();
                }
            );


            subclientSelect.addEventListener(
                'change',
                function() {

                    loadCargoTypes(false);

                    refreshStandbyRule();
                }
            );


            standbyProcess.addEventListener(
                'change',
                refreshStandbyRule
            );


            operationType.addEventListener(
                'change',
                suggestStandbyProcess
            );


            overrideCheck.addEventListener(
                'change',
                toggleOverride
            );


            originType.addEventListener(
                'change',
                toggleOrigin
            );


            destinationType.addEventListener(
                'change',
                toggleDestination
            );


            /*
            |--------------------------------------------------------------------------
            | INICIAR
            |--------------------------------------------------------------------------
            */

            filterClientData();

            toggleOrigin();

            toggleDestination();

            toggleOverride();

            refreshStandbyRule();

            loadCargoTypes(true);
        }
    );
</script>

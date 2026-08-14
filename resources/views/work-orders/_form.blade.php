<div class="row">

    {{-- CLIENTE --}}
    <div class="col-12">

        <h5 class="fw-semibold mb-3">
            Cliente y requerimiento
        </h5>

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Cliente *
        </label>

        <select name="client_id" id="client_id" class="form-select" required>

            <option value="">
                Seleccione cliente
            </option>

            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected(old('client_id', $workOrder->client_id ?? '') == $client->id)>

                    {{ $client->business_name }}

                </option>
            @endforeach

        </select>

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
                    @selected(old('subclient_id', $workOrder->subclient_id ?? '') == $subclient->id)>

                    {{ $subclient->business_name }}

                </option>
            @endforeach

        </select>

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Tipo de carga
        </label>

        <select name="cargo_type_id" id="cargo_type_id" class="form-select">

            <option value="">
                Seleccione carga
            </option>

            @foreach ($cargoTypes as $cargoType)
                <option value="{{ $cargoType->id }}" @selected(old('cargo_type_id', $workOrder->cargo_type_id ?? '') == $cargoType->id)>

                    {{ $cargoType->name }}

                </option>
            @endforeach

        </select>

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

    {{-- OPERACION --}}
    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Operación
        </h5>

    </div>

    <div class="col-md-3 mb-3">

        <label class="form-label">
            Tipo de operación *
        </label>

        <select name="operation_type" class="form-select" required>

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

    <div class="col-md-3 mb-3">

        <label class="form-label">
            Tipo de servicio *
        </label>

        <select name="service_type" class="form-select" required>

            @foreach ([
        'TRANSPORT' => 'Transporte',
        'POSITIONING' => 'Posicionamiento',
        'PICKUP' => 'Retiro',
        'POSITIONING_PICKUP' => 'Posición y retiro',
        'TRANSFER' => 'Transferencia',
        'OTHER' => 'Otro',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('service_type', $workOrder->service_type ?? 'TRANSPORT') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

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

    <div class="col-md-2 mb-3">

        <label class="form-label">
            Viajes solicitados *
        </label>

        <input type="number" name="requested_trips" min="1" max="500" class="form-control"
            value="{{ old('requested_trips', $workOrder->requested_trips ?? 1) }}" required>

    </div>

    {{-- ORIGEN --}}
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

        <select name="origin_location_id" class="form-select">

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

        <select name="destination_location_id" class="form-select">

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

    {{-- PLANIFICACION --}}
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

    {{-- CONTENEDOR --}}
    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Requerimiento de carga y contenedor
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

        <input type="number" step="0.01" min="0" name="estimated_weight_kg" class="form-control"
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

    <button class="btn btn-primary">

        <i class="ti ti-device-floppy me-1"></i>

        {{ isset($workOrder) ? 'Actualizar orden' : 'Guardar orden' }}

    </button>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const clientSelect =
            document.getElementById('client_id');

        const subclientSelect =
            document.getElementById('subclient_id');

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


        function filterByClient(select) {

            if (!select) {
                return;
            }

            const selectedClient =
                clientSelect.value;

            Array.from(select.options)
                .forEach(function(option) {

                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }

                    const client =
                        option.dataset.client;

                    option.hidden =
                        client !== selectedClient;
                });

            const selectedOption =
                select.options[
                    select.selectedIndex
                ];

            if (
                selectedOption &&
                selectedOption.value &&
                selectedOption.dataset.client !==
                selectedClient
            ) {

                select.value = '';
            }
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


        function toggleOrigin() {

            if (
                originType.value === 'PLANT'
            ) {

                originPlantGroup.style.display =
                    '';

                originLocationGroup.style.display =
                    'none';

            } else {

                originPlantGroup.style.display =
                    'none';

                originLocationGroup.style.display =
                    '';
            }
        }


        function toggleDestination() {

            if (
                destinationType.value ===
                'PLANT'
            ) {

                destinationPlantGroup.style.display =
                    '';

                destinationLocationGroup.style.display =
                    'none';

            } else {

                destinationPlantGroup.style.display =
                    'none';

                destinationLocationGroup.style.display =
                    '';
            }
        }


        clientSelect.addEventListener(
            'change',
            filterClientData
        );

        originType.addEventListener(
            'change',
            toggleOrigin
        );

        destinationType.addEventListener(
            'change',
            toggleDestination
        );


        filterClientData();

        toggleOrigin();

        toggleDestination();

    });
</script>

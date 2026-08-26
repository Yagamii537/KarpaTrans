<div class="row">

    {{-- ========================================================= --}}
    {{-- CONDUCTOR --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <h5 class="fw-semibold mb-2">
            Restricción operativa
        </h5>

        <p class="text-muted mb-4">

            Defina cuándo un conductor debe generar
            una advertencia o quedar bloqueado para una operación.

        </p>

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Conductor *
        </label>

        <select name="driver_id" class="form-select @error('driver_id') is-invalid @enderror" required>

            <option value="">
                Seleccione conductor
            </option>


            @foreach ($drivers as $driver)
                <option value="{{ $driver->id }}" @selected(old('driver_id', $driverRestriction->driver_id ?? '') == $driver->id)>

                    {{ $driver->full_name }}

                    -

                    {{ $driver->identification }}

                </option>
            @endforeach

        </select>


        @error('driver_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Acción *
        </label>

        <select name="action_type" class="form-select" required>

            <option value="BLOCK" @selected(old('action_type', $driverRestriction->action_type ?? 'BLOCK') === 'BLOCK')>

                Bloquear asignación

            </option>


            <option value="WARNING" @selected(old('action_type', $driverRestriction->action_type ?? '') === 'WARNING')>

                Solo advertir

            </option>

        </select>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Tipo *
        </label>

        <select name="restriction_type" id="restriction_type" class="form-select" required>

            <option value="INDEFINITE" @selected(old('restriction_type', $driverRestriction->restriction_type ?? 'INDEFINITE') === 'INDEFINITE')>

                Indefinida

            </option>


            <option value="TEMPORARY" @selected(old('restriction_type', $driverRestriction->restriction_type ?? '') === 'TEMPORARY')>

                Temporal

            </option>

        </select>

    </div>


    {{-- ========================================================= --}}
    {{-- ALCANCE --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-2">
            Alcance de la restricción
        </h5>

        <p class="text-muted mb-3">

            Los campos que deje vacíos no limitarán
            la restricción.

            Si deja todos vacíos, la restricción
            será general para el conductor.

        </p>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Cliente
        </label>

        <select name="client_id" id="restriction_client_id" class="form-select">

            <option value="">
                Todos los clientes
            </option>


            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected(old('client_id', $driverRestriction->client_id ?? '') == $client->id)>

                    {{ $client->business_name }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Subcliente
        </label>

        <select name="subclient_id" id="restriction_subclient_id" class="form-select">

            <option value="">
                Todos los subclientes
            </option>


            @foreach ($subclients as $subclient)
                <option value="{{ $subclient->id }}" data-client="{{ $subclient->client_id }}"
                    @selected(old('subclient_id', $driverRestriction->subclient_id ?? '') == $subclient->id)>

                    {{ $subclient->business_name }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Tipo de operación
        </label>

        <select name="operation_type" class="form-select">

            <option value="">
                Todas las operaciones
            </option>


            @foreach ([
        'EXPORT' => 'Exportación',
        'IMPORT' => 'Importación',
        'TRANSFER' => 'Transferencia',
        'OTHER' => 'Otra',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('operation_type', $driverRestriction->operation_type ?? '') === $value)>

                    {{ $label }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Planta
        </label>

        <select name="plant_id" id="restriction_plant_id" class="form-select">

            <option value="">
                Todas las plantas
            </option>


            @foreach ($plants as $plant)
                <option value="{{ $plant->id }}" data-client="{{ $plant->client_id }}"
                    @selected(old('plant_id', $driverRestriction->plant_id ?? '') == $plant->id)>

                    {{ $plant->name }}

                </option>
            @endforeach

        </select>

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Ubicación
        </label>

        <select name="location_id" class="form-select">

            <option value="">
                Todas las ubicaciones
            </option>


            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(old('location_id', $driverRestriction->location_id ?? '') == $location->id)>

                    {{ $location->name }}

                    @if ($location->city)
                        - {{ $location->city }}
                    @endif

                </option>
            @endforeach

        </select>

    </div>


    {{-- ========================================================= --}}
    {{-- MOTIVO --}}
    {{-- ========================================================= --}}

    <div class="col-12">

        <hr>

        <h5 class="fw-semibold mb-3">
            Motivo y vigencia
        </h5>

    </div>


    <div class="col-12 mb-3">

        <label class="form-label">
            Motivo *
        </label>

        <textarea name="reason" rows="2" class="form-control @error('reason') is-invalid @enderror"
            placeholder="Ej.: El conductor no puede regresar a esta planta por disposición del cliente." required>{{ old('reason', $driverRestriction->reason ?? '') }}</textarea>


        @error('reason')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Desde *
        </label>

        <input type="date" name="start_date" class="form-control"
            value="{{ old(
                'start_date',
                isset($driverRestriction?->start_date) ? $driverRestriction->start_date->format('Y-m-d') : now()->format('Y-m-d'),
            ) }}"
            required>

    </div>


    <div class="col-md-6 mb-3" id="end_date_group">

        <label class="form-label">
            Hasta *
        </label>

        <input type="date" name="end_date" id="end_date" class="form-control"
            value="{{ old('end_date', isset($driverRestriction?->end_date) ? $driverRestriction->end_date->format('Y-m-d') : '') }}">

    </div>


    <div class="col-12 mb-3">

        <label class="form-label">
            Observaciones
        </label>

        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $driverRestriction->notes ?? '') }}</textarea>

    </div>


    <div class="col-12 mb-4">

        <div class="form-check form-switch">

            <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input"
                @checked(old('is_active', isset($driverRestriction) ? $driverRestriction->is_active : true))>


            <label for="is_active" class="form-check-label">

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


<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const clientSelect =
                document.getElementById(
                    'restriction_client_id'
                );

            const subclientSelect =
                document.getElementById(
                    'restriction_subclient_id'
                );

            const plantSelect =
                document.getElementById(
                    'restriction_plant_id'
                );

            const restrictionType =
                document.getElementById(
                    'restriction_type'
                );

            const endDateGroup =
                document.getElementById(
                    'end_date_group'
                );

            const endDate =
                document.getElementById(
                    'end_date'
                );


            /*
            |--------------------------------------------------------------------------
            | CLIENTE / SUBCLIENTE / PLANTA
            |--------------------------------------------------------------------------
            */

            function filterByClient(
                select
            ) {

                const clientId =
                    clientSelect.value;


                Array.from(
                    select.options
                ).forEach(
                    function(option) {

                        if (!option.value) {

                            option.hidden =
                                false;

                            return;
                        }


                        /*
                         * Sin cliente:
                         * mostrar todos.
                         */

                        if (!clientId) {

                            option.hidden =
                                false;

                            return;
                        }


                        option.hidden =
                            option.dataset.client !==
                            clientId;
                    }
                );
            }


            function refreshClientFilters() {
                filterByClient(
                    subclientSelect
                );

                filterByClient(
                    plantSelect
                );


                const selectedSubclient =
                    subclientSelect.options[
                        subclientSelect.selectedIndex
                    ];


                if (
                    selectedSubclient &&
                    selectedSubclient.value &&
                    clientSelect.value &&
                    selectedSubclient.dataset.client !==
                    clientSelect.value
                ) {

                    subclientSelect.value =
                        '';
                }


                const selectedPlant =
                    plantSelect.options[
                        plantSelect.selectedIndex
                    ];


                if (
                    selectedPlant &&
                    selectedPlant.value &&
                    clientSelect.value &&
                    selectedPlant.dataset.client !==
                    clientSelect.value
                ) {

                    plantSelect.value =
                        '';
                }
            }


            /*
            |--------------------------------------------------------------------------
            | VIGENCIA
            |--------------------------------------------------------------------------
            */

            function refreshRestrictionType() {
                const temporary =
                    restrictionType.value ===
                    'TEMPORARY';


                endDateGroup.style.display =
                    temporary ?
                    '' :
                    'none';


                endDate.required =
                    temporary;


                if (!temporary) {

                    endDate.value =
                        '';
                }
            }


            /*
            |--------------------------------------------------------------------------
            | EVENTOS
            |--------------------------------------------------------------------------
            */

            clientSelect.addEventListener(
                'change',
                refreshClientFilters
            );


            restrictionType.addEventListener(
                'change',
                refreshRestrictionType
            );


            /*
            |--------------------------------------------------------------------------
            | INICIALIZACIÓN
            |--------------------------------------------------------------------------
            */

            refreshClientFilters();

            refreshRestrictionType();
        }
    );
</script>

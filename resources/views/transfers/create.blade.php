@extends('layouts.app')

@section('title', 'Nueva transferencia | Karpan Logística')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">

                Nueva transferencia

            </h4>


            <p class="text-muted mb-0">

                Registrar un movimiento adicional
                dentro del viaje

                <strong>
                    {{ $trip->trip_number }}
                </strong>.

            </p>

        </div>


        <a href="{{ route('trips.show', $trip) }}" class="btn btn-light">

            <i class="ti ti-arrow-left me-1"></i>

            Regresar al viaje

        </a>

    </div>


    @if ($errors->any())

        <div class="alert alert-danger">

            @foreach ($errors->all() as $error)
                <div>
                    {{ $error }}
                </div>
            @endforeach

        </div>

    @endif


    <div class="row">

        <div class="col-lg-8">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">

                        Datos de la transferencia

                    </h5>


                    <form method="POST" action="{{ route('transfers.store', $trip) }}">

                        @csrf


                        <div class="row">

                            {{-- ORIGEN --}}

                            <div class="col-md-4 mb-3">

                                <label class="form-label">

                                    Tipo de origen *

                                </label>


                                <select name="origin_type" id="origin_type" class="form-select" required>

                                    <option value="PLANT" @selected(old('origin_type', 'PLANT') === 'PLANT')>

                                        Planta

                                    </option>


                                    <option value="LOCATION" @selected(old('origin_type') === 'LOCATION')>

                                        Ubicación

                                    </option>

                                </select>

                            </div>


                            <div class="col-md-8 mb-3" id="origin_plant_group">

                                <label class="form-label">

                                    Planta de origen *

                                </label>


                                <select name="origin_plant_id" class="form-select">

                                    <option value="">

                                        Seleccione planta

                                    </option>


                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->id }}" @selected(old('origin_plant_id') == $plant->id)>

                                            {{ $plant->name }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-8 mb-3" id="origin_location_group" style="display:none;">

                                <label class="form-label">

                                    Ubicación de origen *

                                </label>


                                <select name="origin_location_id" class="form-select">

                                    <option value="">

                                        Seleccione ubicación

                                    </option>


                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" @selected(old('origin_location_id') == $location->id)>

                                            {{ $location->name }}

                                            @if ($location->city)
                                                - {{ $location->city }}
                                            @endif

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            {{-- DESTINO --}}

                            <div class="col-md-4 mb-3">

                                <label class="form-label">

                                    Tipo de destino *

                                </label>


                                <select name="destination_type" id="destination_type" class="form-select" required>

                                    <option value="PLANT" @selected(old('destination_type', 'PLANT') === 'PLANT')>

                                        Planta

                                    </option>


                                    <option value="LOCATION" @selected(old('destination_type') === 'LOCATION')>

                                        Ubicación

                                    </option>

                                </select>

                            </div>


                            <div class="col-md-8 mb-3" id="destination_plant_group">

                                <label class="form-label">

                                    Planta de destino *

                                </label>


                                <select name="destination_plant_id" class="form-select">

                                    <option value="">

                                        Seleccione planta

                                    </option>


                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->id }}" @selected(old('destination_plant_id') == $plant->id)>

                                            {{ $plant->name }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-8 mb-3" id="destination_location_group" style="display:none;">

                                <label class="form-label">

                                    Ubicación de destino *

                                </label>


                                <select name="destination_location_id" class="form-select">

                                    <option value="">

                                        Seleccione ubicación

                                    </option>


                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" @selected(old('destination_location_id') == $location->id)>

                                            {{ $location->name }}

                                            @if ($location->city)
                                                - {{ $location->city }}
                                            @endif

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            {{-- FECHA --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Fecha / hora programada

                                </label>


                                <input type="datetime-local" name="scheduled_at" class="form-control"
                                    value="{{ old('scheduled_at') }}">

                            </div>


                            {{-- MOTIVO --}}

                            <div class="col-12 mb-3">

                                <label class="form-label">

                                    Motivo de la transferencia *

                                </label>


                                <textarea name="reason" rows="3" class="form-control"
                                    placeholder="Ej.: Cliente solicita movilizar la carga desde Planta A hacia Planta B." required>{{ old('reason') }}</textarea>

                            </div>


                            <div class="col-12 mb-4">

                                <label class="form-label">

                                    Observaciones

                                </label>


                                <textarea name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>

                            </div>

                        </div>


                        <div class="d-flex justify-content-end gap-2">

                            <a href="{{ route('trips.show', $trip) }}" class="btn btn-light">

                                Cancelar

                            </a>


                            <button type="submit" class="btn btn-primary">

                                <i class="ti ti-device-floppy me-1"></i>

                                Guardar transferencia

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <div class="col-lg-4">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-3">

                        Viaje relacionado

                    </h5>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Viaje

                        </small>

                        <strong>

                            {{ $trip->trip_number }}

                        </strong>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Orden de trabajo

                        </small>

                        {{ $trip->workOrder->work_order_number }}

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Cliente

                        </small>

                        {{ $trip->client_name_snapshot }}

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Servicio

                        </small>

                        {{ $trip->service_stage_label }}

                    </div>


                    <div class="alert alert-light border mb-0">

                        La transferencia es una
                        suboperación relacionada con este viaje.

                        No crea una nueva Orden de Trabajo.

                    </div>

                </div>

            </div>

        </div>

    </div>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const originType =
                    document.getElementById(
                        'origin_type'
                    );


                const destinationType =
                    document.getElementById(
                        'destination_type'
                    );


                const originPlantGroup =
                    document.getElementById(
                        'origin_plant_group'
                    );


                const originLocationGroup =
                    document.getElementById(
                        'origin_location_group'
                    );


                const destinationPlantGroup =
                    document.getElementById(
                        'destination_plant_group'
                    );


                const destinationLocationGroup =
                    document.getElementById(
                        'destination_location_group'
                    );


                function refreshOrigin() {
                    const plant =
                        originType.value ===
                        'PLANT';


                    originPlantGroup.style.display =
                        plant ?
                        '' :
                        'none';


                    originLocationGroup.style.display =
                        plant ?
                        'none' :
                        '';
                }


                function refreshDestination() {
                    const plant =
                        destinationType.value ===
                        'PLANT';


                    destinationPlantGroup.style.display =
                        plant ?
                        '' :
                        'none';


                    destinationLocationGroup.style.display =
                        plant ?
                        'none' :
                        '';
                }


                originType.addEventListener(
                    'change',
                    refreshOrigin
                );


                destinationType.addEventListener(
                    'change',
                    refreshDestination
                );


                refreshOrigin();

                refreshDestination();
            }
        );
    </script>

@endsection

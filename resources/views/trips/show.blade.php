@extends('layouts.app')

@section('title', 'Detalle de viaje | Karpan Logística')

@section('content')

    {{-- ========================================================= --}}
    {{-- MENSAJES --}}
    {{-- ========================================================= --}}

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">

            <i class="ti ti-alert-circle me-1"></i>

            {{ session('warning') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>

        </div>
    @endif


    @if ($errors->any())

        <div class="alert alert-danger">

            <div class="fw-semibold mb-2">

                <i class="ti ti-alert-circle me-1"></i>

                Se encontraron los siguientes errores:

            </div>

            @foreach ($errors->all() as $error)
                <div>
                    {{ $error }}
                </div>
            @endforeach

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">

                {{ $trip->trip_number }}

            </h4>


            <div class="d-flex flex-wrap gap-2 mb-2">

                <span class="badge {{ $trip->service_stage_badge_class }}">

                    {{ $trip->service_stage_label }}

                </span>


                <span class="badge bg-light text-dark border">

                    Servicio #{{ $trip->service_number ?: '-' }}

                </span>


                @if ($trip->workOrder->service_modality === 'POSITIONING_PICKUP')
                    <span class="badge bg-light text-dark border">

                        {{ $trip->service_stage === 'POSITIONING' ? 'Etapa 1 de 2' : 'Etapa 2 de 2' }}

                    </span>
                @else
                    <span class="badge bg-light text-dark border">

                        Etapa única

                    </span>
                @endif

            </div>


            <p class="text-muted mb-0">

                Orden:

                <strong>

                    {{ $trip->workOrder->work_order_number }}

                </strong>

                · Booking:

                <strong>

                    {{ $trip->booking_number ?: 'No registrado' }}

                </strong>

            </p>

        </div>


        <div class="d-flex gap-2">

            <a href="{{ route('trips.index') }}" class="btn btn-light">

                <i class="ti ti-arrow-left me-1"></i>

                Regresar

            </a>


            @if (!in_array($trip->status, ['COMPLETED', 'CANCELLED']))
                <a href="{{ route('trips.edit', $trip) }}" class="btn btn-outline-primary">

                    <i class="ti ti-edit me-1"></i>

                    Editar planificación

                </a>
            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- BLOQUEO RETIRO --}}
    {{-- ========================================================= --}}

    @if ($trip->service_stage === 'PICKUP' && !$stageUnlocked)

        <div class="alert alert-warning">

            <div class="d-flex gap-2">

                <div>

                    <i class="ti ti-lock fs-5"></i>

                </div>


                <div>

                    <div class="fw-semibold">

                        Retiro pendiente de la etapa de Posición

                    </div>


                    <div class="small mt-1">

                        Este viaje corresponde a la

                        <strong>
                            Etapa 2 de 2
                        </strong>

                        del Servicio #{{ $trip->service_number }}.

                        Antes de asignar recursos o registrar
                        eventos debe completarse la etapa de

                        <strong>
                            Posición.
                        </strong>

                    </div>


                    @if ($positioningTrip)
                        <div class="mt-2">

                            Viaje de Posición:

                            <a href="{{ route('trips.show', $positioningTrip) }}" class="fw-semibold">

                                {{ $positioningTrip->trip_number }}

                            </a>


                            <span
                                class="badge ms-2
                                @if ($positioningTrip->status === 'COMPLETED') bg-success-subtle text-success
                                @else
                                    bg-primary-subtle text-primary @endif">

                                {{ $positioningTrip->status_label }}

                            </span>

                        </div>
                    @endif

                </div>

            </div>

        </div>

    @endif


    <div class="row">

        {{-- ========================================================= --}}
        {{-- COLUMNA PRINCIPAL --}}
        {{-- ========================================================= --}}

        <div class="col-lg-8">


            {{-- ========================================================= --}}
            {{-- INFORMACIÓN --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">

                        Información del viaje

                    </h5>


                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Cliente

                            </small>

                            <div class="fw-semibold">

                                {{ $trip->client_name_snapshot }}

                            </div>

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Subcliente

                            </small>

                            {{ $trip->subclient_name_snapshot ?: 'No aplica' }}

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Tipo de carga

                            </small>

                            {{ $trip->cargo_type_name_snapshot ?: 'No definido' }}

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Tipo de operación

                            </small>

                            <span class="badge bg-primary-subtle text-primary">

                                {{ $trip->operation_type_label }}

                            </span>

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Modalidad OT

                            </small>

                            <strong>

                                {{ $trip->workOrder->service_modality_label }}

                            </strong>

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Etapa operativa

                            </small>

                            <span class="badge {{ $trip->service_stage_badge_class }}">

                                {{ $trip->service_stage_label }}

                            </span>

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Servicio dentro de OT

                            </small>

                            <strong>

                                #{{ $trip->service_number ?: '-' }}

                            </strong>

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Progreso

                            </small>


                            @if ($trip->workOrder->service_modality === 'POSITIONING_PICKUP')
                                {{ $trip->service_stage === 'POSITIONING' ? 'Etapa 1 de 2' : 'Etapa 2 de 2' }}
                            @else
                                Etapa única
                            @endif

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Booking

                            </small>

                            {{ $trip->booking_number ?: '-' }}

                        </div>


                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">

                                Origen

                            </small>

                            <div class="fw-semibold">

                                {{ $trip->origin_name_snapshot }}

                            </div>

                        </div>


                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">

                                Destino

                            </small>

                            <div class="fw-semibold">

                                {{ $trip->destination_name_snapshot }}

                            </div>

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Inicio programado

                            </small>

                            {{ $trip->scheduled_start_at ? $trip->scheduled_start_at->format('d/m/Y H:i') : 'No definido' }}

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Fin estimado

                            </small>

                            {{ $trip->scheduled_end_at ? $trip->scheduled_end_at->format('d/m/Y H:i') : 'No definido' }}

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Estado

                            </small>

                            <span
                                class="badge
                                @if ($trip->status === 'COMPLETED') bg-success-subtle text-success
                                @elseif ($trip->status === 'CANCELLED')
                                    bg-danger-subtle text-danger
                                @elseif ($trip->status === 'IN_TRANSIT')
                                    bg-warning-subtle text-warning
                                @elseif ($trip->status === 'AT_DESTINATION')
                                    bg-info-subtle text-info
                                @else
                                    bg-primary-subtle text-primary @endif">

                                {{ $trip->status_label }}

                            </span>

                        </div>

                    </div>


                    @if ($trip->notes)
                        <hr>


                        <h6 class="fw-semibold mb-2">

                            Observaciones

                        </h6>


                        <p class="mb-0">

                            {{ $trip->notes }}

                        </p>
                    @endif

                </div>

            </div>

            {{-- ========================================================= --}}
            {{-- STAND-BY --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                        <div>

                            <h5 class="fw-semibold mb-1">
                                Stand-by
                            </h5>

                            <p class="text-muted mb-0">
                                Cálculo automático según la regla
                                congelada en la Orden de Trabajo.
                            </p>

                        </div>


                        @if ($trip->standbyCalculation?->status === 'CALCULATED')
                            <span class="badge bg-success-subtle text-success">

                                Calculado

                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning">

                                Pendiente

                            </span>
                        @endif

                    </div>


                    @php

                        $standby = $trip->standbyCalculation;

                    @endphp


                    @if ($standby)

                        <div class="row">

                            <div class="col-md-4 mb-3">

                                <small class="text-muted d-block">
                                    Proceso
                                </small>

                                <strong>
                                    {{ $standby->process_type_label }}
                                </strong>

                            </div>


                            <div class="col-md-4 mb-3">

                                <small class="text-muted d-block">
                                    Inicio de conteo
                                </small>

                                {{ $standby->count_start_type_label }}

                            </div>


                            <div class="col-md-4 mb-3">

                                <small class="text-muted d-block">
                                    Horas libres
                                </small>

                                <strong>
                                    {{ $standby->free_hours }} h
                                </strong>

                            </div>


                            <div class="col-md-4 mb-3">

                                <small class="text-muted d-block">
                                    Hora solicitada
                                </small>

                                {{ $standby->requested_at ? $standby->requested_at->format('d/m/Y H:i') : 'No definida' }}

                            </div>


                            <div class="col-md-4 mb-3">

                                <small class="text-muted d-block">
                                    Llegada real
                                </small>

                                {{ $standby->arrival_at ? $standby->arrival_at->format('d/m/Y H:i') : 'Pendiente' }}

                            </div>


                            <div class="col-md-4 mb-3">

                                <small class="text-muted d-block">
                                    Inicio efectivo
                                </small>

                                {{ $standby->start_at ? $standby->start_at->format('d/m/Y H:i') : 'Pendiente' }}

                            </div>


                            <div class="col-md-4 mb-3">

                                <small class="text-muted d-block">
                                    Fin del cálculo
                                </small>

                                {{ $standby->end_at ? $standby->end_at->format('d/m/Y H:i') : 'Pendiente' }}

                            </div>


                            <div class="col-md-4 mb-3">

                                <small class="text-muted d-block">
                                    Tiempo total
                                </small>

                                @if ($standby->total_minutes !== null)
                                    {{ intdiv($standby->total_minutes, 60) }}
                                    h

                                    {{ $standby->total_minutes % 60 }} min
                                @else
                                    Pendiente
                                @endif

                            </div>


                            <div class="col-md-4 mb-3">

                                <small class="text-muted d-block">
                                    Exceso luego de horas libres
                                </small>

                                {{ $standby->excess_minutes }} min

                            </div>

                        </div>


                        <hr>


                        <div class="row align-items-center">

                            <div class="col-md-6">

                                <small class="text-muted d-block">
                                    Fracción de facturación
                                </small>

                                {{ $standby->fraction_minutes }}
                                minutos

                            </div>


                            <div class="col-md-6 text-md-end mt-3 mt-md-0">

                                <small class="text-muted d-block">
                                    Horas Stand-by facturables
                                </small>

                                <div class="fs-4 fw-bold">

                                    {{ $standby->billable_hours }} h

                                </div>

                            </div>

                        </div>


                        @if ($standby->observation)
                            <div class="alert alert-light border mt-3 mb-0">

                                {{ $standby->observation }}

                            </div>
                        @endif
                    @else
                        <div class="alert alert-light border mb-0">

                            El cálculo comenzará automáticamente
                            cuando se registren los eventos necesarios.

                        </div>

                    @endif

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- RECURSOS --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">

                        {{ $trip->status === 'COMPLETED' ? 'Recursos utilizados' : 'Asignación actual' }}

                    </h5>


                    @if ($displayAssignment)

                        <div class="row">

                            <div class="col-md-6 col-xl-3 mb-3">

                                <small class="text-muted d-block">

                                    Conductor

                                </small>


                                <div class="fw-semibold">

                                    {{ $displayAssignment->driver?->full_name ?: '-' }}

                                </div>


                                <small class="text-muted">

                                    {{ $displayAssignment->driver?->identification ?: '' }}

                                </small>

                            </div>


                            <div class="col-md-6 col-xl-3 mb-3">

                                <small class="text-muted d-block">

                                    Vehículo

                                </small>


                                <div class="fw-semibold">

                                    {{ $displayAssignment->vehicle?->plate ?: '-' }}

                                </div>


                                <small class="text-muted">

                                    {{ $displayAssignment->vehicle?->brand }}

                                    {{ $displayAssignment->vehicle?->model }}

                                </small>

                            </div>


                            <div class="col-md-6 col-xl-3 mb-3">

                                <small class="text-muted d-block">

                                    Chasis portacontenedor

                                </small>


                                <div class="fw-semibold">

                                    {{ $displayAssignment->chassis?->code ?: 'No asignado' }}

                                </div>

                            </div>


                            <div class="col-md-6 col-xl-3 mb-3">

                                <small class="text-muted d-block">

                                    Contenedor

                                </small>


                                <div class="fw-semibold">

                                    {{ $displayAssignment->container?->container_number ?: 'No asignado' }}

                                </div>


                                @if ($displayAssignment->container)
                                    <small class="text-muted">

                                        {{ $displayAssignment->container->container_size }}

                                        ·

                                        {{ $displayAssignment->container->type_label }}

                                    </small>
                                @endif

                            </div>

                        </div>


                        <hr>


                        <div class="row">

                            <div class="col-md-4 mb-2">

                                <small class="text-muted d-block">

                                    Asignado

                                </small>


                                {{ $displayAssignment->assigned_at ? $displayAssignment->assigned_at->format('d/m/Y H:i') : '-' }}

                            </div>


                            <div class="col-md-4 mb-2">

                                <small class="text-muted d-block">

                                    Liberado

                                </small>


                                {{ $displayAssignment->unassigned_at
                                    ? $displayAssignment->unassigned_at->format('d/m/Y H:i')
                                    : 'Asignación actual' }}

                            </div>


                            <div class="col-md-4 mb-2">

                                <small class="text-muted d-block">

                                    Estado de asignación

                                </small>


                                @if ($trip->status === 'COMPLETED')
                                    <span class="badge bg-success-subtle text-success">

                                        Operación finalizada

                                    </span>
                                @elseif (!$displayAssignment->unassigned_at)
                                    <span class="badge bg-primary-subtle text-primary">

                                        Activa

                                    </span>
                                @else
                                    <span class="badge bg-light text-dark">

                                        Histórica

                                    </span>
                                @endif

                            </div>

                        </div>
                    @else
                        <div class="alert alert-warning mb-0">

                            <i class="ti ti-alert-circle me-1"></i>

                            El viaje todavía no tiene recursos asignados.

                        </div>

                    @endif

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- ASIGNACIÓN --}}
            {{-- ========================================================= --}}

            @if (!in_array($trip->status, ['COMPLETED', 'CANCELLED']) && $stageUnlocked)

                @php

                    $selectedDriverId = old('driver_id', $trip->activeAssignment?->driver_id);

                    $selectedVehicleId = old('vehicle_id', $trip->activeAssignment?->vehicle_id);

                    $selectedChassisId = old('chassis_id', $trip->activeAssignment?->chassis_id);

                    $selectedContainerId = old(
                        'container_id',

                        $trip->activeAssignment?->container_id ?? $suggestedContainer?->id,
                    );

                    $selectedDriver = $drivers->firstWhere('id', (int) $selectedDriverId);

                    $selectedVehicle = $vehicles->firstWhere('id', (int) $selectedVehicleId);

                    $selectedChassis = $chassisList->firstWhere('id', (int) $selectedChassisId);

                    $selectedContainer = $containers->firstWhere('id', (int) $selectedContainerId);
                @endphp


                <div class="card">

                    <div class="card-body">

                        <h5 class="fw-semibold mb-1">

                            {{ $trip->activeAssignment ? 'Reasignar recursos' : 'Asignar recursos' }}

                        </h5>


                        <p class="text-muted mb-3">

                            Escriba para buscar por nombre,
                            identificación, placa, código o
                            número de contenedor.

                        </p>


                        @if ($suggestedContainer && !$trip->activeAssignment)
                            <div class="alert alert-info py-2 mb-4">

                                <i class="ti ti-info-circle me-1"></i>

                                Este retiro pertenece al mismo
                                Servicio #{{ $trip->service_number }}.

                                Se propone utilizar nuevamente
                                el contenedor

                                <strong>

                                    {{ $suggestedContainer->container_number }}

                                </strong>

                                utilizado durante la Posición.

                            </div>
                        @endif


                        <form method="POST" action="{{ route('trips.assign', $trip) }}" id="assignment_form">

                            @csrf


                            <div class="row">

                                {{-- CONDUCTOR --}}

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Conductor *

                                    </label>


                                    <input type="text" id="driver_search" list="driver_options"
                                        class="form-control @error('driver_id') is-invalid @enderror" autocomplete="off"
                                        placeholder="Escriba nombre, apellido o identificación"
                                        value="{{ $selectedDriver ? $selectedDriver->full_name . ' - ' . $selectedDriver->identification : '' }}"
                                        required>


                                    <input type="hidden" name="driver_id" id="driver_id"
                                        value="{{ $selectedDriverId }}">


                                    <datalist id="driver_options">

                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->full_name }} - {{ $driver->identification }}"
                                                data-id="{{ $driver->id }}">
                                            </option>
                                        @endforeach

                                    </datalist>


                                    <small class="text-muted">

                                        Buscar por nombre, apellido o cédula.

                                    </small>


                                    @error('driver_id')
                                        <div class="invalid-feedback d-block">

                                            {{ $message }}

                                        </div>
                                    @enderror

                                </div>


                                {{-- VEHÍCULO --}}

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Vehículo *

                                    </label>


                                    <input type="text" id="vehicle_search" list="vehicle_options"
                                        class="form-control @error('vehicle_id') is-invalid @enderror" autocomplete="off"
                                        placeholder="Escriba placa, código, marca o modelo"
                                        value="{{ $selectedVehicle
                                            ? $selectedVehicle->plate . ' - ' . $selectedVehicle->brand . ' ' . $selectedVehicle->model
                                            : '' }}"
                                        required>


                                    <input type="hidden" name="vehicle_id" id="vehicle_id"
                                        value="{{ $selectedVehicleId }}">


                                    <datalist id="vehicle_options">

                                        @foreach ($vehicles as $vehicle)
                                            <option
                                                value="{{ $vehicle->plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}{{ $vehicle->internal_code ? ' - ' . $vehicle->internal_code : '' }}"
                                                data-id="{{ $vehicle->id }}">
                                            </option>
                                        @endforeach

                                    </datalist>


                                    <small class="text-muted">

                                        Buscar por placa, código, marca o modelo.

                                    </small>


                                    @error('vehicle_id')
                                        <div class="invalid-feedback d-block">

                                            {{ $message }}

                                        </div>
                                    @enderror

                                </div>


                                {{-- CHASIS --}}

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Chasis portacontenedor

                                    </label>


                                    <input type="text" id="chassis_search" list="chassis_options"
                                        class="form-control @error('chassis_id') is-invalid @enderror" autocomplete="off"
                                        placeholder="Escriba código o placa"
                                        value="{{ $selectedChassis ? $selectedChassis->display_name : '' }}">


                                    <input type="hidden" name="chassis_id" id="chassis_id"
                                        value="{{ $selectedChassisId }}">


                                    <datalist id="chassis_options">

                                        @foreach ($chassisList as $chassis)
                                            <option value="{{ $chassis->display_name }}" data-id="{{ $chassis->id }}">
                                            </option>
                                        @endforeach

                                    </datalist>


                                    <small class="text-muted">

                                        Déjelo vacío si la operación no requiere chasis.

                                    </small>


                                    @error('chassis_id')
                                        <div class="invalid-feedback d-block">

                                            {{ $message }}

                                        </div>
                                    @enderror

                                </div>


                                {{-- CONTENEDOR --}}

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Contenedor

                                    </label>


                                    <input type="text" id="container_search" list="container_options"
                                        class="form-control @error('container_id') is-invalid @enderror"
                                        autocomplete="off" placeholder="Escriba número, tamaño o tipo"
                                        value="{{ $selectedContainer
                                            ? $selectedContainer->container_number .
                                                ' - ' .
                                                $selectedContainer->container_size .
                                                ' ' .
                                                $selectedContainer->type_label
                                            : '' }}">


                                    <input type="hidden" name="container_id" id="container_id"
                                        value="{{ $selectedContainerId }}">


                                    <datalist id="container_options">

                                        @foreach ($containers as $container)
                                            <option
                                                value="{{ $container->container_number }} - {{ $container->container_size }} {{ $container->type_label }}"
                                                data-id="{{ $container->id }}">
                                            </option>
                                        @endforeach

                                    </datalist>


                                    @if ($trip->workOrder->requested_container_type || $trip->workOrder->requested_container_size)
                                        <small class="text-muted d-block">

                                            Requerido por la OT:

                                            <strong>

                                                {{ $trip->workOrder->requested_container_type ?: 'Tipo libre' }}

                                                /

                                                {{ $trip->workOrder->requested_container_size ?: 'Tamaño libre' }}

                                            </strong>

                                        </small>
                                    @else
                                        <small class="text-muted">

                                            Déjelo vacío si todavía no se conoce
                                            el contenedor real.

                                        </small>
                                    @endif


                                    @error('container_id')
                                        <div class="invalid-feedback d-block">

                                            {{ $message }}

                                        </div>
                                    @enderror

                                </div>


                                <div class="col-12 mb-3">

                                    <label class="form-label">

                                        Motivo / observación

                                    </label>


                                    <textarea name="assignment_reason" rows="2" class="form-control"
                                        placeholder="Ej.: asignación inicial, cambio de conductor, cambio de cabezal...">{{ old('assignment_reason') }}</textarea>

                                </div>

                            </div>


                            <div class="alert alert-light border py-2">

                                <small class="text-muted">

                                    Antes de guardar se validará:
                                    restricciones del conductor,
                                    disponibilidad de recursos,
                                    capacidad del vehículo,
                                    contenedor y compatibilidad del chasis.

                                </small>

                            </div>


                            <button type="submit" class="btn btn-primary">

                                <i class="ti ti-check me-1"></i>


                                {{ $trip->activeAssignment ? 'Guardar reasignación' : 'Guardar asignación' }}

                            </button>

                        </form>

                    </div>

                </div>

            @endif


            {{-- ========================================================= --}}
            {{-- CONTROL DE TIEMPOS --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                        <div>

                            <h5 class="fw-semibold mb-1">

                                Control de tiempos

                            </h5>

                            <p class="text-muted mb-0">

                                Los eventos disponibles dependen
                                automáticamente de la etapa del viaje.

                            </p>

                        </div>


                        <span class="badge bg-primary-subtle text-primary">

                            {{ $trip->times->count() }} evento(s)

                        </span>

                    </div>


                    <div class="alert alert-light border mb-4">

                        <div class="fw-semibold mb-1">

                            <i class="ti ti-route me-1"></i>

                            Secuencia principal para

                            {{ $trip->service_stage_label }}

                        </div>


                        <div class="small text-muted">

                            {{ $eventSequenceHelp }}

                        </div>


                        <div class="small text-muted mt-1">

                            Un evento ya registrado no vuelve
                            a estar disponible.

                        </div>

                    </div>


                    @if (!$stageUnlocked)

                        <div class="alert alert-warning">

                            <i class="ti ti-lock me-1"></i>

                            Los eventos de Retiro se habilitarán
                            cuando la etapa de Posición esté completada.

                        </div>
                    @elseif (!in_array($trip->status, ['CANCELLED', 'COMPLETED']))
                        @if ($trip->activeAssignment)

                            @if (count($availableEvents) > 0)

                                <form method="POST" action="{{ route('trips.times.store', $trip) }}" class="mb-4">

                                    @csrf


                                    <div class="row">

                                        <div class="col-md-4 mb-3">

                                            <label class="form-label">

                                                Tipo de evento *

                                            </label>


                                            <select name="event_type"
                                                class="form-select @error('event_type') is-invalid @enderror" required>

                                                <option value="">

                                                    Seleccione

                                                </option>


                                                @foreach ($availableEvents as $value => $label)
                                                    <option value="{{ $value }}" @selected(old('event_type') === $value)>

                                                        {{ $label }}

                                                    </option>
                                                @endforeach

                                            </select>


                                            @error('event_type')
                                                <div class="invalid-feedback">

                                                    {{ $message }}

                                                </div>
                                            @enderror

                                        </div>


                                        <div class="col-md-4 mb-3">

                                            <label class="form-label">

                                                Fecha y hora *

                                            </label>


                                            <input type="datetime-local" name="event_at"
                                                class="form-control @error('event_at') is-invalid @enderror"
                                                value="{{ old('event_at', now()->format('Y-m-d\TH:i')) }}" required>


                                            @error('event_at')
                                                <div class="invalid-feedback">

                                                    {{ $message }}

                                                </div>
                                            @enderror

                                        </div>


                                        <div class="col-md-4 mb-3">

                                            <label class="form-label">

                                                Tipo de ubicación

                                            </label>


                                            <select name="location_type" id="event_location_type" class="form-select">

                                                <option value="NONE" @selected(old('location_type', 'NONE') === 'NONE')>

                                                    Sin ubicación

                                                </option>


                                                <option value="LOCATION" @selected(old('location_type') === 'LOCATION')>

                                                    Ubicación

                                                </option>


                                                <option value="PLANT" @selected(old('location_type') === 'PLANT')>

                                                    Planta

                                                </option>

                                            </select>

                                        </div>


                                        <div class="col-md-6 mb-3" id="event_location_group" style="display:none;">

                                            <label class="form-label">

                                                Ubicación

                                            </label>


                                            <select name="location_id" class="form-select">

                                                <option value="">

                                                    Seleccione ubicación

                                                </option>


                                                @foreach ($locations as $location)
                                                    <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>

                                                        {{ $location->name }}

                                                        @if ($location->city)
                                                            - {{ $location->city }}
                                                        @endif

                                                    </option>
                                                @endforeach

                                            </select>

                                        </div>


                                        <div class="col-md-6 mb-3" id="event_plant_group" style="display:none;">

                                            <label class="form-label">

                                                Planta

                                            </label>


                                            <select name="plant_id" class="form-select">

                                                <option value="">

                                                    Seleccione planta

                                                </option>


                                                @foreach ($plants as $plant)
                                                    <option value="{{ $plant->id }}" @selected(old('plant_id') == $plant->id)>

                                                        {{ $plant->name }}

                                                    </option>
                                                @endforeach

                                            </select>

                                        </div>


                                        <div class="col-12 mb-3">

                                            <label class="form-label">

                                                Observación

                                            </label>


                                            <textarea name="observation" rows="2" class="form-control"
                                                placeholder="Novedad, referencia o comentario del evento">{{ old('observation') }}</textarea>

                                        </div>

                                    </div>


                                    <button type="submit" class="btn btn-primary">

                                        <i class="ti ti-clock me-1"></i>

                                        Registrar evento

                                    </button>

                                </form>
                            @else
                                <div class="alert alert-info">

                                    No quedan eventos disponibles
                                    para registrar en esta etapa.

                                </div>

                            @endif
                        @else
                            <div class="alert alert-warning">

                                <i class="ti ti-alert-circle me-1"></i>

                                Primero debe asignar conductor y
                                vehículo antes de registrar eventos.

                            </div>

                        @endif
                    @elseif ($trip->status === 'COMPLETED')
                        <div class="alert alert-success">

                            <i class="ti ti-circle-check me-1"></i>

                            El viaje está completado.
                            La línea de tiempo queda bloqueada
                            para preservar la trazabilidad.

                        </div>
                    @else
                        <div class="alert alert-secondary">

                            El viaje está cancelado y no permite
                            registrar nuevos eventos.

                        </div>

                    @endif


                    <hr>


                    <h5 class="fw-semibold mb-4">

                        Línea de tiempo

                    </h5>


                    @forelse ($trip->times
                                            as $event)
                        <div class="d-flex gap-3 mb-4">

                            <div>

                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                    style="width:44px;height:44px;">

                                    <i class="ti ti-clock"></i>

                                </div>

                            </div>


                            <div class="flex-grow-1">

                                <div class="d-flex flex-wrap justify-content-between gap-2">

                                    <div>

                                        <div class="fw-semibold">

                                            {{ $event->event_type_label }}

                                        </div>


                                        <small class="text-muted">

                                            {{ $event->event_at->format('d/m/Y H:i') }}

                                        </small>

                                    </div>


                                    <div>

                                        @if ($event->location_name_snapshot)
                                            <span class="badge bg-light text-dark border">

                                                <i class="ti ti-map-pin me-1"></i>

                                                {{ $event->location_name_snapshot }}

                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border">

                                                Sin ubicación

                                            </span>
                                        @endif

                                    </div>

                                </div>


                                @if ($event->observation)
                                    <p class="mt-2 mb-1">

                                        {{ $event->observation }}

                                    </p>
                                @endif


                                <small class="text-muted">

                                    Registrado por:

                                    {{ $event->creator?->name ?: 'Sistema' }}

                                    @if ($event->is_manual)
                                        · Registro manual
                                    @endif

                                </small>

                            </div>

                        </div>


                    @empty

                        <div class="text-center py-5 text-muted">

                            <i class="ti ti-clock fs-7 d-block mb-2"></i>

                            No existen eventos registrados
                            para este viaje.

                        </div>
                    @endforelse

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- HISTORIAL ASIGNACIONES --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">

                        Historial de asignaciones

                    </h5>


                    <div class="table-responsive">

                        <table class="table align-middle">

                            <thead>

                                <tr>

                                    <th>Asignado</th>
                                    <th>Conductor</th>
                                    <th>Vehículo</th>
                                    <th>Chasis</th>
                                    <th>Contenedor</th>
                                    <th>Hasta</th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse ($trip->assignments
                                                        as $assignment)
                                    <tr>

                                        <td>

                                            {{ $assignment->assigned_at->format('d/m/Y H:i') }}

                                        </td>


                                        <td>

                                            {{ $assignment->driver?->full_name ?: '-' }}

                                        </td>


                                        <td>

                                            {{ $assignment->vehicle?->plate ?: '-' }}

                                        </td>


                                        <td>

                                            {{ $assignment->chassis?->code ?: '-' }}

                                        </td>


                                        <td>

                                            {{ $assignment->container?->container_number ?: '-' }}

                                        </td>


                                        <td>

                                            @if ($assignment->unassigned_at)
                                                {{ $assignment->unassigned_at->format('d/m/Y H:i') }}
                                            @else
                                                <span class="badge bg-success-subtle text-success">

                                                    Actual

                                                </span>
                                            @endif

                                        </td>

                                    </tr>


                                @empty

                                    <tr>

                                        <td colspan="6" class="text-center py-4 text-muted">

                                            No existen asignaciones registradas.

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- DERECHA --}}
        {{-- ========================================================= --}}

        <div class="col-lg-4">


            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">

                        Etapa operativa

                    </h5>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Servicio

                        </small>

                        <strong>

                            #{{ $trip->service_number ?: '-' }}

                        </strong>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Modalidad OT

                        </small>

                        {{ $trip->workOrder->service_modality_label }}

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Etapa

                        </small>

                        <span class="badge {{ $trip->service_stage_badge_class }}">

                            {{ $trip->service_stage_label }}

                        </span>

                    </div>


                    @if ($trip->workOrder->service_modality === 'POSITIONING_PICKUP')
                        <div class="mb-3">

                            <small class="text-muted d-block">

                                Progreso del servicio

                            </small>


                            <strong>

                                {{ $trip->service_stage === 'POSITIONING' ? 'Etapa 1 de 2' : 'Etapa 2 de 2' }}

                            </strong>

                        </div>


                        <div class="alert alert-light border mb-0">

                            <strong>
                                Posición + Retiro
                            </strong>

                            <br>

                            <small class="text-muted">

                                Cada etapa tiene su propia
                                planificación, asignación,
                                eventos y estado.

                            </small>

                        </div>
                    @endif

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- ESTADO --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-3">

                        Estado del viaje

                    </h5>


                    <div class="mb-3">

                        <span
                            class="badge
                            @if ($trip->status === 'COMPLETED') bg-success-subtle text-success
                            @elseif ($trip->status === 'CANCELLED')
                                bg-danger-subtle text-danger
                            @elseif ($trip->status === 'IN_TRANSIT')
                                bg-warning-subtle text-warning
                            @elseif ($trip->status === 'AT_DESTINATION')
                                bg-info-subtle text-info
                            @else
                                bg-primary-subtle text-primary @endif fs-4">

                            {{ $trip->status_label }}

                        </span>

                    </div>


                    @if (!$stageUnlocked)
                        <div class="alert alert-warning">

                            <i class="ti ti-lock me-1"></i>

                            Esperando que finalice la etapa
                            de Posición.

                        </div>
                    @else
                        <div class="alert alert-light border">

                            <i class="ti ti-refresh me-1"></i>

                            El estado se actualiza
                            automáticamente según los eventos.

                        </div>
                    @endif


                    @if (!in_array($trip->status, ['COMPLETED', 'CANCELLED']))
                        <hr>


                        <h6 class="fw-semibold mb-2 text-danger">

                            Cancelar viaje

                        </h6>


                        <form method="POST" action="{{ route('trips.status', $trip) }}"
                            onsubmit="return confirm('¿Está seguro de cancelar este viaje?');">

                            @csrf


                            <input type="hidden" name="status" value="CANCELLED">


                            <div class="mb-3">

                                <label class="form-label">

                                    Motivo *

                                </label>


                                <textarea name="reason" rows="2" class="form-control" required>{{ old('reason') }}</textarea>

                            </div>


                            <button type="submit" class="btn btn-outline-danger w-100">

                                <i class="ti ti-ban me-1"></i>

                                Cancelar viaje

                            </button>

                        </form>
                    @endif

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- HISTORIAL --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">

                        Historial de estados

                    </h5>


                    @forelse ($trip->statusHistory
                                            as $history)
                        <div class="border-start border-2 ps-3 mb-4">

                            <div class="fw-semibold">

                                {{ match ($history->new_status) {
                                    'PENDING' => 'Pendiente',
                                
                                    'ASSIGNED' => 'Asignado',
                                
                                    'IN_TRANSIT' => 'En tránsito',
                                
                                    'AT_DESTINATION' => 'En destino',
                                
                                    'COMPLETED' => 'Completado',
                                
                                    'CANCELLED' => 'Cancelado',
                                
                                    default => $history->new_status,
                                } }}

                            </div>


                            <small class="text-muted d-block">

                                {{ $history->changed_at->format('d/m/Y H:i') }}

                            </small>


                            @if ($history->reason)
                                <div class="small mt-1">

                                    {{ $history->reason }}

                                </div>
                            @endif

                        </div>


                    @empty

                        <div class="text-muted">

                            Sin historial de estados.

                        </div>
                    @endforelse

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- ORDEN --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">

                        Orden de trabajo

                    </h5>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Número

                        </small>


                        <a href="{{ route('work-orders.show', $trip->workOrder) }}" class="fw-semibold">

                            {{ $trip->workOrder->work_order_number }}

                        </a>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Booking

                        </small>

                        {{ $trip->booking_number ?: 'No registrado' }}

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Servicios solicitados

                        </small>

                        {{ $trip->workOrder->requested_trips }}

                    </div>


                    <div>

                        <small class="text-muted d-block">

                            Modalidad

                        </small>

                        {{ $trip->workOrder->service_modality_label }}

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                /*
                |--------------------------------------------------------------------------
                | UBICACIÓN EVENTOS
                |--------------------------------------------------------------------------
                */

                const type =
                    document.getElementById(
                        'event_location_type'
                    );


                const locationGroup =
                    document.getElementById(
                        'event_location_group'
                    );


                const plantGroup =
                    document.getElementById(
                        'event_plant_group'
                    );


                function toggleLocation() {
                    if (
                        !type ||
                        !locationGroup ||
                        !plantGroup
                    ) {
                        return;
                    }


                    locationGroup.style.display =
                        type.value === 'LOCATION' ?
                        '' :
                        'none';


                    plantGroup.style.display =
                        type.value === 'PLANT' ?
                        '' :
                        'none';
                }


                if (type) {

                    type.addEventListener(
                        'change',
                        toggleLocation
                    );


                    toggleLocation();
                }


                /*
                |--------------------------------------------------------------------------
                | BUSCADORES DE ASIGNACIÓN
                |--------------------------------------------------------------------------
                */

                function bindSearch(
                    inputId,
                    hiddenId,
                    datalistId
                ) {

                    const input =
                        document.getElementById(
                            inputId
                        );


                    const hidden =
                        document.getElementById(
                            hiddenId
                        );


                    const datalist =
                        document.getElementById(
                            datalistId
                        );


                    if (
                        !input ||
                        !hidden ||
                        !datalist
                    ) {
                        return;
                    }


                    function syncId() {
                        const typedValue =
                            input
                            .value
                            .trim();


                        let foundId =
                            '';


                        Array
                            .from(
                                datalist.options
                            )

                            .some(
                                function(option) {

                                    if (
                                        option.value ===
                                        typedValue
                                    ) {

                                        foundId =
                                            option
                                            .dataset
                                            .id ||
                                            '';


                                        return true;
                                    }


                                    return false;
                                }
                            );


                        hidden.value =
                            foundId;
                    }


                    input.addEventListener(
                        'input',
                        syncId
                    );


                    input.addEventListener(
                        'change',
                        syncId
                    );
                }


                bindSearch(
                    'driver_search',
                    'driver_id',
                    'driver_options'
                );


                bindSearch(
                    'vehicle_search',
                    'vehicle_id',
                    'vehicle_options'
                );


                bindSearch(
                    'chassis_search',
                    'chassis_id',
                    'chassis_options'
                );


                bindSearch(
                    'container_search',
                    'container_id',
                    'container_options'
                );


                const form =
                    document.getElementById(
                        'assignment_form'
                    );


                if (form) {

                    form.addEventListener(
                        'submit',
                        function(event) {

                            const driverId =
                                document
                                .getElementById(
                                    'driver_id'
                                )
                                ?.value;


                            const vehicleId =
                                document
                                .getElementById(
                                    'vehicle_id'
                                )
                                ?.value;


                            if (
                                !driverId ||
                                !vehicleId
                            ) {

                                event.preventDefault();


                                alert(
                                    'Seleccione un conductor y un vehículo válidos de la lista.'
                                );
                            }
                        }
                    );
                }
            }
        );
    </script>

@endsection

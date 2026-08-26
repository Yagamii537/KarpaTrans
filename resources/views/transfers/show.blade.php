@extends('layouts.app')

@section('title', 'Detalle de transferencia | Karpan Logística')

@section('content')

    {{-- ========================================================= --}}
    {{-- MENSAJES --}}
    {{-- ========================================================= --}}

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">

            <i class="ti ti-circle-check me-1"></i>

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">

            <i class="ti ti-alert-circle me-1"></i>

            {{ session('warning') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

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

                {{ $transfer->transfer_number }}

            </h4>


            <div class="d-flex flex-wrap gap-2 mb-2">

                <span class="badge {{ $transfer->status_badge_class }}">

                    {{ $transfer->status_label }}

                </span>


                <span class="badge bg-light text-dark border">

                    Transferencia

                </span>

            </div>


            <p class="text-muted mb-0">

                Viaje:

                <strong>

                    {{ $transfer->trip->trip_number }}

                </strong>

                · OT:

                <strong>

                    {{ $transfer->trip->workOrder->work_order_number }}

                </strong>

            </p>

        </div>


        <div class="d-flex flex-wrap gap-2">

            <a href="{{ route('transfers.index') }}" class="btn btn-light">

                <i class="ti ti-arrow-left me-1"></i>

                Transferencias

            </a>


            <a href="{{ route('trips.show', $transfer->trip) }}"
                class="btn btn-outline-primary">

                <i class="ti ti-eye me-1"></i>

                Ver viaje

            </a>

        </div>

    </div>


    <div class="row">

        {{-- ========================================================= --}}
        {{-- COLUMNA PRINCIPAL --}}
        {{-- ========================================================= --}}

        <div class="col-lg-8">

            {{-- ========================================================= --}}
            {{-- INFORMACIÓN DE TRANSFERENCIA --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">

                        Información de transferencia

                    </h5>


                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Cliente

                            </small>

                            <strong>

                                {{ $transfer->trip->client_name_snapshot }}

                            </strong>

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Subcliente

                            </small>

                            {{ $transfer->trip->subclient_name_snapshot ?: 'No aplica' }}

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Tipo de carga

                            </small>

                            {{ $transfer->trip->cargo_type_name_snapshot ?: 'No definido' }}

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Estado

                            </small>

                            <span class="badge {{ $transfer->status_badge_class }}">

                                {{ $transfer->status_label }}

                            </span>

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Programada

                            </small>

                            {{ $transfer->scheduled_at ? $transfer->scheduled_at->format('d/m/Y H:i') : 'No definida' }}

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">

                                Registrada

                            </small>

                            {{ $transfer->created_at->format('d/m/Y H:i') }}

                        </div>


                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">

                                Origen

                            </small>

                            <div class="fw-semibold">

                                {{ $transfer->origin_name_snapshot }}

                            </div>

                            <small class="text-muted">

                                {{ $transfer->origin_type === 'PLANT' ? 'Planta' : 'Ubicación' }}

                            </small>

                        </div>


                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">

                                Destino

                            </small>

                            <div class="fw-semibold">

                                {{ $transfer->destination_name_snapshot }}

                            </div>

                            <small class="text-muted">

                                {{ $transfer->destination_type === 'PLANT' ? 'Planta' : 'Ubicación' }}

                            </small>

                        </div>


                        @if ($transfer->started_at)
                            <div class="col-md-4 mb-3">

                                <small class="text-muted d-block">

                                    Inicio real

                                </small>

                                {{ $transfer->started_at->format('d/m/Y H:i') }}

                            </div>
                        @endif


                        @if ($transfer->completed_at)
                            <div class="col-md-4 mb-3">

                                <small class="text-muted d-block">

                                    Finalización

                                </small>

                                {{ $transfer->completed_at->format('d/m/Y H:i') }}

                            </div>
                        @endif

                    </div>


                    <hr>


                    <h6 class="fw-semibold">

                        Motivo

                    </h6>

                    <p>

                        {{ $transfer->reason }}

                    </p>


                    @if ($transfer->notes)
                        <h6 class="fw-semibold mt-4">

                            Observaciones

                        </h6>

                        <p class="mb-0">

                            {{ $transfer->notes }}

                        </p>
                    @endif

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- RECURSOS ACTUALES / UTILIZADOS --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">

                        {{ $transfer->status === 'COMPLETED' ? 'Recursos utilizados' : 'Asignación actual' }}

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

                                    Estado

                                </small>

                                @if (!$displayAssignment->unassigned_at)
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

                            La transferencia todavía no tiene recursos asignados.

                        </div>

                    @endif

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- ASIGNAR / REASIGNAR RECURSOS --}}
            {{-- ========================================================= --}}

            @if (!in_array($transfer->status, ['COMPLETED', 'CANCELLED']))

                @php

                    $driverId = old(
                        'driver_id',
                        $transfer->activeAssignment?->driver_id ?? $parentAssignment?->driver_id,
                    );

                    $vehicleId = old(
                        'vehicle_id',
                        $transfer->activeAssignment?->vehicle_id ?? $parentAssignment?->vehicle_id,
                    );

                    $chassisId = old(
                        'chassis_id',
                        $transfer->activeAssignment?->chassis_id ?? $parentAssignment?->chassis_id,
                    );

                    $containerId = old(
                        'container_id',
                        $transfer->activeAssignment?->container_id ?? $parentAssignment?->container_id,
                    );

                    $selectedDriver = $drivers->firstWhere('id', (int) $driverId);

                    $selectedVehicle = $vehicles->firstWhere('id', (int) $vehicleId);

                    $selectedChassis = $chassisList->firstWhere('id', (int) $chassisId);

                    $selectedContainer = $containers->firstWhere('id', (int) $containerId);
                @endphp


                <div class="card">

                    <div class="card-body">

                        <h5 class="fw-semibold mb-1">

                            {{ $transfer->activeAssignment ? 'Reasignar recursos' : 'Asignar recursos' }}

                        </h5>


                        <p class="text-muted mb-4">

                            Por defecto se proponen los recursos del viaje principal.
                            Puede cambiarlos si esta transferencia utiliza otros recursos.

                        </p>


                        <form method="POST"
                            action="{{ route('transfers.assign', $transfer) }}"
                            id="transfer_assignment_form">

                            @csrf


                            <div class="row">

                                {{-- CONDUCTOR --}}

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Conductor *

                                    </label>


                                    <input type="text" id="driver_search" list="driver_options"
                                        class="form-control @error('driver_id') is-invalid @enderror" autocomplete="off"
                                        placeholder="Nombre, apellido o identificación"
                                        value="{{ $selectedDriver ? $selectedDriver->full_name . ' - ' . $selectedDriver->identification : '' }}"
                                        required>


                                    <input type="hidden" name="driver_id" id="driver_id" value="{{ $driverId }}">


                                    <datalist id="driver_options">

                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->full_name }} - {{ $driver->identification }}"
                                                data-id="{{ $driver->id }}"></option>
                                        @endforeach

                                    </datalist>


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
                                        placeholder="Placa, marca o modelo"
                                        value="{{ $selectedVehicle
                                            ? $selectedVehicle->plate . ' - ' . $selectedVehicle->brand . ' ' . $selectedVehicle->model
                                            : '' }}"
                                        required>


                                    <input type="hidden" name="vehicle_id" id="vehicle_id"
                                        value="{{ $vehicleId }}">


                                    <datalist id="vehicle_options">

                                        @foreach ($vehicles as $vehicle)
                                            <option
                                                value="{{ $vehicle->plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}"
                                                data-id="{{ $vehicle->id }}"></option>
                                        @endforeach

                                    </datalist>


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
                                        placeholder="Código o placa"
                                        value="{{ $selectedChassis ? $selectedChassis->display_name : '' }}">


                                    <input type="hidden" name="chassis_id" id="chassis_id"
                                        value="{{ $chassisId }}">


                                    <datalist id="chassis_options">

                                        @foreach ($chassisList as $chassis)
                                            <option value="{{ $chassis->display_name }}" data-id="{{ $chassis->id }}">
                                            </option>
                                        @endforeach

                                    </datalist>


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
                                        autocomplete="off" placeholder="Número, tamaño o tipo"
                                        value="{{ $selectedContainer
                                            ? $selectedContainer->container_number .
                                                ' - ' .
                                                $selectedContainer->container_size .
                                                ' ' .
                                                $selectedContainer->type_label
                                            : '' }}">


                                    <input type="hidden" name="container_id" id="container_id"
                                        value="{{ $containerId }}">


                                    <datalist id="container_options">

                                        @foreach ($containers as $container)
                                            <option
                                                value="{{ $container->container_number }} - {{ $container->container_size }} {{ $container->type_label }}"
                                                data-id="{{ $container->id }}"></option>
                                        @endforeach

                                    </datalist>


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
                                        placeholder="Ej.: mismos recursos del viaje, cambio de conductor, cambio de cabezal...">{{ old('assignment_reason') }}</textarea>

                                </div>

                            </div>


                            <div class="alert alert-light border py-2">

                                <small class="text-muted">

                                    Antes de guardar se validarán restricciones
                                    del conductor, disponibilidad y compatibilidad
                                    de los recursos.

                                </small>

                            </div>


                            <button type="submit" class="btn btn-primary">

                                <i class="ti ti-check me-1"></i>

                                {{ $transfer->activeAssignment ? 'Guardar reasignación' : 'Guardar asignación' }}

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

                                Secuencia operativa de la transferencia.

                            </p>

                        </div>


                        <span class="badge bg-primary-subtle text-primary">

                            {{ $transfer->events->count() }}
                            evento(s)

                        </span>

                    </div>


                    <div class="alert alert-light border mb-4">

                        <div class="fw-semibold">

                            <i class="ti ti-route me-1"></i>

                            Secuencia principal

                        </div>

                        <small class="text-muted">

                            Llegada al origen
                            →
                            Salida del origen
                            →
                            Llegada al destino
                            →
                            Entrega

                        </small>

                    </div>


                    @if (!in_array($transfer->status, ['COMPLETED', 'CANCELLED']))

                        @if ($transfer->activeAssignment)

                            @if (count($availableEvents) > 0)

                                <form method="POST"
                                    action="{{ route('transfers.events.store', $transfer) }}"
                                    class="mb-4">

                                    @csrf


                                    <div class="row">

                                        <div class="col-md-4 mb-3">

                                            <label class="form-label">

                                                Evento *

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

                                                Fecha / hora *

                                            </label>


                                            <input type="datetime-local" name="event_at"
                                                class="form-control @error('event_at') is-invalid @enderror"
                                                value="{{ old('event_at', now()->format('Y-m-d\TH:i')) }}"
                                                required>


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


                                                <option value="PLANT" @selected(old('location_type') === 'PLANT')>

                                                    Planta

                                                </option>


                                                <option value="LOCATION" @selected(old('location_type') === 'LOCATION')>

                                                    Ubicación

                                                </option>

                                            </select>

                                        </div>


                                        <div class="col-md-6 mb-3" id="event_plant_group" style="display:none;">

                                            <label class="form-label">

                                                Planta

                                            </label>


                                            <select name="plant_id" class="form-select">

                                                <option value="">

                                                    Seleccione

                                                </option>


                                                @foreach ($plants as $plant)
                                                    <option value="{{ $plant->id }}" @selected(old('plant_id') == $plant->id)>

                                                        {{ $plant->name }}

                                                    </option>
                                                @endforeach

                                            </select>

                                        </div>


                                        <div class="col-md-6 mb-3" id="event_location_group" style="display:none;">

                                            <label class="form-label">

                                                Ubicación

                                            </label>


                                            <select name="location_id" class="form-select">

                                                <option value="">

                                                    Seleccione

                                                </option>


                                                @foreach ($locations as $location)
                                                    <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>

                                                        {{ $location->name }}

                                                    </option>
                                                @endforeach

                                            </select>

                                        </div>


                                        <div class="col-12 mb-3">

                                            <label class="form-label">

                                                Observación

                                            </label>


                                            <textarea name="observation" rows="2" class="form-control">{{ old('observation') }}</textarea>

                                        </div>

                                    </div>


                                    <button type="submit" class="btn btn-primary">

                                        <i class="ti ti-clock me-1"></i>

                                        Registrar evento

                                    </button>

                                </form>
                            @else
                                <div class="alert alert-info">

                                    No quedan eventos pendientes
                                    para esta transferencia.

                                </div>

                            @endif
                        @else
                            <div class="alert alert-warning">

                                <i class="ti ti-alert-circle me-1"></i>

                                Primero debe asignar conductor y vehículo
                                a la transferencia.

                            </div>

                        @endif
                    @elseif ($transfer->status === 'COMPLETED')
                        <div class="alert alert-success">

                            <i class="ti ti-circle-check me-1"></i>

                            Transferencia completada.
                            La línea de tiempo se encuentra bloqueada.

                        </div>
                    @else
                        <div class="alert alert-secondary">

                            La transferencia está cancelada
                            y no permite nuevos eventos.

                        </div>

                    @endif


                    <hr>


                    {{-- ========================================================= --}}
                    {{-- LÍNEA DE TIEMPO --}}
                    {{-- ========================================================= --}}

                    <h5 class="fw-semibold mb-4">

                        Línea de tiempo

                    </h5>


                    @forelse ($transfer->events
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


                                    @if ($event->location_name_snapshot)
                                        <span class="badge bg-light text-dark border">

                                            <i class="ti ti-map-pin me-1"></i>

                                            {{ $event->location_name_snapshot }}

                                        </span>
                                    @endif

                                </div>


                                @if ($event->observation)
                                    <p class="mt-2 mb-1">

                                        {{ $event->observation }}

                                    </p>
                                @endif


                                <small class="text-muted">

                                    Registrado por:

                                    {{ $event->creator?->name ?: 'Sistema' }}

                                </small>

                            </div>

                        </div>


                    @empty

                        <div class="text-center py-4 text-muted">

                            <i class="ti ti-clock fs-7 d-block mb-2"></i>

                            No existen eventos registrados.

                        </div>
                    @endforelse

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- HISTORIAL DE ASIGNACIONES --}}
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

                                @forelse ($transfer->assignments
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
        {{-- COLUMNA DERECHA --}}
        {{-- ========================================================= --}}

        <div class="col-lg-4">

            {{-- ========================================================= --}}
            {{-- ESTADO --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-3">

                        Estado de transferencia

                    </h5>


                    <span class="badge {{ $transfer->status_badge_class }} fs-4">

                        {{ $transfer->status_label }}

                    </span>


                    <div class="alert alert-light border mt-3 mb-0">

                        <strong>
                            Flujo automático
                        </strong>

                        <br><br>

                        Asignación

                        <br>

                        ↓

                        <br>

                        Asignada

                        <br><br>

                        Salida del origen

                        <br>

                        ↓

                        <br>

                        En tránsito

                        <br><br>

                        Entrega

                        <br>

                        ↓

                        <br>

                        Completada

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- VIAJE PRINCIPAL --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">

                        Viaje principal

                    </h5>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Viaje

                        </small>


                        <a href="{{ route('trips.show', $transfer->trip) }}"
                            class="fw-semibold">

                            {{ $transfer->trip->trip_number }}

                        </a>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Orden de trabajo

                        </small>

                        <a
                            href="{{ route('work-orders.show', $transfer->trip->workOrder) }}">

                            {{ $transfer->trip->workOrder->work_order_number }}

                        </a>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Cliente

                        </small>

                        {{ $transfer->trip->client_name_snapshot }}

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">

                            Booking

                        </small>

                        {{ $transfer->trip->booking_number ?: 'No registrado' }}

                    </div>


                    <div>

                        <small class="text-muted d-block">

                            Etapa del viaje

                        </small>

                        <span class="badge {{ $transfer->trip->service_stage_badge_class }}">

                            {{ $transfer->trip->service_stage_label }}

                        </span>

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- HISTORIAL DE ESTADOS --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">

                        Historial de estados

                    </h5>


                    @forelse ($transfer->statusHistory
                            as $history)
                        <div class="border-start border-2 ps-3 mb-4">

                            <div class="fw-semibold">

                                {{ match ($history->new_status) {
                                    'PENDING' => 'Pendiente',
                                    'ASSIGNED' => 'Asignada',
                                    'IN_TRANSIT' => 'En tránsito',
                                    'COMPLETED' => 'Completada',
                                    'CANCELLED' => 'Cancelada',
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


                            <small class="text-muted">

                                {{ $history->user?->name ?: 'Sistema' }}

                            </small>

                        </div>


                    @empty

                        <div class="text-muted">

                            Sin historial de estados.

                        </div>
                    @endforelse

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- CANCELAR --}}
            {{-- ========================================================= --}}

            @if (!in_array($transfer->status, ['COMPLETED', 'CANCELLED']))
                <div class="card">

                    <div class="card-body">

                        <h5 class="fw-semibold text-danger mb-3">

                            Cancelar transferencia

                        </h5>


                        <p class="text-muted small">

                            Una transferencia cancelada no permitirá
                            nuevas asignaciones ni eventos.

                        </p>


                        <form method="POST"
                            action="{{ route('transfers.cancel', $transfer) }}"
                            onsubmit="return confirm('¿Está seguro de cancelar esta transferencia?');">

                            @csrf


                            <div class="mb-3">

                                <label class="form-label">

                                    Motivo *

                                </label>


                                <textarea name="reason" rows="3" class="form-control" required>{{ old('reason') }}</textarea>

                            </div>


                            <button type="submit" class="btn btn-outline-danger w-100">

                                <i class="ti ti-ban me-1"></i>

                                Cancelar transferencia

                            </button>

                        </form>

                    </div>

                </div>
            @endif

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
                | BUSCADORES DE RECURSOS
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
                                            option.dataset.id ||
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


                /*
                |--------------------------------------------------------------------------
                | VALIDAR FORMULARIO ASIGNACIÓN
                |--------------------------------------------------------------------------
                */

                const assignmentForm =
                    document.getElementById(
                        'transfer_assignment_form'
                    );


                if (assignmentForm) {

                    assignmentForm.addEventListener(
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


                /*
                |--------------------------------------------------------------------------
                | UBICACIÓN DEL EVENTO
                |--------------------------------------------------------------------------
                */

                const type =
                    document.getElementById(
                        'event_location_type'
                    );

                const plantGroup =
                    document.getElementById(
                        'event_plant_group'
                    );

                const locationGroup =
                    document.getElementById(
                        'event_location_group'
                    );


                function refreshEventLocation() {
                    if (
                        !type ||
                        !plantGroup ||
                        !locationGroup
                    ) {
                        return;
                    }


                    plantGroup.style.display =
                        type.value === 'PLANT' ?
                        '' :
                        'none';


                    locationGroup.style.display =
                        type.value === 'LOCATION' ?
                        '' :
                        'none';
                }


                if (type) {

                    type.addEventListener(
                        'change',
                        refreshEventLocation
                    );


                    refreshEventLocation();
                }

            }
        );
    </script>

@endsection

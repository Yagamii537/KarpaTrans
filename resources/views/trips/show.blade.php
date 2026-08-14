@extends('layouts.app')

@section('title', 'Detalle de viaje | Karpan Logística')

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-circle-check me-1"></i>

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-1"></i>

            {{ session('warning') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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


    <div class="row">

        {{-- ========================================================= --}}
        {{-- COLUMNA PRINCIPAL --}}
        {{-- ========================================================= --}}

        <div class="col-lg-8">

            {{-- ========================================================= --}}
            {{-- INFORMACIÓN DEL VIAJE --}}
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
                                Tipo de servicio
                            </small>

                            {{ $trip->service_type }}

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Viaje dentro de la orden
                            </small>

                            #{{ $trip->sequence_number }}

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

                            {{ $trip->scheduled_start_at->format('d/m/Y H:i') }}

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
            {{-- ASIGNACIÓN ACTUAL --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">
                        Asignación actual
                    </h5>

                    @if ($trip->activeAssignment)

                        <div class="row">

                            <div class="col-md-6 col-xl-3 mb-3">

                                <small class="text-muted d-block">
                                    Conductor
                                </small>

                                <div class="fw-semibold">
                                    {{ $trip->activeAssignment->driver->full_name }}
                                </div>

                                <small class="text-muted">
                                    {{ $trip->activeAssignment->driver->identification }}
                                </small>

                            </div>


                            <div class="col-md-6 col-xl-3 mb-3">

                                <small class="text-muted d-block">
                                    Vehículo
                                </small>

                                <div class="fw-semibold">
                                    {{ $trip->activeAssignment->vehicle->plate }}
                                </div>

                                <small class="text-muted">

                                    {{ $trip->activeAssignment->vehicle->brand }}

                                    {{ $trip->activeAssignment->vehicle->model }}

                                </small>

                            </div>


                            <div class="col-md-6 col-xl-3 mb-3">

                                <small class="text-muted d-block">
                                    Chasis
                                </small>

                                <div class="fw-semibold">

                                    {{ $trip->activeAssignment->chassis?->code ?: 'No asignado' }}

                                </div>

                                @if ($trip->activeAssignment->chassis?->plate)
                                    <small class="text-muted">

                                        {{ $trip->activeAssignment->chassis->plate }}

                                    </small>
                                @endif

                            </div>


                            <div class="col-md-6 col-xl-3 mb-3">

                                <small class="text-muted d-block">
                                    Contenedor
                                </small>

                                <div class="fw-semibold">

                                    {{ $trip->activeAssignment->container?->container_number ?: 'No asignado' }}

                                </div>

                                @if ($trip->activeAssignment->container)
                                    <small class="text-muted">

                                        {{ $trip->activeAssignment->container->container_size }}

                                        ·

                                        {{ $trip->activeAssignment->container->type_label }}

                                    </small>
                                @endif

                            </div>

                        </div>


                        <hr>


                        <div class="row">

                            <div class="col-md-6 mb-2">

                                <small class="text-muted d-block">
                                    Fecha de asignación
                                </small>

                                {{ $trip->activeAssignment->assigned_at->format('d/m/Y H:i') }}

                            </div>


                            <div class="col-md-6 mb-2">

                                <small class="text-muted d-block">
                                    Asignado por
                                </small>

                                {{ $trip->activeAssignment->assignedBy?->name ?: 'Sistema' }}

                            </div>

                        </div>
                    @else
                        <div class="alert alert-warning mb-0">

                            <i class="ti ti-alert-circle me-1"></i>

                            El viaje todavía no tiene
                            conductor, vehículo, chasis o
                            contenedor asignados.

                        </div>

                    @endif

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- ASIGNAR / REASIGNAR RECURSOS --}}
            {{-- ========================================================= --}}

            @if (!in_array($trip->status, ['COMPLETED', 'CANCELLED']))

                <div class="card">

                    <div class="card-body">

                        <h5 class="fw-semibold mb-1">

                            {{ $trip->activeAssignment ? 'Reasignar recursos' : 'Asignar recursos' }}

                        </h5>

                        <p class="text-muted mb-4">

                            La asignación conserva historial
                            de conductor, vehículo, chasis
                            y contenedor.

                        </p>


                        <form method="POST"
                            action="{{ route('trips.assign', $trip) }}">

                            @csrf


                            <div class="row">

                                {{-- CONDUCTOR --}}

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Conductor *
                                    </label>

                                    <select name="driver_id" class="form-select @error('driver_id') is-invalid @enderror"
                                        required>

                                        <option value="">
                                            Seleccione conductor
                                        </option>

                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}" @selected(old('driver_id', $trip->activeAssignment?->driver_id) == $driver->id)>

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


                                {{-- VEHÍCULO --}}

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Vehículo *
                                    </label>

                                    <select name="vehicle_id"
                                        class="form-select @error('vehicle_id') is-invalid @enderror" required>

                                        <option value="">
                                            Seleccione vehículo
                                        </option>

                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $trip->activeAssignment?->vehicle_id) == $vehicle->id)>

                                                {{ $vehicle->plate }}

                                                -

                                                {{ $vehicle->brand }}

                                                {{ $vehicle->model }}

                                            </option>
                                        @endforeach

                                    </select>

                                    @error('vehicle_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- CHASIS --}}

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Chasis
                                    </label>

                                    <select name="chassis_id"
                                        class="form-select @error('chassis_id') is-invalid @enderror">

                                        <option value="">
                                            Sin chasis
                                        </option>

                                        @foreach ($chassisList as $chassis)
                                            <option value="{{ $chassis->id }}" @selected(old('chassis_id', $trip->activeAssignment?->chassis_id) == $chassis->id)>

                                                {{ $chassis->code }}

                                                @if ($chassis->plate)
                                                    - {{ $chassis->plate }}
                                                @endif

                                            </option>
                                        @endforeach

                                    </select>

                                    @error('chassis_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- CONTENEDOR --}}

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Contenedor
                                    </label>

                                    <select name="container_id"
                                        class="form-select @error('container_id') is-invalid @enderror">

                                        <option value="">
                                            Sin contenedor
                                        </option>

                                        @foreach ($containers as $container)
                                            <option value="{{ $container->id }}" @selected(old('container_id', $trip->activeAssignment?->container_id) == $container->id)>

                                                {{ $container->container_number }}

                                                -

                                                {{ $container->container_size }}

                                                {{ $container->type_label }}

                                            </option>
                                        @endforeach

                                    </select>

                                    @error('container_id')
                                        <div class="invalid-feedback">
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
                                Eventos reales registrados durante
                                la ejecución del viaje.
                            </p>

                        </div>

                        <span class="badge bg-primary-subtle text-primary">

                            {{ $trip->times->count() }}
                            evento(s)

                        </span>

                    </div>


                    {{-- REGISTRAR EVENTO --}}

                    @if ($trip->status !== 'CANCELLED')

                        <form method="POST"
                            action="{{ route('trips.times.store', $trip) }}"
                            class="mb-4">

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


                                        @foreach ([
            'ARRIVAL' => 'Llegada',
            'ENTRY' => 'Ingreso',
            'CONTAINER_PICKUP' => 'Retiro de contenedor',
            'LOAD_START' => 'Inicio de carga',
            'LOAD_END' => 'Fin de carga',
            'UNLOAD_START' => 'Inicio de descarga',
            'UNLOAD_END' => 'Fin de descarga',
            'WAIT_START' => 'Inicio de espera',
            'WAIT_END' => 'Fin de espera',
            'DEPARTURE' => 'Salida',
            'POSITIONING' => 'Posicionamiento',
            'PICKUP' => 'Retiro',
            'PORT_ARRIVAL' => 'Llegada a puerto',
            'DELIVERY' => 'Entrega',
            'OTHER' => 'Otro',
        ] as $value => $label)
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

                                        <option value="LOCATION" @selected(old('location_type') === 'LOCATION')>

                                            Ubicación

                                        </option>

                                        <option value="PLANT" @selected(old('location_type') === 'PLANT')>

                                            Planta

                                        </option>

                                    </select>

                                </div>


                                {{-- UBICACIÓN GENERAL --}}

                                <div class="col-md-6 mb-3" id="event_location_group" style="display:none;">

                                    <label class="form-label">
                                        Ubicación
                                    </label>

                                    <select name="location_id"
                                        class="form-select @error('location_id') is-invalid @enderror">

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

                                    @error('location_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- PLANTA --}}

                                <div class="col-md-6 mb-3" id="event_plant_group" style="display:none;">

                                    <label class="form-label">
                                        Planta
                                    </label>

                                    <select name="plant_id" class="form-select @error('plant_id') is-invalid @enderror">

                                        <option value="">
                                            Seleccione planta
                                        </option>

                                        @foreach ($plants as $plant)
                                            <option value="{{ $plant->id }}" @selected(old('plant_id') == $plant->id)>

                                                {{ $plant->name }}

                                                @if ($plant->client)
                                                    -
                                                    {{ $plant->client->business_name }}
                                                @endif

                                            </option>
                                        @endforeach

                                    </select>

                                    @error('plant_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                <div class="col-12 mb-3">

                                    <label class="form-label">
                                        Observación
                                    </label>

                                    <textarea name="observation" rows="2" class="form-control @error('observation') is-invalid @enderror"
                                        placeholder="Novedad, referencia o comentario del evento">{{ old('observation') }}</textarea>

                                    @error('observation')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>


                            <button type="submit" class="btn btn-primary">

                                <i class="ti ti-clock me-1"></i>
                                Registrar evento

                            </button>

                        </form>
                    @else
                        <div class="alert alert-secondary">

                            El viaje está cancelado y no
                            permite registrar nuevos eventos.

                        </div>

                    @endif


                    <hr>


                    {{-- ========================================================= --}}
                    {{-- LÍNEA DE TIEMPO --}}
                    {{-- ========================================================= --}}

                    <h5 class="fw-semibold mb-4">
                        Línea de tiempo
                    </h5>


                    @forelse ($trip->times as $event)
                        <div class="d-flex gap-3 mb-4">

                            {{-- ICONO --}}

                            <div>

                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                    style="width:44px;height:44px;">

                                    <i class="ti ti-clock"></i>

                                </div>

                            </div>


                            {{-- INFORMACIÓN --}}

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

                                @forelse (
                                    $trip->assignments
                                    as $assignment
                                )

                                    <tr>

                                        <td>

                                            {{ $assignment->assigned_at->format('d/m/Y H:i') }}

                                            @if ($assignment->assignedBy)
                                                <small class="text-muted d-block">

                                                    {{ $assignment->assignedBy->name }}

                                                </small>
                                            @endif

                                        </td>


                                        <td>

                                            {{ $assignment->driver->full_name }}

                                        </td>


                                        <td>

                                            {{ $assignment->vehicle->plate }}

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

                                                @if ($assignment->release_reason)
                                                    <small class="text-muted d-block">

                                                        {{ $assignment->release_reason }}

                                                    </small>
                                                @endif
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

                                            No existen asignaciones
                                            registradas.

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
        {{-- COLUMNA LATERAL --}}
        {{-- ========================================================= --}}

        <div class="col-lg-4">

            {{-- ========================================================= --}}
            {{-- CAMBIO DE ESTADO --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-3">
                        Estado del viaje
                    </h5>


                    <div class="mb-4">

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


                    <form method="POST"
                        action="{{ route('trips.status', $trip) }}">

                        @csrf


                        <div class="mb-3">

                            <label class="form-label">
                                Nuevo estado
                            </label>

                            <select name="status" class="form-select" required>

                                @foreach ([
            'PENDING' => 'Pendiente',
            'ASSIGNED' => 'Asignado',
            'IN_TRANSIT' => 'En tránsito',
            'AT_DESTINATION' => 'En destino',
            'COMPLETED' => 'Completado',
            'CANCELLED' => 'Cancelado',
        ] as $value => $label)
                                    <option value="{{ $value }}" @selected($trip->status === $value)>

                                        {{ $label }}

                                    </option>
                                @endforeach

                            </select>

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Motivo / observación
                            </label>

                            <textarea name="reason" rows="2" class="form-control" placeholder="Motivo del cambio de estado"></textarea>

                        </div>


                        <button type="submit" class="btn btn-outline-primary w-100">

                            <i class="ti ti-refresh me-1"></i>
                            Actualizar estado

                        </button>

                    </form>

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


                            @if ($history->user)
                                <small class="text-muted d-block">

                                    Usuario:
                                    {{ $history->user->name }}

                                </small>
                            @endif


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
            {{-- RESUMEN DE LA ORDEN --}}
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

                        <a href="{{ route('work-orders.show', $trip->workOrder) }}"
                            class="fw-semibold">

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
                            Orden del cliente
                        </small>

                        {{ $trip->customer_order_number ?: 'No registrada' }}

                    </div>


                    <div>

                        <small class="text-muted d-block">
                            Viajes solicitados
                        </small>

                        {{ $trip->workOrder->requested_trips }}

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- JAVASCRIPT DE UBICACIÓN DEL EVENTO --}}
    {{-- ========================================================= --}}

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

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


                if (!type) {
                    return;
                }


                function toggleLocation() {

                    if (
                        type.value === 'LOCATION'
                    ) {

                        locationGroup.style.display =
                            '';

                        plantGroup.style.display =
                            'none';

                    } else if (
                        type.value === 'PLANT'
                    ) {

                        locationGroup.style.display =
                            'none';

                        plantGroup.style.display =
                            '';

                    } else {

                        locationGroup.style.display =
                            'none';

                        plantGroup.style.display =
                            'none';
                    }
                }


                type.addEventListener(
                    'change',
                    toggleLocation
                );


                toggleLocation();
            }
        );
    </script>

@endsection

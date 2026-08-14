@extends('layouts.app')

@section('title', 'Orden de trabajo | Karpan Logística')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">

                {{ $workOrder->work_order_number }}

            </h4>

            <p class="text-muted mb-0">

                Booking:
                {{ $workOrder->booking_number ?: 'No registrado' }}

            </p>

        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('work-orders.index') }}" class="btn btn-light">

                <i class="ti ti-arrow-left me-1"></i>
                Regresar

            </a>

            <a href="{{ route('work-orders.edit', $workOrder) }}" class="btn btn-primary">

                <i class="ti ti-edit me-1"></i>
                Editar

            </a>

        </div>

    </div>

    <div class="row">

        <div class="col-lg-8">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">
                        Información de la orden
                    </h5>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Cliente
                            </small>

                            {{ $workOrder->client->business_name }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Subcliente
                            </small>

                            {{ $workOrder->subclient?->business_name ?: 'No aplica' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Tipo de carga
                            </small>

                            {{ $workOrder->cargoType?->name ?: 'No definido' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Operación
                            </small>

                            {{ $workOrder->operation_type_label }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Servicio
                            </small>

                            {{ $workOrder->service_type_label }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Planta principal
                            </small>

                            {{ $workOrder->plant?->name ?: 'No aplica' }}

                        </div>

                    </div>

                    <hr>

                    <h5 class="fw-semibold mb-4">
                        Ruta
                    </h5>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Origen
                            </small>

                            <h6>
                                {{ $workOrder->origin_name }}
                            </h6>

                        </div>

                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Destino
                            </small>

                            <h6>
                                {{ $workOrder->destination_name }}
                            </h6>

                        </div>

                    </div>

                    <hr>

                    <h5 class="fw-semibold mb-4">
                        Planificación
                    </h5>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Fecha solicitada
                            </small>

                            {{ $workOrder->requested_date->format('d/m/Y') }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Hora solicitada
                            </small>

                            {{ $workOrder->requested_time ? substr($workOrder->requested_time, 0, 5) : 'No definida' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Turno / cita
                            </small>

                            {{ $workOrder->appointment_at ? $workOrder->appointment_at->format('d/m/Y H:i') : 'No definido' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Viajes solicitados
                            </small>

                            <h5>
                                {{ $workOrder->requested_trips }}
                            </h5>

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Contenedor
                            </small>

                            {{ $workOrder->requested_container_size ?: '-' }}

                            {{ $workOrder->requested_container_type ?: '' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Peso estimado
                            </small>

                            {{ $workOrder->estimated_weight_kg
                                ? number_format((float) $workOrder->estimated_weight_kg, 2) . ' kg'
                                : 'No definido' }}

                        </div>

                    </div>

                    @if ($workOrder->cargo_description)
                        <hr>

                        <h5 class="fw-semibold mb-3">
                            Descripción de carga
                        </h5>

                        <p>
                            {{ $workOrder->cargo_description }}
                        </p>
                    @endif

                    @if ($workOrder->notes)
                        <hr>

                        <h5 class="fw-semibold mb-3">
                            Observaciones
                        </h5>

                        <p class="mb-0">
                            {{ $workOrder->notes }}
                        </p>
                    @endif

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">
                        Estado
                    </h5>

                    <div class="mb-4">

                        <span class="badge bg-primary-subtle text-primary fs-4">

                            {{ $workOrder->status_label }}

                        </span>

                    </div>

                    <hr>

                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Orden cliente
                        </small>

                        {{ $workOrder->customer_order_number ?: 'No registrada' }}

                    </div>

                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Creada por
                        </small>

                        {{ $workOrder->creator?->name ?: 'Sistema' }}

                    </div>

                    <div>

                        <small class="text-muted d-block">
                            Fecha de creación
                        </small>

                        {{ $workOrder->created_at->format('d/m/Y H:i') }}

                    </div>

                </div>

            </div>

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-3">
                        Viajes
                    </h5>

                    <p class="text-muted">

                        Esta orden solicita:

                    </p>

                    <h2 class="fw-bold">

                        {{ $workOrder->requested_trips }}

                    </h2>

                    <p class="text-muted mb-0">

                        viaje(s)

                    </p>

                    <hr>

                    <div class="card">

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <div>

                                    <h5 class="fw-semibold mb-1">
                                        Viajes
                                    </h5>

                                    <small class="text-muted">

                                        Solicitados:
                                        {{ $workOrder->requested_trips }}

                                        · Generados:
                                        {{ $workOrder->trips->count() }}

                                    </small>

                                </div>

                                @if ($workOrder->trips->count() < $workOrder->requested_trips)
                                    <form method="POST" action="{{ route('work-orders.generate-trips', $workOrder) }}">

                                        @csrf

                                        <button class="btn btn-primary btn-sm">

                                            <i class="ti ti-plus me-1"></i>
                                            Generar viajes

                                        </button>

                                    </form>
                                @endif

                            </div>

                            @forelse ($workOrder->trips as $trip)
                                <a href="{{ route('trips.show', $trip) }}"
                                    class="d-flex justify-content-between align-items-center border rounded p-3 mb-2 text-decoration-none">

                                    <div>

                                        <div class="fw-semibold">

                                            {{ $trip->trip_number }}

                                        </div>

                                        <small class="text-muted">

                                            {{ $trip->scheduled_start_at->format('d/m/Y H:i') }}

                                        </small>

                                    </div>

                                    <span class="badge bg-primary-subtle text-primary">

                                        {{ $trip->status_label }}

                                    </span>

                                </a>

                            @empty

                                <div class="alert alert-light border mb-0">

                                    Todavía no se han generado viajes.

                                </div>
                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

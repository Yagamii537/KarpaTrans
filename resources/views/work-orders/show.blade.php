@extends('layouts.app')

@section('title', 'Orden de trabajo | Karpan Logística')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    @if ($errors->any())

        <div class="alert alert-danger">

            @foreach ($errors->all() as $error)
                <div>
                    {{ $error }}
                </div>
            @endforeach

        </div>

    @endif


    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                {{ $workOrder->work_order_number }}
            </h4>

            <p class="text-muted mb-0">

                {{ $workOrder->client->business_name }}

                @if ($workOrder->subclient)
                    ·
                    {{ $workOrder->subclient->business_name }}
                @endif

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

            {{-- INFORMACIÓN --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">
                        Requerimiento
                    </h5>


                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Cliente
                            </small>

                            <strong>
                                {{ $workOrder->client->business_name }}
                            </strong>

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
                                Booking
                            </small>

                            {{ $workOrder->booking_number ?: '-' }}

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Orden cliente
                            </small>

                            {{ $workOrder->customer_order_number ?: '-' }}

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Referencia
                            </small>

                            {{ $workOrder->customer_reference ?: '-' }}

                        </div>

                    </div>

                </div>

            </div>


            {{-- OPERACIÓN --}}

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">
                        Operación
                    </h5>


                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Tipo de operación
                            </small>

                            <strong>
                                {{ $workOrder->operation_type_label }}
                            </strong>

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Modalidad
                            </small>

                            <span class="badge bg-primary-subtle text-primary">

                                {{ $workOrder->service_modality_label }}

                            </span>

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Cantidad solicitada
                            </small>

                            <strong>
                                {{ $workOrder->requested_trips }}
                            </strong>

                        </div>


                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Origen
                            </small>

                            <strong>
                                {{ $workOrder->origin_name }}
                            </strong>

                        </div>


                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Destino
                            </small>

                            <strong>
                                {{ $workOrder->destination_name }}
                            </strong>

                        </div>


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

                            {{ $workOrder->requested_time ?: '-' }}

                        </div>


                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Turno / cita
                            </small>

                            {{ $workOrder->appointment_at ? $workOrder->appointment_at->format('d/m/Y H:i') : '-' }}

                        </div>

                    </div>

                </div>

            </div>


            {{-- STAND-BY --}}

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h5 class="fw-semibold mb-1">
                                Regla Stand-by aplicada
                            </h5>

                            <small class="text-muted">
                                Esta configuración quedó guardada con la OT.
                            </small>

                        </div>


                        @if ($workOrder->standby_rule_overridden)
                            <span class="badge bg-warning-subtle text-warning">
                                Excepción
                            </span>
                        @endif

                    </div>


                    <div class="row">

                        <div class="col-md-3 mb-3">

                            <small class="text-muted d-block">
                                Proceso
                            </small>

                            <strong>
                                {{ $workOrder->standby_process_type_label }}
                            </strong>

                        </div>


                        <div class="col-md-3 mb-3">

                            <small class="text-muted d-block">
                                Horas libres
                            </small>

                            <strong>
                                {{ $workOrder->standby_free_hours }}
                                h
                            </strong>

                        </div>


                        <div class="col-md-3 mb-3">

                            <small class="text-muted d-block">
                                Inicio conteo
                            </small>

                            {{ $workOrder->standby_count_start_type_label }}

                        </div>


                        <div class="col-md-3 mb-3">

                            <small class="text-muted d-block">
                                Fracción
                            </small>

                            <strong>
                                {{ $workOrder->standby_fraction_minutes }}
                                min
                            </strong>

                        </div>


                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Fuente
                            </small>

                            {{ $workOrder->standby_rule_source_label }}

                        </div>

                    </div>


                    @if ($workOrder->standby_rule_overridden)

                        <hr>


                        <div class="alert alert-warning mb-0">

                            <div class="fw-semibold">
                                Excepción manual
                            </div>

                            <div>
                                {{ $workOrder->standby_override_reason }}
                            </div>


                            @if ($workOrder->standbyOverrideUser)
                                <small class="d-block mt-2">

                                    Registrada por:
                                    {{ $workOrder->standbyOverrideUser->name }}

                                </small>
                            @endif

                        </div>

                    @endif

                </div>

            </div>


            {{-- VIAJES / ETAPAS --}}

            <div class="card">

                <div class="card-body">

                    @php

                        $requiredStages = match ($workOrder->service_modality) {
                            'POSITIONING_PICKUP' => 2,

                            default => 1,
                        };

                        $expectedTrips = $workOrder->requested_trips * $requiredStages;

                        $generatedTrips = $workOrder->trips->count();

                    @endphp


                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                        <div>

                            <h5 class="fw-semibold mb-1">
                                Viajes / etapas
                            </h5>


                            <small class="text-muted">

                                Servicios solicitados:
                                {{ $workOrder->requested_trips }}

                                ·

                                Etapas esperadas:
                                {{ $expectedTrips }}

                                ·

                                Generadas:
                                {{ $generatedTrips }}

                            </small>

                        </div>


                        @if ($generatedTrips < $expectedTrips && !in_array($workOrder->status, ['COMPLETED', 'CANCELLED']))
                            <form method="POST" action="{{ route('work-orders.generate-trips', $workOrder) }}">

                                @csrf


                                <button type="submit" class="btn btn-primary">

                                    <i class="ti ti-plus me-1"></i>

                                    Generar viajes / etapas

                                </button>

                            </form>
                        @endif

                    </div>


                    @if ($workOrder->service_modality === 'POSITIONING_PICKUP')
                        <div class="alert alert-light border">

                            <i class="ti ti-info-circle me-1"></i>

                            Cada servicio solicitado genera dos etapas:
                            <strong>Posición</strong> y
                            <strong>Retiro</strong>.

                        </div>
                    @endif


                    @forelse (
                            $workOrder
                                ->trips
                                ->groupBy('service_number')
                            as $serviceNumber => $serviceTrips
                        )

                        <div class="border rounded p-3 mb-3">

                            <div class="fw-semibold mb-3">

                                Servicio
                                #{{ $serviceNumber }}

                            </div>


                            @foreach ($serviceTrips as $trip)
                                <a href="{{ route('trips.show', $trip) }}"
                                    class="d-flex flex-wrap justify-content-between align-items-center gap-2 border rounded p-3 mb-2 text-decoration-none">

                                    <div>

                                        <div class="d-flex align-items-center gap-2">

                                            <span class="fw-semibold">

                                                {{ $trip->trip_number }}

                                            </span>


                                            <span class="badge {{ $trip->service_stage_badge_class }}">

                                                {{ $trip->service_stage_label }}

                                            </span>

                                        </div>


                                        <small class="text-muted">

                                            {{ $trip->scheduled_start_at->format('d/m/Y H:i') }}

                                        </small>

                                    </div>


                                    <div>

                                        <span class="badge bg-primary-subtle text-primary">

                                            {{ $trip->status_label }}

                                        </span>

                                    </div>

                                </a>
                            @endforeach

                        </div>

                    @empty

                        <div class="alert alert-light border mb-0">

                            No se han generado viajes
                            para esta Orden de Trabajo.

                        </div>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- LATERAL --}}

        <div class="col-lg-4">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">
                        Resumen
                    </h5>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Estado
                        </small>

                        <strong>
                            {{ $workOrder->status }}
                        </strong>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Planta principal
                        </small>

                        {{ $workOrder->plant?->name ?: 'No definida' }}

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Tipo contenedor
                        </small>

                        {{ $workOrder->requested_container_type ?: '-' }}

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Tamaño
                        </small>

                        {{ $workOrder->requested_container_size ?: '-' }}

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Peso estimado
                        </small>

                        {{ $workOrder->estimated_weight_kg ? number_format($workOrder->estimated_weight_kg, 2) . ' kg' : '-' }}

                    </div>


                    @if ($workOrder->cargo_description)
                        <hr>

                        <small class="text-muted d-block">
                            Descripción carga
                        </small>

                        {{ $workOrder->cargo_description }}
                    @endif

                </div>

            </div>


            @if ($workOrder->notes)
                <div class="card">

                    <div class="card-body">

                        <h5 class="fw-semibold mb-3">
                            Observaciones
                        </h5>

                        {{ $workOrder->notes }}

                    </div>

                </div>
            @endif

        </div>

    </div>

@endsection

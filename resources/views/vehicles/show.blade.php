@extends('layouts.app')

@section('title', 'Detalle del vehículo | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                {{ $vehicle->display_name }}
            </h4>

            <p class="text-muted mb-0">
                Información técnica y operativa del vehículo.
            </p>

        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('vehicles.index') }}" class="btn btn-light">

                <i class="ti ti-arrow-left me-1"></i>
                Regresar
            </a>

            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-primary">

                <i class="ti ti-edit me-1"></i>
                Editar

            </a>

        </div>

    </div>

    <div class="row">

        <div class="col-lg-4">

            <div class="card">

                <div class="card-body text-center">

                    @if ($vehicle->photo)
                        <img src="{{ asset('storage/' . $vehicle->photo) }}" class="img-fluid rounded mb-3"
                            style="max-height:220px;">
                    @else
                        <i class="ti ti-truck text-primary" style="font-size:120px;"></i>
                    @endif

                    <h4>
                        {{ $vehicle->plate }}
                    </h4>

                    <p class="text-muted">

                        {{ $vehicle->brand }}
                        {{ $vehicle->model }}

                    </p>

                    <span
                        class="badge
                    @if ($vehicle->operational_status === 'AVAILABLE') bg-success-subtle text-success
                    @elseif ($vehicle->operational_status === 'MAINTENANCE')
                        bg-warning-subtle text-warning
                    @elseif ($vehicle->operational_status === 'OUT_OF_SERVICE')
                        bg-danger-subtle text-danger
                    @else
                        bg-primary-subtle text-primary @endif">

                        {{ $vehicle->operational_status_label }}

                    </span>

                </div>

            </div>

        </div>

        <div class="col-lg-8">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">
                        Información general
                    </h5>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Código interno
                            </small>

                            {{ $vehicle->internal_code ?: 'No registrado' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Tipo
                            </small>

                            {{ $vehicle->vehicle_type }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Año
                            </small>

                            {{ $vehicle->year ?: 'No registrado' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Color
                            </small>

                            {{ $vehicle->color ?: 'No registrado' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                VIN / número de chasis
                            </small>

                            {{ $vehicle->chassis_number ?: 'No registrado' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Número de motor
                            </small>

                            {{ $vehicle->engine_number ?: 'No registrado' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Kilometraje
                            </small>

                            {{ $vehicle->current_odometer ? number_format((float) $vehicle->current_odometer, 2) : 'No registrado' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Propiedad
                            </small>

                            {{ $vehicle->ownership_type }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Propietario
                            </small>

                            {{ $vehicle->owner_name ?: 'No registrado' }}

                        </div>

                    </div>

                    <hr>

                    <h5 class="fw-semibold mb-4">
                        Pesos y medidas
                    </h5>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Tara
                            </small>

                            {{ $vehicle->tare_weight_kg ? number_format((float) $vehicle->tare_weight_kg, 2) . ' kg' : 'No registrada' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Peso máximo
                            </small>

                            {{ $vehicle->max_weight_kg ? number_format((float) $vehicle->max_weight_kg, 2) . ' kg' : 'No registrado' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Capacidad útil
                            </small>

                            @if ($vehicle->tare_weight_kg && $vehicle->max_weight_kg)
                                {{ number_format((float) $vehicle->max_weight_kg - (float) $vehicle->tare_weight_kg, 2) }}
                                kg
                            @else
                                No disponible
                            @endif

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Largo
                            </small>

                            {{ $vehicle->length_m ? $vehicle->length_m . ' m' : 'No registrado' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Ancho
                            </small>

                            {{ $vehicle->width_m ? $vehicle->width_m . ' m' : 'No registrado' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Alto
                            </small>

                            {{ $vehicle->height_m ? $vehicle->height_m . ' m' : 'No registrado' }}

                        </div>

                    </div>

                    <hr>

                    <h5 class="fw-semibold mb-4">
                        Documentación
                    </h5>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Matrícula vence
                            </small>

                            {{ $vehicle->registration_expiration_date
                                ? $vehicle->registration_expiration_date->format('d/m/Y')
                                : 'No registrada' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Revisión vence
                            </small>

                            {{ $vehicle->technical_review_expiration_date
                                ? $vehicle->technical_review_expiration_date->format('d/m/Y')
                                : 'No registrada' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Seguro vence
                            </small>

                            {{ $vehicle->insurance_expiration_date ? $vehicle->insurance_expiration_date->format('d/m/Y') : 'No registrado' }}

                        </div>

                    </div>

                    @if ($vehicle->notes)
                        <hr>

                        <h5 class="fw-semibold mb-3">
                            Observaciones
                        </h5>

                        <p class="mb-0">
                            {{ $vehicle->notes }}
                        </p>
                    @endif

                </div>

            </div>

        </div>

    </div>

@endsection

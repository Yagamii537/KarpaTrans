@extends('layouts.app')

@section('title', 'Detalle del conductor | Karpan Logística')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

    <div>
        <h4 class="fw-semibold mb-1">
            Detalle del conductor
        </h4>

        <p class="text-muted mb-0">
            Información personal, licencia y documentos.
        </p>
    </div>

    <div class="d-flex gap-2">

        <a href="{{ route('drivers.index') }}"
           class="btn btn-light">

            <i class="ti ti-arrow-left me-1"></i>
            Regresar
        </a>

        <a href="{{ route('drivers.edit', $driver) }}"
           class="btn btn-primary">

            <i class="ti ti-edit me-1"></i>
            Editar
        </a>

    </div>
</div>

<div class="row">

    <div class="col-lg-4">

        <div class="card">
            <div class="card-body text-center">

                @if ($driver->photo)
                    <img src="{{ asset('storage/' . $driver->photo) }}"
                         alt="{{ $driver->full_name }}"
                         class="rounded-circle object-fit-cover mb-3"
                         width="130"
                         height="130">
                @else
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold mx-auto mb-3"
                         style="width:130px;height:130px;font-size:36px;">

                        {{ $driver->initials }}
                    </div>
                @endif

                <h4 class="fw-semibold mb-1">
                    {{ $driver->full_name }}
                </h4>

                <p class="text-muted">
                    {{ $driver->identification }}
                </p>

                @if ($driver->is_active)
                    <span class="badge bg-success-subtle text-success">
                        Conductor activo
                    </span>
                @else
                    <span class="badge bg-danger-subtle text-danger">
                        Conductor inactivo
                    </span>
                @endif

            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <h5 class="fw-semibold mb-3">
                    Documentos
                </h5>

                <div class="d-grid gap-2">

                    @if ($driver->identification_document)
                        <a href="{{ asset(
                            'storage/' . $driver->identification_document
                        ) }}"
                           target="_blank"
                           class="btn btn-outline-primary text-start">

                            <i class="ti ti-id me-2"></i>
                            Ver documento de cédula
                        </a>
                    @endif

                    @if ($driver->license_document)
                        <a href="{{ asset(
                            'storage/' . $driver->license_document
                        ) }}"
                           target="_blank"
                           class="btn btn-outline-primary text-start">

                            <i class="ti ti-license me-2"></i>
                            Ver documento de licencia
                        </a>
                    @endif

                    @if (
                        !$driver->identification_document &&
                        !$driver->license_document
                    )
                        <p class="text-muted mb-0">
                            No existen documentos cargados.
                        </p>
                    @endif

                </div>

            </div>
        </div>

    </div>

    <div class="col-lg-8">

        <div class="card">
            <div class="card-body">

                <h5 class="fw-semibold mb-4">
                    Información personal
                </h5>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">
                            Teléfono
                        </small>

                        <span>
                            {{ $driver->phone ?: 'No registrado' }}
                        </span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">
                            Correo electrónico
                        </small>

                        <span>
                            {{ $driver->email ?: 'No registrado' }}
                        </span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">
                            Fecha de nacimiento
                        </small>

                        <span>
                            {{ $driver->birth_date
                                ? $driver->birth_date->format('d/m/Y')
                                : 'No registrada' }}
                        </span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">
                            Fecha de ingreso
                        </small>

                        <span>
                            {{ $driver->hire_date
                                ? $driver->hire_date->format('d/m/Y')
                                : 'No registrada' }}
                        </span>
                    </div>

                    <div class="col-12 mb-3">
                        <small class="text-muted d-block">
                            Dirección
                        </small>

                        <span>
                            {{ $driver->address ?: 'No registrada' }}
                        </span>
                    </div>

                </div>

                <hr>

                <h5 class="fw-semibold mb-4">
                    Licencia
                </h5>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">
                            Número
                        </small>

                        <span>
                            {{ $driver->license_number }}
                        </span>
                    </div>

                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">
                            Tipo
                        </small>

                        <span>
                            {{ $driver->license_type }}
                        </span>
                    </div>

                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">
                            Puntos
                        </small>

                        <span>
                            {{ $driver->license_points ?? 'No registrado' }}
                        </span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">
                            Fecha de emisión
                        </small>

                        <span>
                            {{ $driver->license_issue_date
                                ? $driver->license_issue_date->format('d/m/Y')
                                : 'No registrada' }}
                        </span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">
                            Fecha de vencimiento
                        </small>

                        <span>
                            {{ $driver->license_expiration_date
                                ->format('d/m/Y') }}
                        </span>

                        <span class="badge ms-2
                            @if ($driver->license_status === 'expired')
                                bg-danger-subtle text-danger
                            @elseif ($driver->license_status === 'expiring')
                                bg-warning-subtle text-warning
                            @else
                                bg-success-subtle text-success
                            @endif">

                            {{ $driver->license_status_label }}
                        </span>
                    </div>

                </div>

                <hr>

                <h5 class="fw-semibold mb-4">
                    Contacto de emergencia
                </h5>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">
                            Nombre
                        </small>

                        <span>
                            {{ $driver->emergency_contact_name
                                ?: 'No registrado' }}
                        </span>
                    </div>

                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">
                            Teléfono
                        </small>

                        <span>
                            {{ $driver->emergency_contact_phone
                                ?: 'No registrado' }}
                        </span>
                    </div>

                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">
                            Parentesco
                        </small>

                        <span>
                            {{ $driver->emergency_contact_relationship
                                ?: 'No registrado' }}
                        </span>
                    </div>

                </div>

                @if ($driver->notes)
                    <hr>

                    <h5 class="fw-semibold mb-3">
                        Observaciones
                    </h5>

                    <p class="mb-0">
                        {{ $driver->notes }}
                    </p>
                @endif

            </div>
        </div>

    </div>

</div>

@endsection

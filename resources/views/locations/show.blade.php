@extends('layouts.app')

@section('title', 'Detalle de ubicación | Karpan Logística')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="fw-semibold mb-1">
            {{ $location->name }}
        </h4>

        <p class="text-muted mb-0">
            {{ $location->type_label }}
        </p>
    </div>

    <div class="d-flex gap-2">

        <a href="{{ route('locations.index') }}"
           class="btn btn-light">

            Regresar
        </a>

        <a href="{{ route('locations.edit', $location) }}"
           class="btn btn-primary">

            Editar
        </a>
    </div>

</div>

<div class="row">

    <div class="col-lg-8">

        <div class="card">
            <div class="card-body">

                <h5 class="fw-semibold mb-4">
                    Información general
                </h5>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">
                            Código
                        </small>

                        {{ $location->code ?: 'No registrado' }}
                    </div>

                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">
                            Tipo
                        </small>

                        {{ $location->type_label }}
                    </div>

                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">
                            Estado
                        </small>

                        {{ $location->is_active ? 'Activa' : 'Inactiva' }}
                    </div>

                    <div class="col-12 mb-3">
                        <small class="text-muted d-block">
                            Dirección
                        </small>

                        {{ $location->full_address }}
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">
                            Contacto
                        </small>

                        {{ $location->contact_name ?: 'No registrado' }}
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">
                            Teléfono
                        </small>

                        {{ $location->phone ?: 'No registrado' }}
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">
                            Horario
                        </small>

                        {{ $location->opening_time ?: '--:--' }}
                        a
                        {{ $location->closing_time ?: '--:--' }}
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">
                            Requiere turno
                        </small>

                        {{ $location->requires_appointment ? 'Sí' : 'No' }}
                    </div>

                </div>

            </div>
        </div>

    </div>

    <div class="col-lg-4">

        <div class="card">
            <div class="card-body">

                <h5 class="fw-semibold mb-3">
                    Configuración logística
                </h5>

                <p>
                    <strong>Contenedores vacíos:</strong>
                    {{ $location->receives_empty_containers ? 'Sí' : 'No' }}
                </p>

                <p>
                    <strong>Contenedores llenos:</strong>
                    {{ $location->receives_full_containers ? 'Sí' : 'No' }}
                </p>

                <p class="mb-0">
                    <strong>Coordenadas:</strong><br>

                    {{ $location->latitude ?: 'Sin latitud' }},
                    {{ $location->longitude ?: 'Sin longitud' }}
                </p>

            </div>
        </div>

    </div>

</div>

@endsection

@extends('layouts.app')

@section('title', 'Detalle del vehículo | Karpan Logística')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-semibold mb-1">{{ $vehicle->display_name }}</h4>
        <p class="text-muted mb-0">Información y chasis asociados.</p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('vehicles.index') }}" class="btn btn-light">
            Regresar
        </a>

        <a href="{{ route('vehicles.edit', $vehicle) }}"
           class="btn btn-primary">
            Editar
        </a>
    </div>
</div>

<div class="row">

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">

                @if ($vehicle->photo)
                    <img src="{{ asset('storage/' . $vehicle->photo) }}"
                         class="img-fluid rounded mb-3"
                         style="max-height:220px;">
                @else
                    <i class="ti ti-truck text-primary"
                       style="font-size:120px;"></i>
                @endif

                <h4>{{ $vehicle->plate }}</h4>
                <p class="text-muted">
                    {{ $vehicle->brand }} {{ $vehicle->model }}
                </p>

                <span class="badge bg-primary-subtle text-primary">
                    {{ $vehicle->operational_status_label }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">

                <h5 class="fw-semibold mb-4">Información del vehículo</h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Código interno</small>
                        {{ $vehicle->internal_code ?: 'No registrado' }}
                    </div>

                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Año</small>
                        {{ $vehicle->year ?: 'No registrado' }}
                    </div>

                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Color</small>
                        {{ $vehicle->color ?: 'No registrado' }}
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Número de chasis</small>
                        {{ $vehicle->chassis_number ?: 'No registrado' }}
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Número de motor</small>
                        {{ $vehicle->engine_number ?: 'No registrado' }}
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Kilometraje</small>
                        {{ $vehicle->current_odometer ?: 'No registrado' }}
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Propiedad</small>
                        {{ $vehicle->ownership_type }}
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Chasis asociados</h5>

                    <a href="{{ route('chassis.create', [
                        'vehicle_id' => $vehicle->id
                    ]) }}"
                       class="btn btn-sm btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        Agregar chasis
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Placa</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($vehicle->chassis as $item)
                                <tr>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->plate ?: 'Sin placa' }}</td>
                                    <td>{{ $item->chassis_type }}</td>
                                    <td>{{ $item->operational_status_label }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No existen chasis asociados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

@extends('layouts.app')

@section('title', 'Viajes | Karpan Logística')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h4 class="fw-semibold mb-1">
                Viajes
            </h4>

            <p class="text-muted mb-0">
                Planificación y ejecución de operaciones.
            </p>
        </div>

        <a href="{{ route('trips.create') }}" class="btn btn-primary">

            <i class="ti ti-plus me-1"></i>
            Nuevo viaje

        </a>

    </div>

    <div class="card">

        <div class="card-body">

            <form method="GET" class="row g-2 mb-4">

                <div class="col-md-5">

                    <input type="text" name="search" class="form-control" placeholder="Viaje, booking o cliente"
                        value="{{ $search }}">

                </div>

                <div class="col-md-3">

                    <select name="status" class="form-select">

                        <option value="">
                            Todos los estados
                        </option>

                        @foreach ([
            'PENDING' => 'Pendiente',
            'ASSIGNED' => 'Asignado',
            'IN_TRANSIT' => 'En tránsito',
            'AT_DESTINATION' => 'En destino',
            'COMPLETED' => 'Completado',
            'CANCELLED' => 'Cancelado',
        ] as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>

                                {{ $label }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <div class="col-auto">

                    <button class="btn btn-outline-primary">
                        <i class="ti ti-search"></i>
                        Buscar
                    </button>

                </div>

                <div class="col-auto">

                    <a href="{{ route('trips.index') }}" class="btn btn-light">

                        Limpiar

                    </a>

                </div>

            </form>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>
                            <th>Viaje</th>
                            <th>Cliente</th>
                            <th>Ruta</th>
                            <th>Programación</th>
                            <th>Conductor</th>
                            <th>Vehículo</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($trips as $trip)
                            <tr>

                                <td>

                                    <div class="fw-semibold">
                                        {{ $trip->trip_number }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $trip->workOrder->work_order_number }}
                                    </small>

                                </td>

                                <td>

                                    <div>
                                        {{ $trip->client_name_snapshot }}
                                    </div>

                                    @if ($trip->subclient_name_snapshot)
                                        <small class="text-muted">
                                            {{ $trip->subclient_name_snapshot }}
                                        </small>
                                    @endif

                                </td>

                                <td>

                                    <div>
                                        {{ $trip->origin_name_snapshot }}
                                    </div>

                                    <small class="text-muted">
                                        →
                                        {{ $trip->destination_name_snapshot }}
                                    </small>

                                </td>

                                <td>

                                    {{ $trip->scheduled_start_at->format('d/m/Y') }}

                                    <small class="d-block text-muted">

                                        {{ $trip->scheduled_start_at->format('H:i') }}

                                    </small>

                                </td>

                                <td>

                                    {{ $trip->activeAssignment?->driver?->full_name ?: 'Sin asignar' }}

                                </td>

                                <td>

                                    {{ $trip->activeAssignment?->vehicle?->plate ?: 'Sin asignar' }}

                                </td>

                                <td>

                                    <span
                                        class="badge
                                    @if ($trip->status === 'COMPLETED') bg-success-subtle text-success
                                    @elseif ($trip->status === 'CANCELLED')
                                        bg-danger-subtle text-danger
                                    @elseif ($trip->status === 'IN_TRANSIT')
                                        bg-warning-subtle text-warning
                                    @else
                                        bg-primary-subtle text-primary @endif">

                                        {{ $trip->status_label }}

                                    </span>

                                </td>

                                <td class="text-end">

                                    <a href="{{ route('trips.show', $trip) }}" class="btn btn-sm btn-outline-secondary">

                                        <i class="ti ti-eye"></i>

                                    </a>

                                    <a href="{{ route('trips.edit', $trip) }}" class="btn btn-sm btn-outline-primary">

                                        <i class="ti ti-edit"></i>

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="text-center py-5 text-muted">

                                    <i class="ti ti-truck fs-8 d-block mb-2"></i>

                                    No existen viajes registrados.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{ $trips->links() }}

        </div>

    </div>

@endsection

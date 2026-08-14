@extends('layouts.app')

@section('title', 'Vehículos | Karpan Logística')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h4 class="fw-semibold mb-1">
                Vehículos
            </h4>

            <p class="text-muted mb-0">
                Administración de cabezales y vehículos.
            </p>
        </div>

        <a href="{{ route('vehicles.create') }}" class="btn btn-primary">

            <i class="ti ti-plus me-1"></i>
            Nuevo vehículo
        </a>

    </div>

    <div class="card">
        <div class="card-body">

            <form method="GET" action="{{ route('vehicles.index') }}" class="row g-2 mb-4">

                <div class="col-md-5">

                    <input type="text" name="search" class="form-control"
                        placeholder="Buscar placa, código, marca o modelo" value="{{ $search }}">

                </div>

                <div class="col-md-3">

                    <select name="status" class="form-select">

                        <option value="">
                            Todos los estados
                        </option>

                        <option value="AVAILABLE" @selected($status === 'AVAILABLE')>

                            Disponible
                        </option>

                        <option value="ASSIGNED" @selected($status === 'ASSIGNED')>

                            Asignado
                        </option>

                        <option value="MAINTENANCE" @selected($status === 'MAINTENANCE')>

                            Mantenimiento
                        </option>

                        <option value="OUT_OF_SERVICE" @selected($status === 'OUT_OF_SERVICE')>

                            Fuera de servicio
                        </option>

                    </select>

                </div>

                <div class="col-auto">

                    <button class="btn btn-outline-primary">

                        <i class="ti ti-search me-1"></i>
                        Buscar

                    </button>

                </div>

                <div class="col-auto">

                    <a href="{{ route('vehicles.index') }}" class="btn btn-light">

                        Limpiar
                    </a>

                </div>

            </form>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Vehículo</th>
                            <th>Características</th>
                            <th>Pesos</th>
                            <th>Propiedad</th>
                            <th>Documentos</th>
                            <th>Estado</th>
                            <th class="text-end">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($vehicles as $vehicle)
                            <tr>

                                <td>

                                    <div class="d-flex align-items-center gap-3">

                                        @if ($vehicle->photo)
                                            <img src="{{ asset('storage/' . $vehicle->photo) }}" width="55"
                                                height="45" class="rounded object-fit-cover">
                                        @else
                                            <div class="bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center"
                                                style="width:55px;height:45px;">

                                                <i class="ti ti-truck fs-6"></i>

                                            </div>
                                        @endif

                                        <div>

                                            <div class="fw-semibold">
                                                {{ $vehicle->plate }}
                                            </div>

                                            <small class="text-muted">
                                                {{ $vehicle->internal_code ?: 'Sin código' }}
                                            </small>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <div>
                                        {{ $vehicle->brand }}
                                        {{ $vehicle->model }}
                                    </div>

                                    <small class="text-muted">

                                        {{ $vehicle->year ?: 'Sin año' }}

                                        ·

                                        {{ $vehicle->color ?: 'Sin color' }}

                                    </small>

                                </td>

                                <td>

                                    <div>
                                        Tara:
                                        {{ $vehicle->tare_weight_kg ? number_format((float) $vehicle->tare_weight_kg, 2) . ' kg' : '-' }}
                                    </div>

                                    <small class="text-muted">

                                        Máx:
                                        {{ $vehicle->max_weight_kg ? number_format((float) $vehicle->max_weight_kg, 2) . ' kg' : '-' }}

                                    </small>

                                </td>

                                <td>

                                    {{ ucfirst(strtolower($vehicle->ownership_type)) }}

                                    @if ($vehicle->owner_name)
                                        <small class="text-muted d-block">
                                            {{ $vehicle->owner_name }}
                                        </small>
                                    @endif

                                </td>

                                <td>

                                    @if ($vehicle->has_expired_document)
                                        <span class="badge bg-danger-subtle text-danger">
                                            Documento vencido
                                        </span>
                                    @elseif ($vehicle->has_expiring_document)
                                        <span class="badge bg-warning-subtle text-warning">
                                            Próximo a vencer
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">
                                            Sin alertas
                                        </span>
                                    @endif

                                </td>

                                <td>

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

                                </td>

                                <td class="text-end">

                                    <a href="{{ route('vehicles.show', $vehicle) }}"
                                        class="btn btn-sm btn-outline-secondary">

                                        <i class="ti ti-eye"></i>

                                    </a>

                                    <a href="{{ route('vehicles.edit', $vehicle) }}"
                                        class="btn btn-sm btn-outline-primary">

                                        <i class="ti ti-edit"></i>

                                    </a>

                                    <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                          '¿Eliminar este vehículo?'
                                      )">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger">

                                            <i class="ti ti-trash"></i>

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center py-5 text-muted">

                                    <i class="ti ti-truck fs-8 d-block mb-2"></i>

                                    No existen vehículos registrados.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">
                {{ $vehicles->links() }}
            </div>

        </div>
    </div>

@endsection

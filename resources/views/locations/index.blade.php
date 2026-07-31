@extends('layouts.app')

@section('title', 'Ubicaciones | Karpan Logística')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

    <div>
        <h4 class="fw-semibold mb-1">
            Ubicaciones
        </h4>

        <p class="text-muted mb-0">
            Puertos, depósitos, patios y otros puntos logísticos.
        </p>
    </div>

    <a href="{{ route('locations.create') }}"
       class="btn btn-primary">

        <i class="ti ti-plus me-1"></i>
        Nueva ubicación
    </a>

</div>

<div class="card">
    <div class="card-body">

        <form method="GET"
              action="{{ route('locations.index') }}"
              class="row g-2 mb-4">

            <div class="col-md-4">

                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Buscar nombre, código o ciudad"
                       value="{{ $search }}">
            </div>

            <div class="col-md-3">

                <select name="type"
                        class="form-select">

                    <option value="">
                        Todos los tipos
                    </option>

                    @foreach ([
                        'PORT' => 'Puerto',
                        'DEPOT' => 'Depósito',
                        'YARD' => 'Patio',
                        'WAREHOUSE' => 'Bodega',
                        'EXTERNAL_PLANT' => 'Planta externa',
                        'WORKSHOP' => 'Taller',
                        'CUSTOMER_LOCATION' => 'Punto del cliente',
                        'OTHER' => 'Otro',
                    ] as $value => $label)

                        <option value="{{ $value }}"
                            @selected($type === $value)>

                            {{ $label }}
                        </option>

                    @endforeach
                </select>
            </div>

            <div class="col-md-2">

                <select name="status"
                        class="form-select">

                    <option value="">
                        Todos
                    </option>

                    <option value="active"
                        @selected($status === 'active')>

                        Activos
                    </option>

                    <option value="inactive"
                        @selected($status === 'inactive')>

                        Inactivos
                    </option>
                </select>
            </div>

            <div class="col-auto">

                <button type="submit"
                        class="btn btn-outline-primary">

                    <i class="ti ti-search me-1"></i>
                    Buscar
                </button>
            </div>

            <div class="col-auto">

                <a href="{{ route('locations.index') }}"
                   class="btn btn-light">

                    Limpiar
                </a>
            </div>

        </form>

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Ubicación</th>
                        <th>Tipo</th>
                        <th>Ciudad</th>
                        <th>Contacto</th>
                        <th>Contenedores</th>
                        <th>Turno</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($locations as $location)

                        <tr>

                            <td>
                                <div class="fw-semibold">
                                    {{ $location->name }}
                                </div>

                                <small class="text-muted">
                                    {{ $location->code ?: 'Sin código' }}
                                </small>
                            </td>

                            <td>
                                <span class="badge bg-primary-subtle text-primary">
                                    {{ $location->type_label }}
                                </span>
                            </td>

                            <td>
                                <div>
                                    {{ $location->city ?: 'Sin ciudad' }}
                                </div>

                                <small class="text-muted">
                                    {{ $location->province }}
                                </small>
                            </td>

                            <td>
                                <div>
                                    {{ $location->contact_name ?: 'Sin contacto' }}
                                </div>

                                <small class="text-muted">
                                    {{ $location->phone ?: $location->email }}
                                </small>
                            </td>

                            <td>
                                @if ($location->receives_empty_containers)
                                    <span class="badge bg-info-subtle text-info">
                                        Vacíos
                                    </span>
                                @endif

                                @if ($location->receives_full_containers)
                                    <span class="badge bg-success-subtle text-success">
                                        Llenos
                                    </span>
                                @endif

                                @if (
                                    !$location->receives_empty_containers &&
                                    !$location->receives_full_containers
                                )
                                    <span class="text-muted">
                                        No definido
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if ($location->requires_appointment)
                                    <span class="badge bg-warning-subtle text-warning">
                                        Requiere turno
                                    </span>
                                @else
                                    <span class="text-muted">
                                        No requiere
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if ($location->is_active)
                                    <span class="badge bg-success-subtle text-success">
                                        Activa
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        Inactiva
                                    </span>
                                @endif
                            </td>

                            <td class="text-end">

                                <a href="{{ route(
                                    'locations.show',
                                    $location
                                ) }}"
                                   class="btn btn-sm btn-outline-secondary">

                                    <i class="ti ti-eye"></i>
                                </a>

                                <a href="{{ route(
                                    'locations.edit',
                                    $location
                                ) }}"
                                   class="btn btn-sm btn-outline-primary">

                                    <i class="ti ti-edit"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route(
                                          'locations.destroy',
                                          $location
                                      ) }}"
                                      class="d-inline"
                                      onsubmit="return confirm(
                                          '¿Eliminar esta ubicación?'
                                      )">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger">

                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="8"
                                class="text-center py-5 text-muted">

                                <i class="ti ti-map-pin-off fs-8 d-block mb-2"></i>

                                No existen ubicaciones registradas.
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>

        </div>

        <div class="mt-3">
            {{ $locations->links() }}
        </div>

    </div>
</div>

@endsection

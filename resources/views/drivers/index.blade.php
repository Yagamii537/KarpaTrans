@extends('layouts.app')

@section('title', 'Conductores | Karpan Logística')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

    <div>
        <h4 class="fw-semibold mb-1">
            Conductores
        </h4>

        <p class="text-muted mb-0">
            Administración de conductores y licencias.
        </p>
    </div>

    <a href="{{ route('drivers.create') }}"
       class="btn btn-primary">

        <i class="ti ti-plus me-1"></i>
        Nuevo conductor
    </a>

</div>

<div class="card">
    <div class="card-body">

        <form method="GET"
              action="{{ route('drivers.index') }}"
              class="row g-2 mb-4">

            <div class="col-md-4">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Buscar nombre, cédula o licencia"
                       value="{{ $search }}">
            </div>

            <div class="col-md-2">
                <select name="status"
                        class="form-select">

                    <option value="">
                        Todos los estados
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

            <div class="col-md-3">
                <select name="license_status"
                        class="form-select">

                    <option value="">
                        Todas las licencias
                    </option>

                    <option value="valid"
                        @selected($licenseStatus === 'valid')>
                        Vigentes
                    </option>

                    <option value="expiring"
                        @selected($licenseStatus === 'expiring')>
                        Próximas a vencer
                    </option>

                    <option value="expired"
                        @selected($licenseStatus === 'expired')>
                        Vencidas
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
                <a href="{{ route('drivers.index') }}"
                   class="btn btn-light">
                    Limpiar
                </a>
            </div>

        </form>

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Conductor</th>
                        <th>Cédula</th>
                        <th>Contacto</th>
                        <th>Licencia</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($drivers as $driver)

                        <tr>

                            <td>
                                <div class="d-flex align-items-center gap-3">

                                    @if ($driver->photo)
                                        <img src="{{ asset(
                                            'storage/' . $driver->photo
                                        ) }}"
                                             alt="{{ $driver->full_name }}"
                                             class="rounded-circle object-fit-cover"
                                             width="45"
                                             height="45">
                                    @else
                                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-semibold"
                                             style="width:45px;height:45px;">

                                            {{ $driver->initials }}
                                        </div>
                                    @endif

                                    <div>
                                        <div class="fw-semibold">
                                            {{ $driver->full_name }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $driver->employee_code
                                                ?: 'Sin código interno' }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                {{ $driver->identification }}
                            </td>

                            <td>
                                <div>
                                    {{ $driver->phone ?: 'Sin teléfono' }}
                                </div>

                                <small class="text-muted">
                                    {{ $driver->email }}
                                </small>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    Tipo {{ $driver->license_type }}
                                </div>

                                <small class="text-muted">
                                    {{ $driver->license_number }}
                                </small>
                            </td>

                            <td>
                                <div>
                                    {{ $driver->license_expiration_date
                                        ->format('d/m/Y') }}
                                </div>

                                @if ($driver->license_status === 'expired')
                                    <span class="badge bg-danger-subtle text-danger">
                                        Vencida
                                    </span>
                                @elseif ($driver->license_status === 'expiring')
                                    <span class="badge bg-warning-subtle text-warning">
                                        Próxima a vencer
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success">
                                        Vigente
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if ($driver->is_active)
                                    <span class="badge bg-success-subtle text-success">
                                        Activo
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="text-end">

                                <a href="{{ route(
                                    'drivers.show',
                                    $driver
                                ) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   title="Ver">

                                    <i class="ti ti-eye"></i>
                                </a>

                                <a href="{{ route(
                                    'drivers.edit',
                                    $driver
                                ) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Editar">

                                    <i class="ti ti-edit"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route(
                                          'drivers.destroy',
                                          $driver
                                      ) }}"
                                      class="d-inline"
                                      onsubmit="return confirm(
                                          '¿Eliminar este conductor?'
                                      )">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Eliminar">

                                        <i class="ti ti-trash"></i>
                                    </button>

                                </form>

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="7"
                                class="text-center py-5 text-muted">

                                <i class="ti ti-steering-wheel-off fs-8 d-block mb-2"></i>

                                No existen conductores registrados.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-3">
            {{ $drivers->links() }}
        </div>

    </div>
</div>

@endsection

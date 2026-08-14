@extends('layouts.app')

@section('title', 'Restricciones | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-semibold mb-1">
                Restricciones de conductores
            </h4>

            <p class="text-muted mb-0">
                Restricciones de acceso, retorno y asignación.
            </p>
        </div>

        <a href="{{ route('driver-restrictions.create') }}" class="btn btn-primary">

            <i class="ti ti-plus me-1"></i>
            Nueva restricción
        </a>

    </div>

    <div class="card">
        <div class="card-body">

            <form method="GET" class="row g-2 mb-4">

                <div class="col-md-5">

                    <input type="text" name="search" class="form-control"
                        placeholder="Buscar conductor, cliente o motivo" value="{{ $search }}">

                </div>

                <div class="col-auto">

                    <button class="btn btn-outline-primary">
                        <i class="ti ti-search me-1"></i>
                        Buscar
                    </button>

                </div>

                <div class="col-auto">

                    <a href="{{ route('driver-restrictions.index') }}" class="btn btn-light">
                        Limpiar
                    </a>

                </div>

            </form>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Conductor</th>
                            <th>Aplica a</th>
                            <th>Motivo</th>
                            <th>Vigencia</th>
                            <th>Acción</th>
                            <th>Estado</th>
                            <th class="text-end">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($restrictions as $restriction)
                            <tr>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $restriction->driver->full_name }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $restriction->driver->identification }}
                                    </small>
                                </td>

                                <td>

                                    @if ($restriction->client)
                                        <div>
                                            Cliente:
                                            {{ $restriction->client->business_name }}
                                        </div>
                                    @endif

                                    @if ($restriction->subclient)
                                        <small class="d-block">
                                            Subcliente:
                                            {{ $restriction->subclient->business_name }}
                                        </small>
                                    @endif

                                    @if ($restriction->plant)
                                        <small class="d-block">
                                            Planta:
                                            {{ $restriction->plant->name }}
                                        </small>
                                    @endif

                                    @if ($restriction->location)
                                        <small class="d-block">
                                            Ubicación:
                                            {{ $restriction->location->name }}
                                        </small>
                                    @endif

                                    @if (!$restriction->client && !$restriction->subclient && !$restriction->plant && !$restriction->location)
                                        <span class="badge bg-danger-subtle text-danger">
                                            Restricción general
                                        </span>
                                    @endif

                                </td>

                                <td>
                                    {{ $restriction->reason }}
                                </td>

                                <td>
                                    <div>
                                        Desde:
                                        {{ $restriction->start_date->format('d/m/Y') }}
                                    </div>

                                    <small class="text-muted">
                                        @if ($restriction->end_date)
                                            Hasta:
                                            {{ $restriction->end_date->format('d/m/Y') }}
                                        @else
                                            Sin fecha fin
                                        @endif
                                    </small>
                                </td>

                                <td>
                                    @if ($restriction->action_type === 'BLOCK')
                                        <span class="badge bg-danger-subtle text-danger">
                                            Bloqueo
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">
                                            Advertencia
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($restriction->is_active)
                                        <span class="badge bg-success-subtle text-success">
                                            Activa
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            Inactiva
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end">

                                    <a href="{{ route('driver-restrictions.edit', $restriction) }}"
                                        class="btn btn-sm btn-outline-primary">

                                        <i class="ti ti-edit"></i>

                                    </a>

                                    <form method="POST" action="{{ route('driver-restrictions.destroy', $restriction) }}"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                          '¿Eliminar esta restricción?'
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

                                    <i class="ti ti-alert-circle fs-8 d-block mb-2"></i>

                                    No existen restricciones registradas.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">
                {{ $restrictions->links() }}
            </div>

        </div>
    </div>

@endsection

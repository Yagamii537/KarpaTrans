@extends('layouts.app')

@section('title', 'Chasis | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                Chasis
            </h4>

            <p class="text-muted mb-0">
                Administración de chasis para contenedores.
            </p>

        </div>

        <a href="{{ route('chassis.create') }}" class="btn btn-primary">

            <i class="ti ti-plus me-1"></i>
            Nuevo chasis

        </a>

    </div>

    <div class="card">

        <div class="card-body">

            <form method="GET" action="{{ route('chassis.index') }}" class="row g-2 mb-4">

                <div class="col-md-5">

                    <input type="text" name="search" class="form-control"
                        placeholder="Buscar código, placa o número de serie" value="{{ $search }}">

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

                    <a href="{{ route('chassis.index') }}" class="btn btn-light">

                        Limpiar

                    </a>

                </div>

            </form>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Chasis</th>
                            <th>Características</th>
                            <th>Compatibilidad</th>
                            <th>Documentos</th>
                            <th>Estado</th>

                            <th class="text-end">
                                Acciones
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($chassisList as $chassis)
                            <tr>

                                <td>

                                    <div class="fw-semibold">
                                        {{ $chassis->code }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $chassis->plate ?: 'Sin placa' }}
                                    </small>

                                </td>

                                <td>

                                    <div>
                                        {{ $chassis->chassis_type }}
                                    </div>

                                    <small class="text-muted">

                                        {{ $chassis->axles ?: '-' }}
                                        ejes

                                        ·

                                        {{ $chassis->maximum_capacity_tons ?: '-' }}
                                        t

                                    </small>

                                </td>

                                <td>

                                    @if ($chassis->supports_20ft)
                                        <span class="badge bg-primary-subtle text-primary">
                                            20'
                                        </span>
                                    @endif

                                    @if ($chassis->supports_40ft)
                                        <span class="badge bg-primary-subtle text-primary">
                                            40'
                                        </span>
                                    @endif

                                    @if ($chassis->supports_reefer)
                                        <span class="badge bg-info-subtle text-info">
                                            Reefer
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @if ($chassis->has_expired_document)
                                        <span class="badge bg-danger-subtle text-danger">
                                            Documento vencido
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
                                    @if ($chassis->operational_status === 'AVAILABLE') bg-success-subtle text-success
                                    @elseif ($chassis->operational_status === 'MAINTENANCE')
                                        bg-warning-subtle text-warning
                                    @elseif ($chassis->operational_status === 'OUT_OF_SERVICE')
                                        bg-danger-subtle text-danger
                                    @else
                                        bg-primary-subtle text-primary @endif">

                                        {{ $chassis->operational_status_label }}

                                    </span>

                                </td>

                                <td class="text-end">

                                    <a href="{{ route('chassis.show', $chassis) }}"
                                        class="btn btn-sm btn-outline-secondary">

                                        <i class="ti ti-eye"></i>

                                    </a>

                                    <a href="{{ route('chassis.edit', $chassis) }}" class="btn btn-sm btn-outline-primary">

                                        <i class="ti ti-edit"></i>

                                    </a>

                                    <form method="POST" action="{{ route('chassis.destroy', $chassis) }}" class="d-inline"
                                        onsubmit="return confirm(
                                          '¿Eliminar este chasis?'
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

                                <td colspan="6" class="text-center py-5 text-muted">

                                    <i class="ti ti-tool fs-8 d-block mb-2"></i>

                                    No existen chasis registrados.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">

                {{ $chassisList->links() }}

            </div>

        </div>

    </div>

@endsection

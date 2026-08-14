@extends('layouts.app')

@section('title', 'Contenedores | Karpan Logística')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                Contenedores
            </h4>

            <p class="text-muted mb-0">
                Administración y trazabilidad de contenedores.
            </p>

        </div>

        <a href="{{ route('containers.create') }}" class="btn btn-primary">

            <i class="ti ti-plus me-1"></i>
            Nuevo contenedor

        </a>

    </div>

    <div class="card">

        <div class="card-body">

            <form method="GET" action="{{ route('containers.index') }}" class="row g-2 mb-4">

                <div class="col-md-4">

                    <input type="text" name="search" class="form-control" placeholder="Buscar número, sello o naviera"
                        value="{{ $search }}">

                </div>

                <div class="col-md-3">

                    <select name="size" class="form-select">

                        <option value="">
                            Todos los tamaños
                        </option>

                        @foreach ([
            '20FT' => '20 pies',
            '40FT' => '40 pies',
            '40HC' => '40 HC',
            '45FT' => '45 pies',
            'OTHER' => 'Otro',
        ] as $value => $label)
                            <option value="{{ $value }}" @selected($size === $value)>

                                {{ $label }}

                            </option>
                        @endforeach

                    </select>

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

                        <option value="IN_TRANSIT" @selected($status === 'IN_TRANSIT')>
                            En tránsito
                        </option>

                        <option value="AT_CLIENT" @selected($status === 'AT_CLIENT')>
                            En cliente
                        </option>

                        <option value="AT_PORT" @selected($status === 'AT_PORT')>
                            En puerto
                        </option>

                        <option value="AT_DEPOT" @selected($status === 'AT_DEPOT')>
                            En depósito
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

                    <a href="{{ route('containers.index') }}" class="btn btn-light">
                        Limpiar
                    </a>

                </div>

            </form>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Contenedor</th>
                            <th>Tipo</th>
                            <th>Carga</th>
                            <th>Ubicación actual</th>
                            <th>Naviera</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($containers as $container)
                            <tr>

                                <td>

                                    <div class="fw-semibold">
                                        {{ $container->container_number }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $container->container_size }}
                                    </small>

                                </td>

                                <td>

                                    {{ $container->type_label }}

                                    @if ($container->container_type === 'REEFER')
                                        <span class="badge bg-info-subtle text-info ms-1">
                                            Reefer
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @if ($container->load_status === 'FULL')
                                        <span class="badge bg-success-subtle text-success">
                                            Lleno
                                        </span>
                                    @elseif ($container->load_status === 'EMPTY')
                                        <span class="badge bg-primary-subtle text-primary">
                                            Vacío
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            No definido
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @if ($container->currentLocation)
                                        <div>
                                            {{ $container->currentLocation->name }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $container->currentLocation->type_label }}
                                        </small>
                                    @else
                                        <span class="text-muted">
                                            Sin ubicación
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    {{ $container->shipping_line ?: 'No registrada' }}

                                </td>

                                <td>

                                    <span class="badge bg-primary-subtle text-primary">

                                        {{ $container->operational_status_label }}

                                    </span>

                                </td>

                                <td class="text-end">

                                    <a href="{{ route('containers.show', $container) }}"
                                        class="btn btn-sm btn-outline-secondary">

                                        <i class="ti ti-eye"></i>

                                    </a>

                                    <a href="{{ route('containers.edit', $container) }}"
                                        class="btn btn-sm btn-outline-primary">

                                        <i class="ti ti-edit"></i>

                                    </a>

                                    <form method="POST" action="{{ route('containers.destroy', $container) }}"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                          '¿Eliminar este contenedor?'
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

                                    <i class="ti ti-package fs-8 d-block mb-2"></i>

                                    No existen contenedores registrados.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">

                {{ $containers->links() }}

            </div>

        </div>

    </div>

@endsection

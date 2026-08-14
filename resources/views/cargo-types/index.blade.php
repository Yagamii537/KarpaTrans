@extends('layouts.app')

@section('title', 'Tipos de carga | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-semibold mb-1">
                Tipos de carga
            </h4>

            <p class="text-muted mb-0">
                Catálogo de cargas permitidas por cliente.
            </p>
        </div>

        <a href="{{ route('cargo-types.create') }}" class="btn btn-primary">

            <i class="ti ti-plus me-1"></i>
            Nuevo tipo de carga
        </a>

    </div>

    <div class="card">
        <div class="card-body">

            <form method="GET" class="row g-2 mb-4">

                <div class="col-md-5">

                    <input type="text" name="search" class="form-control" value="{{ $search }}"
                        placeholder="Buscar nombre o código">

                </div>

                <div class="col-auto">

                    <button class="btn btn-outline-primary">
                        <i class="ti ti-search"></i>
                        Buscar
                    </button>

                </div>

                <div class="col-auto">

                    <a href="{{ route('cargo-types.index') }}" class="btn btn-light">

                        Limpiar
                    </a>

                </div>

            </form>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Tipo de carga</th>
                            <th>Código</th>
                            <th>Clientes</th>
                            <th>Subclientes</th>
                            <th>Estado</th>
                            <th class="text-end">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($cargoTypes as $cargoType)
                            <tr>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $cargoType->name }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $cargoType->description }}
                                    </small>
                                </td>

                                <td>
                                    {{ $cargoType->code ?: '-' }}
                                </td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $cargoType->clients_count }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-info-subtle text-info">
                                        {{ $cargoType->subclients_count }}
                                    </span>
                                </td>

                                <td>

                                    @if ($cargoType->is_active)
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

                                    <a href="{{ route('cargo-types.edit', $cargoType) }}"
                                        class="btn btn-sm btn-outline-primary">

                                        <i class="ti ti-edit"></i>

                                    </a>

                                    <form method="POST" action="{{ route('cargo-types.destroy', $cargoType) }}"
                                        class="d-inline" onsubmit="return confirm('¿Eliminar este tipo de carga?')">

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

                                    <i class="ti ti-package fs-8 d-block mb-2"></i>

                                    No existen tipos de carga registrados.

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">
                {{ $cargoTypes->links() }}
            </div>

        </div>
    </div>

@endsection

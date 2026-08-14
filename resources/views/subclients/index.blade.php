@extends('layouts.app')

@section('title', 'Subclientes | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-semibold mb-1">
                Subclientes
            </h4>

            <p class="text-muted mb-0">
                Subclientes vinculados a los clientes principales.
            </p>
        </div>

        <a href="{{ route('subclients.create') }}" class="btn btn-primary">

            <i class="ti ti-plus me-1"></i>
            Nuevo subcliente

        </a>

    </div>

    <div class="card">
        <div class="card-body">

            <form method="GET" class="row g-2 mb-4">

                <div class="col-md-4">

                    <input type="text" name="search" class="form-control" placeholder="Buscar subcliente"
                        value="{{ $search }}">

                </div>

                <div class="col-md-4">

                    <select name="client_id" class="form-select">

                        <option value="">
                            Todos los clientes
                        </option>

                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" @selected($clientId == $client->id)>

                                {{ $client->business_name }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <div class="col-auto">

                    <button class="btn btn-outline-primary">

                        <i class="ti ti-search me-1"></i>
                        Buscar

                    </button>

                </div>

                <div class="col-auto">

                    <a href="{{ route('subclients.index') }}" class="btn btn-light">

                        Limpiar

                    </a>

                </div>

            </form>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Subcliente</th>
                            <th>Cliente principal</th>
                            <th>Identificación</th>
                            <th>Contacto</th>
                            <th>Tipos de carga</th>
                            <th>Estado</th>
                            <th class="text-end">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($subclients as $subclient)

                            <tr>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $subclient->display_name }}
                                    </div>

                                    @if ($subclient->trade_name)
                                        <small class="text-muted">
                                            {{ $subclient->business_name }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    {{ $subclient->client->business_name }}
                                </td>

                                <td>
                                    {{ $subclient->identification ?: 'Sin identificación' }}
                                </td>

                                <td>
                                    <div>
                                        {{ $subclient->contact_name ?: 'Sin contacto' }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $subclient->phone }}
                                    </small>
                                </td>

                                <td>

                                    @forelse ($subclient->cargoTypes as $cargoType)
                                        <span class="badge bg-primary-subtle text-primary mb-1">
                                            {{ $cargoType->name }}
                                        </span>

                                    @empty

                                        <span class="text-muted">
                                            Sin configuración
                                        </span>
                                    @endforelse

                                </td>

                                <td>

                                    @if ($subclient->is_active)
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

                                    <a href="{{ route('subclients.edit', $subclient) }}"
                                        class="btn btn-sm btn-outline-primary">

                                        <i class="ti ti-edit"></i>

                                    </a>

                                    <form method="POST" action="{{ route('subclients.destroy', $subclient) }}"
                                        class="d-inline" onsubmit="return confirm('¿Eliminar este subcliente?')">

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

                                    <i class="ti ti-users fs-8 d-block mb-2"></i>

                                    No existen subclientes registrados.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">
                {{ $subclients->links() }}
            </div>

        </div>
    </div>

@endsection

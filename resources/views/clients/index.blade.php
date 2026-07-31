@extends('layouts.app')

@section('title', 'Clientes | Karpan Logística')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

    <div>
        <h4 class="fw-semibold mb-1">
            Clientes
        </h4>

        <p class="text-muted mb-0">
            Administración de clientes y reglas operativas.
        </p>
    </div>

    <a href="{{ route('clients.create') }}"
       class="btn btn-primary">

        <i class="ti ti-plus me-1"></i>
        Nuevo cliente
    </a>

</div>

<div class="card">

    <div class="card-body">

        <form method="GET"
              action="{{ route('clients.index') }}"
              class="row g-2 mb-4">

            <div class="col-md-5">
                <div class="input-group">

                    <span class="input-group-text">
                        <i class="ti ti-search"></i>
                    </span>

                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Buscar por razón social, RUC o contacto"
                           value="{{ $search }}">

                </div>
            </div>

            <div class="col-auto">
                <button class="btn btn-outline-primary"
                        type="submit">
                    Buscar
                </button>
            </div>

            @if ($search)
                <div class="col-auto">
                    <a href="{{ route('clients.index') }}"
                       class="btn btn-light">
                        Limpiar
                    </a>
                </div>
            @endif

        </form>

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Identificación</th>
                        <th>Contacto</th>
                        <th>Horas libres</th>
                        <th>Conteo</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($clients as $client)

                        <tr>

                            <td>
                                <div class="fw-semibold">
                                    {{ $client->business_name }}
                                </div>

                                @if ($client->trade_name)
                                    <small class="text-muted">
                                        {{ $client->trade_name }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                <small class="text-muted d-block">
                                    {{ $client->identification_type }}
                                </small>

                                {{ $client->identification }}
                            </td>

                            <td>
                                <div>
                                    {{ $client->contact_name ?: 'Sin contacto' }}
                                </div>

                                <small class="text-muted">
                                    {{ $client->phone ?: $client->email }}
                                </small>
                            </td>

                            <td>
                                <div>
                                    Carga: {{ $client->free_loading_hours }} h
                                </div>

                                <small class="text-muted">
                                    Descarga: {{ $client->free_unloading_hours }} h
                                </small>
                            </td>

                            <td>
                                {{ $client->service_time_start_label }}

                                <small class="text-muted d-block">
                                    Fracción: {{ $client->standby_fraction_minutes }} min
                                </small>
                            </td>

                            <td>
                                @if ($client->is_active)
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

                                <a href="{{ route('clients.edit', $client) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Editar">

                                    <i class="ti ti-edit"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('clients.destroy', $client) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este cliente?')">

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

                                <i class="ti ti-users fs-8 d-block mb-2"></i>

                                No existen clientes registrados.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-3">
            {{ $clients->links() }}
        </div>

    </div>

</div>

@endsection

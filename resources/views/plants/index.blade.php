@extends('layouts.app')

@section('title', 'Plantas | Karpan Logística')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

    <div>
        <h4 class="fw-semibold mb-1">
            Plantas
        </h4>

        <p class="text-muted mb-0">
            Plantas, bodegas y puntos operativos pertenecientes a los clientes.
        </p>
    </div>

    <a href="{{ route('plants.create') }}"
       class="btn btn-primary">

        <i class="ti ti-plus me-1"></i>
        Nueva planta
    </a>

</div>

<div class="card">
    <div class="card-body">

        <form method="GET"
              action="{{ route('plants.index') }}"
              class="row g-2 mb-4">

            <div class="col-md-4">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Buscar planta, cliente, ciudad o dirección"
                       value="{{ $search }}">
            </div>

            <div class="col-md-4">
                <select name="client_id"
                        class="form-select">

                    <option value="">
                        Todos los clientes
                    </option>

                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}"
                            @selected($clientId == $client->id)>

                            {{ $client->business_name }}
                        </option>
                    @endforeach
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
                <a href="{{ route('plants.index') }}"
                   class="btn btn-light">
                    Limpiar
                </a>
            </div>
        </form>

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Planta</th>
                        <th>Cliente</th>
                        <th>Ubicación</th>
                        <th>Contacto</th>
                        <th>Reglas efectivas</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($plants as $plant)

                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    {{ $plant->name }}
                                </div>

                                <small class="text-muted">
                                    {{ $plant->code ?: 'Sin código' }}
                                </small>
                            </td>

                            <td>
                                {{ $plant->client->business_name }}
                            </td>

                            <td>
                                <div>
                                    {{ $plant->city ?: 'Sin ciudad' }}
                                </div>

                                <small class="text-muted">
                                    {{ \Illuminate\Support\Str::limit(
                                        $plant->address,
                                        45
                                    ) }}
                                </small>
                            </td>

                            <td>
                                <div>
                                    {{ $plant->contact_name ?: 'Sin contacto' }}
                                </div>

                                <small class="text-muted">
                                    {{ $plant->phone ?: $plant->email }}
                                </small>
                            </td>

                            <td>
                                <div>
                                    Carga:
                                    {{ $plant->effective_free_loading_hours }} h
                                </div>

                                <div>
                                    Descarga:
                                    {{ $plant->effective_free_unloading_hours }} h
                                </div>

                                <small class="text-muted">
                                    {{ $plant->effective_service_time_start_label }}
                                    ·
                                    {{ $plant->effective_standby_fraction_minutes }} min
                                </small>
                            </td>

                            <td>
                                @if ($plant->is_active)
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

                                <a href="{{ route('plants.edit', $plant) }}"
                                   class="btn btn-sm btn-outline-primary">

                                    <i class="ti ti-edit"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('plants.destroy', $plant) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Eliminar esta planta?')">

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
                            <td colspan="7"
                                class="text-center py-5 text-muted">

                                <i class="ti ti-building-factory fs-8 d-block mb-2"></i>

                                No existen plantas registradas.
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>

        </div>

        <div class="mt-3">
            {{ $plants->links() }}
        </div>

    </div>
</div>

@endsection

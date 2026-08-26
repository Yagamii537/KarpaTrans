@extends('layouts.app')

@section('title', 'Plantas | Karpan Logística')

@section('content')

    {{-- ========================================================= --}}
    {{-- MENSAJES --}}
    {{-- ========================================================= --}}

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">

            <i class="ti ti-circle-check me-1"></i>

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>

        </div>
    @endif


    @if ($errors->any())

        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            <div class="fw-semibold mb-2">

                <i class="ti ti-alert-circle me-1"></i>

                Se encontraron los siguientes errores:

            </div>

            @foreach ($errors->all() as $error)
                <div>
                    {{ $error }}
                </div>
            @endforeach

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                Plantas
            </h4>

            <p class="text-muted mb-0">

                Plantas y puntos operativos
                pertenecientes a los clientes.

            </p>

        </div>


        <a href="{{ route('plants.create') }}" class="btn btn-primary">

            <i class="ti ti-plus me-1"></i>

            Nueva planta

        </a>

    </div>


    {{-- ========================================================= --}}
    {{-- TARJETA PRINCIPAL --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-body">

            {{-- ========================================================= --}}
            {{-- FILTROS --}}
            {{-- ========================================================= --}}

            <form method="GET" action="{{ route('plants.index') }}" class="mb-4">

                <div class="row g-2">

                    <div class="col-md-5">

                        <input type="text" name="search" class="form-control" value="{{ $search }}"
                            placeholder="Buscar planta, cliente, ciudad o dirección">

                    </div>


                    <div class="col-md-4">

                        <select name="client_id" class="form-select">

                            <option value="">
                                Todos los clientes
                            </option>


                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected((string) $clientId === (string) $client->id)>

                                    {{ $client->business_name }}

                                </option>
                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-auto">

                        <button type="submit" class="btn btn-outline-primary">

                            <i class="ti ti-search me-1"></i>

                            Buscar

                        </button>

                    </div>


                    <div class="col-md-auto">

                        <a href="{{ route('plants.index') }}" class="btn btn-light">

                            Limpiar

                        </a>

                    </div>

                </div>

            </form>


            {{-- ========================================================= --}}
            {{-- TABLA --}}
            {{-- ========================================================= --}}

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>
                                Planta
                            </th>

                            <th>
                                Cliente
                            </th>

                            <th>
                                Ubicación
                            </th>

                            <th>
                                Contacto
                            </th>

                            <th>
                                Código
                            </th>

                            <th>
                                Estado
                            </th>

                            <th class="text-end">
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($plants as $plant)

                            <tr>

                                {{-- PLANTA --}}

                                <td>

                                    <div class="fw-semibold">

                                        {{ $plant->name }}

                                    </div>


                                    @if ($plant->reference)
                                        <small class="text-muted d-block">

                                            {{ \Illuminate\Support\Str::limit($plant->reference, 45) }}

                                        </small>
                                    @endif

                                </td>


                                {{-- CLIENTE --}}

                                <td>

                                    @if ($plant->client)
                                        <div class="fw-semibold">

                                            {{ $plant->client->business_name }}

                                        </div>


                                        @if ($plant->client->trade_name)
                                            <small class="text-muted">

                                                {{ $plant->client->trade_name }}

                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">

                                            Sin cliente

                                        </span>
                                    @endif

                                </td>


                                {{-- UBICACIÓN --}}

                                <td>

                                    @if ($plant->city)
                                        <div class="fw-semibold">

                                            <i class="ti ti-map-pin me-1"></i>

                                            {{ $plant->city }}

                                        </div>
                                    @endif


                                    @if ($plant->address)
                                        <small class="text-muted d-block">

                                            {{ \Illuminate\Support\Str::limit($plant->address, 55) }}

                                        </small>
                                    @endif


                                    @if (!$plant->city && !$plant->address)
                                        <span class="text-muted">

                                            No registrada

                                        </span>
                                    @endif

                                </td>


                                {{-- CONTACTO --}}

                                <td>

                                    @if ($plant->contact_name)
                                        <div>

                                            {{ $plant->contact_name }}

                                        </div>
                                    @endif


                                    @if ($plant->phone)
                                        <small class="text-muted d-block">

                                            <i class="ti ti-phone me-1"></i>

                                            {{ $plant->phone }}

                                        </small>
                                    @endif


                                    @if ($plant->email)
                                        <small class="text-muted d-block">

                                            {{ $plant->email }}

                                        </small>
                                    @endif


                                    @if (!$plant->contact_name && !$plant->phone && !$plant->email)
                                        <span class="text-muted">

                                            Sin contacto

                                        </span>
                                    @endif

                                </td>


                                {{-- CÓDIGO --}}

                                <td>

                                    @if ($plant->code)
                                        <span class="badge bg-light text-dark border">

                                            {{ $plant->code }}

                                        </span>
                                    @else
                                        <span class="text-muted">

                                            -

                                        </span>
                                    @endif

                                </td>


                                {{-- ESTADO --}}

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


                                {{-- ACCIONES --}}

                                <td class="text-end">

                                    <div class="d-inline-flex gap-1">

                                        {{-- VER --}}

                                        <a href="{{ route('plants.show', $plant) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Ver">

                                            <i class="ti ti-eye"></i>

                                        </a>


                                        {{-- EDITAR --}}

                                        <a href="{{ route('plants.edit', $plant) }}" class="btn btn-sm btn-outline-primary"
                                            title="Editar">

                                            <i class="ti ti-edit"></i>

                                        </a>


                                        {{-- ELIMINAR --}}

                                        <form method="POST" action="{{ route('plants.destroy', $plant) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Está seguro de eliminar esta planta?');">

                                            @csrf
                                            @method('DELETE')


                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">

                                                <i class="ti ti-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="ti ti-building-factory fs-7 d-block mb-2"></i>

                                        No existen plantas registradas.

                                    </div>


                                    <a href="{{ route('plants.create') }}" class="btn btn-primary btn-sm mt-3">

                                        <i class="ti ti-plus me-1"></i>

                                        Registrar primera planta

                                    </a>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ========================================================= --}}
            {{-- PAGINACIÓN --}}
            {{-- ========================================================= --}}

            @if ($plants->hasPages())
                <div class="d-flex justify-content-end mt-3">

                    {{ $plants->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection

@extends('layouts.app')

@section('title', 'Transferencias | Karpan Logística')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                Transferencias
            </h4>

            <p class="text-muted mb-0">
                Movimientos adicionales originados durante la ejecución de los viajes.
            </p>

        </div>

    </div>


    <div class="card">

        <div class="card-body">

            {{-- ========================================================= --}}
            {{-- FILTROS --}}
            {{-- ========================================================= --}}

            <form method="GET" action="{{ route('transfers.index') }}" class="row g-2 mb-4">

                <div class="col-md-5">

                    <input type="text" name="search" class="form-control"
                        placeholder="Transferencia, viaje, OT, cliente, origen o destino" value="{{ $search }}">

                </div>


                <div class="col-md-3">

                    <select name="status" class="form-select">

                        <option value="">
                            Todos los estados
                        </option>

                        @foreach ([
            'PENDING' => 'Pendiente',
            'ASSIGNED' => 'Asignada',
            'IN_TRANSIT' => 'En tránsito',
            'COMPLETED' => 'Completada',
            'CANCELLED' => 'Cancelada',
        ] as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>

                                {{ $label }}

                            </option>
                        @endforeach

                    </select>

                </div>


                <div class="col-auto">

                    <button type="submit" class="btn btn-outline-primary">

                        <i class="ti ti-search me-1"></i>

                        Buscar

                    </button>

                </div>


                <div class="col-auto">

                    <a href="{{ route('transfers.index') }}" class="btn btn-light">

                        Limpiar

                    </a>

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
                                Transferencia
                            </th>

                            <th>
                                Viaje / OT
                            </th>

                            <th>
                                Cliente
                            </th>

                            <th>
                                Ruta
                            </th>

                            <th>
                                Programación
                            </th>

                            <th>
                                Recursos
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

                        @forelse ($transfers as $transfer)

                            @php

                                $assignment =
                                    $transfer->activeAssignment ??
                                    (in_array($transfer->status, ['COMPLETED', 'CANCELLED'])
                                        ? $transfer->assignments->sortByDesc('assigned_at')->first()
                                        : null);
                            @endphp


                            <tr>

                                {{-- TRANSFERENCIA --}}

                                <td>

                                    <div class="fw-semibold">

                                        {{ $transfer->transfer_number }}

                                    </div>


                                    <small class="text-muted">

                                        ID: {{ $transfer->id }}

                                    </small>

                                </td>


                                {{-- VIAJE / OT --}}

                                <td>

                                    <div>

                                        {{ $transfer->trip?->trip_number ?: '-' }}

                                    </div>


                                    <small class="text-muted d-block">

                                        {{ $transfer->trip?->workOrder?->work_order_number ?: '-' }}

                                    </small>

                                </td>


                                {{-- CLIENTE --}}

                                <td>

                                    <div>

                                        {{ $transfer->trip?->client_name_snapshot ?: '-' }}

                                    </div>


                                    @if ($transfer->trip?->subclient_name_snapshot)
                                        <small class="text-muted d-block">

                                            {{ $transfer->trip->subclient_name_snapshot }}

                                        </small>
                                    @endif

                                </td>


                                {{-- RUTA --}}

                                <td>

                                    <div>

                                        {{ $transfer->origin_name_snapshot }}

                                    </div>


                                    <small class="text-muted d-block">

                                        <i class="ti ti-arrow-right me-1"></i>

                                        {{ $transfer->destination_name_snapshot }}

                                    </small>

                                </td>


                                {{-- PROGRAMACIÓN --}}

                                <td>

                                    @if ($transfer->scheduled_at)
                                        {{ $transfer->scheduled_at->format('d/m/Y') }}

                                        <small class="text-muted d-block">

                                            {{ $transfer->scheduled_at->format('H:i') }}

                                        </small>
                                    @else
                                        <span class="text-muted">

                                            No programada

                                        </span>
                                    @endif

                                </td>


                                {{-- RECURSOS --}}

                                <td>

                                    @if ($assignment)
                                        <div class="small">

                                            <i class="ti ti-user me-1"></i>

                                            {{ $assignment->driver?->full_name ?: '-' }}

                                        </div>


                                        <small class="text-muted d-block">

                                            <i class="ti ti-truck me-1"></i>

                                            {{ $assignment->vehicle?->plate ?: '-' }}

                                        </small>


                                        @if ($assignment->container)
                                            <small class="text-muted d-block">

                                                <i class="ti ti-box me-1"></i>

                                                {{ $assignment->container->container_number }}

                                            </small>
                                        @endif


                                        @if ($transfer->status === 'COMPLETED')
                                            <small class="text-success d-block mt-1">

                                                Recursos utilizados

                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">

                                            Sin asignar

                                        </span>
                                    @endif

                                </td>


                                {{-- ESTADO --}}

                                <td>

                                    <span class="badge {{ $transfer->status_badge_class }}">

                                        {{ $transfer->status_label }}

                                    </span>

                                </td>


                                {{-- ACCIÓN --}}

                                <td class="text-end">

                                    <a href="{{ route('transfers.show', $transfer) }}"
                                        class="btn btn-sm btn-outline-primary" title="Ver transferencia">

                                        <i class="ti ti-eye"></i>

                                    </a>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="8" class="text-center py-5 text-muted">

                                    <i class="ti ti-arrows-exchange fs-7 d-block mb-2"></i>

                                    No existen transferencias registradas.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="mt-3">

                {{ $transfers->links() }}

            </div>

        </div>

    </div>

@endsection

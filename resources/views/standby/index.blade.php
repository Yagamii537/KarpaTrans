@extends('layouts.app')

@section('title', 'Stand-by | Karpan Logística')

@section('content')

    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                Stand-by
            </h4>

            <p class="text-muted mb-0">

                Consolidado de tiempos de espera
                y horas facturables por viaje.

            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- RESUMEN --}}
    {{-- ========================================================= --}}

    <div class="row">

        <div class="col-md-6 col-xl-3 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <small class="text-muted d-block mb-2">

                        Total cálculos

                    </small>


                    <div class="fs-5 fw-semibold">

                        {{ $totalCalculations }}

                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-xl-3 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <small class="text-muted d-block mb-2">

                        Pendientes

                    </small>


                    <div class="fs-5 fw-semibold text-warning">

                        {{ $pendingCalculations }}

                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-xl-3 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <small class="text-muted d-block mb-2">

                        Calculados

                    </small>


                    <div class="fs-5 fw-semibold text-success">

                        {{ $calculatedCalculations }}

                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-xl-3 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <small class="text-muted d-block mb-2">

                        Horas facturables

                    </small>


                    <div class="fs-5 fw-semibold">

                        {{ $totalBillableHours }} h

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- LISTADO --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-body">

            {{-- ========================================================= --}}
            {{-- FILTROS --}}
            {{-- ========================================================= --}}

            <form method="GET" action="{{ route('standby.index') }}" class="row g-2 mb-4">

                <div class="col-md-4">

                    <input type="text" name="search" class="form-control" placeholder="Viaje, OT, booking, cliente..."
                        value="{{ $search }}">

                </div>


                <div class="col-md-2">

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


                <div class="col-md-2">

                    <select name="status" class="form-select">

                        <option value="">

                            Todos los estados

                        </option>


                        <option value="PENDING" @selected($status === 'PENDING')>

                            Pendiente

                        </option>


                        <option value="CALCULATED" @selected($status === 'CALCULATED')>

                            Calculado

                        </option>

                    </select>

                </div>


                <div class="col-md-2">

                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">

                </div>


                <div class="col-md-2">

                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">

                </div>


                <div class="col-auto">

                    <button type="submit" class="btn btn-outline-primary">

                        <i class="ti ti-search me-1"></i>

                        Buscar

                    </button>

                </div>


                <div class="col-auto">

                    <a href="{{ route('standby.index') }}" class="btn btn-light">

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
                                Viaje
                            </th>

                            <th>
                                Servicio
                            </th>

                            <th>
                                Cliente
                            </th>

                            <th>
                                Inicio
                            </th>

                            <th>
                                Fin
                            </th>

                            <th>
                                Tiempo
                            </th>

                            <th>
                                Regla
                            </th>

                            <th>
                                Stand-by
                            </th>

                            <th>
                                Estado
                            </th>

                            <th class="text-end">
                                Acción
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($calculations as $standby)
                            @php

                                $trip = $standby->trip;

                                $workOrder = $trip?->workOrder;

                            @endphp


                            <tr>

                                {{-- VIAJE --}}

                                <td>

                                    <div class="fw-semibold">

                                        {{ $trip?->trip_number ?: '-' }}

                                    </div>


                                    <small class="text-muted d-block">

                                        {{ $workOrder?->work_order_number ?: '-' }}

                                    </small>

                                </td>


                                {{-- SERVICIO --}}

                                <td>

                                    <div>

                                        Servicio #{{ $trip?->service_number ?: '-' }}

                                    </div>


                                    <small class="text-muted d-block">

                                        {{ $trip?->service_stage_label ?: '-' }}

                                    </small>


                                    @if ($workOrder?->service_modality === 'POSITIONING_PICKUP')
                                        <small class="text-muted d-block">

                                            {{ $trip->service_stage === 'POSITIONING' ? 'Etapa 1 de 2' : 'Etapa 2 de 2' }}

                                        </small>
                                    @endif

                                </td>


                                {{-- CLIENTE --}}

                                <td>

                                    <div>

                                        {{ $trip?->client_name_snapshot ?: '-' }}

                                    </div>


                                    @if ($trip?->subclient_name_snapshot)
                                        <small class="text-muted d-block">

                                            {{ $trip->subclient_name_snapshot }}

                                        </small>
                                    @endif

                                </td>


                                {{-- INICIO --}}

                                <td>

                                    @if ($standby->start_at)
                                        {{ $standby->start_at->format('d/m/Y') }}

                                        <small class="text-muted d-block">

                                            {{ $standby->start_at->format('H:i') }}

                                        </small>
                                    @else
                                        <span class="text-muted">

                                            Pendiente

                                        </span>
                                    @endif

                                </td>


                                {{-- FIN --}}

                                <td>

                                    @if ($standby->end_at)
                                        {{ $standby->end_at->format('d/m/Y') }}

                                        <small class="text-muted d-block">

                                            {{ $standby->end_at->format('H:i') }}

                                        </small>
                                    @else
                                        <span class="text-muted">

                                            Pendiente

                                        </span>
                                    @endif

                                </td>


                                {{-- TIEMPO --}}

                                <td>

                                    @if ($standby->total_minutes !== null)
                                        <div>

                                            {{ intdiv($standby->total_minutes, 60) }}
                                            h

                                            {{ $standby->total_minutes % 60 }} min

                                        </div>


                                        <small class="text-muted">

                                            Exceso:
                                            {{ $standby->excess_minutes }} min

                                        </small>
                                    @else
                                        <span class="text-muted">

                                            -

                                        </span>
                                    @endif

                                </td>


                                {{-- REGLA --}}

                                <td>

                                    <div>

                                        {{ $standby->free_hours }} h libres

                                    </div>


                                    <small class="text-muted d-block">

                                        Fracción:
                                        {{ $standby->fraction_minutes }} min

                                    </small>


                                    <small class="text-muted d-block">

                                        {{ $standby->count_start_type_label }}

                                    </small>

                                </td>


                                {{-- FACTURABLE --}}

                                <td>

                                    <div class="fw-semibold">

                                        {{ $standby->billable_hours }} h

                                    </div>


                                    @if ($standby->billable_hours > 0)
                                        <small class="text-success">

                                            Facturable

                                        </small>
                                    @else
                                        <small class="text-muted">

                                            Sin excedente facturable

                                        </small>
                                    @endif

                                </td>


                                {{-- ESTADO --}}

                                <td>

                                    @if ($standby->status === 'CALCULATED')
                                        <span class="badge bg-success-subtle text-success">

                                            Calculado

                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">

                                            Pendiente

                                        </span>
                                    @endif

                                </td>


                                {{-- ACCIONES --}}

                                <td class="text-end">

                                    @if ($trip)
                                        <a href="{{ route('trips.show', $trip) }}" class="btn btn-sm btn-outline-primary"
                                            title="Ver viaje">

                                            <i class="ti ti-eye"></i>

                                        </a>
                                    @endif

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="10" class="text-center py-5 text-muted">

                                    <i class="ti ti-clock fs-7 d-block mb-2"></i>

                                    No existen cálculos de Stand-by registrados.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ========================================================= --}}
            {{-- PAGINACIÓN --}}
            {{-- ========================================================= --}}

            <div class="mt-3">

                {{ $calculations->links() }}

            </div>

        </div>

    </div>

@endsection

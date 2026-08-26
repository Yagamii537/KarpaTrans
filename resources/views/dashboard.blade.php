@extends('layouts.app')

@section('title', 'Dashboard | Karpan Logística')

@section('content')

    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">

                Dashboard

            </h4>

            <p class="text-muted mb-0">

                Resumen operativo de Karpan Transt.

            </p>

        </div>


        <div>

            <span class="badge bg-light text-dark border">

                {{ now()->format('d/m/Y') }}

            </span>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- INDICADORES PRINCIPALES --}}
    {{-- ========================================================= --}}

    <div class="row">

        {{-- ÓRDENES --}}

        <div class="col-sm-6 col-xl-3">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>

                            <small class="text-muted d-block mb-1">

                                Órdenes activas

                            </small>

                            <h3 class="fw-bold mb-0">

                                {{ $activeWorkOrders }}

                            </h3>

                        </div>


                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                            style="width:50px;height:50px;">

                            <i class="ti ti-file-text fs-6"></i>

                        </div>

                    </div>


                    <div class="mt-3">

                        <a href="{{ route('work-orders.index') }}" class="small fw-semibold">

                            Ver órdenes

                            <i class="ti ti-arrow-right ms-1"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- VIAJES --}}

        <div class="col-sm-6 col-xl-3">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>

                            <small class="text-muted d-block mb-1">

                                Viajes activos

                            </small>

                            <h3 class="fw-bold mb-0">

                                {{ $activeTrips }}

                            </h3>

                        </div>


                        <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center"
                            style="width:50px;height:50px;">

                            <i class="ti ti-truck fs-6"></i>

                        </div>

                    </div>


                    <div class="mt-3">

                        <a href="{{ route('trips.index') }}" class="small fw-semibold">

                            Ver viajes

                            <i class="ti ti-arrow-right ms-1"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- TRANSFERENCIAS --}}

        <div class="col-sm-6 col-xl-3">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>

                            <small class="text-muted d-block mb-1">

                                Transferencias activas

                            </small>

                            <h3 class="fw-bold mb-0">

                                {{ $activeTransfers }}

                            </h3>

                        </div>


                        <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center"
                            style="width:50px;height:50px;">

                            <i class="ti ti-arrows-exchange fs-6"></i>

                        </div>

                    </div>


                    <div class="mt-3">

                        <a href="{{ route('transfers.index') }}" class="small fw-semibold">

                            Ver transferencias

                            <i class="ti ti-arrow-right ms-1"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- STAND-BY --}}

        <div class="col-sm-6 col-xl-3">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>

                            <small class="text-muted d-block mb-1">

                                Stand-by pendiente

                            </small>

                            <h3 class="fw-bold mb-0">

                                {{ $pendingStandby }}

                            </h3>

                        </div>


                        <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center"
                            style="width:50px;height:50px;">

                            <i class="ti ti-clock fs-6"></i>

                        </div>

                    </div>


                    <div class="mt-3">

                        @if (Route::has('standby.index'))
                            <a href="{{ route('standby.index') }}" class="small fw-semibold">

                                Ver Stand-by

                                <i class="ti ti-arrow-right ms-1"></i>

                            </a>
                        @else
                            <span class="small text-muted">

                                Seguimiento operativo

                            </span>
                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ESTADO DE OPERACIÓN --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                <div>

                    <h5 class="fw-semibold mb-1">

                        Estado de los viajes

                    </h5>

                    <p class="text-muted mb-0">

                        Situación actual de la operación.

                    </p>

                </div>


                <a href="{{ route('trips.index') }}" class="btn btn-sm btn-outline-primary">

                    Ver todos

                </a>

            </div>


            <div class="row text-center">

                <div class="col-6 col-md mb-3">

                    <div class="border rounded p-3 h-100">

                        <div class="fs-4 fw-bold">

                            {{ $tripStats['pending'] }}

                        </div>

                        <small class="text-muted">

                            Pendientes

                        </small>

                    </div>

                </div>


                <div class="col-6 col-md mb-3">

                    <div class="border rounded p-3 h-100">

                        <div class="fs-4 fw-bold">

                            {{ $tripStats['assigned'] }}

                        </div>

                        <small class="text-muted">

                            Asignados

                        </small>

                    </div>

                </div>


                <div class="col-6 col-md mb-3">

                    <div class="border rounded p-3 h-100">

                        <div class="fs-4 fw-bold">

                            {{ $tripStats['in_transit'] }}

                        </div>

                        <small class="text-muted">

                            En tránsito

                        </small>

                    </div>

                </div>


                <div class="col-6 col-md mb-3">

                    <div class="border rounded p-3 h-100">

                        <div class="fs-4 fw-bold">

                            {{ $tripStats['at_destination'] }}

                        </div>

                        <small class="text-muted">

                            En destino

                        </small>

                    </div>

                </div>


                <div class="col-12 col-md mb-3">

                    <div class="border rounded p-3 h-100">

                        <div class="fs-4 fw-bold">

                            {{ $tripStats['completed_today'] }}

                        </div>

                        <small class="text-muted">

                            Completados hoy

                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ALERTAS --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-body">

            <h5 class="fw-semibold mb-1">

                Alertas operativas

            </h5>

            <p class="text-muted mb-4">

                Situaciones que requieren revisión.

            </p>


            <div class="row">

                {{-- VIAJES SIN RECURSOS --}}

                <div class="col-md-4 mb-3">

                    <div class="border rounded p-3 h-100">

                        <div class="d-flex align-items-center gap-3">

                            <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center"
                                style="width:44px;height:44px;">

                                <i class="ti ti-alert-circle"></i>

                            </div>


                            <div>

                                <div class="fw-semibold">

                                    Viajes sin recursos

                                </div>

                                <div class="fs-4 fw-bold">

                                    {{ $tripsWithoutAssignment }}

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- CONDUCTORES --}}

                <div class="col-md-4 mb-3">

                    <div class="border rounded p-3 h-100">

                        <div class="d-flex align-items-center gap-3">

                            <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center"
                                style="width:44px;height:44px;">

                                <i class="ti ti-users"></i>

                            </div>


                            <div>

                                <div class="fw-semibold">

                                    Alertas de licencias

                                </div>

                                <div class="fs-4 fw-bold">

                                    {{ $driversWithAlerts->count() }}

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- VEHÍCULOS --}}

                <div class="col-md-4 mb-3">

                    <div class="border rounded p-3 h-100">

                        <div class="d-flex align-items-center gap-3">

                            <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center"
                                style="width:44px;height:44px;">

                                <i class="ti ti-truck"></i>

                            </div>


                            <div>

                                <div class="fw-semibold">

                                    Vehículos con documentos vencidos

                                </div>

                                <div class="fs-4 fw-bold">

                                    {{ $vehiclesWithAlerts->count() }}

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            @if ($driversWithAlerts->count() > 0 || $vehiclesWithAlerts->count() > 0)

                <hr>


                <div class="row">

                    {{-- DETALLE CONDUCTORES --}}

                    @if ($driversWithAlerts->count() > 0)

                        <div class="col-lg-6 mb-3">

                            <h6 class="fw-semibold mb-3">

                                Conductores

                            </h6>


                            @foreach ($driversWithAlerts->take(5) as $driver)
                                <div class="d-flex justify-content-between border-bottom py-2">

                                    <div>

                                        <div class="fw-semibold">

                                            {{ $driver->full_name }}

                                        </div>

                                        <small class="text-muted">

                                            {{ $driver->identification }}

                                        </small>

                                    </div>


                                    @if ($driver->license_status === 'expired')
                                        <span class="badge bg-danger-subtle text-danger align-self-center">

                                            Vencida

                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning align-self-center">

                                            Por vencer

                                        </span>
                                    @endif

                                </div>
                            @endforeach

                        </div>

                    @endif


                    {{-- DETALLE VEHÍCULOS --}}

                    @if ($vehiclesWithAlerts->count() > 0)

                        <div class="col-lg-6 mb-3">

                            <h6 class="fw-semibold mb-3">

                                Vehículos

                            </h6>


                            @foreach ($vehiclesWithAlerts->take(5) as $vehicle)
                                <div class="d-flex justify-content-between border-bottom py-2">

                                    <div>

                                        <div class="fw-semibold">

                                            {{ $vehicle->plate }}

                                        </div>

                                        <small class="text-muted">

                                            {{ $vehicle->brand }}
                                            {{ $vehicle->model }}

                                        </small>

                                    </div>


                                    <span class="badge bg-danger-subtle text-danger align-self-center">

                                        Revisar documentos

                                    </span>

                                </div>
                            @endforeach

                        </div>

                    @endif

                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- VIAJES RECIENTES --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="fw-semibold mb-1">

                        Viajes recientes

                    </h5>

                    <p class="text-muted mb-0">

                        Últimos viajes registrados en el sistema.

                    </p>

                </div>


                <a href="{{ route('trips.index') }}" class="btn btn-sm btn-outline-primary">

                    Ver todos

                </a>

            </div>


            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Viaje</th>

                            <th>Cliente</th>

                            <th>Ruta</th>

                            <th>Programación</th>

                            <th>Recursos</th>

                            <th>Estado</th>

                            <th></th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($recentTrips
                                                as $trip)
                            <tr>

                                <td>

                                    <div class="fw-semibold">

                                        {{ $trip->trip_number }}

                                    </div>

                                    <small class="text-muted">

                                        {{ $trip->stage_progress_label }}

                                    </small>

                                </td>


                                <td>

                                    {{ $trip->client_name_snapshot }}

                                    @if ($trip->subclient_name_snapshot)
                                        <small class="text-muted d-block">

                                            {{ $trip->subclient_name_snapshot }}

                                        </small>
                                    @endif

                                </td>


                                <td>

                                    <div>

                                        {{ $trip->origin_name_snapshot }}

                                    </div>

                                    <small class="text-muted">

                                        <i class="ti ti-arrow-right me-1"></i>

                                        {{ $trip->destination_name_snapshot }}

                                    </small>

                                </td>


                                <td>

                                    {{ $trip->scheduled_start_at ? $trip->scheduled_start_at->format('d/m/Y') : '-' }}

                                    <small class="text-muted d-block">

                                        {{ $trip->scheduled_start_at ? $trip->scheduled_start_at->format('H:i') : '' }}

                                    </small>

                                </td>


                                <td>

                                    @if ($trip->activeAssignment)
                                        <div class="small">

                                            {{ $trip->activeAssignment->driver?->full_name ?: '-' }}

                                        </div>

                                        <small class="text-muted">

                                            {{ $trip->activeAssignment->vehicle?->plate ?: '-' }}

                                        </small>
                                    @else
                                        <span class="text-muted">

                                            Sin asignar

                                        </span>
                                    @endif

                                </td>


                                <td>

                                    <span
                                        class="badge
                                        @if ($trip->status === 'COMPLETED') bg-success-subtle text-success
                                        @elseif ($trip->status === 'CANCELLED')
                                            bg-danger-subtle text-danger
                                        @elseif ($trip->status === 'IN_TRANSIT')
                                            bg-warning-subtle text-warning
                                        @elseif ($trip->status === 'AT_DESTINATION')
                                            bg-info-subtle text-info
                                        @else
                                            bg-primary-subtle text-primary @endif">

                                        {{ $trip->status_label }}

                                    </span>

                                </td>


                                <td class="text-end">

                                    <a href="{{ route('trips.show', $trip) }}" class="btn btn-sm btn-outline-primary">

                                        <i class="ti ti-eye"></i>

                                    </a>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="7" class="text-center py-4 text-muted">

                                    No existen viajes registrados.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- TRANSFERENCIAS + STAND-BY --}}
    {{-- ========================================================= --}}

    <div class="row">

        {{-- TRANSFERENCIAS --}}

        <div class="col-xl-7">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h5 class="fw-semibold mb-1">

                                Transferencias recientes

                            </h5>

                            <p class="text-muted mb-0">

                                Últimos movimientos adicionales.

                            </p>

                        </div>


                        <a href="{{ route('transfers.index') }}" class="btn btn-sm btn-outline-primary">

                            Ver todas

                        </a>

                    </div>


                    @forelse ($recentTransfers
                                            as $transfer)
                        @php

                            $assignment =
                                $transfer->activeAssignment ??
                                $transfer->assignments->sortByDesc('assigned_at')->first();

                        @endphp


                        <div class="border rounded p-3 mb-3">

                            <div class="d-flex justify-content-between gap-3">

                                <div>

                                    <a href="{{ route('transfers.show', $transfer) }}" class="fw-semibold">

                                        {{ $transfer->transfer_number }}

                                    </a>


                                    <small class="text-muted d-block">

                                        {{ $transfer->trip?->trip_number }}

                                    </small>


                                    <div class="mt-2">

                                        {{ $transfer->origin_name_snapshot }}

                                        <i class="ti ti-arrow-right mx-1"></i>

                                        {{ $transfer->destination_name_snapshot }}

                                    </div>


                                    @if ($assignment)
                                        <small class="text-muted d-block mt-1">

                                            {{ $assignment->driver?->full_name ?: '-' }}

                                            ·

                                            {{ $assignment->vehicle?->plate ?: '-' }}

                                        </small>
                                    @endif

                                </div>


                                <div>

                                    <span class="badge {{ $transfer->status_badge_class }}">

                                        {{ $transfer->status_label }}

                                    </span>

                                </div>

                            </div>

                        </div>


                    @empty

                        <div class="text-center text-muted py-4">

                            No existen transferencias registradas.

                        </div>
                    @endforelse

                </div>

            </div>

        </div>


        {{-- STAND-BY --}}

        <div class="col-xl-5">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h5 class="fw-semibold mb-1">

                                Stand-by reciente

                            </h5>

                            <p class="text-muted mb-0">

                                Últimos cálculos registrados.

                            </p>

                        </div>

                    </div>


                    @forelse ($recentStandby
                                            as $standby)
                        <div class="border-bottom py-3">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <a href="{{ route('trips.show', $standby->trip) }}" class="fw-semibold">

                                        {{ $standby->trip?->trip_number ?: '-' }}

                                    </a>


                                    <small class="text-muted d-block">

                                        {{ $standby->trip?->client_name_snapshot ?: '-' }}

                                    </small>

                                </div>


                                <div class="text-end">

                                    @if ($standby->status === 'CALCULATED')
                                        <span class="badge bg-success-subtle text-success">

                                            {{ $standby->billable_hours }} h

                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">

                                            Pendiente

                                        </span>
                                    @endif

                                </div>

                            </div>

                        </div>


                    @empty

                        <div class="text-center text-muted py-4">

                            No existen cálculos de Stand-by.

                        </div>
                    @endforelse

                </div>

            </div>

        </div>

    </div>

@endsection

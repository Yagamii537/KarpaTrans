@extends('layouts.app')

@section('title', 'Reportes | Karpan Logística')

@section('content')

    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">

                Reportes estadísticos

            </h4>

            <p class="text-muted mb-0">

                Análisis consolidado de la operación logística.

            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- FILTROS --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-body">

            <h5 class="fw-semibold mb-4">

                Filtros del reporte

            </h5>


            <form method="GET" action="{{ route('reports.index') }}">

                <div class="row">

                    <div class="col-md-2 mb-3">

                        <label class="form-label">

                            Desde

                        </label>


                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">

                    </div>


                    <div class="col-md-2 mb-3">

                        <label class="form-label">

                            Hasta

                        </label>


                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">

                    </div>


                    <div class="col-md-2 mb-3">

                        <label class="form-label">

                            Estado

                        </label>


                        <select name="status" class="form-select">

                            <option value="">

                                Todos

                            </option>


                            <option value="PENDING" @selected($status === 'PENDING')>

                                Pendiente

                            </option>


                            <option value="ASSIGNED" @selected($status === 'ASSIGNED')>

                                Asignado

                            </option>


                            <option value="IN_TRANSIT" @selected($status === 'IN_TRANSIT')>

                                En tránsito

                            </option>


                            <option value="AT_DESTINATION" @selected($status === 'AT_DESTINATION')>

                                En destino

                            </option>


                            <option value="COMPLETED" @selected($status === 'COMPLETED')>

                                Completado

                            </option>


                            <option value="CANCELLED" @selected($status === 'CANCELLED')>

                                Cancelado

                            </option>

                        </select>

                    </div>


                    <div class="col-md-2 mb-3">

                        <label class="form-label">

                            Operación

                        </label>


                        <select name="operation_type" class="form-select">

                            <option value="">

                                Todas

                            </option>


                            <option value="EXPORT" @selected($operationType === 'EXPORT')>

                                Exportación

                            </option>


                            <option value="IMPORT" @selected($operationType === 'IMPORT')>

                                Importación

                            </option>


                            <option value="TRANSFER" @selected($operationType === 'TRANSFER')>

                                Transferencia

                            </option>


                            <option value="OTHER" @selected($operationType === 'OTHER')>

                                Otro

                            </option>

                        </select>

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label">

                            Cliente / Subcliente

                        </label>


                        <input type="text" name="client" class="form-control" placeholder="Buscar cliente..."
                            value="{{ $client }}">

                    </div>


                    <div class="col-md-1 mb-3 d-flex align-items-end">

                        <button type="submit" class="btn btn-primary w-100">

                            <i class="ti ti-search"></i>

                        </button>

                    </div>

                </div>


                <a href="{{ route('reports.index') }}" class="btn btn-light">

                    <i class="ti ti-refresh me-1"></i>

                    Limpiar filtros

                </a>

            </form>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- INDICADORES --}}
    {{-- ========================================================= --}}

    <div class="row">

        <div class="col-sm-6 col-xl-3">

            <div class="card">

                <div class="card-body">

                    <small class="text-muted d-block">

                        Órdenes

                    </small>

                    <h3 class="fw-bold mb-0">

                        {{ $summary['work_orders'] }}

                    </h3>

                </div>

            </div>

        </div>


        <div class="col-sm-6 col-xl-3">

            <div class="card">

                <div class="card-body">

                    <small class="text-muted d-block">

                        Viajes

                    </small>

                    <h3 class="fw-bold mb-0">

                        {{ $summary['trips'] }}

                    </h3>

                </div>

            </div>

        </div>


        <div class="col-sm-6 col-xl-3">

            <div class="card">

                <div class="card-body">

                    <small class="text-muted d-block">

                        Completados

                    </small>

                    <h3 class="fw-bold mb-0">

                        {{ $summary['completed_trips'] }}

                    </h3>

                </div>

            </div>

        </div>


        <div class="col-sm-6 col-xl-3">

            <div class="card">

                <div class="card-body">

                    <small class="text-muted d-block">

                        Transferencias

                    </small>

                    <h3 class="fw-bold mb-0">

                        {{ $summary['transfers'] }}

                    </h3>

                </div>

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-md-4">

            <div class="card">

                <div class="card-body">

                    <small class="text-muted d-block">

                        Stand-by facturable

                    </small>

                    <h3 class="fw-bold mb-0">

                        {{ number_format((float) $summary['standby_hours'], 2) }}
                        h

                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card">

                <div class="card-body">

                    <small class="text-muted d-block">

                        Costos vigentes

                    </small>

                    <h3 class="fw-bold mb-0">

                        ${{ number_format((float) $summary['cost_total'], 2) }}

                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card">

                <div class="card-body">

                    <small class="text-muted d-block">

                        Costos aprobados

                    </small>

                    <h3 class="fw-bold mb-0">

                        ${{ number_format((float) $summary['approved_cost_total'], 2) }}

                    </h3>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- GRÁFICOS FILA 1 --}}
    {{-- ========================================================= --}}

    <div class="row">

        {{-- VIAJES POR ESTADO --}}

        <div class="col-xl-6">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-1">

                        Viajes por estado

                    </h5>

                    <p class="text-muted mb-4">

                        Distribución actual de los viajes.

                    </p>


                    <div style="height:350px;">

                        <canvas id="statusChart"></canvas>

                    </div>

                </div>

            </div>

        </div>


        {{-- OPERACIONES --}}

        <div class="col-xl-6">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-1">

                        Viajes por operación

                    </h5>

                    <p class="text-muted mb-4">

                        Distribución por tipo de operación logística.

                    </p>


                    <div style="height:350px;">

                        <canvas id="operationChart"></canvas>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- GRÁFICOS FILA 2 --}}
    {{-- ========================================================= --}}

    <div class="row">

        {{-- VIAJES POR DÍA --}}

        <div class="col-xl-7">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-1">

                        Viajes por día

                    </h5>

                    <p class="text-muted mb-4">

                        Evolución del volumen operativo.

                    </p>


                    <div style="height:360px;">

                        <canvas id="dailyChart"></canvas>

                    </div>

                </div>

            </div>

        </div>


        {{-- CLIENTES --}}

        <div class="col-xl-5">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-1">

                        Principales clientes

                    </h5>

                    <p class="text-muted mb-4">

                        Clientes con mayor cantidad de viajes.

                    </p>


                    <div style="height:360px;">

                        <canvas id="clientChart"></canvas>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- GRÁFICOS FILA 3 --}}
    {{-- ========================================================= --}}

    <div class="row">

        {{-- COSTOS --}}

        <div class="col-xl-6">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-1">

                        Costos por concepto

                    </h5>

                    <p class="text-muted mb-4">

                        Distribución económica de la operación.

                    </p>


                    <div style="height:350px;">

                        <canvas id="costChart"></canvas>

                    </div>

                </div>

            </div>

        </div>


        {{-- STANDBY --}}

        <div class="col-xl-6">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-1">

                        Mayor Stand-by

                    </h5>

                    <p class="text-muted mb-4">

                        Viajes con más horas facturables de espera.

                    </p>


                    <div style="height:350px;">

                        <canvas id="standbyChart"></canvas>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DETALLE --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-body">

            <h5 class="fw-semibold mb-1">

                Detalle de operación

            </h5>

            <p class="text-muted mb-4">

                Viajes correspondientes a los filtros seleccionados.

            </p>


            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Viaje</th>

                            <th>OT</th>

                            <th>Cliente</th>

                            <th>Ruta</th>

                            <th>Recursos</th>

                            <th>Stand-by</th>

                            <th>Transferencias</th>

                            <th>Costos</th>

                            <th>Estado</th>

                            <th></th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($trips as $trip)
                            @php

                                $assignment =
                                    $trip->activeAssignment ?? $trip->assignments->sortByDesc('assigned_at')->first();

                                $tripCost = $trip->costs->where('status', '!=', 'CANCELLED')->sum('subtotal');

                            @endphp


                            <tr>

                                <td>

                                    <div class="fw-semibold">

                                        {{ $trip->trip_number }}

                                    </div>

                                    <small class="text-muted">

                                        {{ $trip->service_stage_label }}

                                    </small>

                                </td>


                                <td>

                                    {{ $trip->workOrder?->work_order_number ?: '-' }}

                                </td>


                                <td>

                                    <div>

                                        {{ $trip->client_name_snapshot }}

                                    </div>


                                    @if ($trip->subclient_name_snapshot)
                                        <small class="text-muted">

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

                                    @if ($assignment)
                                        <div class="small">

                                            {{ $assignment->driver?->full_name ?: '-' }}

                                        </div>

                                        <small class="text-muted">

                                            {{ $assignment->vehicle?->plate ?: '-' }}

                                        </small>
                                    @else
                                        <span class="text-muted">

                                            Sin asignar

                                        </span>
                                    @endif

                                </td>


                                <td>

                                    @if ($trip->standbyCalculation && $trip->standbyCalculation->status === 'CALCULATED')
                                        <span class="badge bg-warning-subtle text-warning">

                                            {{ $trip->standbyCalculation->billable_hours }}
                                            h

                                        </span>
                                    @else
                                        <span class="text-muted">

                                            -

                                        </span>
                                    @endif

                                </td>


                                <td>

                                    <span class="badge bg-info-subtle text-info">

                                        {{ $trip->transfers->count() }}

                                    </span>

                                </td>


                                <td>

                                    <strong>

                                        ${{ number_format((float) $tripCost, 2) }}

                                    </strong>

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


                                <td>

                                    <a href="{{ route('trips.show', $trip) }}" class="btn btn-sm btn-outline-primary">

                                        <i class="ti ti-eye"></i>

                                    </a>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="10" class="text-center py-5 text-muted">

                                    No existen datos para los filtros seleccionados.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="mt-3">

                {{ $trips->links() }}

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- CHART.JS --}}
    {{-- ========================================================= --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                /*
                |--------------------------------------------------------------------------
                | CONFIGURACIÓN GENERAL
                |--------------------------------------------------------------------------
                */

                Chart.defaults.font.family =
                    "'Plus Jakarta Sans', sans-serif";


                /*
                |--------------------------------------------------------------------------
                | VIAJES POR ESTADO
                |--------------------------------------------------------------------------
                */

                new Chart(
                    document.getElementById(
                        'statusChart'
                    ), {
                        type: 'doughnut',

                        data: {

                            labels: @json($statusChart['labels']),

                            datasets: [

                                {
                                    data: @json($statusChart['data'])
                                }
                            ]
                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            plugins: {

                                legend: {

                                    position: 'bottom'
                                }
                            }
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | VIAJES POR OPERACIÓN
                |--------------------------------------------------------------------------
                */

                new Chart(
                    document.getElementById(
                        'operationChart'
                    ), {
                        type: 'pie',

                        data: {

                            labels: @json($operationChart['labels']),

                            datasets: [

                                {
                                    data: @json($operationChart['data'])
                                }
                            ]
                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            plugins: {

                                legend: {

                                    position: 'bottom'
                                }
                            }
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | VIAJES POR DÍA
                |--------------------------------------------------------------------------
                */

                new Chart(
                    document.getElementById(
                        'dailyChart'
                    ), {
                        type: 'line',

                        data: {

                            labels: @json($dailyChart['labels']),

                            datasets: [

                                {
                                    label: 'Viajes',

                                    data: @json($dailyChart['data']),

                                    tension: 0.3,

                                    fill: false
                                }
                            ]
                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            scales: {

                                y: {

                                    beginAtZero: true,

                                    ticks: {

                                        precision: 0
                                    }
                                }
                            }
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | VIAJES POR CLIENTE
                |--------------------------------------------------------------------------
                */

                new Chart(
                    document.getElementById(
                        'clientChart'
                    ), {
                        type: 'bar',

                        data: {

                            labels: @json($clientChart['labels']),

                            datasets: [

                                {
                                    label: 'Viajes',

                                    data: @json($clientChart['data'])
                                }
                            ]
                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            indexAxis: 'y',

                            scales: {

                                x: {

                                    beginAtZero: true,

                                    ticks: {

                                        precision: 0
                                    }
                                }
                            },

                            plugins: {

                                legend: {

                                    display: false
                                }
                            }
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | COSTOS
                |--------------------------------------------------------------------------
                */

                new Chart(
                    document.getElementById(
                        'costChart'
                    ), {
                        type: 'bar',

                        data: {

                            labels: @json($costChart['labels']),

                            datasets: [

                                {
                                    label: 'USD',

                                    data: @json($costChart['data'])
                                }
                            ]
                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            scales: {

                                y: {

                                    beginAtZero: true
                                }
                            },

                            plugins: {

                                legend: {

                                    display: false
                                }
                            }
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | STAND-BY
                |--------------------------------------------------------------------------
                */

                new Chart(
                    document.getElementById(
                        'standbyChart'
                    ), {
                        type: 'bar',

                        data: {

                            labels: @json($standbyChart['labels']),

                            datasets: [

                                {
                                    label: 'Horas',

                                    data: @json($standbyChart['data'])
                                }
                            ]
                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            indexAxis: 'y',

                            scales: {

                                x: {

                                    beginAtZero: true
                                }
                            },

                            plugins: {

                                legend: {

                                    display: false
                                }
                            }
                        }
                    }
                );

            }
        );
    </script>

@endsection

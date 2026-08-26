@extends('layouts.app')

@section('title', 'Costos y Liquidaciones | Karpan Logística')

@section('content')

    {{-- ========================================================= --}}
    {{-- MENSAJES --}}
    {{-- ========================================================= --}}

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">

            <i class="ti ti-circle-check me-1"></i>

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">

            <i class="ti ti-alert-circle me-1"></i>

            {{ session('warning') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    @if ($errors->any())

        <div class="alert alert-danger">

            <div class="fw-semibold mb-2">

                <i class="ti ti-alert-circle me-1"></i>

                Se encontraron los siguientes errores:

            </div>


            @foreach ($errors->all() as $error)
                <div>
                    {{ $error }}
                </div>
            @endforeach

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">

                Costos y Liquidaciones

            </h4>

            <p class="text-muted mb-0">

                Control económico de viajes, Stand-by,
                transferencias y conceptos adicionales.

            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- RESUMEN --}}
    {{-- ========================================================= --}}

    <div class="row">

        {{-- PENDIENTE --}}

        <div class="col-md-4">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <small class="text-muted d-block mb-1">

                                Pendiente de aprobación

                            </small>

                            <h3 class="fw-bold mb-0">

                                ${{ number_format((float) $summary['pending'], 2) }}

                            </h3>

                        </div>


                        <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center"
                            style="width:50px;height:50px;">

                            <i class="ti ti-clock fs-6"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- APROBADO --}}

        <div class="col-md-4">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <small class="text-muted d-block mb-1">

                                Aprobado

                            </small>

                            <h3 class="fw-bold mb-0">

                                ${{ number_format((float) $summary['approved'], 2) }}

                            </h3>

                        </div>


                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center"
                            style="width:50px;height:50px;">

                            <i class="ti ti-circle-check fs-6"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- TOTAL --}}

        <div class="col-md-4">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <small class="text-muted d-block mb-1">

                                Total vigente

                            </small>

                            <h3 class="fw-bold mb-0">

                                ${{ number_format((float) $summary['total'], 2) }}

                            </h3>

                        </div>


                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                            style="width:50px;height:50px;">

                            <i class="ti ti-receipt fs-6"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- VIAJES SIN COSTOS --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                <div>

                    <h5 class="fw-semibold mb-1">

                        Viajes pendientes de costos

                    </h5>

                    <p class="text-muted mb-0">

                        Viajes que todavía no tienen conceptos económicos registrados.

                    </p>

                </div>


                <span class="badge bg-warning-subtle text-warning">

                    {{ $tripsWithoutCosts->count() }}
                    pendiente(s)

                </span>

            </div>


            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Viaje</th>

                            <th>Orden</th>

                            <th>Cliente</th>

                            <th>Ruta</th>

                            <th>Programación</th>

                            <th>Stand-by</th>

                            <th>Transferencias</th>

                            <th>Estado</th>

                            <th></th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($tripsWithoutCosts
                                            as $trip)
                            <tr>

                                {{-- VIAJE --}}

                                <td>

                                    <div class="fw-semibold">

                                        {{ $trip->trip_number }}

                                    </div>


                                    <small class="text-muted">

                                        {{ $trip->service_stage_label }}

                                        @if ($trip->service_number)
                                            · Servicio #{{ $trip->service_number }}
                                        @endif

                                    </small>

                                </td>


                                {{-- OT --}}

                                <td>

                                    @if ($trip->workOrder)
                                        <a href="{{ route('work-orders.show', $trip->workOrder) }}" class="fw-semibold">

                                            {{ $trip->workOrder->work_order_number }}

                                        </a>
                                    @else
                                        -
                                    @endif

                                </td>


                                {{-- CLIENTE --}}

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


                                {{-- RUTA --}}

                                <td>

                                    <div>

                                        {{ $trip->origin_name_snapshot }}

                                    </div>

                                    <small class="text-muted">

                                        <i class="ti ti-arrow-right me-1"></i>

                                        {{ $trip->destination_name_snapshot }}

                                    </small>

                                </td>


                                {{-- PROGRAMACIÓN --}}

                                <td>

                                    @if ($trip->scheduled_start_at)
                                        {{ $trip->scheduled_start_at->format('d/m/Y') }}

                                        <small class="text-muted d-block">

                                            {{ $trip->scheduled_start_at->format('H:i') }}

                                        </small>
                                    @else
                                        -
                                    @endif

                                </td>


                                {{-- STAND-BY --}}

                                <td>

                                    @if ($trip->standbyCalculation && $trip->standbyCalculation->status === 'CALCULATED')
                                        <span class="badge bg-success-subtle text-success">

                                            {{ $trip->standbyCalculation->billable_hours }}
                                            h

                                        </span>
                                    @elseif ($trip->standbyCalculation)
                                        <span class="badge bg-warning-subtle text-warning">

                                            Pendiente

                                        </span>
                                    @else
                                        <span class="text-muted">

                                            -

                                        </span>
                                    @endif

                                </td>


                                {{-- TRANSFERENCIAS --}}

                                <td>

                                    @if ($trip->transfers->count() > 0)
                                        <span class="badge bg-info-subtle text-info">

                                            {{ $trip->transfers->count() }}

                                        </span>
                                    @else
                                        <span class="text-muted">

                                            0

                                        </span>
                                    @endif

                                </td>


                                {{-- ESTADO --}}

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


                                {{-- ACCIÓN --}}

                                <td class="text-end">

                                    <a href="{{ route('costs.trip', $trip) }}" class="btn btn-sm btn-primary">

                                        <i class="ti ti-plus me-1"></i>

                                        Registrar costo

                                    </a>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="9" class="text-center py-5 text-muted">

                                    <i class="ti ti-circle-check fs-7 d-block mb-2"></i>

                                    No existen viajes pendientes de costos.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- VIAJES CON COSTOS PENDIENTES --}}
    {{-- ========================================================= --}}

    @if ($tripsWithPendingCosts->count() > 0)

        <div class="card">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h5 class="fw-semibold mb-1">

                            Pendientes de aprobación

                        </h5>

                        <p class="text-muted mb-0">

                            Viajes que ya tienen costos registrados
                            pero todavía poseen valores pendientes.

                        </p>

                    </div>


                    <span class="badge bg-warning-subtle text-warning">

                        {{ $tripsWithPendingCosts->count() }}

                    </span>

                </div>


                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th>Viaje</th>

                                <th>OT</th>

                                <th>Cliente</th>

                                <th>Booking</th>

                                <th>Pendiente</th>

                                <th></th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach ($tripsWithPendingCosts as $trip)
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

                                        {{ $trip->client_name_snapshot }}

                                    </td>


                                    <td>

                                        {{ $trip->booking_number ?: '-' }}

                                    </td>


                                    <td>

                                        <strong class="text-warning">

                                            ${{ number_format((float) $trip->pending_cost_total, 2) }}

                                        </strong>

                                    </td>


                                    <td class="text-end">

                                        <a href="{{ route('costs.trip', $trip) }}" class="btn btn-sm btn-outline-primary">

                                            <i class="ti ti-eye me-1"></i>

                                            Revisar

                                        </a>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- COSTOS REGISTRADOS --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                <div>

                    <h5 class="fw-semibold mb-1">

                        Costos registrados

                    </h5>

                    <p class="text-muted mb-0">

                        Histórico de conceptos económicos ingresados al sistema.

                    </p>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- FILTROS --}}
            {{-- ========================================================= --}}

            <form method="GET" action="{{ route('costs.index') }}" class="row g-2 mb-4">

                <div class="col-md-4">

                    <input type="text" name="search" class="form-control"
                        placeholder="Viaje, OT, cliente, booking, transferencia..." value="{{ $search }}">

                </div>


                <div class="col-md-3">

                    <select name="type" class="form-select">

                        <option value="">

                            Todos los tipos

                        </option>


                        <option value="BASE" @selected($type === 'BASE')>

                            Tarifa base

                        </option>


                        <option value="STANDBY" @selected($type === 'STANDBY')>

                            Stand-by

                        </option>


                        <option value="TRANSFER" @selected($type === 'TRANSFER')>

                            Transferencia

                        </option>


                        <option value="ADDITIONAL" @selected($type === 'ADDITIONAL')>

                            Adicional

                        </option>

                    </select>

                </div>


                <div class="col-md-3">

                    <select name="status" class="form-select">

                        <option value="">

                            Todos los estados

                        </option>


                        <option value="PENDING" @selected($status === 'PENDING')>

                            Pendiente

                        </option>


                        <option value="APPROVED" @selected($status === 'APPROVED')>

                            Aprobado

                        </option>


                        <option value="CANCELLED" @selected($status === 'CANCELLED')>

                            Cancelado

                        </option>

                    </select>

                </div>


                <div class="col-md-2">

                    <button type="submit" class="btn btn-outline-primary w-100">

                        <i class="ti ti-search me-1"></i>

                        Buscar

                    </button>

                </div>

            </form>


            {{-- ========================================================= --}}
            {{-- TABLA --}}
            {{-- ========================================================= --}}

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Viaje</th>

                            <th>Cliente</th>

                            <th>Concepto</th>

                            <th>Cantidad</th>

                            <th>Unitario</th>

                            <th>Subtotal</th>

                            <th>Estado</th>

                            <th></th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($costs
                                            as $cost)
                            <tr>

                                {{-- VIAJE --}}

                                <td>

                                    <div class="fw-semibold">

                                        {{ $cost->trip?->trip_number ?: '-' }}

                                    </div>


                                    @if ($cost->transfer)
                                        <small class="text-muted">

                                            {{ $cost->transfer->transfer_number }}

                                        </small>
                                    @endif

                                </td>


                                {{-- CLIENTE --}}

                                <td>

                                    {{ $cost->trip?->client_name_snapshot ?: '-' }}

                                </td>


                                {{-- CONCEPTO --}}

                                <td>

                                    <div class="fw-semibold">

                                        {{ $cost->cost_type_label }}

                                    </div>

                                    <small class="text-muted">

                                        {{ $cost->description }}

                                    </small>

                                </td>


                                {{-- CANTIDAD --}}

                                <td>

                                    {{ number_format((float) $cost->quantity, 3) }}

                                </td>


                                {{-- UNITARIO --}}

                                <td>

                                    ${{ number_format((float) $cost->unit_price, 2) }}

                                </td>


                                {{-- SUBTOTAL --}}

                                <td>

                                    <strong>

                                        ${{ number_format((float) $cost->subtotal, 2) }}

                                    </strong>

                                </td>


                                {{-- ESTADO --}}

                                <td>

                                    <span class="badge {{ $cost->status_badge_class }}">

                                        {{ $cost->status_label }}

                                    </span>

                                </td>


                                {{-- ACCIÓN --}}

                                <td class="text-end">

                                    @if ($cost->trip)
                                        <a href="{{ route('costs.trip', $cost->trip) }}"
                                            class="btn btn-sm btn-outline-primary">

                                            <i class="ti ti-eye"></i>

                                        </a>
                                    @endif

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="8" class="text-center py-5 text-muted">

                                    <i class="ti ti-receipt fs-7 d-block mb-2"></i>

                                    Todavía no existen costos registrados.

                                    <div class="small mt-2">

                                        Utilice la sección
                                        <strong>
                                            Viajes pendientes de costos
                                        </strong>
                                        para registrar el primero.

                                    </div>

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

                {{ $costs->links() }}

            </div>

        </div>

    </div>

@endsection

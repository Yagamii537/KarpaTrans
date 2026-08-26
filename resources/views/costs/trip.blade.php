@extends('layouts.app')

@section('title', 'Costos del viaje | Karpan Logística')

@section('content')

    {{-- ========================================================= --}}
    {{-- MENSAJES --}}
    {{-- ========================================================= --}}

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    @if (session('warning'))
        <div class="alert alert-warning">

            {{ session('warning') }}

        </div>
    @endif


    @if ($errors->any())

        <div class="alert alert-danger">

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

                Costos del viaje

                {{ $trip->trip_number }}

            </h4>

            <p class="text-muted mb-0">

                {{ $trip->client_name_snapshot }}

                ·

                {{ $trip->origin_name_snapshot }}

                →

                {{ $trip->destination_name_snapshot }}

            </p>

        </div>


        <div class="d-flex gap-2">

            <a href="{{ route('costs.index') }}" class="btn btn-light">

                Regresar

            </a>


            <a href="{{ route('trips.show', $trip) }}" class="btn btn-outline-primary">

                Ver viaje

            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- TOTALES --}}
    {{-- ========================================================= --}}

    <div class="row">

        <div class="col-md-4">

            <div class="card">

                <div class="card-body">

                    <small class="text-muted d-block">

                        Pendiente

                    </small>

                    <h3 class="fw-bold">

                        ${{ number_format($totalPending, 2) }}

                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card">

                <div class="card-body">

                    <small class="text-muted d-block">

                        Aprobado

                    </small>

                    <h3 class="fw-bold">

                        ${{ number_format($totalApproved, 2) }}

                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card">

                <div class="card-body">

                    <small class="text-muted d-block">

                        Total vigente

                    </small>

                    <h3 class="fw-bold">

                        ${{ number_format($total, 2) }}

                    </h3>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- STAND-BY --}}
    {{-- ========================================================= --}}

    @if ($trip->standbyCalculation && $trip->standbyCalculation->status === 'CALCULATED')
        <div class="card">

            <div class="card-body">

                <h5 class="fw-semibold mb-1">

                    Stand-by calculado

                </h5>

                <p class="text-muted">

                    El viaje tiene un cálculo de Stand-by
                    que puede convertirse en costo.

                </p>


                <div class="row align-items-end">

                    <div class="col-md-4 mb-3">

                        <small class="text-muted d-block">

                            Horas facturables

                        </small>

                        <div class="fs-4 fw-bold">

                            {{ $trip->standbyCalculation->billable_hours }}
                            h

                        </div>

                    </div>


                    <div class="col-md-5 mb-3">

                        <form method="POST" action="{{ route('costs.standby', $trip) }}" class="row g-2">

                            @csrf


                            <div class="col">

                                <label class="form-label">

                                    Valor por hora

                                </label>

                                <input type="number" name="standby_unit_price" step="0.01" min="0"
                                    class="form-control" required>

                            </div>


                            <div class="col-auto align-self-end">

                                <button type="submit" class="btn btn-outline-primary">

                                    Agregar

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- NUEVO COSTO --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-body">

            <h5 class="fw-semibold mb-4">

                Registrar costo

            </h5>


            <form method="POST" action="{{ route('costs.store', $trip) }}">

                @csrf


                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            Tipo *

                        </label>

                        <select name="cost_type" id="cost_type" class="form-select" required>

                            <option value="">
                                Seleccione
                            </option>

                            <option value="BASE">
                                Tarifa base
                            </option>

                            <option value="STANDBY">
                                Stand-by manual
                            </option>

                            <option value="TRANSFER">
                                Transferencia
                            </option>

                            <option value="ADDITIONAL">
                                Adicional
                            </option>

                        </select>

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            Transferencia

                        </label>

                        <select name="trip_transfer_id" class="form-select">

                            <option value="">

                                No aplica

                            </option>


                            @foreach ($trip->transfers as $transfer)
                                <option value="{{ $transfer->id }}">

                                    {{ $transfer->transfer_number }}

                                    -

                                    {{ $transfer->origin_name_snapshot }}

                                    →

                                    {{ $transfer->destination_name_snapshot }}

                                </option>
                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            Descripción *

                        </label>

                        <input type="text" name="description" class="form-control" required>

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label">

                            Cantidad *

                        </label>

                        <input type="number" name="quantity" step="0.001" min="0" class="form-control"
                            value="1" required>

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label">

                            Valor unitario *

                        </label>

                        <input type="number" name="unit_price" step="0.01" min="0" class="form-control" required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Observaciones

                        </label>

                        <input type="text" name="notes" class="form-control">

                    </div>

                </div>


                <button type="submit" class="btn btn-primary">

                    Guardar costo

                </button>

            </form>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- COSTOS REGISTRADOS --}}
    {{-- ========================================================= --}}

    <div class="card">

        <div class="card-body">

            <h5 class="fw-semibold mb-4">

                Detalle económico

            </h5>


            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Tipo</th>

                            <th>Descripción</th>

                            <th>Cantidad</th>

                            <th>Unitario</th>

                            <th>Subtotal</th>

                            <th>Estado</th>

                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($trip->costs
                                                                        as $cost)
                            <tr>

                                <td>

                                    {{ $cost->cost_type_label }}

                                </td>


                                <td>

                                    {{ $cost->description }}

                                    @if ($cost->transfer)
                                        <small class="text-muted d-block">

                                            {{ $cost->transfer->transfer_number }}

                                        </small>
                                    @endif

                                </td>


                                <td>

                                    {{ number_format((float) $cost->quantity, 3) }}

                                </td>


                                <td>

                                    ${{ number_format((float) $cost->unit_price, 2) }}

                                </td>


                                <td>

                                    <strong>

                                        ${{ number_format((float) $cost->subtotal, 2) }}

                                    </strong>

                                </td>


                                <td>

                                    <span class="badge {{ $cost->status_badge_class }}">

                                        {{ $cost->status_label }}

                                    </span>

                                </td>


                                <td>

                                    @if ($cost->status === 'PENDING')
                                        <form method="POST" action="{{ route('costs.approve', $cost) }}"
                                            class="d-inline">

                                            @csrf

                                            <button class="btn btn-sm btn-outline-success">

                                                Aprobar

                                            </button>

                                        </form>
                                    @endif


                                    @if ($cost->status !== 'CANCELLED')
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="collapse" data-bs-target="#cancel-cost-{{ $cost->id }}">

                                            Cancelar

                                        </button>
                                    @endif

                                </td>

                            </tr>


                            @if ($cost->status !== 'CANCELLED')
                                <tr class="collapse" id="cancel-cost-{{ $cost->id }}">

                                    <td colspan="7">

                                        <form method="POST" action="{{ route('costs.cancel', $cost) }}"
                                            class="row g-2">

                                            @csrf


                                            <div class="col">

                                                <input type="text" name="reason" class="form-control"
                                                    placeholder="Motivo de cancelación" required>

                                            </div>


                                            <div class="col-auto">

                                                <button type="submit" class="btn btn-danger">

                                                    Confirmar cancelación

                                                </button>

                                            </div>

                                        </form>

                                    </td>

                                </tr>
                            @endif


                        @empty

                            <tr>

                                <td colspan="7" class="text-center py-4 text-muted">

                                    Este viaje todavía no tiene costos registrados.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection

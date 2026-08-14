@extends('layouts.app')

@section('title', 'Nuevo viaje | Karpan Logística')

@section('content')

    <div class="mb-4">

        <h4 class="fw-semibold mb-1">
            Nuevo viaje
        </h4>

        <p class="text-muted mb-0">
            Cree un viaje a partir de una orden de trabajo.
        </p>

    </div>

    <div class="card">

        <div class="card-body">

            <form method="POST" action="{{ route('trips.store') }}">

                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Orden de trabajo *
                        </label>

                        <select name="work_order_id" class="form-select" required>

                            <option value="">
                                Seleccione
                            </option>

                            @foreach ($workOrders as $order)
                                <option value="{{ $order->id }}" @selected(old('work_order_id', $selectedWorkOrder?->id) == $order->id)>

                                    {{ $order->work_order_number }}
                                    -
                                    {{ $order->client->business_name }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Inicio planificado *
                        </label>

                        <input type="datetime-local" name="scheduled_start_at" class="form-control"
                            value="{{ old('scheduled_start_at') }}" required>

                    </div>

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Fin estimado
                        </label>

                        <input type="datetime-local" name="scheduled_end_at" class="form-control"
                            value="{{ old('scheduled_end_at') }}">

                    </div>

                    <div class="col-12 mb-3">

                        <label class="form-label">
                            Observaciones
                        </label>

                        <textarea name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>

                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('trips.index') }}" class="btn btn-light">

                        Cancelar

                    </a>

                    <button class="btn btn-primary">

                        <i class="ti ti-device-floppy me-1"></i>
                        Guardar viaje

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection

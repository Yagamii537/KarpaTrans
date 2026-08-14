@extends('layouts.app')

@section('title', 'Editar viaje | Karpan Logística')

@section('content')

    <div class="mb-4">

        <h4 class="fw-semibold mb-1">
            {{ $trip->trip_number }}
        </h4>

        <p class="text-muted mb-0">
            Editar planificación.
        </p>

    </div>

    <div class="card">

        <div class="card-body">

            <form method="POST" action="{{ route('trips.update', $trip) }}">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Inicio planificado
                        </label>

                        <input type="datetime-local" name="scheduled_start_at" class="form-control"
                            value="{{ old('scheduled_start_at', $trip->scheduled_start_at->format('Y-m-d\TH:i')) }}"
                            required>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Fin estimado
                        </label>

                        <input type="datetime-local" name="scheduled_end_at" class="form-control"
                            value="{{ old('scheduled_end_at', $trip->scheduled_end_at ? $trip->scheduled_end_at->format('Y-m-d\TH:i') : '') }}">

                    </div>

                    <div class="col-12 mb-3">

                        <label class="form-label">
                            Observaciones
                        </label>

                        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $trip->notes) }}</textarea>

                    </div>

                </div>

                <button class="btn btn-primary">
                    Guardar cambios
                </button>

            </form>

        </div>

    </div>

@endsection

@extends('layouts.app')

@section('title', 'Editar restricción | Karpan Logística')

@section('content')

    <div class="mb-4">
        <h4 class="fw-semibold mb-1">
            Editar restricción
        </h4>

        <p class="text-muted mb-0">
            {{ $driverRestriction->driver->full_name }}
        </p>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST"
                action="{{ route('driver-restrictions.update', $driverRestriction) }}">

                @csrf
                @method('PUT')

                @include('driver-restrictions._form')

            </form>

        </div>
    </div>

@endsection

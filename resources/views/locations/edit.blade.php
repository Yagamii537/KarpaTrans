@extends('layouts.app')

@section('title', 'Editar ubicación | Karpan Logística')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="fw-semibold mb-1">
            Editar ubicación
        </h4>

        <p class="text-muted mb-0">
            {{ $location->name }}
        </p>
    </div>

    <a href="{{ route('locations.index') }}"
       class="btn btn-outline-secondary">

        <i class="ti ti-arrow-left me-1"></i>
        Regresar
    </a>

</div>

<div class="card">
    <div class="card-body">

        <form method="POST"
              action="{{ route('locations.update', $location) }}">

            @csrf
            @method('PUT')

            @include('locations._form')
        </form>

    </div>
</div>

@endsection

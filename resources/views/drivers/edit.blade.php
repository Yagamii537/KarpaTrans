@extends('layouts.app')

@section('title', 'Editar conductor | Karpan Logística')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">

    <div>
        <h4 class="fw-semibold mb-1">
            Editar conductor
        </h4>

        <p class="text-muted mb-0">
            {{ $driver->full_name }}
        </p>
    </div>

    <a href="{{ route('drivers.index') }}"
       class="btn btn-outline-secondary">

        <i class="ti ti-arrow-left me-1"></i>
        Regresar
    </a>

</div>

<div class="card">
    <div class="card-body">

        <form method="POST"
              action="{{ route('drivers.update', $driver) }}"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            @include('drivers._form')

        </form>

    </div>
</div>

@endsection

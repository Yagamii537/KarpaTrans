@extends('layouts.app')

@section('title', 'Nuevo vehículo | Karpan Logística')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-semibold mb-1">Nuevo vehículo</h4>
        <p class="text-muted mb-0">Registre un cabezal o vehículo de la flota.</p>
    </div>

    <a href="{{ route('vehicles.index') }}"
       class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i>
        Regresar
    </a>
</div>

<div class="card">
    <div class="card-body">

        <form method="POST"
              action="{{ route('vehicles.store') }}"
              enctype="multipart/form-data">

            @csrf
            @include('vehicles._form')
        </form>

    </div>
</div>

@endsection

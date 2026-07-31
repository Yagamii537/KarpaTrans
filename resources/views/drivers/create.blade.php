@extends('layouts.app')

@section('title', 'Nuevo conductor | Karpan Logística')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">

    <div>
        <h4 class="fw-semibold mb-1">
            Nuevo conductor
        </h4>

        <p class="text-muted mb-0">
            Registre la información personal, licencia y documentos.
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
              action="{{ route('drivers.store') }}"
              enctype="multipart/form-data">

            @csrf

            @include('drivers._form')

        </form>

    </div>
</div>

@endsection

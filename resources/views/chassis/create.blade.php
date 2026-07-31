@extends('layouts.app')

@section('title', 'Nuevo chasis | Karpan Logística')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-semibold mb-1">Nuevo chasis</h4>
        <p class="text-muted mb-0">Registre un chasis de transporte.</p>
    </div>

    <a href="{{ route('chassis.index') }}"
       class="btn btn-outline-secondary">
        Regresar
    </a>
</div>

<div class="card">
    <div class="card-body">

        <form method="POST"
              action="{{ route('chassis.store') }}"
              enctype="multipart/form-data">

            @csrf
            @include('chassis._form')
        </form>

    </div>
</div>

@endsection

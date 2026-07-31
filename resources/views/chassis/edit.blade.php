@extends('layouts.app')

@section('title', 'Editar chasis | Karpan Logística')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-semibold mb-1">Editar chasis</h4>
        <p class="text-muted mb-0">{{ $chassis->display_name }}</p>
    </div>

    <a href="{{ route('chassis.index') }}"
       class="btn btn-outline-secondary">
        Regresar
    </a>
</div>

<div class="card">
    <div class="card-body">

        <form method="POST"
              action="{{ route('chassis.update', $chassis) }}"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            @include('chassis._form')
        </form>

    </div>
</div>

@endsection

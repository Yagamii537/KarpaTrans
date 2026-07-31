@extends('layouts.app')

@section('title', 'Editar planta | Karpan Logística')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">

    <div>
        <h4 class="fw-semibold mb-1">
            Editar planta
        </h4>

        <p class="text-muted mb-0">
            {{ $plant->name }} — {{ $plant->client->business_name }}
        </p>
    </div>

    <a href="{{ route('plants.index') }}"
       class="btn btn-outline-secondary">

        <i class="ti ti-arrow-left me-1"></i>
        Regresar
    </a>

</div>

<div class="card">
    <div class="card-body">

        <form method="POST"
              action="{{ route('plants.update', $plant) }}">

            @csrf
            @method('PUT')

            @include('plants._form')
        </form>

    </div>
</div>

@endsection

@extends('layouts.app')

@section('title', 'Editar cliente | Karpan Logística')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">

    <div>
        <h4 class="fw-semibold mb-1">
            Editar cliente
        </h4>

        <p class="text-muted mb-0">
            {{ $client->business_name }}
        </p>
    </div>

    <a href="{{ route('clients.index') }}"
       class="btn btn-outline-secondary">

        <i class="ti ti-arrow-left me-1"></i>
        Regresar
    </a>

</div>

<div class="card">

    <div class="card-body">

        <form method="POST"
              action="{{ route('clients.update', $client) }}">

            @csrf
            @method('PUT')

            @include('clients._form')

        </form>

    </div>

</div>

@endsection

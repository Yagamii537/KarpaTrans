@extends('layouts.app')

@section('title', 'Editar tipo de carga | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-semibold mb-1">
                Editar tipo de carga
            </h4>

            <p class="text-muted mb-0">
                {{ $cargoType->name }}
            </p>
        </div>

    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('cargo-types.update', $cargoType) }}">

                @csrf
                @method('PUT')

                @include('cargo-types._form')

            </form>

        </div>
    </div>

@endsection

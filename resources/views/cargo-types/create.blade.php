@extends('layouts.app')

@section('title', 'Nuevo tipo de carga | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-semibold mb-1">
                Nuevo tipo de carga
            </h4>

            <p class="text-muted mb-0">
                Configure un tipo de mercancía o carga.
            </p>
        </div>

    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('cargo-types.store') }}">

                @csrf

                @include('cargo-types._form')

            </form>

        </div>
    </div>

@endsection

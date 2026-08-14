@extends('layouts.app')

@section('title', 'Nuevo contenedor | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                Nuevo contenedor
            </h4>

            <p class="text-muted mb-0">
                Registre un contenedor y su ubicación inicial.
            </p>

        </div>

        <a href="{{ route('containers.index') }}" class="btn btn-outline-secondary">

            <i class="ti ti-arrow-left me-1"></i>
            Regresar

        </a>

    </div>

    <div class="card">

        <div class="card-body">

            <form method="POST" action="{{ route('containers.store') }}">

                @csrf

                @include('containers._form')

            </form>

        </div>

    </div>

@endsection
